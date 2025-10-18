<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Home - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Home</title>

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
      margin-bottom: 1rem;
    }

    .screenshot {
      max-width: 100%;
      height: auto;
      margin: 1rem 0;
      border: 1px solid #dee2e6;
      border-radius: 4px;
    }

    .quick-links-list {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
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
            <h1 class="m-0 text-dark">Home</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Home</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Welcome to Finance Realm System Documentation</h3>
        <p class="mb-0">Your centralized hub for managing financial operations efficiently.</p>
      </div>

      <!-- Documentation Purpose Section -->
      <div class="section">
        <h2><i class="fas fa-book"></i> Purpose of This Documentation</h2>
        <p>This documentation serves as a comprehensive guide to the Finance Realm System Admin Dashboard, detailing each interface, feature, and workflow. It is designed for administrators and Employees to understand system capabilities, facilitate onboarding, ensure consistent usage, and support maintenance or enhancements. Screenshots and descriptions provide visual references for professional training, auditing, and reference purposes.</p>
      </div>

      <!-- Login Interface Section -->
      <div class="section">
        <h2><i class="fas fa-sign-in-alt"></i> Login Interface</h2>
        <p>The initial access point to the system is the secure login interface, where users enter credentials (username and password) to authenticate. It features input validation, password masking, and error handling for invalid attempts, ensuring compliance with security standards. Upon successful login, users are redirected to the dashboard home.</p>
        <img src="screenshots/login_interface.jpeg" alt="Login Interface Screenshot" class="screenshot">
        <p><strong>Session Security Note:</strong> For enhanced protection, the system implements an automatic logout after 60 seconds of inactivity. This mitigates risks from unattended sessions, prompting re-authentication to maintain data confidentiality and integrity in shared or public environments.</p>
      </div>

      <!-- Quick Links Section -->
      <div class="section">
        <h2><i class="fas fa-link"></i> Quick Links</h2>
        <p>Access key areas of the system directly from here for streamlined navigation and productivity:</p>
        <div class="quick-links-list">
          <a href="index.php" class="btn btn-primary btn-sm">Home</a>
          <a href="customer_management.php" class="btn btn-info btn-sm">Customer Information</a>
          <a href="customer-logins.php" class="btn btn-success btn-sm">Customer Logins</a>
          <a href="enquiries.php" class="btn btn-warning btn-sm">Enquiries</a>
          <a href="groups.php" class="btn btn-danger btn-sm">Groups</a>
          <a href="loan-settings.php" class="btn btn-secondary btn-sm">Loan Settings</a>
          <a href="loan_information.php" class="btn btn-dark btn-sm">Loan Information</a>
          <a href="collection_sheet.php" class="btn btn-primary btn-sm">Collection Sheet</a>
          <a href="loan_repayments.php" class="btn btn-info btn-sm">Loan Repayments</a>
          <a href="cost-of-funding.php" class="btn btn-success btn-sm">Cost of Funding</a>
          <a href="savings-account.php" class="btn btn-warning btn-sm">Savings Account</a>
          <a href="internal-accounts.php" class="btn btn-danger btn-sm">Internal Accounts</a>
          <a href="cash-operations.php" class="btn btn-secondary btn-sm">Cash Operations</a>
          <a href="reports.php" class="btn btn-dark btn-sm">Reports</a>
          <a href="expenses.php" class="btn btn-primary btn-sm">Expenses</a>
          <a href="employee-user-mgt.php" class="btn btn-info btn-sm">Employee & User Mgt</a>
          <a href="global_administration.php" class="btn btn-success btn-sm">Global Administration</a>
          <a href="profile.php" class="btn btn-warning btn-sm">My Account</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('includes/footer.php'); ?>
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