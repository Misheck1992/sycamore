<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Terminologies - Finance Realm System Documentation">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Terminologies</title>

  <!-- Favicon -->
  <link rel="icon" href="img/favicon.ico" type="image/x-icon">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <!-- Custom CSS -->
  <style>
    .content-wrapper {
      margin-left: 250px !important;
    }

    @media (max-width: 768px) {
      .content-wrapper {
        margin-left: 0 !important;
      }
    }

    .welcome-header {
      background: linear-gradient(135deg, #28a745 0%, #17a2b8 100%);
      color: white;
      padding: 1.5rem;
      margin-bottom: 2rem;
      border-radius: .25rem;
      box-shadow: 0 4px 20px 0 rgba(0,0,0,.1);
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .content-header {
      padding-bottom: 0;
    }

    .section {
      background: #f8f9fa;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px;
      margin-bottom: 20px;
      transition: box-shadow 0.3s ease;
    }

    .section:hover {
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .section h2 {
      color: #28a745;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
    }

    .section h2 i {
      margin-right: 0.5rem;
      color: #17a2b8;
    }

    .section p {
      color: #6c757d;
      line-height: 1.6;
    }

    .alphabet-filter {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 0.5rem;
      margin-bottom: 2rem;
    }

    .alphabet-filter button {
      background: #007bff;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 0.25rem;
      font-size: 0.9rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .alphabet-filter button:hover {
      background: #0056b3;
    }

    .alphabet-filter button:disabled {
      background: #6c757d;
      cursor: not-allowed;
    }

    .term-group {
      margin-bottom: 1.5rem;
      display: none; /* Hidden by default */
    }

    .term-group.active {
      display: block;
    }

    .term-group h3 {
      color: #28a745;
      border-bottom: 1px solid #dee2e6;
      padding-bottom: 0.5rem;
      margin-bottom: 1rem;
    }

    .term {
      margin-bottom: 1rem;
    }

    .term strong {
      color: #17a2b8;
    }

    @media (max-width: 767.98px) {
      .section {
        width: 100%;
      }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
      .section {
        width: 50%;
      }
    }

    /* Active Menu Highlight */
    .main-sidebar .nav-sidebar .nav-link.active {
      background-color: #007bff !important;
      color: #fff !important;
    }

    .main-sidebar .nav-sidebar .nav-link.active i {
      color: #fff !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <?php require_once('includes/navbar.php'); ?>

  <!-- Sidebar -->
  <?php require_once('includes/sidebar.php'); ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Terminologies</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Terminologies</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Finance Realm Terminologies</h3>
        <p class="mb-0">A glossary of key terms used in the Finance Realm system for quick reference.</p>
      </div>

      <!-- Alphabet Filter -->
      <div class="section">
        <h2><i class="fas fa-filter"></i> Filter by Letter</h2>
        <div class="alphabet-filter">
          <button data-letter="all">All</button>
          <?php
          for ($i = 65; $i <= 90; $i++) {
            echo '<button data-letter="' . chr($i) . '">' . chr($i) . '</button>';
          }
          ?>
        </div>
      </div>

      <!-- Terminologies Glossary -->
      <div class="section">
        <h2><i class="fas fa-book"></i> Glossary</h2>
        <p>Browse terms alphabetically. Click a letter above to filter.</p>

        <!-- A -->
        <div class="term-group active" id="group-A">
          <h3>A</h3>
          <div class="term"><strong>Account</strong>: A record tracking financial transactions for an individual or entity, central to monitoring balances and payments in the Finance Realm system.</div>
          <div class="term"><strong>Accrual</strong>: Recording revenue or expenses when earned or incurred, not when cash is received or paid, critical for accurate loan interest calculations.</div>
          <div class="term"><strong>Affirmative Covenant</strong>: A loan agreement clause requiring borrowers to perform specific actions, like maintaining insurance, to protect lender interests.</div>
          <div class="term"><strong>Amortization</strong>: Gradual repayment of a loan through scheduled payments covering principal and interest, reducing the balance over time.</div>
          <div class="term"><strong>Amortization Schedule</strong>: A table detailing loan payment breakdowns into principal and interest over the loan term.</div>
          <div class="term"><strong>Annual Percentage Rate (APR)</strong>: Total yearly cost of borrowing, including interest and fees, expressed as a percentage for loan comparison.</div>
          <div class="term"><strong>Asset</strong>: Any item of value owned by a lender or borrower, often used as collateral in secured loans.</div>
          <div class="term"><strong>Automated Clearing House (ACH)</strong>: An electronic network for processing direct deposits and payments, used for loan disbursements and repayments.</div>
          <div class="term"><strong>Approve Loan</strong>: The process of reviewing and authorizing a loan application after verifying eligibility and risk in the Finance Realm system.</div>
          <div class="term"><strong>Approve Groups</strong>: Validation and authorization of customer groups for collective lending or management purposes.</div>
          <div class="term"><strong>Approve Customer</strong>: Verification and acceptance of new customer profiles for system access and services.</div>
          <div class="term"><strong>Audit Trail</strong>: A chronological record of system activities for compliance and security reviews, tracked in Global Administration.</div>
        </div>

        <!-- B -->
        <div class="term-group active" id="group-B">
          <h3>B</h3>
          <div class="term"><strong>Balance Sheet</strong>: A financial statement showing assets, liabilities, and equity, used to assess financial health in Reports.</div>
          <div class="term"><strong>Balloon Payment</strong>: A large final payment due at the end of a loan term, often used in short-term financing.</div>
          <div class="term"><strong>Bank</strong>: A financial institution that accepts deposits and makes loans, central to the Finance Realm ecosystem.</div>
          <div class="term"><strong>Bank Run</strong>: A situation where many depositors withdraw funds simultaneously, impacting liquidity.</div>
          <div class="term"><strong>Bankruptcy</strong>: A legal process where a borrower unable to repay debts seeks relief, affecting loan recovery.</div>
          <div class="term"><strong>Base Rate</strong>: The minimum interest rate set by a central bank, influencing lending rates in Loan Settings.</div>
          <div class="term"><strong>Bond</strong>: A debt instrument issued to raise funds, where investors lend money for fixed interest payments.</div>
          <div class="term"><strong>Borrower</strong>: An individual or entity receiving a loan, obligated to repay principal and interest.</div>
          <div class="term"><strong>Branch Management</strong>: Administration of branch locations and operations, configurable in Global Administration.</div>
          <div class="term"><strong>Bridge Loan</strong>: Short-term financing to bridge funding gaps, often with higher interest rates.</div>
          <div class="term"><strong>Broker</strong>: An intermediary facilitating loans between borrowers and lenders, earning commissions.</div>
          <div class="term"><strong>Budget</strong>: A financial plan outlining income and expenses, used to assess repayment capacity in Customer Information.</div>
          <div class="term"><strong>Backups</strong>: System-generated data copies for recovery, managed in Global Administration.</div>
        </div>

        <!-- C -->
        <div class="term-group active" id="group-C">
          <h3>C</h3>
          <div class="term"><strong>Call Loan</strong>: A short-term loan repayable on demand, typically used by brokers.</div>
          <div class="term"><strong>Capital</strong>: Funds available for lending, forming the basis for financial operations.</div>
          <div class="term"><strong>Capital Adequacy Ratio (CAR)</strong>: A measure of a bank's capital relative to risk-weighted assets, ensuring lending stability.</div>
          <div class="term"><strong>Cash Flow</strong>: Net cash moving in and out, critical for evaluating repayment ability in Loan Information.</div>
          <div class="term"><strong>Cash Operations</strong>: Management of daily cash inflows and outflows for operational efficiency, tracked in Cash Operations.</div>
          <div class="term"><strong>Central Bank</strong>: A national institution regulating money supply and interest rates, influencing lending conditions.</div>
          <div class="term"><strong>Charge-Off</strong>: A loan deemed uncollectible, removed from assets but pursued through collections.</div>
          <div class="term"><strong>Collateral</strong>: Assets pledged to secure a loan, forfeited if repayment fails, logged in Loan Information.</div>
          <div class="term"><strong>Collection Sheet</strong>: A daily summary of due payments and collections for efficient recovery, managed in Collection Sheet.</div>
          <div class="term"><strong>Commercial Bank</strong>: A bank providing loans and deposit services, integrated with Finance Realm.</div>
          <div class="term"><strong>Commercial Paper</strong>: Unsecured short-term debt issued by corporations for operational financing.</div>
          <div class="term"><strong>Commitment Fee</strong>: A charge for keeping a loan line available but unused.</div>
          <div class="term"><strong>Compound Interest</strong>: Interest calculated on the principal and accumulated interest, affecting loan costs.</div>
          <div class="term"><strong>Consumer Credit</strong>: Loans for personal use, regulated by consumer protection laws.</div>
          <div class="term"><strong>Cost of Funding</strong>: Expenses associated with sourcing capital for lending, tracked in Cost of Funding.</div>
          <div class="term"><strong>CRB Report</strong>: Credit bureau report assessing borrower creditworthiness, used in Approve Customer.</div>
          <div class="term"><strong>Credit Expenses</strong>: Costs related to credit operations, tracked in Expenses.</div>
          <div class="term"><strong>Customer</strong>: An individual or entity using financial services, managed in Customer Information.</div>
          <div class="term"><strong>Customer Information</strong>: Detailed profiles for managing client data and KYC compliance.</div>
          <div class="term"><strong>Customer Logins</strong>: Secure authentication and tracking of user access, managed in Customer Logins.</div>
        </div>

        <!-- D -->
        <div class="term-group active" id="group-D">
          <h3>D</h3>
          <div class="term"><strong>Debt</strong>: Borrowed money to be repaid with interest, tracked in Loan Information.</div>
          <div class="term"><strong>Debt Service</strong>: Payments covering interest and principal on loans.</div>
          <div class="term"><strong>Debtor</strong>: The borrower owing money to a creditor.</div>
          <div class="term"><strong>Default</strong>: Failure to meet loan repayment terms, triggering collection actions.</div>
          <div class="term"><strong>Delinquency</strong>: Late payment on a loan, monitored in Loan Repayments.</div>
          <div class="term"><strong>Depository Institution</strong>: A bank or credit union holding deposits and making loans.</div>
          <div class="term"><strong>Discount Rate</strong>: Interest rate charged by a central bank for loans to commercial banks.</div>
          <div class="term"><strong>Disburse Loan</strong>: The release of approved funds to the borrower, logged in Loan Information.</div>
          <div class="term"><strong>Diversification</strong>: Spreading loans to reduce risk, analyzed in Reports.</div>
          <div class="term"><strong>Drawdown</strong>: Borrowing funds from an approved credit line.</div>
          <div class="term"><strong>Due Diligence</strong>: Investigation of a borrower's financial health before loan approval.</div>
        </div>

        <!-- E -->
        <div class="term-group active" id="group-E">
          <h3>E</h3>
          <div class="term"><strong>Employee & User Mgt</strong>: Administration of staff profiles and system access roles, managed in Employee & User Mgt.</div>
          <div class="term"><strong>Enquiries</strong>: Customer requests for information or services, logged in Enquiries.</div>
          <div class="term"><strong>Equity</strong>: Ownership interest in assets after liabilities, used as collateral.</div>
          <div class="term"><strong>Escrow</strong>: Funds held by a third party until loan conditions are met.</div>
          <div class="term"><strong>Exchange Rate</strong>: Value of one currency in terms of another, affecting international lending.</div>
          <div class="term"><strong>Expenses</strong>: Operational costs tracked in Expenses for budgeting and profitability.</div>
        </div>

        <!-- F -->
        <div class="term-group active" id="group-F">
          <h3>F</h3>
          <div class="term"><strong>Factor</strong>: A company purchasing receivables for immediate cash.</div>
          <div class="term"><strong>Federal Funds Rate</strong>: Interest rate for overnight loans between banks.</div>
          <div class="term"><strong>Finance Charge</strong>: Total cost of credit, including interest and fees.</div>
          <div class="term"><strong>Financial Institution</strong>: Entity providing lending and deposit services, like Finance Realm.</div>
          <div class="term"><strong>Financial Year</strong>: Defined period for accounting and reporting, configurable in System Settings.</div>
          <div class="term"><strong>Financial Year Holidays</strong>: Non-operational days affecting payment schedules, set in Global Administration.</div>
          <div class="term"><strong>Fixed Rate</strong>: Interest rate that remains constant, configurable in Loan Settings.</div>
          <div class="term"><strong>Floating Rate</strong>: Interest rate that varies with market conditions.</div>
          <div class="term"><strong>Foreclosure</strong>: Legal process to repossess collateral on defaulted loans.</div>
          <div class="term"><strong>Funds Source Config</strong>: Setup for sources of lending capital, managed in Global Administration.</div>
        </div>

        <!-- G -->
        <div class="term-group active" id="group-G">
          <h3>G</h3>
          <div class="term"><strong>Global Administration</strong>: System-wide configurations for operations, security, and settings.</div>
          <div class="term"><strong>Grace Period</strong>: Time after due date before late fees apply, set in Loan Settings.</div>
          <div class="term"><strong>Group File</strong>: Documents associated with group activities, stored in Groups.</div>
          <div class="term"><strong>Group Loan</strong>: Loan issued to a group with collective responsibility, managed in Groups.</div>
          <div class="term"><strong>Group Member Loan</strong>: Loan to an individual within a group, tracked in Groups.</div>
          <div class="term"><strong>Groups</strong>: Collections of customers for joint lending or management, managed in Groups.</div>
          <div class="term"><strong>Guarantee</strong>: Promise to repay if the primary borrower fails.</div>
          <div class="term"><strong>Guarantor</strong>: Person or entity backing a loan if the borrower defaults.</div>
        </div>

        <!-- H -->
        <div class="term-group active" id="group-H">
          <h3>H</h3>
          <div class="term"><strong>Home Equity Loan</strong>: Loan using home value as collateral.</div>
          <div class="term"><strong>Hygiene</strong>: Data cleaning processes to maintain record accuracy in Customer Information.</div>
        </div>

        <!-- I -->
        <div class="term-group active" id="group-I">
          <h3>I</h3>
          <div class="term"><strong>Interest</strong>: Cost of borrowing money, configured in Loan Settings.</div>
          <div class="term"><strong>Interest Rate Cap</strong>: Maximum rate on variable loans.</div>
          <div class="term"><strong>Internal Accounts</strong>: Institution's accounts for internal transactions, managed in Internal Accounts.</div>
          <div class="term"><strong>Investment Bank</strong>: Institution underwriting securities, relevant for large-scale lending.</div>
        </div>

        <!-- J -->
        <div class="term-group active" id="group-J">
          <h3>J</h3>
          <div class="term"><strong>Joint Account</strong>: Shared account with multiple owners, common for co-borrowers.</div>
          <div class="term"><strong>Joint Liability</strong>: Shared responsibility in group loans, tracked in Groups.</div>
          <div class="term"><strong>Jumbo Loan</strong>: Mortgage exceeding conforming loan limits.</div>
        </div>

        <!-- K -->
        <div class="term-group active" id="group-K">
          <h3>K</h3>
          <div class="term"><strong>KYC (Know Your Customer)</strong>: Verification process for borrower identity, used in Approve Customer.</div>
        </div>

        <!-- L -->
        <div class="term-group active" id="group-L">
          <h3>L</h3>
          <div class="term"><strong>Lender</strong>: Financial institution providing loans, like Finance Realm.</div>
          <div class="term"><strong>Lending Limit</strong>: Maximum exposure to a single borrower, set in Global Administration.</div>
          <div class="term"><strong>Leverage</strong>: Using borrowed funds to amplify returns.</div>
          <div class="term"><strong>Lien</strong>: Legal claim on collateral for unpaid debt.</div>
          <div class="term"><strong>Line of Credit</strong>: Revolving loan allowing repeated borrowing.</div>
          <div class="term"><strong>Liquidity</strong>: Ease of converting assets to cash, monitored in Cash Operations.</div>
          <div class="term"><strong>Loan</strong>: Sum of money lent with repayment obligation, managed in Loan Information.</div>
          <div class="term"><strong>Loan Agreement</strong>: Contract outlining terms, stored in Loan Information.</div>
          <div class="term"><strong>Loan Information</strong>: Detailed records of active and historical loans.</div>
          <div class="term"><strong>Loan Repayments</strong>: Tracking and processing borrower payments, managed in Loan Repayments.</div>
          <div class="term"><strong>Loan Settings</strong>: Configurations for loan products, rates, and terms.</div>
          <div class="term"><strong>Loan-to-Value Ratio (LTV)</strong>: Loan amount divided by asset value, used in loan approval.</div>
          <div class="term"><strong>Long-Term Loan</strong>: Loan with repayment over several years.</div>
        </div>

        <!-- M -->
        <div class="term-group active" id="group-M">
          <h3>M</h3>
          <div class="term"><strong>Margin Call</strong>: Demand for additional collateral when value drops.</div>
          <div class="term"><strong>Maturity</strong>: Loan end date when principal is due.</div>
          <div class="term"><strong>Mortgage</strong>: Secured loan using property as collateral.</div>
          <div class="term"><strong>My Account</strong>: Personal user profile and settings, managed in My Account.</div>
        </div>

        <!-- N -->
        <div class="term-group active" id="group-N">
          <h3>N</h3>
          <div class="term"><strong>Net Interest Margin</strong>: Difference between interest earned and paid, analyzed in Reports.</div>
          <div class="term"><strong>Non-Performing Loan (NPL)</strong>: Loan overdue by 90+ days, tracked in Loan Repayments.</div>
        </div>

        <!-- O -->
        <div class="term-group active" id="group-O">
          <h3>O</h3>
          <div class="term"><strong>Origination</strong>: Process of issuing a new loan, managed in Approve Loan.</div>
          <div class="term"><strong>Origination Fee</strong>: Charge for processing a loan.</div>
          <div class="term"><strong>Overdraft</strong>: Borrowing when account balance is negative, tracked in Internal Accounts.</div>
        </div>

        <!-- P -->
        <div class="term-group active" id="group-P">
          <h3>P</h3>
          <div class="term"><strong>PAR Report</strong>: Portfolio at Risk analysis for overdue loans, generated in Reports.</div>
          <div class="term"><strong>Payday Loan</strong>: Short-term, high-interest loan until next paycheck.</div>
          <div class="term"><strong>Personal Loan</strong>: Unsecured loan for individual use.</div>
          <div class="term"><strong>Portfolio</strong>: Collection of loans held, analyzed in Reports.</div>
          <div class="term"><strong>Prepayment Penalty</strong>: Fee for early loan repayment, set in Loan Settings.</div>
          <div class="term"><strong>Prime Rate</strong>: Interest rate for best borrowers.</div>
          <div class="term"><strong>Principal</strong>: Original loan amount, excluding interest.</div>
          <div class="term"><strong>Private Equity</strong>: Investments in private companies, sometimes for lending.</div>
        </div>

        <!-- Q -->
        <div class="term-group active" id="group-Q">
          <h3>Q</h3>
          <div class="term"><strong>Qualifying Ratio</strong>: Borrower's income-to-debt ratio for loan approval.</div>
          <div class="term"><strong>Quarterly Report</strong>: Financial summary for a three-month period, generated in Reports.</div>
        </div>

        <!-- R -->
        <div class="term-group active" id="group-R">
          <h3>R</h3>
          <div class="term"><strong>Rate Lock</strong>: Agreement fixing interest rate for a period.</div>
          <div class="term"><strong>Refinance</strong>: Replacing existing loan with new terms.</div>
          <div class="term"><strong>Repayment Plan</strong>: Schedule for loan installments, tracked in Loan Repayments.</div>
          <div class="term"><strong>Reports</strong>: Generated summaries of system data for analysis, managed in Reports.</div>
          <div class="term"><strong>Restructure Loan</strong>: Modifying terms of an existing loan, logged in Loan Information.</div>
          <div class="term"><strong>Revolving Credit</strong>: Credit line that replenishes after payments.</div>
          <div class="term"><strong>Risk Recoveries Report</strong>: Tracking recoveries from high-risk loans, generated in Reports.</div>
        </div>

        <!-- S -->
        <div class="term-group active" id="group-S">
          <h3>S</h3>
          <div class="term"><strong>Savings Account</strong>: Deposit account for customer savings with interest, managed in Savings Account.</div>
          <div class="term"><strong>Secured Loan</strong>: Loan backed by collateral, configured in Loan Settings.</div>
          <div class="term"><strong>Signature Loan</strong>: Unsecured loan based on borrower's signature.</div>
          <div class="term"><strong>SMS Sending Config</strong>: Setup for notification delivery via SMS, managed in Global Administration.</div>
          <div class="term"><strong>Subprime Loan</strong>: Loan to borrowers with poor credit.</div>
          <div class="term"><strong>Surety</strong>: Guarantee by a third party for loan repayment.</div>
          <div class="term"><strong>System Settings</strong>: Global configurations for the platform, managed in Global Administration.</div>
        </div>

        <!-- T -->
        <div class="term-group active" id="group-T">
          <h3>T</h3>
          <div class="term"><strong>Term Loan</strong>: Loan with fixed repayment schedule, configured in Loan Settings.</div>
          <div class="term"><strong>Title Loan</strong>: Secured loan using vehicle title as collateral.</div>
          <div class="term"><strong>Track Loan</strong>: Monitoring loan performance and repayments, managed in Loan Repayments.</div>
          <div class="term"><strong>Treasury Bill</strong>: Short-term government debt instrument.</div>
        </div>

        <!-- U -->
        <div class="term-group active" id="group-U">
          <h3>U</h3>
          <div class="term"><strong>Underwriting</strong>: Process assessing borrower risk, part of Approve Loan.</div>
          <div class="term"><strong>Unsecured Loan</strong>: Loan without collateral, higher rates.</div>
          <div class="term"><strong>User Roles</strong>: Defined permissions for system access, managed in Employee & User Mgt.</div>
        </div>

        <!-- V -->
        <div class="term-group active" id="group-V">
          <h3>V</h3>
          <div class="term"><strong>Variable Rate</strong>: Interest rate that fluctuates with market, set in Loan Settings.</div>
          <div class="term"><strong>Vendor Finance</strong>: Loan from seller for purchase.</div>
        </div>

        <!-- W -->
        <div class="term-group active" id="group-W">
          <h3>W</h3>
          <div class="term"><strong>Warehouse Line</strong>: Short-term funding for mortgage lenders.</div>
          <div class="term"><strong>Write Off Loan</strong>: Declaring a loan uncollectible, logged in Loan Information.</div>
        </div>

        <!-- X -->
        <div class="term-group active" id="group-X">
          <h3>X</h3>
          <div class="term"><strong>Xerox Fee</strong>: Charge for copying loan documents, rare in digital systems.</div>
        </div>

        <!-- Y -->
        <div class="term-group active" id="group-Y">
          <h3>Y</h3>
          <div class="term"><strong>Yield</strong>: Return on investment from loan interest, analyzed in Reports.</div>
        </div>

        <!-- Z -->
        <div class="term-group active" id="group-Z">
          <h3>Z</h3>
          <div class="term"><strong>Zero Percent Financing</strong>: Promotional loan with no interest, often for short terms.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php require_once('includes/footer.php'); ?>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<!-- Active Menu Highlight Script -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const currentPage = location.pathname.split('/').pop().split('?')[0] || 'index.php';
    document.querySelectorAll('.main-sidebar .nav-link').forEach(link => {
      const linkHref = link.getAttribute('href').split('?')[0];
      if (linkHref === currentPage) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  });
</script>

<!-- Alphabet Filter Script -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.alphabet-filter button');
    const groups = document.querySelectorAll('.term-group');

    // Check if letter has content
    function hasContent(letter) {
      const group = document.getElementById('group-' + letter);
      if (!group) return false;
      const terms = group.querySelectorAll('.term');
      return Array.from(terms).some(term => !term.textContent.includes('No terms defined yet'));
    }

    // Disable buttons for empty letters
    buttons.forEach(btn => {
      const letter = btn.getAttribute('data-letter').toUpperCase();
      if (letter !== 'ALL' && !hasContent(letter)) {
        btn.disabled = true;
      }
    });

    buttons.forEach(btn => {
      btn.addEventListener('click', function () {
        const letter = this.getAttribute('data-letter');

        groups.forEach(group => {
          if (letter === 'all') {
            group.classList.add('active');
          } else {
            const groupLetter = group.id.split('-')[1];
            if (groupLetter === letter) {
              group.classList.add('active');
            } else {
              group.classList.remove('active');
            }
          }
        });

        // Scroll to glossary
        document.querySelector('.section h2 ~ p').scrollIntoView({ behavior: 'smooth' });
      });
    });

    // Show all by default
    groups.forEach(group => group.classList.add('active'));
  });
</script>

</body>
</html>