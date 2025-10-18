<?php
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Customer Information Management - Finance Realm System Admin Dashboard">
  <meta name="author" content="Finance Realm Team">
  <title>Finance Realm System | Customer Management</title>

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
            <h1 class="m-0 text-dark">Customer Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item active">Customer Management</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
      <div class="welcome-header">
        <h3>Manage Customer Information</h3>
        <p class="mb-0">Comprehensive customer management system for viewing, adding, editing, and approving customers. Access to these functions is restricted to users who have been assigned the appropriate roles and permissions to perform them.</p>
      </div>

      <!-- Customer Information Section -->
      <div class="section">
        <h2>Customer Information</h2>
        <p>This section provides a complete overview of all customer records in the system. Admins can search, filter, and access detailed profiles containing personal, contact, address, and financial information while performing actions like editing or archiving for data integrity and compliance.</p>
        <img src="screenshots/customer_information.jpeg" alt="Customer Information Screenshot" class="screenshot">
        <p>This section below displays all customer records in the system. after you click on  View from above Screenshot, you can access the full KYC details for the selected customer, including personal, contact, address, and financial information. Admins can also perform actions such as exporting or archiving records to maintain data accuracy and compliance.</p>
        <img src="screenshots/view_customer_information.jpeg" alt="Customer Information Screenshot" class="screenshot">
      </div>

      <!-- Add Customer Section -->
      <div class="section">
        <h2>Add Customer</h2>
        <p>This feature enables the creation of new individual customer profiles. Enter essential details including title, name, gender, marital status, date of birth, email, phone, addresses, geographic information, residential status, profession, income sources, monthly earnings, notes, and branch assignment to ensure accurate onboarding and KYC compliance.</p>
        <img src="screenshots/add_customer.jpeg" alt="Add Customer Screenshot" class="screenshot">
      </div>

      <!-- My Customers Section -->
      <div class="section">
        <h2>My Customers</h2>
        <p>This personalized view displays all users or customers added to the system, showing key details such as Client ID, Title, First Name, Middle Name, Last Name, Gender, Date of Birth, Status, and Created On for efficient management and reference.</p>
        <img src="screenshots/my_customers.jpeg" alt="My Customers Screenshot" class="screenshot">
      </div>

      <!-- Approve Customer Section -->
      <div class="section">
        <h2>Approve Customer</h2>
        <p>This workflow allows review of new customer applications. Check submission dates, statuses, and supporting documents, then approve or reject with reasons to maintain security standards and regulatory adherence in the onboarding process.</p>
        <img src="screenshots/approve_customer.jpeg" alt="Approve Customer Screenshot" class="screenshot">
      </div>

      <!-- Recommend Customer Section -->
      <div class="section">
        <h2>Recommend Customer</h2>
        <p>This tool supports targeted service recommendations based on customer profiles. Add or update suggestions for products and services to enhance cross-selling opportunities and personalize financial advice directly from the dashboard.</p>
        <img src="screenshots/recommend_customer.jpeg" alt="Recommend Customer Screenshot" class="screenshot">
      </div>

      <!-- Approved Customers Section -->
      <div class="section">
        <h2>Approved Customers</h2>
        <p>This archive displays all successfully onboarded customers. View approval dates and summaries, with links to detailed records for ongoing monitoring, updates, and analytics.</p>
        <img src="screenshots/approved_customers.jpeg" alt="Approved Customers Screenshot" class="screenshot">
      </div>

      <!-- Edit Customer Section -->
      <div class="section">
        <h2>Edit Customer</h2>
        <p>This form-based editor loads existing customer data for modifications. Update any field securely, with validation checks to preserve data accuracy and trigger audit logs for changes in personal or financial details.</p>
        <img src="screenshots/edit_customer.jpeg" alt="Edit Customer Screenshot" class="screenshot">
      </div>

      <!-- Edit Customer KYC Section -->
      <div class="section">
        <h2>Edit Customer KYC</h2>
        <p>This specialized update section handles Know Your Customer compliance. Upload new documents, revise verification details, and manage status flags to meet anti-money laundering requirements and refresh risk assessments.</p>
        <img src="screenshots/edit_kyc.jpeg" alt="Edit KYC Screenshot" class="screenshot">
      </div>

      <!-- Rejected Customers Section -->
      <div class="section">
        <h2>Rejected Customers</h2>
        <p>This list tracks denied applications with rejection rationales. Review for potential appeals, analyze patterns for process improvements, and re-initiate onboarding if criteria are met post-correction.</p>
        <img src="screenshots/rejected_customers.jpeg" alt="Rejected Customers Screenshot" class="screenshot">
      </div>

      <!-- Archive/Blacklist Customer Section -->
      <div class="section">
        <h2>Archive/Blacklist Customer</h2>
        <p>This management area handles inactive or flagged customers. View reasons for archiving or blacklisting, access historical data, and perform actions like reinstatement or permanent removal while complying with data retention policies.</p>
        <img src="screenshots/blacklist_customers.jpeg" alt="Blacklist Customers Screenshot" class="screenshot">
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