const express = require('express');
const util = require('util')
const { query } = require('./databaseHelpers');
const moment = require('moment');
const fs = require('fs');
const path= require('path');
const { generateLoanPortfolioReport } = require('./loanPortfolioReport');
const { generateLoanPortfolioWriteOffReport } = require('./loanPortfolioWriteOffReport');
const { generateLoanCollectionsReport } = require('./loanCollectionReport');
const { generateUpcomingInstallmentReport } = require('./upcomingInstallmentReport');
const { generateTransactionsReport } = require('./transactionsReport');
const { generateRBMClassificationReport } = require('./rbmClassificationReport');
const { generateArrearsReport } = require('./arrearsReport');
const {
    generatePARReportV2,
    generatePARReportV2Enhanced,
    validatePARReportParameters,
    calculatePARForDaysRange,
    generateExcelStylePARReport,
    getDetailedPaymentAnalysis,
    calculateOutstandingBalance,
    calculateArrearsDetails,
    calculatePaymentTotals,
    getLastPaymentDate,
    determineRiskClassification,
    getFilterDisplayNames,
    generateEnhancedPARPortfolioReport
} = require('./parReports');
const {
    generatePrincipalBalancePARReport,
    generatePrincipalBalancePARHTML,
    getPrincipalBalance,
    getOldestOverduePayment,
    getTotalPortfolioPrincipal
} = require('./parReportsPrincipalBalance');
const {
  getAmountOfArrears,
  getAmountOfArrearsPaid,
  getDaysOfArrears,
  getLoanDetails,
  getUserProfile,
  getLoanRepayments,
  getLoanStatus,
  getTotalPayments,
  rbmReport,
  getAllById,
  loanCollection,
  getById,
  getPreviousLoan,
  getRBMLoanPaymentById,
    getPARsummary,
    getPARsummaryu,
    getParPrincipal,
    getAllOverDuePayments,
    sumTotalPar
} = require('./databaseHelpers');

const app = express();
const port = process.env.PORT || 4300;

// Database connection is now handled by databaseHelpers.js
// Helper function to maintain callback compatibility
const db = {
    query: async (sql, params, callback) => {
        if (typeof params === 'function') {
            callback = params;
            params = [];
        }
        try {
            const results = await query(sql, params);
            callback(null, results);
        } catch (err) {
            callback(err);
        }
    }
};

let reportTrackers  = {}; // Store the percentage for reports
let hasNewData = false; // Flag to track if new data is available
app.use(express.json());
app.get('/', async (req, res) => {
	// Acknowledge the request with a "processing" response
	res.status(202).json({message: 'Welcome something is working.'});
})
const updatePercentageToDB = (reportId) => {
    const tracker = reportTrackers[reportId];
    if (tracker) {
        const { percentage } = tracker;
        db.query('UPDATE reports SET percentage = ? WHERE id = ?', [percentage, reportId], (err) => {
            if (err) {
                console.error(`Failed to update percentage for report ${reportId}: `, err);
            } else {
                console.log(`Updated percentage for report ${reportId}: ${percentage}%`);
            }
        });
    }
};


app.post('/', async (req, res) => {
	const report = {
		user: req.body.user,
		report_type: req.body.report_type,
		status: 'in progress',
	};
	// Acknowledge the request with a "processing" response
	res.status(202).json({message: 'Welcome something is working.',report:report});
});
// Add this to your existing import section in index.js


// Add this new endpoint after your existing report endpoints
app.post('/generate-report-rbm-classification', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'RBM Loan Classification Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'RBM_CLASSIFICATION',
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Use the utility function to get paths
            const paths = getReportPaths('report_rbm_classification', currentDate);

            // Initialize report tracker
            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Extract filter parameters from request
            const filterOptions = {
                branch: req.body.branch || 'All',
                officer: req.body.officer || 'All',
                product: req.body.product || 'All'
            };

            console.log(`Generating RBM Classification Report with options: ${JSON.stringify(filterOptions)}`);

            // Generate HTML content
            const html = await generateRBMClassificationReport(
                filterOptions,
                result.insertId,
                reportTrackers,
                req.body.base_url,
                db
            );

            // Write file using absolute path for file system operations
            fs.writeFile(paths.filePath, html, async (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write data to the file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Data saved to the file:', paths.filePath);

                // Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: paths.dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update report status: ', updateErr);
                        return;
                    }
                    console.log('Report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});
// Add this new endpoint after your existing report endpoints
app.post('/generate-report-transactions', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Payments Transactions Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'PAYMENTS_TRANSACTIONS',
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Use the utility function to get paths
            const paths = getReportPaths('report_transactions', currentDate);

            // Initialize report tracker
            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Extract filter parameters from request
            const filterOptions = {
                branch: req.body.branch || null,
                transaction_type: req.body.transaction_type_id || null,
                loan: req.body.loan || null,
                product: req.body.product || null,
                officer: req.body.officer || null,
                from: req.body.from || null,
                to: req.body.to || null
            };

            console.log(`Generating Payments Transactions Report with options: ${JSON.stringify(filterOptions)}`);

            // Generate HTML content
            const html = await generateTransactionsReport(
                filterOptions,
                result.insertId,
                reportTrackers,
                db
            );

            // Write file using absolute path for file system operations
            fs.writeFile(paths.filePath, html, async (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write data to the file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Data saved to the file:', paths.filePath);

                // Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: paths.dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update report status: ', updateErr);
                        return;
                    }
                    console.log('Report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});
// 1. First, modify the portfolio report endpoint
app.post('/generate-report-portfolio', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Loan Portfolio Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'LOAN_PORTFOLIO',
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Create directory for file system operations only
            const directory = path.resolve(__dirname, 'reports');

            // Ensure the directory exists
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            // CHANGE: Use relative path for DB storage, but absolute path for file operations
            const fileName = `report_portfolio_${currentDate}.html`;
            const filePath = path.join(directory, fileName); // Absolute path for file operations
            const dbPath = `reports/${fileName}`; // Relative path for database storage

            // Initialize report tracker
            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Extract filter parameters from request
            const filterOptions = {
                user: req.body.officer || 'All',
                branch: req.body.branch || 'All',
                branchgp: req.body.branchgp || req.body.branch || 'All',
                product: req.body.productid || 'All',
                status: req.body.status || 'All',
                from: req.body.date_from || '',
                to: req.body.date_to || ''
            };

            // Generate HTML content
            const html = await generateLoanPortfolioReport(
                filterOptions,
                result.insertId,
                reportTrackers
            );

            // Write file using absolute path for file system operations
            fs.writeFile(filePath, html, async (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write data to the file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Data saved to the file:', filePath);

                // CHANGE: Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update report status: ', updateErr);
                        return;
                    }
                    console.log('Report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});

// 2. Modify the portfolio write-off report endpoint
app.post('/generate-report-portfolio-write-off', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Loan Portfolio Write Off Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'LOAN_PORTFOLIO_WRITE_OFF',
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Create directory for file system operations
            const directory = path.resolve(__dirname, 'reports');

            // Ensure the directory exists
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            // CHANGE: Use relative path for DB storage, but absolute path for file operations
            const fileName = `report_portfolio_write_off_${currentDate}.html`;
            const filePath = path.join(directory, fileName); // Absolute path for file operations
            const dbPath = `reports/${fileName}`; // Relative path for database storage

            // Initialize report tracker
            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Extract filter parameters from request
            const filterOptions = {
                user: req.body.officer || 'All',
                branch: req.body.branch || 'All',
                branchgp: req.body.branchgp || req.body.branch || 'All',
                product: req.body.productid || 'All',
                status: req.body.status || 'All',
                from: req.body.date_from || '',
                to: req.body.date_to || ''
            };

            // Generate HTML content
            const html = await generateLoanPortfolioWriteOffReport(
                filterOptions,
                result.insertId,
                reportTrackers
            );

            // Write file using absolute path for file system operations
            fs.writeFile(filePath, html, async (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write data to the file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Data saved to the file:', filePath);

                // CHANGE: Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update report status: ', updateErr);
                        return;
                    }
                    console.log('Report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});

// 3. Modify the report collections endpoint
app.post('/generate-report-collections', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Loan Collections Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'LOAN_COLLECTION_RATE',
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Create directory for file system operations
            const directory = path.resolve(__dirname, 'reports');

            // Ensure the directory exists
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            // CHANGE: Use relative path for DB storage, but absolute path for file operations
            const fileName = `report_collections_${currentDate}.html`;
            const filePath = path.join(directory, fileName); // Absolute path for file operations
            const dbPath = `reports/${fileName}`; // Relative path for database storage

            // Initialize report tracker
            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Extract filter parameters from request
            const filterOptions = {
                user: req.body.officer || 'All',
                officer_name: req.body.officer_name || 'All',
                branch: req.body.branch || 'All',
                branch_name: req.body.branch_name || 'All',
                period: req.body.period || '',
                from: req.body.date_from || null,
                to: req.body.date_to || null
            };

            // Adjust dates based on period selection (existing code)...
            if (filterOptions.period) {
                switch(filterOptions.period) {
                    case 'today':
                        filterOptions.from = moment().format('YYYY-MM-DD');
                        filterOptions.to = moment().format('YYYY-MM-DD');
                        break;
                    case 'this_week':
                        filterOptions.from = moment().startOf('week').format('YYYY-MM-DD');
                        filterOptions.to = moment().format('YYYY-MM-DD');
                        break;
                    case 'this_month':
                        filterOptions.from = moment().startOf('month').format('YYYY-MM-DD');
                        filterOptions.to = moment().format('YYYY-MM-DD');
                        break;
                    case 'last_month':
                        filterOptions.from = moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD');
                        filterOptions.to = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
                        break;
                    case '': // Custom period
                        if (!filterOptions.from && !filterOptions.to) {
                            console.log('Custom period with no dates - using all data with no date filtering');
                        }
                        else if (filterOptions.from && !filterOptions.to) {
                            filterOptions.to = moment().format('YYYY-MM-DD');
                            console.log(`Only from date provided, setting to date to today: ${filterOptions.to}`);
                        }
                        else if (!filterOptions.from && filterOptions.to) {
                            filterOptions.from = moment(filterOptions.to).subtract(30, 'days').format('YYYY-MM-DD');
                            console.log(`Only to date provided, setting from date to 30 days before: ${filterOptions.from}`);
                        }
                        break;
                }
            } else {
                if (!filterOptions.from && !filterOptions.to) {
                    console.log('No period or dates - using all data with no date filtering');
                }
            }

            console.log(`Generating Collections Report with options: ${JSON.stringify(filterOptions)}`);

            // Generate HTML content
            const html = await generateLoanCollectionsReport(
                filterOptions,
                result.insertId,
                reportTrackers,
                db
            );

            // Write file using absolute path for file system operations
            fs.writeFile(filePath, html, async (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write data to the file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Data saved to the file:', filePath);

                // CHANGE: Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update report status: ', updateErr);
                        return;
                    }
                    console.log('Report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});

// 4. Modify the upcoming installment report endpoint
app.post('/generate-report-upcoming-installment', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Upcoming Installment Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'UPCOMING_INSTALLMENT',
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Create directory for file system operations
            const directory = path.resolve(__dirname, 'reports');

            // Ensure the directory exists
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            // CHANGE: Use relative path for DB storage, but absolute path for file operations
            const fileName = `report_upcoming_installment_${currentDate}.html`;
            const filePath = path.join(directory, fileName); // Absolute path for file operations
            const dbPath = `reports/${fileName}`; // Relative path for database storage

            // Initialize report tracker
            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Extract filter parameters from request
            const filterOptions = {
                user: req.body.officer || '',
                branch: req.body.branch || '',
                product: req.body.product || '',
            };

            console.log(`Generating Upcoming Installment Report with options: ${JSON.stringify(filterOptions)}`);

            // Generate HTML content
            const html = await generateUpcomingInstallmentReport(
                filterOptions,
                result.insertId,
                reportTrackers,
                db
            );

            // Write file using absolute path for file system operations
            fs.writeFile(filePath, html, async (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write data to the file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Data saved to the file:', filePath);

                // CHANGE: Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update report status: ', updateErr);
                        return;
                    }
                    console.log('Report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});

// 5. Also modify the CRB report endpoint
app.post('/generate-report-crb', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: req.body.report_type,
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Create directory for file system operations
            const directory = path.resolve(__dirname, 'reports');

            // Ensure the directory exists
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            // CHANGE: Use relative path for DB storage, but absolute path for file operations
            const fileName = `reportcrb_${currentDate}.html`;
            const filePath = path.join(directory, fileName); // Absolute path for file operations
            const dbPath = `reports/${fileName}`; // Relative path for database storage

            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Generate HTML content
            const html = await generateHtmlCrb(result.insertId);

            // Write file using absolute path for file system operations
            fs.writeFile(filePath, html, (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write data to the file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Data saved to the file:', filePath);

                // CHANGE: Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update report status: ', updateErr);
                        return;
                    }
                    console.log('Report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});
app.post('/generate-report-par', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: req.body.report_type,
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');
            // Use absolute path for file storage
            const directory = path.resolve(__dirname, 'reports'); // Create a reports directory

            // Ensure the directory exists
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            const filePath = path.join(directory, `reportpar_${currentDate}.html`);

            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Generate HTML content
            const html = await generateHtml(result.insertId, req.body.officer, req.body.product);

            // Use promisified writeFile
            await fs.promises.writeFile(filePath, html);
            console.log('Data saved to the file:', filePath);

            // After processing, update the report status to 'completed'
            const updateReport = {
                status: 'completed',
                percentage: 100,
                download_link: filePath,
                completed_time: new Date(),
            };

            // Update the report record in the database
            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                if (updateErr) {
                    console.error('Failed to update report status: ', updateErr);
                    return;
                }
                console.log('Report processing completed and updated');
            });
        } catch (err) {
            console.error('Error during report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Report processing failed and updated');
            });
        }
    });
});

// 6. Create a utility function to standardize path handling across all reports
function getReportPaths(reportName, timestamp) {
    // Create filename with timestamp
    const fileName = `${reportName}_${timestamp}.html`;

    // Create directory path (for file system operations)
    const directory = path.resolve(__dirname, 'reports');

    // Ensure the directory exists
    if (!fs.existsSync(directory)) {
        fs.mkdirSync(directory, { recursive: true });
    }

    return {
        // Full filesystem path for writing the file
        filePath: path.join(directory, fileName),

        // Relative path for database storage (for web access)
        dbPath: `reports/${fileName}`
    };
}


 async function generateHtmlCrb(reportId){
  console.log('started processing');
    let results = await  rbmReport();
     const totalCount = results.length;
    let table = `<style>
table, td, th {
  border: 1px solid;
}

table {
  width: 100%;
  border-collapse: collapse;
}
.export-buttons {
                margin-bottom: 15px;
                text-align: right;
            }
            .export-buttons button {
                padding: 6px 12px;
                background-color: #153505;
                color: white;
                border: none;
                border-radius: 3px;
                margin-left: 5px;
                cursor: pointer;
            }
            .export-buttons button:hover {
                background-color: #2e7d32;
            }
            .export-buttons span {
                margin-right: 10px;
                font-weight: bold;
            }
</style>
<!-- Include SheetJS library for Excel exports -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>



function exportData(type){
    const fileName = 'CRB_report.' + type
    const table = document.getElementById("resulta")
    const wb = XLSX.utils.table_to_book(table)
    XLSX.writeFile(wb, fileName)
}
</script>
<div class="export-buttons">
                <span>Export as:</span>
                <button onclick="exportData('xlsx')">Excel (xlsx)</button>
                <button onclick="exportData('xls')">Excel (xls)</button>
                <button onclick="exportData('csv')">CSV</button>
            </div>
      <table style ="border-collapse: collapse;" cellspacing="1" id ="resulta">
                    <thead>
                    <tr>
                        <th>Salutation   </th>
                        <th>Surname   </th>
                        <th>First Name   </th>
                        <th>Middle Name   </th>
                        <th>Maiden Name   </th>
                        <th>Gender   </th>
                        <th>Marital Status   </th>
                        <th>No. of Dependents   </th>
                        <th>Date of Birth   </th>
                        <th>National ID No.  </th>
                        <th>ID Type   </th>
                        <th>ID No.  </th>
                        <th>Nationality  </th>
                        <th>Village   </th>
                        <th> T/A   </th>
                        <th> Home District  </th>
                        <th>Resident Permit No.  </th>
                        <th>Phone No.   </th>
                        <th>Postal Address   </th>
                        <th>Email Address   </th>
                        <th>Residential Address</th>
                        <th>Residential District  </th>
                        <th>Plot No.	  </th>
                        <th>Profession/Occupation  </th>
                        <th>Employer Name   </th>
                        <th>Employer Address  </th>
                        <th>Employer Phone No.  </th>
                        <th>Employment Date</th>
                        <th>Branch Code/Name  </th>
                        <th>Loan Reference No.  </th>
                        <th>Old Loan Reference No.  </th>
                        <th>Currency  </th>
                        <th>Approved Amount  </th>
                        <th>Approved Amount(MWK)  </th>
                        <th>Disbursed  Amount </th>

                        <th>Disbursed Amount (MWK)   </th>
                        <th>Disbursement Date  </th>
                        <th>Maturity Date  </th>
                        <th>Borrower Type  </th>
                        <th>Group Name  </th>
                        <th>Group No.  </th>
                        <th>Product Type  </th>

                        <th>Payment Terms  </th>
                        <th>Collateral Status  </th>
                        <th>Reserve Bank Classification </th>
                        <th>Account Status </th>
                        <th>Account Status Change Date </th>
                        <th>Scheduled Repayment Amount </th>
                        <th>Scheduled Repayment Amount(MWK) </th>
                        <th>Total Amount Paid To Date  </th>
                        <th>Total Amount Paid To Date(MWK)</th>
                        <th>Current Balance	Current Balance(MWK)  </th>
                        <th>Available Credit  </th>
                        <th>Available Credit(MWK)  </th>
                        <th>Amount In Arrears  </th>
                        <th>Amount In Arrears(MWK)  </th>
                        <th>Days In Arrears	  </th>
                        <th>No. of Installments In Arrears   </th>
                        <th>Default Date  </th>
                        <th>Pay Off/Termination</th>
                        <th>Date Reason For Closure  </th>
                        <th>First Payment Date  </th>
                        <th>Last Payment Date  </th>
                        <th>Last Payment Amount</th>
                        <th>Last Payment Amount (MWK)</th>
                    </tr>
                    </thead>
                    <tbody>`;
                    
                    let count = 1;
                    let processedCount = 0;
                    for (const record of results) {
                      // console.log('Processing loan'+count, record.loan_id);
                        processedCount += 1;

                        // Calculate the percentage of processed data
                        const processedPercentage = (processedCount / totalCount) * 100;

                        // Calculate the remaining percentage
                        const remainingPercentage = 100 - processedPercentage;
                        reportTrackers[reportId].percentage = processedPercentage;
                        console.log('processed',processedPercentage);
                        console.log('remaingng', remainingPercentage);
                      let group_name = "";
                      let group_code = "";
                        let payments =await  getAllById('payement_schedules','loan_id', record.loan_id);
                        let total_payments = await  loanCollection(record.loan_id);
                        // let custbranch = getById('branches','Code', record.Branch );
                        let previousloan = await getPreviousLoan(record.loan_customer);
                        let pay_balance = (record.loan_amount_total-total_payments[0].total);
                        let paymentslast= await getRBMLoanPaymentById(record.loan_id,record.loan_period);
                        let arreasamount= await getAmountOfArrears(record.loan_id);
                        let arreasamount_paid= await getAmountOfArrearsPaid(record.loan_id);
                        let days_in_arrears= await getDaysOfArrears(record.loan_id);
                        let final_arrears_amount = arreasamount - arreasamount_paid
                        let datewithoutD = "";
                        let product =await getById('loan_products','loan_product_id', record.loan_product );
                        
                        // Get customer details to fetch branch
                        let customer = null;
                        let branchName = '';
                        if( record.customer_type==="group")
                          {
                          let custgroup =await getById('groups','group_id', record.loan_customer );
                          
                        
                          group_name = custgroup[0].group_name
                          group_code = custgroup[0].group_code

                          // Get branch for group customers (groups.Branch references branches.id)
                          if (custgroup && custgroup.length > 0 && custgroup[0].Branch) {
                              let branch = await getById('branches','id', custgroup[0].Branch);
                              if (branch && branch.length > 0) {
                                  branchName = capitalizeFirstLetter(branch[0].BranchName);
                              }
                          }
                          }
                        else {
                            // For individual customers, get their branch
                            customer = await getById('individual_customers','id', record.loan_customer);
                            if (customer && customer.length > 0 && customer[0].Branch) {
                                let branch = await getById('branches','BranchCode', customer[0].Branch);
                                if (branch && branch.length > 0) {
                                    branchName = capitalizeFirstLetter(branch[0].BranchName);
                                }
                            }
                        }
                          if (paymentslast) {
                            // Remove dashes from the date string
                            const dateWithoutDashes = formatDate(paymentslast.payment_schedule);
                            
                            if (dateWithoutDashes) {
                                // Output the date in the desired format (YYYYMMDD)
                                datewithoutD = dateWithoutDashes
                            } else {
                                // Handle invalid date format
                                
                                datewithoutD = "Invalid date format";
                            }
                        }

                   let dob = formatDate(record.DateOfBirth);
                    let disburse_date = formatDate(record.disbursed_date);
                    
                    // Helper function to capitalize first letter
                    function capitalizeFirstLetter(str) {
                        if (!str) return '';
                        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
                    }
                    
                    // Map ID Type values
                    let mappedIDType = '';
                    if (record.IDType) {
                        switch(record.IDType) {
                            case 'NATIONAL_IDENTITY_CARD':
                                mappedIDType = 'NATIONAL ID';
                                break;
                            case 'DRIVING_LISENCE':
                                mappedIDType = 'DRIVING LICENSE';
                                break;
                            case 'PASSPORT':
                                mappedIDType = 'PASSPORT';
                                break;
                            default:
                                mappedIDType = 'PASSPORT'; // fallback
                        }
                    } else {
                        mappedIDType = 'PASSPORT'; // fallback for null/empty values
                    }
                    
                    // Map frequency to payment terms codes
                    let paymentTermsCode = '';
                    if (product && product.length > 0 && product[0].frequency) {
                        switch(product[0].frequency.toLowerCase()) {
                            case 'weekly':
                                paymentTermsCode = '003';
                                break;
                            case 'monthly':
                                paymentTermsCode = '005';
                                break;
                            case 'bi weekly':
                            case 'biweekly':
                                paymentTermsCode = '004';
                                break;
                            default:
                                paymentTermsCode = '005'; // default to monthly
                        }
                    } else {
                        paymentTermsCode = '005'; // default to monthly if no frequency found
                    }
                    
                    // Map Reserve Bank Classification based on days in arrears
                    let reserveBankClassification = '';
                    if (days_in_arrears >= 0 && days_in_arrears <= 30) {
                        reserveBankClassification = 'STAGE 1';
                    } else if (days_in_arrears >= 31 && days_in_arrears <= 90) {
                        reserveBankClassification = 'STAGE 2';
                    } else if (days_in_arrears > 90) {
                        reserveBankClassification = 'STAGE 3';
                    } else {
                        reserveBankClassification = 'STAGE 1'; // default for new disbursements or no arrears data
                    }
                    
                    // Map Account Status based on Reserve Bank Classification
                    let accountStatus = '';
                    if (reserveBankClassification === 'STAGE 3') {
                        accountStatus = '002';
                    } else {
                        accountStatus = '003';
                    }
                    
table+=`

                        

                       <tr>

                        <?php

                            ?>


                            <td>  ${record.Title ? record.Title.toUpperCase() : ''} </td>
                            <td>  ${record.Lastname}  </td>
                            <td> ${record.Firstname}  </td>
                            <td>  ${record.Middlename} </td>
                            <td></td>
                            <td>${record.Gender ? record.Gender.toUpperCase() : ''} </td>
                            <td>${record.Marital_status ? record.Marital_status.toUpperCase() : 'UNMARRIED'} </td>
                            <td></td>
                            <td> ${dob} </td>
                            <td> ${record.IDNumber}  </td>
                            <td>   ${mappedIDType} </td>
                            <td>  ${record.IDNumber} </td>
                            <td>Malawi</td>
                            <td>  ${capitalizeFirstLetter(record.Village)} </td>
                            <td>  ${capitalizeFirstLetter(record.Province)}</td>
                            <td>  ${capitalizeFirstLetter(record.City)}</td>
                            <td>${capitalizeFirstLetter(record.AddressLine2)}   </td>
                            <td> ${record.PhoneNumber}  </td>
                            <td> ${record.AddressLine1}  </td>
                            <td>  ${record.EmailAddress} </td>
                            <td> ${branchName}  </td>
                            <td> ${record.AddressLine2} </td>
                            <td> ${record.AddressLine3}  </td>
                            <td> ${record.Profession}  </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>




                        <td> </td>
                        <td>  ${record.loan_number}</td>
                        <td>

                          

                        </td>
                        <td>MWK</td>
                        <td> ${record.loan_principal} </td>
                        <td> ${record.loan_principal} </td>
                        <td> ${record.loan_principal} </td>

                        <td> ${record.loan_principal} </td>

                        <td>  ${disburse_date}</td>
                        <td>

                           ${datewithoutD}
                        </td>
                        <td> ${record.customer_type === 'group' ? 'GROUP' : record.customer_type === 'individual' ? 'PERSONAL' : record.customer_type}  </td>
                        <td> ${group_name}</td>
                        <td>  ${group_code}</td>
                       <td> 003</td>



                        <td> ${paymentTermsCode} 
                        </td>
                        <td>SECURED</td>
                        <td> ${reserveBankClassification} </td>
                        <td>${accountStatus}</td>
                        <td> </td>
                        <td> ${record.loan_amount_term}</td>
                        <td> ${record.loan_amount_term}</td>
                        <td>

                            ${total_payments ? total_payments[0].total:0}
                        </td>
                        <td>
                        ${total_payments ? total_payments[0].total:0}

                        </td>
                        <td>${pay_balance}
                        </td>

                        <td>

                          ${final_arrears_amount}
                        </td>
                        <td>

                            ${final_arrears_amount}
                        </td>


                        <td>

                           ${arreasamount}
                        </td>
                        <td>

                           ${arreasamount}
                        </td>
                        <td>

                           ${days_in_arrears}
                        </td>
                        <td>
                           ${days_in_arrears}


                        </td>
                        
                        <td> </td>

                        <td></td>


                        <td> </td>

                        <td> 
                        ${datewithoutD}
                        </td>
                        <td>
                           ${datewithoutD}
                        </td>
                        <td>
                        ${paymentslast ? paymentslast.amount:0}</td>
                        <td>${paymentslast ? paymentslast.amount:0}</td>
                        </tr>
`;
                       

                    }
                    
                    table +=`
                    </tbody>
                </table>`;
       clearInterval(reportTrackers[reportId].intervalId); // Stop the interval
       delete reportTrackers[reportId]; // Remove the tracker
                console.log('retuning data');
                return table;
  }

  async function  generateHtml(reportId, user, product) {
      let processedCount = 0;
      let data = await getPARsummaryu();


      let all_active_schedules = await sumTotalPar();
      let p1 = 0;
      let p  = 0;
      for (const tamt2  of all_active_schedules){
          if(Number(tamt2.paid_amount) >= Number(tamt2.principal)){

               p=0;
              p1 += p;

          }else if(Number(tamt2.paid_amount) < Number(tamt2.principal)){
              p = Number(tamt2.principal)- Number(tamt2.paid_amount);
              p1 +=p;

          }

      }

      const totalCount =  data.length;
      console.log(totalCount,'data count')
    // Variables
    let tarrears = 0;
    let totalprincipal = 0;
    let tzerotoseven = 0;
    let morethanseven = 0;
    let morethanthirty = 0;
    let morethansixty = 0;
    let morethanninety = 0;
    let morethanonetwenty = 0;
    let morethanoneeighty = 0;
    let morethanthreesixty = 0;

    let t_payment = 0;
    let t_principal = 0;
    let t_interest = 0;
    let t_balance = 0;
    let t_amount = 0;
    let u_payment = 0;
    let uga = 0;



    // Define routes and render HTML
    let table = `
    <style>
table, td, th {
  border: 1px solid;
}

table {
  width: 100%;
  border-collapse: collapse;
}
.export-buttons {
                margin-bottom: 15px;
                text-align: right;
            }
            .export-buttons button {
                padding: 6px 12px;
                background-color: #153505;
                color: white;
                border: none;
                border-radius: 3px;
                margin-left: 5px;
                cursor: pointer;
            }
            .export-buttons button:hover {
                background-color: #2e7d32;
            }
            .export-buttons span {
                margin-right: 10px;
                font-weight: bold;
            }
</style>
<!-- Include SheetJS library for Excel exports -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>



function exportData(type){
    const fileName = 'par_report.' + type
    const table = document.getElementById("resulta")
    const wb = XLSX.utils.table_to_book(table)
    XLSX.writeFile(wb, fileName)
}
</script>
<div class="export-buttons">
                <span>Export as:</span>
                <button onclick="exportData('xlsx')">Excel (xlsx)</button>
                <button onclick="exportData('xls')">Excel (xls)</button>
                <button onclick="exportData('csv')">CSV</button>
            </div>
      <table style ="border-collapse: collapse;" cellspacing="1" id ="resulta">
        <thead>
          <tr>
            <th>Product</th>
            <th>Loan #</th>
            <th>Customer</th>
            <th>Officer</th>
           
            <th>Total Payments in arrears</th>
            
            <th>Aged 0-7 days</th>
            <th>Aged 8-30 days</th>
            <th>Aged 31-60 days</th>
            <th>Aged 61-90 days</th>
            <th>Aged 91-120 days</th>
            <th>Aged 121-180 days</th>
            <th>Aged 181-366 days</th>
            <th>Aged 367+ days</th>
          </tr>
        </thead>
        <tbody>`;

      for (const row of data) {
      // Your calculations for variables here
        processedCount += 1;

        // Calculate the percentage of processed data
        const processedPercentage = (processedCount / totalCount) * 100;

        // Calculate the remaining percentage
        const remainingPercentage = 100 - processedPercentage;
        reportTrackers[reportId].percentage = processedPercentage;
        console.log('processed',processedPercentage);
        console.log('remaingng', remainingPercentage);

          let customer_name = "";
console.log(row.loan_customer, 'customer_id')
console.log(row.customer_type, 'customer_ty')
          // my did
          if(row.customer_type=="group"){
              let group = await getById('groups','group_id', row.loan_customer);

              customer_name = group[0].group_name +'('+group[0].group_code+')';

          }else {
              let indi = await getById('individual_customers','id', row.loan_customer);
              customer_name = indi[0].Firstname+' '+indi[0].Lastname;

          }
          let overdue = await getAllOverDuePayments(row.loan_id)

          let product = await getById('loan_products','loan_product_id', row.loan_product );
          tarrears += Number(overdue);


          //   t_payment += Number(row.lm);
          // u_payment += Number(row.u_payment);
          // t_principal += Number(row.t_principal);
          // t_interest += Number(row.t_interest);
          // t_balance += Number(row.t_balance);
          // Assuming 'row' and 'uga' variables are defined earlier
          const u = +uga + +overdue;
          const currentDate = moment().startOf('day'); // Current date set to midnight
          const maxDate = moment(row.max_d).startOf('day'); // max_d date set to midnight

          console.log(maxDate, 'max date');
          const diffInDays = Math.abs(maxDate.diff(currentDate, 'days')); // Absolute difference in days
          console.log(diffInDays, 'day range');
console.log(u);

          if (diffInDays >= 0 && diffInDays <= 7) {
    tzerotoseven += u;

    t1 = u.toFixed(2);
  } else {
    t1 = 0;
  }
if (diffInDays >= 8 && diffInDays <= 30) {
       morethanseven  += u;

    t2 = u.toFixed(2);
  } else {
    t2 = 0;
  }
if (diffInDays >= 31 && diffInDays <= 60) {
       morethanthirty  += u;

    t3 = u.toFixed(2);
  } else {
    t3 = 0;
  }
if (diffInDays >= 61 && diffInDays <= 90) {
       morethansixty  += u;

    t4 = u.toFixed(2);
  } else {
    t4 = 0;
  }
if (diffInDays >= 91 && diffInDays <= 120) {
       morethanninety  += u;

    t5 = u.toFixed(2);
  } else {
    t5 = 0;
  }
if (diffInDays >= 121 && diffInDays <= 180) {
       morethanonetwenty  += u;

    t6 = u.toFixed(2);
  } else {
    t6 = 0;
  }
if (diffInDays >= 181 && diffInDays <= 366) {
       morethanoneeighty  += u;

    t7 = u.toFixed(2);
  } else {
    t7 = 0;
  }
if (diffInDays >= 367  ) {
       morethanthreesixty  += u;

    t8 = u.toFixed(2);
  } else {
    t8 = 0;
  }


          table += `
    <tr>
      <td>${product[0]?.product_name || 0}</td>
      <td>${row.lnumber}</td>
      <td>${customer_name}</td>
      <td>${row.Firstname} ${row.Lastname} </td>
      <td>${overdue}</td>
      <td>${t1}</td>
      <td>${t2}</td>
      <td>${t3}</td>
      <td>${t4}</td>
      <td>${t5}</td>
      <td>${t6}</td>
      <td>${t7}</td>
      <td>${t8}</td>
    </tr>
  `;
    }


    table += `</tbody>`;
table +=`
 <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>${tarrears}</td>

                            
                            <td>${tzerotoseven}</td>
                            <td>${morethanseven}</td>
                            <td>${morethanthirty}</td>
                            <td>${morethansixty}</td>
                            <td>${morethanninety}</td>
                            <td>${morethanonetwenty}</td>
                            <td>${morethanoneeighty}</td>
                            <td>${morethanthreesixty}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                         
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                        </tr>
                        <tr>
                            <td>TOTAL PORTFOLIO</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>MK${p1}</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                            <td>PORTFOLIO AT RISK</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${totalprincipal/p1}%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          
                        </tr>
                        <tr>
                            <td>MORE THAN 0 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${totalprincipal/p1}%'</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                            <td>MORE THAN 7 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${morethanseven/p1}</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                            <td>MORE THAN 30 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${morethanthirty/p1}</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                            <td>MORE THAN 60 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${(morethansixty/p1)*100}%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                        </tr>
                        <tr>
                            <td>MORE THAN 90 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${(morethanninety/p1)*100}%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                        </tr>
                        <tr>
                            <td>MORE THAN 120 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${(morethanonetwenty/p1)*100}%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                            <td>MORE THAN 180 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${(morethanoneeighty/p1)*100}%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                            <td>MORE THAN 366 DAYS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>${ (morethanthreesixty/p1)*100}%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            
                        </tr>
                        <tr><td colspan="6">
                                <!-- Display your data here -->

                                <!-- Display pagination links -->
                               



                            </td></tr>
                        </tfoot>

`;

    table += `
    
    
    </table>`;
      clearInterval(reportTrackers[reportId].intervalId); // Stop the interval
      delete reportTrackers[reportId]; // Remove the tracker
      console.log('retuning par data');
    return table;
  }
  
 
  function disbursed() {
    return new Promise((resolve, reject) => {
      // Get a connection from the pool
    
  
        // Define your SQL query
        const query = `
        SELECT loan.*, loan.loan_principal AS lm
        FROM loan
        JOIN payement_schedules ON payement_schedules.loan_id = loan.loan_id
        WHERE loan.disbursed = 'Yes';
      `;
  
        // Execute the query
        db.query(query, (err, results) => {
         
  
          if (err) {
            return reject(err);
          }
  
          resolve(results);
        });
      
    });
  }
  function totalloans() {
    return new Promise((resolve, reject) => {
      // Get a connection from the pool
    
  
        // Define your SQL query
        const query = `
        SELECT loan.*, loan.loan_principal AS lm
        FROM loan
        WHERE loan.loan_status = 'ACTIVE';
      `;
  
        // Execute the query
        db.query(query, (err, results) => {
         
  
          if (err) {
            return reject(err);
          }
  
          resolve(results);
        });
     
    });
  }

  function formatDate(date) {
    // Ensure date is a Date object
    if (!(date instanceof Date)) {
        throw new Error('Input must be a Date object');
    }

    // Get the year, month, and day
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
    const day = String(date.getDate()).padStart(2, '0');

    // Format as yyyymmdd
    return `${year}${month}${day}`;
}

// Add this function to your existing file

// PAR Constants
const PAR_THRESHOLDS = [1, 30, 60, 90];



// PAR Report generation endpoint with progress tracking

// Fixed code to use a consistent filename throughout the process
// Updated PAR Report endpoint to use the enhanced version
app.post('/generate-report-par-v2', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Enhanced PAR Detailed Portfolio Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'DETAILED_PORTFOLIO',
        user: req.body.user || 'System',
        user_id: req.body.user_id || 0,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert report: ', err);
            return;
        }

        try {
            // Generate filename with timestamp
            const reportId = result.insertId;
            const timestamp = moment().format('YYYYMMDD_HHmmss');
            const reportFileName = `PAR_Detailed_Portfolio_Report_${timestamp}.html`;

            // Use absolute path for file storage
            const directory = path.resolve(__dirname, 'reports');

            // Ensure the directory exists
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            // Full filesystem path for writing the file
            const filePath = path.join(directory, reportFileName);
            // Relative path for database storage
            const dbPath = `reports/${reportFileName}`;

            console.log(`Enhanced PAR Report will be saved as: ${reportFileName}`);
            console.log(`Full path: ${filePath}`);
            console.log(`DB path: ${dbPath}`);

            // Store the interval ID for later cleanup
            const intervalId = setInterval(() => updatePercentageToDB(reportId), 5000);

            // Store ALL the information including paths in the tracker object
            reportTrackers[reportId] = {
                percentage: 0,
                intervalId: intervalId,
                reportFileName: reportFileName,
                filePath: filePath,
                dbPath: dbPath
            };

            // Get filters from request body
            const officer = req.body.officer;
            const product = req.body.product;
            const branch = req.body.branch;
            const dateFrom = req.body.date_from;
            const dateTo = req.body.date_to;

            console.log(`Generating Enhanced PAR Report with filters:`, {
                officer: officer || 'All',
                product: product || 'All',
                branch: branch || 'All',
                dateFrom: dateFrom || 'No start date',
                dateTo: dateTo || 'No end date'
            });

            // Generate HTML content using the enhanced version
            await generatePARReportV2Enhanced(reportId, officer, product, branch, dateFrom, dateTo, db, reportTrackers);

            // Clear the interval to stop updates
            if (reportTrackers[reportId] && reportTrackers[reportId].intervalId) {
                clearInterval(reportTrackers[reportId].intervalId);
            }

            // After processing, update the report status to 'completed'
            const updateReport = {
                status: 'completed',
                percentage: 100,
                download_link: reportTrackers[reportId].dbPath,
                completed_time: new Date(),
            };

            // Update the report record in the database
            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, reportId], (updateErr) => {
                if (updateErr) {
                    console.error('Failed to update report status: ', updateErr);
                    return;
                }
                console.log('Enhanced PAR Report processing completed and updated');
                console.log(`Download link saved in database: ${reportTrackers[reportId].dbPath}`);

                // Clean up the tracker object after using it
                delete reportTrackers[reportId];
            });

        } catch (err) {
            console.error('Error during enhanced PAR report generation: ', err);

            // Clear the interval in case of error
            if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                clearInterval(reportTrackers[result.insertId].intervalId);
            }

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Enhanced PAR Report processing failed and updated');

                // Clean up the tracker object
                delete reportTrackers[result.insertId];
            });
        }
    });
});

// You can also add a new endpoint specifically for the enhanced PAR report
app.post('/generate-report-par-detailed-portfolio', async (req, res) => {
    // This endpoint specifically generates the enhanced PAR detailed portfolio report
    // Use the same logic as above but with a different report type identifier

    res.status(202).json({ message: 'PAR Detailed Portfolio Report generation is processing...' });

    const report = {
        report_type: 'PAR_DETAILED_PORTFOLIO_ENHANCED',
        user: req.body.user || 'System',
        user_id: req.body.user_id || 0,
        status: 'in progress',
    };

    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert PAR detailed portfolio report: ', err);
            return;
        }

        try {
            const reportId = result.insertId;
            const timestamp = moment().format('YYYYMMDD_HHmmss');
            const reportFileName = `PAR_Detailed_Portfolio_Enhanced_${timestamp}.html`;

            const directory = path.resolve(__dirname, 'reports');
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            const filePath = path.join(directory, reportFileName);
            const dbPath = `reports/${reportFileName}`;

            const intervalId = setInterval(() => updatePercentageToDB(reportId), 5000);

            reportTrackers[reportId] = {
                percentage: 0,
                intervalId: intervalId,
                reportFileName: reportFileName,
                filePath: filePath,
                dbPath: dbPath
            };

            // Extract all possible filter parameters
            const filterOptions = {
                officer: req.body.officer || req.body.loan_officer,
                product: req.body.product || req.body.loan_product,
                branch: req.body.branch,
                dateFrom: req.body.date_from || req.body.from_date,
                dateTo: req.body.date_to || req.body.to_date,
                status: req.body.status || 'ACTIVE',
                customer_type: req.body.customer_type
            };

            console.log(`Generating PAR Detailed Portfolio Report with comprehensive filters:`, filterOptions);

            // Generate the enhanced report
            await generatePARReportV2Enhanced(
                reportId,
                filterOptions.officer,
                filterOptions.product,
                filterOptions.branch,
                filterOptions.dateFrom,
                filterOptions.dateTo,
                db,
                reportTrackers
            );

            // Clear the interval
            if (reportTrackers[reportId] && reportTrackers[reportId].intervalId) {
                clearInterval(reportTrackers[reportId].intervalId);
            }

            // Update report status to completed
            const updateReport = {
                status: 'completed',
                percentage: 100,
                download_link: reportTrackers[reportId].dbPath,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, reportId], (updateErr) => {
                if (updateErr) {
                    console.error('Failed to update PAR detailed portfolio report status: ', updateErr);
                    return;
                }
                console.log('PAR Detailed Portfolio Report processing completed successfully');
                delete reportTrackers[reportId];
            });

        } catch (err) {
            console.error('Error during PAR detailed portfolio report generation: ', err);

            if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                clearInterval(reportTrackers[result.insertId].intervalId);
            }

            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('PAR Detailed Portfolio Report processing failed');
                delete reportTrackers[result.insertId];
            });
        }
    });
});

// Updated customer name handling and export functionality
// app.post('/generate-report-par-v2', async (req, res) => {
//     // Respond immediately that the report generation is in progress
//     res.status(202).json({ message: 'PAR Report generation is processing...' });
//
//     // Define the report object
//     const report = {
//         report_type: 'PAR',
//         user: req.body.user || 'System',
//         user_id: req.body.user_id || 0,
//         status: 'in progress',
//     };
//
//     // Insert the report into the database
//     db.query('INSERT INTO reports SET ?', report, async (err, result) => {
//         if (err) {
//             console.error('Failed to insert report: ', err);
//             return;
//         }
//
//         try {
//             // Generate filename with timestamp - STORE THIS FOR LATER USE
//             const reportId = result.insertId;
//             const timestamp = moment().format('YYYYMMDD_HHmmss');
//             const reportFileName = `PAR_Report_${timestamp}.html`;
//
//             // Use absolute path for file storage
//             const directory = path.resolve(__dirname, 'reports');
//
//             // Ensure the directory exists
//             if (!fs.existsSync(directory)) {
//                 fs.mkdirSync(directory, { recursive: true });
//             }
//
//             // Full filesystem path for writing the file
//             const filePath = path.join(directory, reportFileName);
//             // Relative path for database storage
//             const dbPath = `reports/${reportFileName}`;
//
//             console.log(`Report will be saved as: ${reportFileName}`);
//             console.log(`Full path: ${filePath}`);
//             console.log(`DB path: ${dbPath}`);
//
//             // Store the interval ID for later cleanup
//             const intervalId = setInterval(() => updatePercentageToDB(reportId), 5000);
//
//             // Store ALL the information including paths in the tracker object
//             reportTrackers[reportId] = {
//                 percentage: 0,
//                 intervalId: intervalId,
//                 reportFileName: reportFileName,
//                 filePath: filePath,
//                 dbPath: dbPath
//             };
//
//             // Get filters from request body
//             const officer = req.body.officer;
//             const product = req.body.product;
//             const branch = req.body.branch;
//             const dateFrom = req.body.date_from;
//             const dateTo = req.body.date_to;
//
//             // Generate HTML content and write to the file with the filters
//             await generatePARReportV2(reportId, officer, product, branch, dateFrom, dateTo);
//
//             // Clear the interval to stop updates
//             if (reportTrackers[reportId] && reportTrackers[reportId].intervalId) {
//                 clearInterval(reportTrackers[reportId].intervalId);
//             }
//
//             // After processing, update the report status to 'completed'
//             // Use the paths from the tracker to ensure consistency
//             const updateReport = {
//                 status: 'completed',
//                 percentage: 100,
//                 download_link: reportTrackers[reportId].dbPath,
//                 completed_time: new Date(),
//             };
//
//             // Update the report record in the database
//             db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, reportId], (updateErr) => {
//                 if (updateErr) {
//                     console.error('Failed to update report status: ', updateErr);
//                     return;
//                 }
//                 console.log('PAR Report processing completed and updated');
//                 console.log(`Download link saved in database: ${reportTrackers[reportId].dbPath}`);
//
//                 // Clean up the tracker object after using it
//                 delete reportTrackers[reportId];
//             });
//         } catch (err) {
//             console.error('Error during report generation: ', err);
//
//             // Clear the interval in case of error too
//             if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
//                 clearInterval(reportTrackers[result.insertId].intervalId);
//             }
//
//             // Update report status to 'failed' in case of error
//             const updateReport = {
//                 status: 'failed',
//                 error_message: err.message,
//                 completed_time: new Date(),
//             };
//
//             db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
//                 console.error('Report processing failed and updated');
//
//                 // Clean up the tracker object
//                 delete reportTrackers[result.insertId];
//             });
//         }
//     });
// });




// Principal Balance PAR Report endpoint
app.post('/generate-report-par-principal-balance', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Principal Balance PAR Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'PAR_PRINCIPAL_BALANCE',
        user: req.body.user || 'System',
        user_id: req.body.user_id || 0,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert Principal Balance PAR report: ', err);
            return;
        }

        try {
            const reportId = result.insertId;
            const timestamp = moment().format('YYYYMMDD_HHmmss');
            const reportFileName = `PAR_Principal_Balance_Report_${timestamp}.html`;

            const directory = path.resolve(__dirname, 'reports');
            if (!fs.existsSync(directory)) {
                fs.mkdirSync(directory, { recursive: true });
            }

            const filePath = path.join(directory, reportFileName);
            const dbPath = `reports/${reportFileName}`;

            const intervalId = setInterval(() => updatePercentageToDB(reportId), 5000);

            reportTrackers[reportId] = {
                percentage: 0,
                intervalId: intervalId,
                reportFileName: reportFileName,
                filePath: filePath,
                dbPath: dbPath
            };

            // Extract filter parameters
            const filterOptions = {
                officer: req.body.officer || req.body.loan_officer,
                product: req.body.product || req.body.loan_product,
                branch: req.body.branch,
                dateFrom: req.body.date_from || req.body.from_date,
                dateTo: req.body.date_to || req.body.to_date
            };

            console.log(`Generating Principal Balance PAR Report with filters:`, filterOptions);

            // Generate the Principal Balance PAR report
            await generatePrincipalBalancePARReport(
                reportId,
                filterOptions.officer,
                filterOptions.product,
                filterOptions.branch,
                filterOptions.dateFrom,
                filterOptions.dateTo,
                db,
                reportTrackers
            );

            // Clear the interval
            if (reportTrackers[reportId] && reportTrackers[reportId].intervalId) {
                clearInterval(reportTrackers[reportId].intervalId);
            }

            // Update report status to completed
            const updateReport = {
                status: 'completed',
                percentage: 100,
                download_link: reportTrackers[reportId].dbPath,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, reportId], (updateErr) => {
                if (updateErr) {
                    console.error('Failed to update Principal Balance PAR report status: ', updateErr);
                    return;
                }
                console.log('Principal Balance PAR Report processing completed successfully');
                delete reportTrackers[reportId];
            });

        } catch (err) {
            console.error('Error during Principal Balance PAR report generation: ', err);

            if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                clearInterval(reportTrackers[result.insertId].intervalId);
            }

            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Principal Balance PAR Report processing failed');
                delete reportTrackers[result.insertId];
            });
        }
    });
});

// Arrears Report endpoint
app.post('/generate-report-arrears', async (req, res) => {
    // Respond immediately that the report generation is in progress
    res.status(202).json({ message: 'Arrears Report generation is processing...' });

    // Define the report object
    const report = {
        report_type: 'ARREARS_REPORT',
        user: req.body.user,
        user_id: req.body.user_id,
        status: 'in progress',
    };

    // Insert the report into the database
    db.query('INSERT INTO reports SET ?', report, async (err, result) => {
        if (err) {
            console.error('Failed to insert arrears report: ', err);
            return;
        }

        try {
            const currentDate = moment().format('YYYYMMDD_HHmmss');

            // Use the utility function to get paths
            const paths = getReportPaths('report_arrears', currentDate);

            // Initialize report tracker
            reportTrackers[result.insertId] = {
                percentage: 0,
                intervalId: setInterval(() => updatePercentageToDB(result.insertId), 15000),
            };

            // Extract filter parameters from request
            const filterOptions = {
                start_date: req.body.start_date || null,
                end_date: req.body.end_date || null,
                officer_id: req.body.officer_id || null,
                officer_name: req.body.officer_name || 'All Officers',
                branch_id: req.body.branch_id || null,
                branch_name: req.body.branch_name || 'All Branches'
            };

            console.log(`Generating Arrears Report with options: ${JSON.stringify(filterOptions)}`);

            // Generate HTML content
            const html = await generateArrearsReport(
                filterOptions,
                result.insertId,
                reportTrackers,
                db
            );

            // Write file using absolute path for file system operations
            fs.writeFile(paths.filePath, html, async (writeErr) => {
                if (writeErr) {
                    console.error('Failed to write arrears report file: ', writeErr);

                    // Update report status to 'failed' in case of error
                    const updateReport = {
                        status: 'failed',
                        error_message: writeErr.message,
                        completed_time: new Date(),
                    };

                    db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId]);

                    // Clear interval
                    if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                        clearInterval(reportTrackers[result.insertId].intervalId);
                        delete reportTrackers[result.insertId];
                    }

                    return;
                }

                console.log('Arrears report saved to file:', paths.filePath);

                // Store relative path for the download_link
                const updateReport = {
                    status: 'completed',
                    percentage: 100,
                    download_link: paths.dbPath,  // Store relative path for better web access
                    completed_time: new Date(),
                };

                // Update the report record in the database
                db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], (updateErr) => {
                    if (updateErr) {
                        console.error('Failed to update arrears report status: ', updateErr);
                        return;
                    }
                    console.log('Arrears report processing completed and updated');

                    // Clear the interval for this report
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                });
            });
        } catch (err) {
            console.error('Error during arrears report generation: ', err);

            // Update report status to 'failed' in case of error
            const updateReport = {
                status: 'failed',
                error_message: err.message,
                completed_time: new Date(),
            };

            db.query('UPDATE reports SET ? WHERE id = ?', [updateReport, result.insertId], () => {
                console.error('Arrears report processing failed and updated');

                // Clear the interval for this report
                if (reportTrackers[result.insertId] && reportTrackers[result.insertId].intervalId) {
                    clearInterval(reportTrackers[result.insertId].intervalId);
                    delete reportTrackers[result.insertId]; // Remove the tracker
                }
            });
        }
    });
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
