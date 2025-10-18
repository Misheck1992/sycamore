<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="enhack">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Employee & User Management - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Employee & User Management</title>

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
        margin-left: 0 !important  ;
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
            <h1 class="m-0 text-dark">Employee & User Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Employee & User Management</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Manage Employees and Users</h3>
        <p class="mb-0">Oversee employee records, user configurations, roles, privileges, and audit trails for secure access control.</p>
      </div>

      <!-- Employees Section -->
      <div class="section">
        <h2>Employees</h2>
        <p>This section maintains employee profiles with personal, contact, and role details. It supports HR functions like onboarding, updates, and performance tracking within the organization.</p>
        <img src="screenshots/employees.jpeg" alt="Employees Screenshot" class="screenshot">
      </div>

      <!-- Configure System Users Section -->
      <div class="section">
        <h2>Configure System Users</h2>
        <p>This creates and edits user accounts linked to employees, setting credentials and statuses. It ensures proper authentication and integration with system modules for operational access.</p>
        <img src="screenshots/configure_system_users.jpeg" alt="Configure System Users Screenshot" class="screenshot">
      </div>

      <!-- Approve System User Config Section -->
      <div class="section">
        <h2>Approve System User Config</h2>
        <p>This reviews and authorizes new or modified user setups for compliance. It enforces security protocols before activation to prevent unauthorized access.</p>
        <img src="screenshots/approve_system_user_config.jpeg" alt="Approve System User Config Screenshot" class="screenshot">
      </div>

      <!-- Configure User Roles Section -->
      <div class="section">
        <h2>Configure User Roles</h2>
        <p>This defines roles with bundled permissions for tasks and modules. It streamlines access management and supports scalability in team structures.</p>
        <img src="screenshots/configure_user_roles.jpeg" alt="Configure User Roles Screenshot" class="screenshot">
      </div>

      <!-- System Access Privileges Section -->
      <div class="section">
        <h2>System Access Privileges</h2>
        <p>This assigns granular privileges to roles or users for data and functions. It enhances security by adhering to least-privilege principles and audit requirements.</p>
        <img src="screenshots/system_access_privileges.jpeg" alt="System Access Privileges Screenshot" class="screenshot">
      </div>

      <!-- Audit Trail Section -->
      <div class="section">
        <h2>Audit Trail</h2>
        <p>This logs all user actions, changes, and accesses with timestamps and details. It facilitates compliance reviews, investigations, and system integrity monitoring.</p>
        <img src="screenshots/audit_trail.jpeg" alt="Audit Trail Screenshot" class="screenshot">
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