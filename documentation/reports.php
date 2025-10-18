<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Reports Management - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Reports</title>

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
    }

    .section h2 {
      color: #28a745;
      margin-bottom: 1rem;
    }

    .section p {
      color: #6c757d;
      line-height: 1.6;
      margin-bottom: 1rem;
    }

    .screenshot {
      max-width: 100%;
      height: auto;
      margin: 1rem 0;
      border: 1px solid #dee2e6;
      border-radius: 4px;
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
            <h1 class="m-0 text-dark">Reports</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Generate Reports</h3>
        <p class="mb-0">Access detailed financial and operational reports for analysis, compliance, and decision-making.</p>
      </div>

      <!-- Revenue Analysis Section -->
      <div class="section">
        <h2>Revenue Analysis</h2>
        <p>This report breaks down revenue from loans by type, period, and segments to evaluate profitability. It supports strategic planning with visual charts and export options for stakeholders.</p>
        <img src="screenshots/revenue_analysis.jpeg" alt="Revenue Analysis Screenshot" class="screenshot">
      </div>

      <!-- Payments Report Section -->
      <div class="section">
        <h2>Payments Report</h2>
        <p>This generates summaries of received payments with filters for periods and types. It aids in reconciliation and tracking cash flow for operational efficiency.</p>
        <img src="screenshots/payments_report.jpeg" alt="Payments Report Screenshot" class="screenshot">
      </div>

      <!-- Company Revenue Section -->
      <div class="section">
        <h2>Company Revenue</h2>
        <p>This aggregates overall income from loans, fees, and sources by period or department. It provides insights into financial health for management reviews.</p>
        <img src="screenshots/company_revenue.jpeg" alt="Company Revenue Screenshot" class="screenshot">
      </div>

      <!-- PAR Report Section -->
      <div class="section">
        <h2>PAR Report</h2>
        <p>This assesses portfolio risk by overdue categories for loans. It helps in monitoring delinquency and informing risk mitigation strategies.</p>
        <img src="screenshots/par_report.jpeg" alt="PAR Report Screenshot" class="screenshot">
      </div>

      <!-- Arrears Report Section -->
      <div class="section">
        <h2>Arrears Report</h2>
        <p>This details overdue loans with amounts, durations, and borrower info. It facilitates targeted collections and reduces bad debt exposure.</p>
        <img src="screenshots/arrears_report.jpeg" alt="Arrears Report Screenshot" class="screenshot">
      </div>

      <!-- Loan Book Report Section -->
      <div class="section">
        <h2>Loan Book Report</h2>
        <p>This overviews the active loan portfolio with balances and metrics. It supports portfolio management and performance evaluations.</p>
        <img src="screenshots/loan_book_report.jpeg" alt="Loan Book Report Screenshot" class="screenshot">
      </div>

      <!-- Period Analysis Section -->
      <div class="section">
        <h2>Period Analysis</h2>
        <p>This compares disbursements, repayments, and revenue across timeframes. It identifies trends for forecasting and operational adjustments.</p>
        <img src="screenshots/period_analysis.jpeg" alt="Period Analysis Screenshot" class="screenshot">
      </div>

      <!-- Collection Rate Report Section -->
      <div class="section">
        <h2>Collection Rate Report</h2>
        <p>This evaluates collection efficiency against expected payments. It highlights recovery performance for process improvements.</p>
        <img src="screenshots/collection_rate_report.jpeg" alt="Collection Rate Report Screenshot" class="screenshot">
      </div>

      <!-- Loan Outstanding Balance Section -->
      <div class="section">
        <h2>Loan Outstanding Balance</h2>
        <p>This lists total and individual outstanding balances with schedules. It ensures accurate tracking of liabilities and repayment progress.</p>
        <img src="screenshots/loan_outstanding_balance.jpeg" alt="Loan Outstanding Balance Screenshot" class="screenshot">
      </div>

      <!-- Regulator Loan Classification Section -->
      <div class="section">
        <h2>Regulator Loan Classification</h2>
        <p>This classifies loans per regulatory standards for compliance reporting. It categorizes assets to meet auditing and governance requirements.</p>
        <img src="screenshots/regulator_loan_classification.jpeg" alt="Regulator Loan Classification Screenshot" class="screenshot">
      </div>

      <!-- Written Off Loans Section -->
      <div class="section">
        <h2>Written Off Loans</h2>
        <p>This tracks irrecoverable loans with reasons and dates. It maintains records for financial adjustments and lessons learned.</p>
        <img src="screenshots/written_off_loans.jpeg" alt="Written Off Loans Screenshot" class="screenshot">
      </div>

      <!-- Generated Reports Section -->
      <div class="section">
        <h2>Generated Reports</h2>
        <p>This archives past reports for access, download, or regeneration. It centralizes historical data for audits and references.</p>
        <img src="screenshots/generated_reports.jpeg" alt="Generated Reports Screenshot" class="screenshot">
      </div>

      <!-- Risk Recoveries Report Section -->
      <div class="section">
        <h2>Risk Recoveries Report</h2>
        <p>This analyzes recoveries from delinquent loans with methods and amounts. It measures effectiveness of recovery efforts on high-risk accounts.</p>
        <img src="screenshots/risk_recoveries_report.jpeg" alt="Risk Recoveries Report Screenshot" class="screenshot">
      </div>

      <!-- Upcoming Installments Section -->
      <div class="section">
        <h2>Upcoming Installments</h2>
        <p>This schedules future dues with borrower details and amounts. It plans collections and sends reminders proactively.</p>
        <img src="screenshots/upcoming_installments.jpeg" alt="Upcoming Installments Screenshot" class="screenshot">
      </div>

      <!-- Dashboard Statistics Section -->
      <div class="section">
        <h2>dashboard Statistics</h2>
        <p>This summarizes key metrics like loans, repayments, and rates. It delivers quick insights for daily monitoring and decisions.</p>
        <img src="screenshots/dashboard_statistics.jpeg" alt="Dashboard Statistics Screenshot" class="screenshot">
      </div>

      <!-- CRB Report Section -->
      <div class="section">
        <h2>CRB Report</h2>
        <p>This prepares credit bureau submissions on performance and compliance. It ensures regulatory sharing of borrower data accurately.</p>
        <img src="screenshots/crb_report.jpeg" alt="CRB Report Screenshot" class="screenshot">
      </div>
    </div>
  </div>
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

</body>
</html>