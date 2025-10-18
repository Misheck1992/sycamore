<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Customer Logins Management - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Customer Logins</title>

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
            <h1 class="m-0 text-dark">Customer Logins</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Customer Logins</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Manage Customer Logins</h3>
        <p class="mb-0">Create, track, approve, and review customer access requests securely.</p>
      </div>

      <!-- Create/Track Section -->
      <div class="section">
        <h2>Create/Track</h2>
        <p>This section allows admins to create new customer logins and track activities via a grid displaying columns like  Customer Id (unique identifier), Phone Number (for verification), Created By (initiator), Approved By/Denied By, Status, Stamp, and Action (buttons for operations). It ensures secure onboarding and auditability.</p>
        <img src="screenshots/customer-create-track.jpeg" alt="Create/Track Screenshot" class="screenshot">
      </div>

      <!-- Approve Customer access Section -->
      <div class="section">
        <h2>Approve Customer access</h2>
        <p>Review pending requests in a grid with columns including No, Customer Id, Phone Number, Created By, Approved By, Denied By, Status, Stamp, and Action (e.g., Approve/Reject). This enforces compliance, verifies details, and updates status automatically upon decision.</p>
        <img src="screenshots/customer-approve.jpeg" alt="Approve Screenshot" class="screenshot">
      </div>

      <!-- Approved list Section -->
      <div class="section">
        <h2>Approved list</h2>
        <p>View granted accesses in a grid listing No, Customer Id, Phone Number, Created By, Approved By, Denied By, Status (Approved), Stamp, and Action. Supports monitoring, exports, and security audits for active accounts.</p>
        <img src="screenshots/customer-approved-list.jpeg" alt="Approved List Screenshot" class="screenshot">
      </div>

      <!-- Rejected list Section -->
      <div class="section">
        <h2>Rejected list</h2>
        <p>Archive denied requests using a grid with columns No, Customer Id, Phone Number, Created By, Approved By, Denied By, Status (Denied), Stamp, and Action . Aids in appeals, reason analysis, and process improvements.</p>
        <img src="screenshots/customer-rejected-list.jpeg" alt="Rejected List Screenshot" class="screenshot">
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