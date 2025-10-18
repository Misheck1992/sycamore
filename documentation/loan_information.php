<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Loan Management - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Loans</title>

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
            <h1 class="m-0 text-dark">Loans</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Loans</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Manage Loans</h3>
        <p class="mb-0">Handle loan creation, approval, disbursement, tracking, and configurations across personal, group, and member levels.</p>
      </div>


      <!-- Add Personal Loan Section -->
      <div class="section">
        <h2>Add Personal Loan</h2>
        <p>This feature initiates new personal loans by entering borrower details, amount, terms, and collateral. It integrates with customer profiles to ensure eligibility checks and compliance before submission.</p>
        <img src="screenshots/add_personal_loan.jpeg" alt="Add Personal Loan Screenshot" class="screenshot">
      </div>

      <!-- Add Group Loan Section -->
      <div class="section">
        <h2>Add Group Loan</h2>
        <p>This tool creates loans for customer groups, specifying shared terms, member contributions, and collective responsibility. It promotes microfinance models with built-in risk distribution assessments.</p>
        <img src="screenshots/add_group_loan.jpeg" alt="Add Group Loan Screenshot" class="screenshot">
      </div>

      <!-- Add Group Member Loan Section -->
      <div class="section">
        <h2>Add Group Member Loan</h2>
        <p>This allows adding loans to individual members within a group, linking to group guarantees while tracking personal repayments. It maintains group oversight with separate member accountability.</p>
        <img src="screenshots/add_group_member_loan.jpeg" alt="Add Group Member Loan Screenshot" class="screenshot">
      </div>

      <!-- Approve Loan Section -->
      <div class="section">
        <h2>Approve Loan</h2>
        <p>This workflow reviews pending loan applications, verifying documents and credit scores before approval. It enforces policies with options to add conditions or reject with reasons for transparency.</p>
        <img src="screenshots/approve_loan.jpeg" alt="Approve Loan Screenshot" class="screenshot">
      </div>

      <!-- Disburse Loan Section -->
      <div class="section">
        <h2>Disburse Loan</h2>
        <p>This step releases approved funds to borrowers. It includes confirmation checks and generates disbursement receipts for accounting.</p>
        <img src="screenshots/disburse_loan.jpeg" alt="Disburse Loan Screenshot" class="screenshot">
      </div>

      <!-- Recommend Loan Section -->
      <div class="section">
        <h2>Recommend Loan</h2>
        <p>This suggests suitable loan products to customers based on profiles and needs. It facilitates upselling with customizable recommendations tied to eligibility previews.</p>
        <img src="screenshots/recommend_loan.jpeg" alt="Recommend Loan Screenshot" class="screenshot">
      </div>

      <!-- Track Loan Section -->
      <div class="section">
        <h2>Track Loan</h2>
        <p>This monitors loan progress, repayments, and delinquencies in real-time with dashboards. It supports proactive collections and performance analytics for portfolio management.</p>
        <img src="screenshots/track_loan.jpeg" alt="Track Loan Screenshot" class="screenshot">
      </div>

      <!-- Group File Section -->
      <div class="section">
        <h2>Group File</h2>
        <p>This displays all information about the groups selected for compliance audits.</p>
        <img src="screenshots/group_file.jpeg" alt="Group File Screenshot" class="screenshot">
      </div>

      <!-- Write Off Loan Section -->
      <div class="section">
        <h2>Write Off Loan</h2>
        <p>This initiates write-offs for irrecoverable loans after due diligence, adjusting accounts accordingly. It requires justification and updates financial reports to reflect bad debts.</p>
        <img src="screenshots/write_off_loan.jpeg" alt="Write Off Loan Screenshot" class="screenshot">
      </div>

      <!-- Approve Loan Write-off Section -->
      <div class="section">
        <h2>Approve Loan Write-off</h2>
        <p>This reviews and authorizes write-off requests, ensuring policy adherence and documentation. It finalizes adjustments with audit trails for regulatory reporting.</p>
        <img src="screenshots/approve_loan_writeoff.jpeg" alt="Approve Loan Write-off Screenshot" class="screenshot">
      </div>

      <!-- Restructure Loan Section -->
      <div class="section">
        <h2>Restructure Loan</h2>
        <p>This modifies troubled loans by adjusting terms like extensions or rate reductions to aid recovery. It evaluates impacts on interest and requires approval workflows.</p>
        <img src="screenshots/restructure_loan.jpeg" alt="Restructure Loan Screenshot" class="screenshot">
      </div>

      <!-- Recommend Loan Edit Section -->
      <div class="section">
        <h2>Recommend Loan Edit</h2>
        <p>This updates existing loan recommendations with new options or details based on changes. It keeps suggestions relevant and aligned with customer updates.</p>
        <img src="screenshots/recommend_loan_edit.jpeg" alt="Recommend Loan Edit Screenshot" class="screenshot">
      </div>

      <!-- Approve Loan Edit Section -->
      <div class="section">
        <h2>Approve Loan Edit</h2>
        <p>This authorizes edits to active loans, reviewing proposed changes for risk and compliance. It ensures modifications are documented and communicated to borrowers.</p>
        <img src="screenshots/approve_loan_edit.jpeg" alt="Approve Loan Edit Screenshot" class="screenshot">
      </div>

      <!-- Recommend Loan Delete Section -->
      <div class="section">
        <h2>Recommend Loan Delete</h2>
        <p>This proposes removal of outdated or erroneous loan recommendations from records. It prevents confusion while maintaining history logs for reference.</p>
        <img src="screenshots/recommend_loan_delete.jpeg" alt="Recommend Loan Delete Screenshot" class="screenshot">
      </div>

      <!-- Approve Loan Delete Section -->
      <div class="section">
        <h2>Approve Loan Delete</h2>
        <p>This confirms and executes deletions of loans or recommendations after verification. It safeguards data integrity with irreversible actions and notifications.</p>
        <img src="screenshots/approve_loan_delete.jpeg" alt="Approve Loan Delete Screenshot" class="screenshot">
      </div>

      <!-- Funds Source Config Section -->
      <div class="section">
        <h2>Funds Source Config</h2>
        <p>This configures sources of funding for loans, such as banks or investors, with allocation rules. It optimizes capital management and tracks usage for financial planning.</p>
        <img src="screenshots/funds_source_config.jpeg" alt="Funds Source Config Screenshot" class="screenshot">
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