const mysql = require('mysql2');

// Create MySQL connection pool
const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'finfin'
});

// Helper function to execute queries
const query = (sql, params) => {
    return new Promise((resolve, reject) => {
        pool.query(sql, params, (err, results) => {
            if (err) return reject(err);
            resolve(results);
        });
    });
};

// Function to get amount of arrears
const getAmountOfArrears = async (loanID) => {
    const sql = `
        SELECT SUM(ps.amount) AS amount_arrears
        FROM loan l
        JOIN payement_schedules ps ON l.loan_id = ps.loan_id
        WHERE l.loan_id = ${loanID}
          AND l.loan_status = 'Active'
          AND ps.payment_schedule < CURDATE()
          AND ps.status = 'NOT PAID'
    `;
    const results = await query(sql);
    
    // Check if results are returned and if amount_arrears is not null
    return results[0]?.amount_arrears || 0;
};

const getAmountOfArrearsPaid = async (loanID) => {
    const sql = `
        SELECT SUM(ps.paid_amount) AS amount_arrears
        FROM loan l
        JOIN payement_schedules ps ON l.loan_id = ps.loan_id
        WHERE l.loan_id = ${loanID}
          AND l.loan_status = 'Active'
          AND ps.payment_schedule < CURDATE()
          AND ps.status = 'NOT PAID'
    `;
    const results = await query(sql);
    return results[0]?.amount_arrears || 0;
};
const getDaysOfArrears = async (loanID) => {
    const sql = `
        SELECT DATEDIFF(CURRENT_DATE(), MAX(ps.payment_schedule)) AS days_in_arrears
        FROM loan l
        JOIN payement_schedules ps ON l.loan_id = ps.loan_id
        WHERE l.loan_id = ?
        AND ps.payment_schedule < CURDATE()
        AND l.loan_status = 'Active'
        AND ps.status = 'NOT PAID'
    `;

    const results = await query(sql, [loanID]);
    return results[0]?.days_in_arrears || 0 ; // Since you're expecting a single row
};

// Function to get loan details
const getLoanDetails = async (loanID) => {
    const sql = `
        SELECT * FROM loan
        WHERE loan_id = ?
    `;
    const results = await query(sql, [loanID]);
    return results[0];
};

// Function to get user profile
const getUserProfile = async (userID) => {
    const sql = `
        SELECT * FROM users
        WHERE user_id = ?
    `;
    const results = await query(sql, [userID]);
    return results[0];
};

// Function to get loan repayments
const getLoanRepayments = async (loanID) => {
    const sql = `
        SELECT * FROM loan_repayments
        WHERE loan_id = ?
    `;
    const results = await query(sql, [loanID]);
    return results;
};
const getPARsummaryu = async (user, loanproduct) => {
    // Initialize the query
    let q = '';

    // Concatenate filters based on the provided values
    if (user && user !== 'All') {
        // If user is provided and not 'All', add filter for user
        q += `AND AA.loan_added_by = '${user}' `;
    }

    if (loanproduct && loanproduct !== 'All') {
        // If loanproduct is provided and not 'All', add filter for loan product
        q += `AND AA.loan_product = '${loanproduct}' `;
    }

    // Default filter for non-deleted loans, if no user or product filters were applied
    if (q === '') {
        q = `AND AA.loan_status != 'deleted'`;
    }

    // Define the query string
    const sql = `
    SELECT 
        AA.loan_id as loan_id,
        AA.loan_added_by as loan_added_by,
        AA.loan_customer as loan_customer,
        AA.customer_type as customer_type,
        AA.loan_number as lnumber,
        AA.loan_principal as lm,
        AA.loan_product,
        EP.Firstname,
        EP.Lastname,
        SUM(PS.amount) AS total_amount,
        (SELECT MIN(payment_schedule) 
            FROM payement_schedules 
            WHERE customer = AA.loan_customer 
            AND status = 'NOT PAID' 
            AND payment_schedule <= DATE(NOW())) AS max_d 
    FROM payement_schedules PS 
    JOIN loan AA ON PS.loan_id = AA.loan_id 
    JOIN employees EP ON AA.loan_added_by = EP.id 
    WHERE PS.status = 'NOT PAID' 
    AND PS.payment_schedule <= DATE(NOW()) 
    AND AA.loan_status != 'deleted' 
    ${q}
    GROUP BY AA.loan_id
    `;

    // Execute the query and return results
    const results = await query(sql);
    return results;
};


const getPARsummary = async () => {
    const sql = `
SELECT AA.loan_id as loan_id,
       AA.loan_number as lnumber,
       AA.loan_principal as lm,
       BB.id as bid,
       BD.Firstname,
       BD.Lastname,
       ROUND(IFNULL((SELECT sum(amount) FROM payement_schedules WHERE customer = AA.loan_customer AND status = 'PAID' AND payment_schedule <= DATE(NOW()) ),0),2)
           AS t_payment,
    ROUND(IFNULL((SELECT sum(amount) FROM payement_schedules WHERE customer = AA.loan_customer AND status = 'NOT PAID' AND payment_schedule <= DATE(NOW()) ),0),2)
        AS u_payment,
    (SELECT MIN(payment_schedule) FROM payement_schedules WHERE customer = AA.loan_customer AND status = 'NOT PAID' AND payment_schedule <= DATE(NOW()) ) 
        AS max_d, 
    ROUND(IFNULL((SELECT sum(amount) FROM payement_schedules WHERE customer = AA.loan_customer AND status = 'NOT PAID' ),0),2) 
    AS t_balance,
    ROUND(IFNULL((SELECT sum(principal) FROM payement_schedules WHERE customer = AA.loan_customer AND status = 'NOT PAID' AND payment_schedule <= DATE(NOW()) ),0),2)
    AS t_principal,
    ROUND(IFNULL((SELECT sum(interest) FROM payement_schedules WHERE customer = AA.loan_customer AND status = 'NOT PAID' AND payment_schedule <= DATE(NOW()))  ,0),2) 
    AS t_interest, 
    CC.loan_id as is_due, 
    CC.loan_status as l_state 
    FROM loan AS AA 
    INNER JOIN payement_schedules AS BB ON AA.loan_id = BB.loan_id 
    INNER JOIN individual_customers AS BD ON BB.customer = BD.id 
    LEFT JOIN (SELECT a.* FROM loan a INNER JOIN payement_schedules b ON a.loan_id = b.loan_id	WHERE payment_schedule <= DATE(NOW()) AND a.loan_status = 'ACTIVE' AND b.status = 'NOT PAID' GROUP BY a.loan_id)
        as CC ON CC.loan_id = AA.loan_id 
    GROUP BY AA.loan_id ORDER BY AA.loan_id
    `;
    const results = await query(sql);
    return results;
};
const getParPrincipal = async () => {
    const sql = `
        SELECT SUM(loan_principal) AS total_principal
        FROM loan
        WHERE disbursed = "Yes" AND  loan_status = "ACTIVE" 
    `;
    const results = await query(sql);
    return results[0]?.total_principal || 0 ;
};
// Function to get loan status
const getLoanStatus = async (loanID) => {
    const sql = `
        SELECT loan_status FROM loan
        WHERE loan_id = ?
    `;
    const results = await query(sql, [loanID]);
    return results[0];
};

// Function to get total payments made
const getTotalPayments = async (loanID) => {
    const sql = `
        SELECT SUM(amount) AS total_payments
        FROM loan_payments
        WHERE loan_id = ?
    `;
    const results = await query(sql, [loanID]);
    return results[0];
};

// Function to get paginated report data
const rbmReport1 = async () => {
    const sql = `
        SELECT * FROM loan  
      
        LEFT JOIN individual_customers ON loan.loan_customer = individual_customers.id 
        LEFT JOIN proofofidentity ON proofofidentity.ClientID = individual_customers.ClientID 
        ORDER BY loan.loan_id DESC
    `;
    const results = await query(sql);
    return results;
};
const rbmReport = async () => {
    const sql = `
        SELECT * FROM individual_customers 
            INNER JOIN proofofidentity ON proofofidentity.ClientID = individual_customers.ClientID 
            INNER JOIN loan ON loan.loan_customer = individual_customers.id 
            ORDER BY loan.loan_id DESC
    `;
    const results = await query(sql);
    return results;
};
// Function to get all records by table name, key, and value
const getAllById = async (table, key, value, callback) => {
    const sql = `SELECT * FROM ?? WHERE ?? = ?`;
   await  query(sql, [table, key, value], (err, results) => {
        if (err) return callback(err);
        callback(null, results);
    });
};
const loanCollection =  (loanID) => {
    const sql = `
        SELECT SUM(paid_amount) AS total
        FROM payement_schedules
        WHERE loan_id = ${loanID}
    `;
   const result = query(sql)
   return result;
};
// Function to get a single record by table name, key, and value
// In databaseHelpers.js
const getById = async (table, key, value) => {
    // Escape table name with backtick
    const sql = `SELECT * FROM \`${table}\` WHERE \`${key}\` = ${value}`;
    const results = await query(sql);
    return results;
};


// Function to get previous loan
const getPreviousLoan = async (customerID, callback) => {
    const sql = `
        SELECT * FROM loan
        WHERE loan_customer = ?
          AND loan_id < (SELECT MAX(loan_id) FROM loan WHERE loan_customer = ?)
        ORDER BY loan_id DESC
        LIMIT 1
    `;
   await query(sql, [customerID, customerID], (err, results) => {
        if (err) return callback(err);
        callback(null, results[0]);
    });
};
const getRBMLoanPaymentById = async (loanId, paymentNumber) => {
    const sql = `
        SELECT * 
        FROM payement_schedules 
        WHERE loan_id = ? 
          AND payment_number = ?
    `;
    const results = await query(sql, [loanId, paymentNumber]);
    return results[0]; // Return the first result (single record)
};
const getAllOverDuePayments = async (loanId) => {
    const sql = `
        SELECT SUM(principal) AS total_arrears
        FROM payement_schedules
        WHERE loan_id = ?
          AND status = 'NOT PAID'
          AND payment_schedule <= CURDATE()
    `;
    const results = await query(sql, [loanId]);
    return results[0]?.total_arrears || 0; // Return the first result (single record with total_arrears)
};
const sumTotalPar = async () => {
    const sql = `
        SELECT *, loan.loan_principal AS lm
        FROM loan
       LEFT JOIN payement_schedules ON payement_schedules.loan_id = loan.loan_id
        WHERE disbursed = 'Yes' AND  loan_status = "ACTIVE"
    `;
    const results = await query(sql);
    return results; // Return the result set
};

// Export all functions
module.exports = {
    query, // Export the query function for other files to use
    getAmountOfArrears,
    getAmountOfArrearsPaid,
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
    getDaysOfArrears,
    getParPrincipal,
    getPARsummary,
    getPARsummaryu,
    getAllOverDuePayments,
    sumTotalPar
};
