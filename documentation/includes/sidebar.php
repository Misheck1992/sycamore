<aside class="main-sidebar sidebar-light-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link text-center">
    <img src="img/logo.png" alt="Finance Realm Logo" class="img-fluid" style="max-width: 40%; height: auto;">
  </a>

  <div class="sidebar os-host os-theme-dark os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition">
    <div class="os-padding">
      <div class="os-viewport os-viewport-native-scrollbars-invisible" style="">
        <div class="os-content" style="padding: 0px; height: 100%; width: 100%;">
          <!-- Sidebar Menu (Flat List) -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-widget="treeview">
              <!-- Home -->
              <li class="nav-item">
                <a href="index.php" class="nav-link">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>Home</p>
                </a>
              </li>

              <!-- Customer Information -->
              <li class="nav-item">
                <a href="customer_management.php" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Customer Information</p>
                </a>
              </li>

              <!-- Customer Logins -->
              <li class="nav-item">
                <a href="customer-logins.php" class="nav-link">
                  <i class="nav-icon fas fa-sign-in-alt"></i>
                  <p>Customer Logins</p>
                </a>
              </li>

              <!-- Enquiries -->
              <li class="nav-item">
                <a href="enquiries.php" class="nav-link">
                  <i class="nav-icon fas fa-envelope-open-text"></i>
                  <p>Enquiries</p>
                </a>
              </li>

              <!-- Groups -->
              <li class="nav-item">
                <a href="groups.php" class="nav-link">
                  <i class="nav-icon fas fa-users-cog"></i>
                  <p>Groups</p>
                </a>
              </li>

              <!-- Loan Settings -->
              <li class="nav-item">
                <a href="loan-settings.php" class="nav-link">
                  <i class="nav-icon fas fa-cog"></i>
                  <p>Loan Settings</p>
                </a>
              </li>

              <!-- Loan Information -->
              <li class="nav-item">
                <a href="loan_information.php" class="nav-link">
                  <i class="nav-icon fas fa-info-circle"></i>
                  <p>Loan Information</p>
                </a>
              </li>

              <!-- Collection Sheet -->
              <li class="nav-item">
                <a href="collection_sheet.php" class="nav-link">
                  <i class="nav-icon fas fa-file-alt"></i>
                  <p>Collection Sheet</p>
                </a>
              </li>

              <!-- Loan Repayments -->
              <li class="nav-item">
                <a href="loan_repayments.php" class="nav-link">
                  <i class="nav-icon fas fa-money-check-alt"></i>
                  <p>Loan Repayments</p>
                </a>
              </li>

              <!-- Cost of funding -->
              <!-- <li class="nav-item">
                <a href="cost-of-funding.php" class="nav-link">
                  <i class="nav-icon fas fa-dollar-sign"></i>
                  <p>Cost of funding</p>
                </a>
              </li> -->

              <!-- Savings Account -->
              <!-- <li class="nav-item">
                <a href="savings-account.php" class="nav-link">
                  <i class="nav-icon fas fa-piggy-bank"></i>
                  <p>Savings Account</p>
                </a>
              </li> -->

              <!-- Internal Accounts -->
              <!-- <li class="nav-item">
                <a href="internal-accounts.php" class="nav-link">
                  <i class="nav-icon fas fa-university"></i>
                  <p>Internal Accounts</p>
                </a>
              </li> -->

              <!-- Cash Operations -->
              <!-- <li class="nav-item">
                <a href="cash-operations.php" class="nav-link">
                  <i class="nav-icon fas fa-cash-register"></i>
                  <p>Cash Operations</p>
                </a>
              </li> -->

              <!-- Reports -->
              <li class="nav-item">
                <a href="reports.php" class="nav-link">
                  <i class="nav-icon fas fa-chart-bar"></i>
                  <p>Reports</p>
                </a>
              </li>

              <!-- Expenses -->
              <li class="nav-item">
                <a href="expenses.php" class="nav-link">
                  <i class="nav-icon fas fa-file-invoice-dollar"></i>
                  <p>Expenses</p>
                </a>
              </li>

              <!-- Employee & User Mgt -->
              <li class="nav-item">
                <a href="employee-user-mgt.php" class="nav-link">
                  <i class="nav-icon fas fa-user-tie"></i>
                  <p>Employee & User Mgt</p>
                </a>
              </li>

              <!-- Global Administration -->
              <li class="nav-item">
                <a href="global_administration.php" class="nav-link">
                  <i class="nav-icon fas fa-cogs"></i>
                  <p>Global Administration</p>
                </a>
              </li>

              <!-- My account -->
              <li class="nav-item">
                <a href="profile.php" class="nav-link">
                  <i class="nav-icon fas fa-user-cog"></i>
                  <p>My account</p>
                </a>
              </li>

            </ul>
          </nav>
        </div>
      </div>
    </div>
    <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
      <div class="os-scrollbar-track">
        <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px);"></div>
      </div>
    </div>
    <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
      <div class="os-scrollbar-track">
        <div class="os-scrollbar-handle" style="height: 50%; transform: translate(0px);"></div>
      </div>
    </div>
    <div class="os-scrollbar-corner"></div>
  </div>
</aside>

<!-- Initialize Scrollbar Script -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    OverlayScrollbars(document.querySelectorAll('.sidebar'), {
      className: 'os-theme-dark',
      overflowBehavior: { x: 'hidden', y: 'scroll' },
      scrollbars: { autoHide: 'leave', autoHideDelay: 500 }
    });
  });
</script>