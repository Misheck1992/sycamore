<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Global Administration - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Global Administration</title>

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
            <h1 class="m-0 text-dark">Global Administration</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Global Administration</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Global System Settings</h3>
        <p class="mb-0">Configure system-wide settings, charges, and administrative functions for seamless operations.</p>
      </div>

      <!-- SMS Sending Config Section -->
      <div class="section">
        <h2>SMS Sending Config</h2>
        <p>This configures SMS providers, templates, and schedules for notifications. It ensures reliable customer communication for alerts, reminders, and updates across the platform.</p>
        <img src="screenshots/sms_sending_config.jpeg" alt="SMS Sending Config Screenshot" class="screenshot">
      </div>

      <!-- Charges Section -->
      <div class="section">
        <h2>Charges</h2>
        <p>This manages fees like processing or penalties applied to loans and accounts. It allows creation, edits, and removals to align with policies and revenue models.</p>
        <img src="screenshots/charges.jpeg" alt="Charges Screenshot" class="screenshot">
      </div>

      <!-- Manage Account Types Section -->
      <div class="section">
        <h2>Manage Account Types</h2>
        <p>This defines account variants with features, eligibility, and fees. It supports product diversification and customization for different customer needs.</p>
        <img src="screenshots/manage_account_types.jpeg" alt="Manage Account Types Screenshot" class="screenshot">
      </div>

      <!-- Financial Year Section -->
      <div class="section">
        <h2>Financial Year</h2>
        <p>This sets fiscal start/end dates for reporting alignment. It automates period-based calculations and ensures compliance with organizational calendars.</p>
        <img src="screenshots/financial_year.jpeg" alt="Financial Year Screenshot" class="screenshot">
      </div>

      <!-- Branch Management Section -->
      <div class="section">
        <h2>Branch Management</h2>
        <p>This oversees branch details like locations and contacts. It centralizes multi-branch operations for consistent management and performance tracking.</p>
        <img src="screenshots/branch_management.jpeg" alt="Branch Management Screenshot" class="screenshot">
      </div>

      <!-- Financial Year Holidays Section -->
      <div class="section">
        <h2>Financial Year Holidays</h2>
        <p>This defines non-operational dates affecting schedules. It adjusts automations for payments and reporting to maintain accuracy during closures.</p>
        <img src="screenshots/financial_year_holidays.jpeg" alt="Financial Year Holidays Screenshot" class="screenshot">
      </div>

      <!-- System Settings Section -->
      <div class="section">
        <h2>System Settings</h2>
        <p>This customizes global preferences like currency and language. It tailors the platform to regional and operational requirements for user efficiency.</p>
        <img src="screenshots/system_settings.jpeg" alt="System Settings Screenshot" class="screenshot">
      </div>

      <!-- Backups Section -->
      <div class="section">
        <h2>Backups</h2>
        <p>This schedules and manages data backups with restoration options. It safeguards information integrity and enables quick recovery from incidents.</p>
        <img src="screenshots/backups.jpeg" alt="Backups Screenshot" class="screenshot">
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