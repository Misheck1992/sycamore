const moment = require('moment');
const fs = require('fs');
const path = require('path');

// PAR Constants
const PAR_THRESHOLDS = [1, 30, 60, 90];

/**
 * Additional helper function to validate PAR report parameters
 * @param {Object} params - Parameters to validate
 * @returns {Object} Validated parameters
 */
function validatePARReportParameters(params) {
    const validatedParams = {
        officer: params.officer || 'All',
        product: params.product || 'All',
        branch: params.branch || 'All',
        dateFrom: null,
        dateTo: null
    };

    // Validate dates
    if (params.dateFrom) {
        const fromDate = moment(params.dateFrom);
        if (fromDate.isValid()) {
            validatedParams.dateFrom = fromDate.format('YYYY-MM-DD');
        }
    }

    if (params.dateTo) {
        const toDate = moment(params.dateTo);
        if (toDate.isValid()) {
            validatedParams.dateTo = toDate.format('YYYY-MM-DD');
        }
    }

    // Ensure date range is logical
    if (validatedParams.dateFrom && validatedParams.dateTo) {
        const fromMoment = moment(validatedParams.dateFrom);
        const toMoment = moment(validatedParams.dateTo);

        if (fromMoment.isAfter(toMoment)) {
            // Swap dates if from date is after to date
            validatedParams.dateFrom = toMoment.format('YYYY-MM-DD');
            validatedParams.dateTo = fromMoment.format('YYYY-MM-DD');
        }
    }

    return validatedParams;
}

/**
 * Calculate PAR for a specific days range
 * @param {Array} payments - Array of payment data
 * @param {number} minDays - Minimum days
 * @param {number} maxDays - Maximum days
 * @returns {number} PAR amount for the range
 */
function calculatePARForDaysRange(payments, minDays, maxDays) {
    return payments
        .filter(payment => {
            const days = payment.days_overdue || 0;
            return days >= minDays && days <= maxDays;
        })
        .reduce((total, payment) => total + (parseFloat(payment.amount_due) || 0), 0);
}

/**
 * Get outstanding principal for a loan
 * @param {number} loanId - Loan ID
 * @param {Object} db - Database connection
 * @returns {Promise<number>} Outstanding principal
 */
async function getOutstandingPrincipal(loanId, db) {
    return new Promise((resolve, reject) => {
        const query = `
            SELECT 
                COALESCE(SUM(principal), 0) - COALESCE(SUM(CASE WHEN status = 'PAID' THEN principal ELSE 0 END), 0) as outstanding_principal
            FROM payement_schedules
            WHERE loan_id = ?
        `;

        db.query(query, [loanId], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            const result = results[0] || {};
            resolve(parseFloat(result.outstanding_principal) || 0);
        });
    });
}

/**
 * Get all payments for a loan
 * @param {number} loanId - Loan ID
 * @param {Object} db - Database connection
 * @returns {Promise<Array>} Array of payments
 */
async function getAllPaymentsForLoan(loanId, db) {
    return new Promise((resolve, reject) => {
        const query = `
            SELECT 
                id, payment_schedule, payment_number, amount, principal, interest,
                paid_amount, status, 
                DATEDIFF(CURDATE(), payment_schedule) as days_overdue,
                (amount - paid_amount) as amount_due
            FROM payement_schedules
            WHERE loan_id = ? AND payment_schedule < CURDATE()
            ORDER BY payment_number ASC
        `;

        db.query(query, [loanId], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            resolve(results || []);
        });
    });
}

/**
 * Generate PAR Report V2
 * @param {number} reportId - Report ID
 * @param {string} officer - Officer filter
 * @param {string} product - Product filter
 * @param {string} branch - Branch filter
 * @param {string} dateFrom - Start date
 * @param {string} dateTo - End date
 * @param {Object} db - Database connection
 * @param {Object} reportTrackers - Report trackers object
 * @returns {Promise<boolean>} Success status
 */
async function generatePARReportV2(reportId, officer, product, branch, dateFrom, dateTo, db, reportTrackers) {
    console.log('====== PAR REPORT GENERATION STARTED ======');
    console.log('[1/6] Establishing database connection...');
    console.log(`Report will be saved to: ${reportTrackers[reportId].filePath}`);
    console.log(`Filters - Officer: ${officer || 'All'}, Product: ${product}, Branch: ${branch }`);
    console.log(`Date Range - From: ${dateFrom} To: ${dateTo}`);

    try {
        reportTrackers[reportId].percentage = 10;
        console.log('[2/6] Database connection established successfully.');
         console.log('new file of par');
        // Get current date for report
        const currentDate = moment().format('YYYY-MM-DD');

        console.log('[3/6] Calculating total portfolio size...');
        // Calculate total portfolio size using regular callback-style query
        return new Promise((resolve, reject) => {
            db.query(`
                SELECT SUM(loan_principal) as total_portfolio
                FROM loan
                WHERE loan_status IN ('APPROVED', 'ACTIVE')
                  AND disbursed = 'Yes'
            `, async (err, portfolioRows) => {
                if (err) {
                    reject(err);
                    return;
                }

                const totalPortfolio = parseFloat(portfolioRows[0]?.total_portfolio || 0);
                console.log(`      Total portfolio size: K${new Intl.NumberFormat('en-US').format(totalPortfolio.toFixed(2))}`);

                reportTrackers[reportId].percentage = 20;

                console.log('[4/6] Fetching loan data for PAR analysis...');
                
                // Build where clause based on filters
                let whereClause = `l.loan_status IN ('APPROVED', 'ACTIVE') AND l.disbursed = 'Yes'`;
                const queryParams = [];

                if (officer && officer !== 'All') {
                    whereClause += ` AND l.loan_added_by = ?`;
                    queryParams.push(officer);
                }

                if (product && product !== 'All') {
                    whereClause += ` AND l.loan_product = ?`;
                    queryParams.push(product);
                }

                if (branch && branch !== 'All') {
                    whereClause += ` AND l.branch = ?`;
                    queryParams.push(branch);
                }

                const loanQuery = `
                    SELECT
                        l.loan_id,
                        l.loan_number,
                        l.loan_principal,
                        l.loan_date,
                        l.loan_period,
                        l.loan_interest,
                        l.loan_added_by,
                        l.branch,
                        l.loan_product,
                        l.loan_customer,
                        l.customer_type,
                        CASE
                            WHEN l.customer_type = 'individual' THEN CONCAT(ic.Firstname, ' ', ic.Lastname)
                            WHEN l.customer_type = 'group' THEN CONCAT(g.group_name, ' (', g.group_code, ')')
                            ELSE 'Unknown Customer'
                        END as customer_name,
                        e.Firstname as officer_first_name,
                        e.Lastname as officer_last_name,
                        b.BranchName as branch_name,
                        lp.product_name
                    FROM loan l
                    LEFT JOIN individual_customers ic ON l.loan_customer = ic.id AND l.customer_type = 'individual'
                    LEFT JOIN \`groups\` g ON l.loan_customer = g.group_id AND l.customer_type = 'group'
                    LEFT JOIN employees e ON l.loan_added_by = e.id
                    LEFT JOIN branches b ON l.branch = b.id
                    LEFT JOIN loan_products lp ON l.loan_product = lp.loan_product_id
                    WHERE ${whereClause}
                `;

                db.query(loanQuery, queryParams, async (err, loans) => {
                    if (err) {
                        reject(err);
                        return;
                    }

                    console.log(`      Found ${loans.length} active loans to analyze`);
                    reportTrackers[reportId].percentage = 40;

                    console.log('[5/6] Processing loan data for PAR calculations...');
                    const processedLoans = [];
                    let processedCount = 0;

                    try {
                        // Process each loan for PAR calculations
                        for (const loan of loans) {
                            const loanId = loan.loan_id;
                            const customerName = loan.customer_name || 'Unknown';
                            const officerName = `${loan.officer_first_name || ''} ${loan.officer_last_name || ''}`.trim() || 'Unknown';
                            const branchName = loan.branch_name || 'Unknown';

                            // Get outstanding principal
                            const outstandingPrincipal = await getOutstandingPrincipal(loanId, db);

                            // Get all payments for this loan
                            const allPayments = await getAllPaymentsForLoan(loanId, db);

                            // Calculate PAR for different day ranges
                            const par_1_7_days = calculatePARForDaysRange(allPayments, 1, 7);
                            const par_8_15_days = calculatePARForDaysRange(allPayments, 8, 15);
                            const par_16_30_days = calculatePARForDaysRange(allPayments, 16, 30);
                            const par_31plus_days = calculatePARForDaysRange(allPayments, 31, 999999);
                            const par_1day = calculatePARForDaysRange(allPayments, 1, 999999);

                            processedLoans.push({
                                customerName,
                                loanNumber: loan.loan_number || 'N/A',
                                productName: loan.product_name || 'N/A',
                                officerName,
                                branchName,
                                loanDate: loan.loan_date,
                                loanPeriod: loan.loan_period,
                                loanInterest: loan.loan_interest,
                                currentBalance: parseFloat(outstandingPrincipal) || 0,
                                par_1_7_days,
                                par_8_15_days,
                                par_16_30_days,
                                par_31plus_days,
                                par_1day
                            });

                            processedCount++;
                            if (processedCount % 10 === 0) {
                                console.log(`      Processed ${processedCount}/${loans.length} loans`);
                            }
                        }

                        reportTrackers[reportId].percentage = 80;

                        console.log('[6/6] Generating Excel-style HTML report and saving to file...');

                        // Get the branch name if a specific branch was selected
                        let selectedBranchName = 'All Branches';
                        if (branch && branch !== 'All') {
                            try {
                                const branchResult = await new Promise((resolve, reject) => {
                                    db.query('SELECT BranchName FROM branches WHERE id = ?',
                                        [branch], (err, result) => {
                                            if (err) {
                                                reject(err);
                                            } else {
                                                resolve(result);
                                            }
                                        });
                                });

                                if (branchResult && branchResult.length > 0) {
                                    selectedBranchName = branchResult[0].BranchName;
                                }
                            } catch (error) {
                                console.error(`Error getting selected branch name: ${error.message}`);
                            }
                        }

                        // Generate HTML report that matches Excel format
                        const reportHtml = generateExcelStylePARReport(
                            currentDate,
                            processedLoans,
                            totalPortfolio,
                            selectedBranchName,
                            dateFrom,
                            dateTo
                        );

                        // Write to the file path stored in the tracker
                        fs.writeFileSync(reportTrackers[reportId].filePath, reportHtml);

                        reportTrackers[reportId].percentage = 100;

                        console.log(`\n✅ PAR Report generated successfully: ${reportTrackers[reportId].filePath}`);
                        console.log('====== PAR REPORT GENERATION COMPLETED ======');

                        // Resolve with success
                        resolve(true);
                    } catch (error) {
                        reject(error);
                    }
                });
            });
        });
    } catch (error) {
        console.error('\n❌ Error generating PAR report:', error);
        console.log('====== PAR REPORT GENERATION FAILED ======');
        throw error;
    }
}

/**
 * Function to generate HTML report in Excel format - update to include date filters in the header
 * @param {string} currentDate - Current date
 * @param {Array} loans - Array of processed loans
 * @param {number} totalPortfolio - Total portfolio amount
 * @param {string} branchName - Branch name
 * @param {string} dateFrom - Start date
 * @param {string} dateTo - End date
 * @returns {string} HTML report content
 */
function generateExcelStylePARReport(currentDate, loans, totalPortfolio, branchName, dateFrom, dateTo) {
    // Format date for display
    const formatDisplayDate = (dateString) => {
        if (!dateString) return '';
        return moment(dateString).format('MM/DD/YYYY');
    };

    // Format functions
    const formatCurrency = (amount) => {
        if (isNaN(amount) || amount === null || amount === undefined) {
            return '0.00';
        }
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    };

    const formatPercentage = (value) => {
        if (isNaN(value) || value === null || value === undefined || value === 0) {
            return '0.00%';
        }
        return new Intl.NumberFormat('en-US', {
            style: 'percent',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value / 100);
    };

    // Create date range display
    let dateRangeDisplay = '';
    if (dateFrom && dateTo) {
        dateRangeDisplay = `Date Range: ${formatDisplayDate(dateFrom)} - ${formatDisplayDate(dateTo)}`;
    } else if (dateFrom) {
        dateRangeDisplay = `From: ${formatDisplayDate(dateFrom)}`;
    } else if (dateTo) {
        dateRangeDisplay = `To: ${formatDisplayDate(dateTo)}`;
    }

    // Calculate totals and PAR percentages
    const totalCurrentBalance = loans.reduce((sum, loan) => sum + loan.currentBalance, 0);
    const total_1_7_days = loans.reduce((sum, loan) => sum + loan.par_1_7_days, 0);
    const total_8_15_days = loans.reduce((sum, loan) => sum + loan.par_8_15_days, 0);
    const total_16_30_days = loans.reduce((sum, loan) => sum + loan.par_16_30_days, 0);
    const total_31plus_days = loans.reduce((sum, loan) => sum + loan.par_31plus_days, 0);
    const total_1day = loans.reduce((sum, loan) => sum + loan.par_1day, 0);

    // Calculate percentages
    const par_1_7_percent = totalCurrentBalance > 0 ? (total_1_7_days / totalCurrentBalance) * 100 : 0;
    const par_8_15_percent = totalCurrentBalance > 0 ? (total_8_15_days / totalCurrentBalance) * 100 : 0;
    const par_16_30_percent = totalCurrentBalance > 0 ? (total_16_30_days / totalCurrentBalance) * 100 : 0;
    const par_31plus_percent = totalCurrentBalance > 0 ? (total_31plus_days / totalCurrentBalance) * 100 : 0;
    const par_1day_percent = totalCurrentBalance > 0 ? (total_1day / totalCurrentBalance) * 100 : 0;

    // Generate loan rows
    const loanRows = loans.map(loan => {
        const loan_par_1_7_percent = loan.currentBalance > 0 ? (loan.par_1_7_days / loan.currentBalance) * 100 : 0;
        const loan_par_8_15_percent = loan.currentBalance > 0 ? (loan.par_8_15_days / loan.currentBalance) * 100 : 0;
        const loan_par_16_30_percent = loan.currentBalance > 0 ? (loan.par_16_30_days / loan.currentBalance) * 100 : 0;
        const loan_par_31plus_percent = loan.currentBalance > 0 ? (loan.par_31plus_days / loan.currentBalance) * 100 : 0;
        const loan_par_1day_percent = loan.currentBalance > 0 ? (loan.par_1day / loan.currentBalance) * 100 : 0;

        return `
            <tr>
                <td>${loan.customerName}</td>
                <td>${loan.loanNumber}</td>
                <td>${loan.productName}</td>
                <td>${loan.officerName}</td>
                <td>${loan.branchName}</td>
                <td style="text-align: right;">${formatCurrency(loan.currentBalance)}</td>
                <td style="text-align: right;">${formatCurrency(loan.par_1_7_days)}</td>
                <td style="text-align: right;">${formatPercentage(loan_par_1_7_percent)}</td>
                <td style="text-align: right;">${formatCurrency(loan.par_8_15_days)}</td>
                <td style="text-align: right;">${formatPercentage(loan_par_8_15_percent)}</td>
                <td style="text-align: right;">${formatCurrency(loan.par_16_30_days)}</td>
                <td style="text-align: right;">${formatPercentage(loan_par_16_30_percent)}</td>
                <td style="text-align: right;">${formatCurrency(loan.par_31plus_days)}</td>
                <td style="text-align: right;">${formatPercentage(loan_par_31plus_percent)}</td>
                <td style="text-align: right;">${formatCurrency(loan.par_1day)}</td>
                <td style="text-align: right;">${formatPercentage(loan_par_1day_percent)}</td>
            </tr>
        `;
    }).join('');

    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>PAR Report - ${moment(currentDate).format('MM/DD/YYYY')}</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
                table { border-collapse: collapse; width: 100%; font-size: 11px; }
                th, td { border: 1px solid #000; padding: 4px 6px; }
                .header-row td { background-color: #f0f0f0; font-weight: bold; text-align: center; }
                .total-row { background-color: #e0e0e0; font-weight: bold; }
                .date-filter { font-style: italic; color: #666; }
                @media print { body { margin: 0; } }
                .action { float: right; margin-bottom: 10px; }
                button { padding: 5px 15px; margin-left: 5px; cursor: pointer; }
            </style>
            <!-- Include SheetJS library for Excel exports -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
            <script>
                function exportData(type) {
                    const fileName = 'PAR_Report.' + type;
                    const table = document.getElementById("results-table");
                    const wb = XLSX.utils.table_to_book(table);
                    XLSX.writeFile(wb, fileName);
                }
            </script>
        </head>
        <body>
            <div class="action">
                <span>Export table to:</span>
                <button onclick="exportData('xlsx')">Excel (xlsx)</button>
                <button onclick="exportData('xls')">Excel (xls)</button>
                <button onclick="exportData('csv')">CSV</button>
            </div>
            
            <table id="results-table">
                <tr class="header-row">
                    <td>Sycamore Limited (MALAWI)</td>
                    <td colspan="5">Summarized Outstanding portfolio Report</td>
                    <td>As Of:</td>
                    <td colspan="2">${moment(currentDate).format('MM/DD/YYYY')}</td>
                    <td colspan="7"></td>
                </tr>
                <tr class="header-row">
                    <td>Branch:</td>
                    <td colspan="5">${branchName}</td>
                    <td colspan="10" class="date-filter">${dateRangeDisplay}</td>
                </tr>
                <tr class="header-row">
                    <td>Client Name</td>
                    <td>Loan #</td>
                    <td>Product</td>
                    <td>Relationship Officer</td>
                    <td>Branch</td>
                    <td>CURRENT BAL<br>(Principal Balances MK)</td>
                    <td>1-7 days MK</td>
                    <td>%PAR</td>
                    <td>8_15 days MK</td>
                    <td>%PAR</td>
                    <td>16_30D MK</td>
                    <td>%PAR</td>
                    <td>>31DAYS MK</td>
                    <td>%PAR</td>
                    <td>1Day MK</td>
                    <td>%PAR</td>
                </tr>
                ${loanRows}
                <tr class="total-row">
                    <td colspan="5">TOTAL</td>
                    <td style="text-align: right;">${formatCurrency(totalCurrentBalance)}</td>
                    <td style="text-align: right;">${formatCurrency(total_1_7_days)}</td>
                    <td style="text-align: right;">${formatPercentage(par_1_7_percent)}</td>
                    <td style="text-align: right;">${formatCurrency(total_8_15_days)}</td>
                    <td style="text-align: right;">${formatPercentage(par_8_15_percent)}</td>
                    <td style="text-align: right;">${formatCurrency(total_16_30_days)}</td>
                    <td style="text-align: right;">${formatPercentage(par_16_30_percent)}</td>
                    <td style="text-align: right;">${formatCurrency(total_31plus_days)}</td>
                    <td style="text-align: right;">${formatPercentage(par_31plus_percent)}</td>
                    <td style="text-align: right;">${formatCurrency(total_1day)}</td>
                    <td style="text-align: right;">${formatPercentage(par_1day_percent)}</td>
                </tr>
            </table>
        </body>
        </html>
    `;
}

/**
 * Enhanced PAR Report with detailed loan information
 * @param {number} reportId - Report ID
 * @param {string} officer - Officer filter
 * @param {string} product - Product filter
 * @param {string} branch - Branch filter
 * @param {string} dateFrom - Start date
 * @param {string} dateTo - End date
 * @param {Object} db - Database connection
 * @param {Object} reportTrackers - Report trackers object
 * @returns {Promise<boolean>} Success status
 */
async function generatePARReportV2Enhanced(reportId, officer, product, branch, dateFrom, dateTo, db, reportTrackers) {
    console.log('====== ENHANCED PAR DETAILED PORTFOLIO REPORT GENERATION STARTED ======');
    console.log('[1/6] Establishing database connection...');
    console.log(`Report will be saved to: ${reportTrackers[reportId].filePath}`);
    console.log(`Filters - Officer: ${officer || 'All'}, Product: ${product}, Branch: ${branch}`);
    console.log(`Date Range - From: ${dateFrom} To: ${dateTo}`);

    try {
        reportTrackers[reportId].percentage = 10;
        console.log('[2/6] Database connection established successfully.');

        // Get current date for report
        const currentDate = moment().format('YYYY-MM-DD');

        console.log('[3/6] Calculating total portfolio size...');
        // Calculate total portfolio size
        return new Promise((resolve, reject) => {
            db.query(`
                SELECT SUM(loan_principal) as total_portfolio
                FROM loan
                WHERE loan_status IN ('APPROVED', 'ACTIVE')
                  AND disbursed = 'Yes'
            `, async (err, portfolioRows) => {
                if (err) {
                    reject(err);
                    return;
                }

                const totalPortfolio = parseFloat(portfolioRows[0]?.total_portfolio || 0);
                console.log(`      Total portfolio size: K${new Intl.NumberFormat('en-US').format(totalPortfolio.toFixed(2))}`);

                reportTrackers[reportId].percentage = 20;

                console.log('[4/6] Fetching detailed loan data with PAR analysis...');
                // Enhanced query with more loan details - REMOVED maturity_date
                let whereClause = `
                    l.loan_status IN ('APPROVED', 'ACTIVE')
                    AND l.disbursed = 'Yes'
                `;

                if (officer && officer !== 'All') {
                    whereClause += ` AND l.loan_added_by = ${officer}`;
                }

                if (product && product !== 'All') {
                    whereClause += ` AND l.loan_product = ${product}`;
                }

                if (branch && branch !== 'All') {
                    whereClause += ` AND l.branch = ${branch}`;
                }

                // FIXED: Removed l.maturity_date from the query
                const enhancedLoanQuery = `
                    SELECT 
                        l.loan_id,
                        l.loan_number,
                        l.loan_principal,
                        l.loan_amount_total as total_loan_amount,
                        l.loan_period,
                        l.period_type,
                        l.loan_interest,
                        l.loan_added_date,
                        l.loan_date,
                        l.loan_added_by,
                        l.branch,
                        l.loan_product,
                        l.loan_customer,
                        l.customer_type,
                        l.loan_status,
                        CASE 
                            WHEN l.customer_type = 'individual' THEN CONCAT(ic.Firstname, ' ', COALESCE(ic.Lastname, ''))
                            WHEN l.customer_type = 'group' THEN CONCAT(g.group_name, ' (', g.group_code, ')')
                            ELSE 'Unknown Customer'
                        END as customer_name,
                        CONCAT(e.Firstname, ' ', COALESCE(e.Lastname, '')) as loan_officer,
                        b.BranchName as branch_name,
                        lp.product_name,
                        l.loan_amount_term as installment_amount
                    FROM loan l
                    LEFT JOIN individual_customers ic ON l.loan_customer = ic.id AND l.customer_type = 'individual'
                    LEFT JOIN \`groups\` g ON l.loan_customer = g.group_id AND l.customer_type = 'group'
                    LEFT JOIN employees e ON l.loan_added_by = e.id
                    LEFT JOIN branches b ON l.branch = b.id
                    LEFT JOIN loan_products lp ON l.loan_product = lp.loan_product_id
                    WHERE ${whereClause}
                `;

                db.query(enhancedLoanQuery, async (err, loans) => {
                    if (err) {
                        reject(err);
                        return;
                    }

                    console.log(`      Found ${loans.length} active loans for enhanced analysis`);
                    reportTrackers[reportId].percentage = 40;

                    console.log('[5/6] Processing enhanced loan data with detailed PAR analysis...');
                    const processedLoans = [];

                    try {
                        // Process each loan with comprehensive analysis
                        for (let i = 0; i < loans.length; i++) {
                            const loan = loans[i];
                            const loanId = loan.loan_id;
                            const loanNum = loan.loan_number;

                            // Get payment analysis
                            const paymentAnalysis = await getDetailedPaymentAnalysis(loanId, db);
                            const arrearsInfo = await calculateArrearsDetails(loanId, db);
                            const outstandingBalance = await calculateOutstandingBalance(loanId, db);
                            const paymentTotals = await calculatePaymentTotals(loanId, db);
                            const lastPaymentDate = await getLastPaymentDate(loanNum, db);
                            console.log(`      Processing loan ${i + 1}/${loans.length}: ${loanNum} and last payment  date is ${lastPaymentDate}`);
                            // Calculate maturity date based on loan date and period
                            const maturityDate = await calculateMaturityDate(loanId, db);

                            // Calculate collection rate
                            const collectionRate = paymentTotals.total_expected > 0 
                                ? (paymentTotals.total_collected / paymentTotals.total_expected) * 100 
                                : 0;

                            const processedLoan = {
                                // Basic loan information
                                customer_name: loan.customer_name || 'Unknown',
                                loan_number: loan.loan_number || 'N/A',
                                product_name: loan.product_name || 'N/A',
                                branch_name: loan.branch_name || 'Unknown',
                                loan_officer: loan.loan_officer || 'Unknown',

                                // Loan details - ADDED MISSING FIELDS
                                loan_date: loan.loan_date,
                                loan_period: loan.loan_period,
                                period_type: loan.period_type,
                                loan_interest: loan.loan_interest,

                                // Financial details
                                loan_principal: parseFloat(loan.loan_principal) || 0,
                                total_loan_amount: parseFloat(loan.total_loan_amount) || 0,
                                installment_amount: parseFloat(loan.installment_amount) || 0,
                                outstanding_balance: outstandingBalance,
                                total_expected_installments: paymentTotals.total_expected,
                                actual_payments: paymentTotals.total_collected,
                                collection_rate: collectionRate,
                                collateral_value: 0, // Set to 0 for now

                                // Arrears and PAR information
                                days_in_arrears: arrearsInfo.days_in_arrears,
                                amount_in_arrears: arrearsInfo.amount_in_arrears,
                                payments_in_arrears: arrearsInfo.payments_in_arrears,

                                // PAR classifications - CORRECTED TO MATCH getDetailedPaymentAnalysis RETURN VALUES
                                par_1_14_days: paymentAnalysis.par_1_14,
                                par_15_29_days: paymentAnalysis.par_15_29,
                                par_30_59_days: paymentAnalysis.par_30_59,
                                par_60_89_days: paymentAnalysis.par_60_89,
                                par_90_179_days: paymentAnalysis.par_90_179,
                                par_180_364_days: paymentAnalysis.par_180_364,
                                par_365_plus_days: paymentAnalysis.par_365_plus,

                                // FIXED: Dates
                                last_payment_date: lastPaymentDate,
                                maturity_date: maturityDate,
                                loan_added_date: loan.loan_added_date,

                                // Status and classification
                                loan_status: loan.loan_status,
                                risk_classification: determineRiskClassification(arrearsInfo.days_in_arrears),
                                customer_type: loan.customer_type
                            };

                            processedLoans.push(processedLoan);
                        }

                        console.log(`Successfully processed ${processedLoans.length} loans`);
                        reportTrackers[reportId].percentage = 80;

                        console.log('[6/6] Generating Enhanced PAR Portfolio Report...');

                        // Get filter display names
                        const filterDisplayNames = await getFilterDisplayNames(branch, officer, product, db);

                        // Generate the enhanced HTML report
                        const reportHtml = generateEnhancedPARPortfolioReport(
                            currentDate,
                            processedLoans,
                            totalPortfolio,
                            filterDisplayNames,
                            dateFrom,
                            dateTo
                        );

                        // Write to file
                        fs.writeFileSync(reportTrackers[reportId].filePath, reportHtml);
                        reportTrackers[reportId].percentage = 100;

                        console.log(`\n✅ Enhanced PAR Portfolio Report generated: ${reportTrackers[reportId].filePath}`);
                        console.log(`Total loans in report: ${processedLoans.length}`);
                        console.log('====== ENHANCED PAR DETAILED PORTFOLIO REPORT COMPLETED ======');

                        resolve(true);

                    } catch (error) {
                        console.error('Error in loan processing loop:', error);
                        reject(error);
                    }
                });
            });
        });
    } catch (error) {
        console.error('\n❌ Error generating enhanced PAR report:', error);
        throw error;
    }
}

/**
 * Calculate maturity date based on loan date and period
 * @param {Date} loanDate - Loan date
 * @param {number} period - Loan period
 * @param {string} periodType - Period type (days, weeks, months, years)
 * @returns {string} Formatted maturity date
 */
async function calculateMaturityDate(loanId, db) {
    return new Promise((resolve, reject) => {
        const query = `
            SELECT MAX(payment_schedule) as maturity_date
            FROM payement_schedules
            WHERE loan_id = ?
        `;

        db.query(query, [loanId], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            const result = results[0] || {};
            const date = result.maturity_date;
            resolve(date ? moment(date).format('YYYY-MM-DD') : null);
        });
    });
}

/**
 * Get detailed payment analysis for a loan
 * @param {number} loanId - Loan ID
 * @param {Object} db - Database connection
 * @returns {Promise<Object>} Payment analysis data
 */
async function getDetailedPaymentAnalysis(loanId, db) {
    return new Promise((resolve, reject) => {
        const query = `
            SELECT 
                SUM(CASE WHEN DATEDIFF(CURDATE(), payment_schedule) BETWEEN 1 AND 14
                    AND status = 'NOT PAID' THEN (amount - paid_amount) ELSE 0 END) as par_1_14,
                SUM(CASE WHEN DATEDIFF(CURDATE(), payment_schedule) BETWEEN 15 AND 29
                    AND status = 'NOT PAID' THEN (amount - paid_amount) ELSE 0 END) as par_15_29,
                SUM(CASE WHEN DATEDIFF(CURDATE(), payment_schedule) BETWEEN 30 AND 59
                    AND status = 'NOT PAID' THEN (amount - paid_amount) ELSE 0 END) as par_30_59,
                SUM(CASE WHEN DATEDIFF(CURDATE(), payment_schedule) BETWEEN 60 AND 89
                    AND status = 'NOT PAID' THEN (amount - paid_amount) ELSE 0 END) as par_60_89,
                SUM(CASE WHEN DATEDIFF(CURDATE(), payment_schedule) BETWEEN 90 AND 179
                    AND status = 'NOT PAID' THEN (amount - paid_amount) ELSE 0 END) as par_90_179,
                SUM(CASE WHEN DATEDIFF(CURDATE(), payment_schedule) BETWEEN 180 AND 364
                    AND status = 'NOT PAID' THEN (amount - paid_amount) ELSE 0 END) as par_180_364,
                SUM(CASE WHEN DATEDIFF(CURDATE(), payment_schedule) >= 365
                    AND status = 'NOT PAID' THEN (amount - paid_amount) ELSE 0 END) as par_365_plus
            FROM payement_schedules
            WHERE loan_id = ? AND payment_schedule < CURDATE()
        `;

        db.query(query, [loanId], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            const result = results[0] || {};
            resolve({
                par_1_14: parseFloat(result.par_1_14) || 0,
                par_15_29: parseFloat(result.par_15_29) || 0,
                par_30_59: parseFloat(result.par_30_59) || 0,
                par_60_89: parseFloat(result.par_60_89) || 0,
                par_90_179: parseFloat(result.par_90_179) || 0,
                par_180_364: parseFloat(result.par_180_364) || 0,
                par_365_plus: parseFloat(result.par_365_plus) || 0
            });
        });
    });
}

/**
 * Calculate outstanding balance for a loan
 * @param {number} loanId - Loan ID
 * @param {Object} db - Database connection
 * @returns {Promise<number>} Outstanding balance
 */
async function calculateOutstandingBalance(loanId, db) {
    return new Promise((resolve, reject) => {
        const query = `
            SELECT 
                COALESCE(SUM(amount), 0) - COALESCE(SUM(paid_amount), 0) as outstanding_balance
            FROM payement_schedules
            WHERE loan_id = ?
        `;

        db.query(query, [loanId], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            const result = results[0] || {};
            resolve(parseFloat(result.outstanding_balance) || 0);
        });
    });
}

/**
 * Helper function to calculate detailed arrears information
 * @param {number} loanId - Loan ID
 * @param {Object} db - Database connection
 * @returns {Promise<Object>} Arrears details
 */
async function calculateArrearsDetails(loanId, db) {
    return new Promise((resolve, reject) => {
        const query = `
            SELECT 
                COUNT(CASE WHEN status = 'NOT PAID' AND payment_schedule < CURDATE() THEN 1 END) as payments_in_arrears,
                SUM(CASE WHEN status = 'NOT PAID' AND payment_schedule < CURDATE() 
                         THEN (amount - paid_amount) ELSE 0 END) as amount_in_arrears,
                MAX(CASE WHEN status = 'NOT PAID' AND payment_schedule < CURDATE() 
                         THEN DATEDIFF(CURDATE(), payment_schedule) ELSE 0 END) as days_in_arrears
            FROM payement_schedules
            WHERE loan_id = ?
        `;

        db.query(query, [loanId], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            const result = results[0] || {};
            resolve({
                payments_in_arrears: parseInt(result.payments_in_arrears) || 0,
                amount_in_arrears: parseFloat(result.amount_in_arrears) || 0,
                days_in_arrears: parseInt(result.days_in_arrears) || 0
            });
        });
    });
}

/**
 * Calculate payment totals for a loan
 * @param {number} loanId - Loan ID
 * @param {Object} db - Database connection
 * @returns {Promise<Object>} Payment totals
 */
async function calculatePaymentTotals(loanId, db) {
    return new Promise((resolve, reject) => {
        const query = `
            SELECT 
                SUM(amount) as total_expected,
                SUM(paid_amount) as total_collected
            FROM payement_schedules
            WHERE loan_id = ?
        `;

        db.query(query, [loanId], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            const result = results[0] || {};
            resolve({
                total_expected: parseFloat(result.total_expected) || 0,
                total_collected: parseFloat(result.total_collected) || 0
            });
        });
    });
}

/**
 * Get last payment date for a loan from transaction table
 * @param {number} loanId - Loan ID
 * @param {Object} db - Database connection
 * @returns {Promise<string>} Last payment date
 */
async function getLastPaymentDate(loanNumber, db) {
    return new Promise((resolve, reject) => {
        const transactionQuery = `
            SELECT MAX(system_time) as last_payment_date
            FROM transaction
            WHERE account_number = ? AND credit > 0
        `;

        db.query(transactionQuery, [loanNumber], (err, results) => {
            if (err) {
                reject(err);
                return;
            }

            const result = results[0] || {};
            const date = result.last_payment_date;
            resolve(date ? moment(date).format('YYYY-MM-DD') : null);
        });
    });
}
/**
 * Determine risk classification based on days in arrears
 * @param {number} daysInArrears - Number of days in arrears
 * @returns {string} Risk classification
 */
function determineRiskClassification(daysInArrears) {
    if (daysInArrears === 0) return 'Current';
    if (daysInArrears <= 30) return 'Watch';
    if (daysInArrears <= 90) return 'Substandard';
    if (daysInArrears <= 180) return 'Doubtful';
    return 'Loss';
}

/**
 * Get filter display names
 * @param {string} branch - Branch ID
 * @param {string} officer - Officer ID
 * @param {string} product - Product ID
 * @param {Object} db - Database connection
 * @returns {Promise<Object>} Filter display names
 */
async function getFilterDisplayNames(branch, officer, product, db) {
    const filterNames = {
        branchName: 'All Branches',
        officerName: 'All Officers',
        productName: 'All Products'
    };

    try {
        if (branch && branch !== 'All') {
            const branchResult = await new Promise((resolve, reject) => {
                db.query('SELECT BranchName FROM branches WHERE id = ?', [branch], (err, result) => {
                    if (err) reject(err);
                    else resolve(result);
                });
            });
            if (branchResult && branchResult.length > 0) {
                filterNames.branchName = branchResult[0].BranchName;
            }
        }

        if (officer && officer !== 'All') {
            const officerResult = await new Promise((resolve, reject) => {
                db.query('SELECT CONCAT(Firstname, " ", Lastname) as name FROM employees WHERE id = ?', [officer], (err, result) => {
                    if (err) reject(err);
                    else resolve(result);
                });
            });
            if (officerResult && officerResult.length > 0) {
                filterNames.officerName = officerResult[0].name;
            }
        }

        if (product && product !== 'All') {
            const productResult = await new Promise((resolve, reject) => {
                db.query('SELECT product_name FROM loan_products WHERE loan_product_id = ?', [product], (err, result) => {
                    if (err) reject(err);
                    else resolve(result);
                });
            });
            if (productResult && productResult.length > 0) {
                filterNames.productName = productResult[0].product_name;
            }
        }
    } catch (error) {
        console.error('Error getting filter names:', error);
    }

    return filterNames;
}

/**
 * Generate Enhanced PAR Portfolio Report HTML
 * @param {string} currentDate - Current date
 * @param {Array} loans - Array of processed loans
 * @param {number} totalPortfolio - Total portfolio amount
 * @param {Object} filterDisplayNames - Filter display names
 * @param {string} dateFrom - Start date
 * @param {string} dateTo - End date
 * @returns {string} HTML report content
 */
function generateEnhancedPARPortfolioReport(currentDate, loans, totalPortfolio, filterDisplayNames, dateFrom, dateTo) {
    const formatCurrency = (amount) => {
        if (isNaN(amount) || amount === null || amount === undefined) {
            return '0.00';
        }
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    };

    const formatPercentage = (value) => {
        if (isNaN(value) || value === null || value === undefined) {
            return '0.00%';
        }
        return new Intl.NumberFormat('en-US', {
            style: 'percent',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value / 100);
    };

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        return moment(dateString).format('MM/DD/YYYY');
    };

    // Calculate summary statistics
    const totalLoans = loans.length;
    const totalOutstandingBalance = loans.reduce((sum, loan) => sum + (loan.outstanding_balance || 0), 0);
    const totalInArrears = loans.reduce((sum, loan) => sum + (loan.amount_in_arrears || 0), 0);
    const totalPAR1_14 = loans.reduce((sum, loan) => sum + (loan.par_1_14_days || 0), 0);
    const totalPAR15_29 = loans.reduce((sum, loan) => sum + (loan.par_15_29_days || 0), 0);
    const totalPAR30_59 = loans.reduce((sum, loan) => sum + (loan.par_30_59_days || 0), 0);
    const totalPAR60_89 = loans.reduce((sum, loan) => sum + (loan.par_60_89_days || 0), 0);
    const totalPAR90_179 = loans.reduce((sum, loan) => sum + (loan.par_90_179_days || 0), 0);
    const totalPAR180_364 = loans.reduce((sum, loan) => sum + (loan.par_180_364_days || 0), 0);
    const totalPAR365Plus = loans.reduce((sum, loan) => sum + (loan.par_365_plus_days || 0), 0);

    // Generate loan rows
    const loanRows = loans.map((loan, index) => {
        return `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${loan.customer_name || 'N/A'}</td>
                <td>${loan.loan_number || 'N/A'}</td>
                <td>${loan.product_name || 'N/A'}</td>
                <td>${loan.branch_name || 'N/A'}</td>
                <td>${formatDate(loan.loan_date)}</td>
                <td class="text-right">${formatCurrency(loan.loan_principal)}</td>
                <td>${loan.loan_period || 0} ${loan.period_type || ''}</td>
                <td>${loan.loan_interest || 0}%</td>
                <td class="text-right">${formatCurrency(loan.total_loan_amount)}</td>
                <td class="text-right">${formatCurrency(loan.installment_amount)}</td>
                <td class="text-right">${formatCurrency(loan.outstanding_balance)}</td>
                <td class="text-right">${formatCurrency(loan.amount_in_arrears)}</td>
                <td class="text-center">${loan.days_in_arrears || 0}</td>
                <td class="text-center">${loan.payments_in_arrears || 0}</td>
                <td class="text-right">${formatCurrency(loan.par_1_14_days || 0)}</td>
                <td class="text-right">${formatCurrency(loan.par_15_29_days || 0)}</td>
                <td class="text-right">${formatCurrency(loan.par_30_59_days || 0)}</td>
                <td class="text-right">${formatCurrency(loan.par_60_89_days || 0)}</td>
                <td class="text-right">${formatCurrency(loan.par_90_179_days || 0)}</td>
                <td class="text-right">${formatCurrency(loan.par_180_364_days || 0)}</td>
                <td class="text-right">${formatCurrency(loan.par_365_plus_days || 0)}</td>
                <td>${loan.risk_classification || 'Current'}</td>
                <td class="text-right">${formatPercentage(loan.collection_rate)}</td>
                <td>${formatDate(loan.last_payment_date)}</td>
                <td class="text-right">${formatCurrency(loan.collateral_value)}</td>
                <td>${formatDate(loan.maturity_date)}</td>
                <td class="text-right">${formatCurrency(loan.total_expected_installments)}</td>
                <td class="text-right">${formatCurrency(loan.actual_payments)}</td>
                <td>${loan.loan_status || 'N/A'}</td>
                <td>${loan.loan_officer || 'N/A'}</td>
                <td>${formatDate(loan.loan_added_date)}</td>
            </tr>
        `;
    });

    // Generate the complete HTML with CORRECT PAR structure
    return `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PAR Detailed Portfolio Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 10px; font-size: 12px; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #153505; padding-bottom: 10px; }
            .header h1 { color: #153505; margin: 0; font-size: 24px; }
            .filters { background-color: #f5f5f5; padding: 10px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #153505; }
            .summary { background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
            .summary-item { text-align: center; padding: 8px; background-color: white; border-radius: 3px; border: 1px solid #ddd; }
            .summary-label { font-weight: bold; color: #153505; font-size: 11px; }
            .summary-value { font-size: 14px; font-weight: bold; color: #333; }
            table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: left; white-space: nowrap; }
            th { background-color: #153505; color: white; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .export-buttons { margin-bottom: 15px; text-align: right; }
            .export-buttons button { padding: 8px 15px; margin-left: 5px; cursor: pointer; background-color: #153505; color: white; border: none; border-radius: 3px; }
            .export-buttons button:hover { background-color: #0d2503; }
            @media print { 
                .export-buttons { display: none; }
                body { padding: 0; font-size: 10px; }
                th, td { padding: 3px 2px; }
            }
        </style>
        <!-- Include SheetJS library for Excel exports -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script>
            function exportData(type) {
                const fileName = 'PAR_Detailed_Portfolio_Report.' + type;
                const table = document.getElementById("main-table");
                const wb = XLSX.utils.table_to_book(table);
                XLSX.writeFile(wb, fileName);
            }
        </script>
    </head>
    <body>
        <div class="header">
            <h1>PAR Detailed Portfolio Report</h1>
            <div>Sycamore Credit Limited - Generated on ${formatDate(currentDate)}</div>
        </div>

        <div class="filters">
            <strong>Filters Applied:</strong> 
            Branch: ${filterDisplayNames.branchName} | 
            Officer: ${filterDisplayNames.officerName} | 
            Product: ${filterDisplayNames.productName}
            ${dateFrom && dateTo ? ` | Period: ${formatDate(dateFrom)} to ${formatDate(dateTo)}` : ''}
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">Total Loans</div>
                <div class="summary-value">${totalLoans}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Portfolio</div>
                <div class="summary-value">K${formatCurrency(totalPortfolio)}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Outstanding Balance</div>
                <div class="summary-value">K${formatCurrency(totalOutstandingBalance)}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total in Arrears</div>
                <div class="summary-value">K${formatCurrency(totalInArrears)}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">PAR 1-14 Days</div>
                <div class="summary-value">K${formatCurrency(totalPAR1_14)}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">PAR 30+ Days</div>
                <div class="summary-value">K${formatCurrency(totalPAR30_59 + totalPAR60_89 + totalPAR90_179 + totalPAR180_364 + totalPAR365Plus)}</div>
            </div>
        </div>

        <div class="export-buttons">
            <span>Export as:</span>
            <button onclick="exportData('xlsx')">Excel (xlsx)</button>
            <button onclick="exportData('csv')">CSV</button>
            <button onclick="window.print()">Print</button>
        </div>

        <table id="main-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Loan Number</th>
                    <th>Product</th>
                    <th>Branch</th>
                    <th>Loan Date</th>
                    <th>Principal Amount</th>
                    <th>Loan Period</th>
                    <th>Interest Rate</th>
                    <th>Total Loan Amount</th>
                    <th>Installment Amount</th>
                    <th>Outstanding Balance</th>
                    <th>Amount in Arrears</th>
                    <th>Days in Arrears</th>
                    <th>Payments in Arrears</th>
                    <th>PAR 1-14 Days</th>
                    <th>PAR 15-29 Days</th>
                    <th>PAR 30-59 Days</th>
                    <th>PAR 60-89 Days</th>
                    <th>PAR 90-179 Days</th>
                    <th>PAR 180-364 Days</th>
                    <th>PAR 365+ Days</th>
                    <th>Risk Classification</th>
                    <th>Collection Rate</th>
                    <th>Last Payment Date</th>
                    <th>Collateral Value</th>
                    <th>Maturity Date</th>
                    <th>Expected Installments</th>
                    <th>Actual Payments</th>
                    <th>Loan Status</th>
                    <th>Relationship Officer (RO)</th>
                    <th>Date Added</th>
                </tr>
            </thead>            <tbody>
                ${loanRows.join('')}
            </tbody>
            <tfoot>
                <tr style="background-color: #f0f8f0; font-weight: bold; border-top: 2px solid #153505;">
                    <td class="text-center" colspan="6">TOTALS</td>
                    <td class="text-right">${formatCurrency(loans.reduce((sum, loan) => sum + (loan.loan_principal || 0), 0))}</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">${formatCurrency(loans.reduce((sum, loan) => sum + (loan.total_loan_amount || 0), 0))}</td>
                    <td class="text-right">${formatCurrency(loans.reduce((sum, loan) => sum + (loan.installment_amount || 0), 0))}</td>
                    <td class="text-right">${formatCurrency(totalOutstandingBalance)}</td>
                    <td class="text-right">${formatCurrency(totalInArrears)}</td>
                    <td class="text-center">${Math.round(loans.reduce((sum, loan) => sum + (loan.days_in_arrears || 0), 0) / totalLoans)}</td>
                    <td class="text-center">${loans.reduce((sum, loan) => sum + (loan.payments_in_arrears || 0), 0)}</td>
                    <td class="text-right">${formatCurrency(totalPAR1_14)}</td>
                    <td class="text-right">${formatCurrency(totalPAR15_29)}</td>
                    <td class="text-right">${formatCurrency(totalPAR30_59)}</td>
                    <td class="text-right">${formatCurrency(totalPAR60_89)}</td>
                    <td class="text-right">${formatCurrency(totalPAR90_179)}</td>
                    <td class="text-right">${formatCurrency(totalPAR180_364)}</td>
                    <td class="text-right">${formatCurrency(totalPAR365Plus)}</td>
                    <td class="text-center">-</td>
                    <td class="text-right">${formatPercentage(loans.reduce((sum, loan) => sum + (loan.collection_rate || 0), 0) / totalLoans)}</td>
                    <td class="text-center">-</td>
                    <td class="text-right">${formatCurrency(loans.reduce((sum, loan) => sum + (loan.collateral_value || 0), 0))}</td>
                    <td class="text-center">-</td>
                    <td class="text-right">${formatCurrency(loans.reduce((sum, loan) => sum + (loan.total_expected_installments || 0), 0))}</td>
                    <td class="text-right">${formatCurrency(loans.reduce((sum, loan) => sum + (loan.actual_payments || 0), 0))}</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
                    <tr style="background-color: #e3f2fd; font-weight: bold;">
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">TOTAL PORTFOLIO PRINCIPAL</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalOutstandingBalance)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">100.00%</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Total outstanding principal</td>
                    </tr>
                    <tr style="background-color: #fff3e0; font-weight: bold;">
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">PORTFOLIO AT RISK</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalInArrears)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalInArrears / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Total amount at risk</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">Aged 0-7 days</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalPAR1_14)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR1_14 / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Loans overdue 0-7 days</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">Aged 8-30 days</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalPAR15_29 + totalPAR30_59)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? ((totalPAR15_29 + totalPAR30_59) / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Loans overdue 8-30 days</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">Aged 31-60 days</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalPAR60_89)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR60_89 / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Loans overdue 31-60 days</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">Aged 61-90 days</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalPAR90_179)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR90_179 / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Loans overdue 61-90 days</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">Aged 91-120 days</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalPAR180_364)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR180_364 / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Loans overdue 91-120 days</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">Aged 121-180 days</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalPAR365Plus)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR365Plus / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Loans overdue 121-180 days</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">Aged 181+ days</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatCurrency(totalPAR365Plus)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR365Plus / totalOutstandingBalance) * 100 : 0)}</td>
                        <td style="padding: 10px 8px; text-align: center; border: 1px solid #ddd;">Loans overdue 181+ days</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PAR SUMMARY SECTION -->
        <div style="margin: 30px 0; padding: 20px; background-color: #e8f5e8; border-radius: 8px; border-left: 5px solid #153505;">
            <h3 style="margin-top: 0; color: #153505; font-size: 18px; border-bottom: 2px solid #153505; padding-bottom: 10px;">PAR Summary by Age Buckets</h3>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px;">
                <thead>
                    <tr style="background-color: #153505; color: white;">
                        <th style="padding: 12px 8px; text-align: left; border: 1px solid #ddd;">Age Category</th>
                        <th style="padding: 12px 8px; text-align: right; border: 1px solid #ddd;">Amount (MWK)</th>
                        <th style="padding: 12px 8px; text-align: right; border: 1px solid #ddd;">Percentage (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #e3f2fd; font-weight: bold;">
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">TOTAL PORTFOLIO PRINCIPAL</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalOutstandingBalance)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">100.00%</td>
                    </tr>
                    <tr style="background-color: #fff3e0; font-weight: bold;">
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">PORTFOLIO AT RISK %</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalInArrears)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalInArrears / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 0 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalInArrears)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalInArrears / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 7 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalPAR1_14)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR1_14 / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 30 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalPAR30_59)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR30_59 / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 60 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalPAR60_89)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR60_89 / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 90 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalPAR90_179)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR90_179 / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 120 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalPAR180_364)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR180_364 / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 180 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalPAR365Plus)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR365Plus / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 8px; border: 1px solid #ddd;">MORE THAN 366 DAYS</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">MK${formatCurrency(totalPAR365Plus)}</td>
                        <td style="padding: 10px 8px; text-align: right; border: 1px solid #ddd;">${formatPercentage(totalOutstandingBalance > 0 ? (totalPAR365Plus / totalOutstandingBalance) * 100 : 0)}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px; padding: 15px; background-color: #f5f5f5; border-radius: 5px; font-size: 11px;">
            <h3 style="margin-top: 0; color: #153505;">Report Notes:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li>PAR (Portfolio at Risk) represents the outstanding balance of loans with payments overdue by the specified number of days</li>
                <li>Risk Classification: Current (0 days), Watch (1-30 days), Substandard (31-90 days), Doubtful (91-180 days), Loss (180+ days)</li>
                <li>Collection Rate = (Actual Payments / Expected Installments) × 100</li>
                <li>This report includes all active loans meeting the specified filter criteria</li>
                <li><strong>PAR Percentages:</strong> Calculated as (PAR Amount / Total Outstanding Balance) × 100</li>
            </ul>
        </div>
    </body>
    </html>
    `;
}

module.exports = {
    PAR_THRESHOLDS,
    validatePARReportParameters,
    calculatePARForDaysRange,
    generatePARReportV2,
    generateExcelStylePARReport,
    generatePARReportV2Enhanced,
    getDetailedPaymentAnalysis,
    calculateOutstandingBalance,
    calculateArrearsDetails,
    calculatePaymentTotals,
    getLastPaymentDate,
    determineRiskClassification,
    getFilterDisplayNames,
    generateEnhancedPARPortfolioReport,
    getOutstandingPrincipal,
    getAllPaymentsForLoan,
    calculateMaturityDate
};