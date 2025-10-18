<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="index.php" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Centered Heading -->
  <div class="navbar-brand mx-auto">
    <h5 class="m-0 font-weight-bold">Documentation</h5>
  </div>

  <!-- Right navbar links: Real-time Date Display -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <span class="nav-link" id="realTimeDate"></span>
    </li>
  </ul>
</nav>

<!-- Real-time Date Script -->
<script>
  function updateDateTime() {
    const now = new Date();
    const options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true
    };
    document.getElementById('realTimeDate').textContent = now.toLocaleDateString('en-US', options);
  }

  // Update immediately and every second
  updateDateTime();
  setInterval(updateDateTime, 1000);
</script>