<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Groups Management - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Groups</title>

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
            <h1 class="m-0 text-dark">Groups</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Groups</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Manage Groups</h3>
        <p class="mb-0">Organize, create, and track customer groups within the system.</p>
      </div>

      <!-- All Groups Section -->
      <div class="section">
        <h2>All Groups</h2>
        <p>This section displays a comprehensive list of customer groups with details like name, member count, and status. It features tabs for filtering by Pending Approval, Approved/Active, and Rejected groups, enabling quick access to profiles and actions such as editing or deletion for efficient administration.</p>
        <img src="screenshots/all_groups.jpeg" alt="All Groups Screenshot" class="screenshot">
      </div>

      <!-- Create Group Section -->
      <div class="section">
        <h2>Create Group</h2>
        <p>This feature facilitates the formation of new customer groups by inputting name, description, and initial members. It streamlines organization for shared services, loans, or monitoring while ensuring compliance through structured data entry.</p>
        <img src="screenshots/create_group.jpeg" alt="Create Group Screenshot" class="screenshot">
      </div>

      <!-- Assign Group Member/Edit Group Section -->
      <div class="section">
        <h2>Assign Group Member/Edit Group</h2>
        <p>This tool allows dynamic management of group membership by adding or removing individuals and updating details like name or description. It supports ongoing adjustments to maintain accurate group structures and operational efficiency.</p>
        <img src="screenshots/assign_edit_group.jpeg" alt="Assign Group Member/Edit Group Screenshot" class="screenshot">
      </div>

      <!-- Approve Groups Section -->
      <div class="section">
        <h2>Approve Groups</h2>
        <p>This workflow reviews new or updated groups for approval, showing details and status. Admins can verify criteria and approve or reject to enforce policies and security in group formations.</p>
        <img src="screenshots/approve_groups.jpeg" alt="Approve Groups Screenshot" class="screenshot">
      </div>

      <!-- Group Track/Export Section -->
      <div class="section">
        <h2>Group Track/Export</h2>
        <p>This area monitors group activities, performance metrics, and status changes while enabling data exports in multiple formats. It aids in analytics, reporting, and informed decision-making for group-based strategies.</p>
        <img src="screenshots/group_track_export.jpeg" alt="Group Track/Export Screenshot" class="screenshot">
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