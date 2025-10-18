<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title ?? 'GetFit - Professional Fitness & Wellness'; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="<?php echo $page_keywords ?? 'fitness, gym, weight loss, health products'; ?>" name="keywords">
    <meta content="<?php echo $page_description ?? 'Transform your life with GetFit\'s state-of-the-art gym, online weight loss programs, and premium health products.'; ?>" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="lib/animate/animate.min.css"/>

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }
        .navbar {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            width: 100%;
            margin: 0;
            z-index: 999;
            padding: 15px 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            max-height: 70px;
            width: auto;
        }
        .navbar-toggler {
            border: none;
            padding: 0;
            font-size: 24px;
            color: #384679;
            transition: all 0.3s;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .navbar-nav .nav-link {
            color: #384679 !important;
            padding: 8px 20px !important;
            margin: 0 5px;
            transition: all 0.3s;
            border-radius: 20px;
            position: relative;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: #b9d53f;
            color: #384679 !important;
        }
        @media (min-width: 992px) {
            .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
            }
        }
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: none;
            padding: 10px;
            background-color: #fff;
        }
        .dropdown-item {
            padding: 8px 20px;
            color: #384679;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .dropdown-item:hover,
        .dropdown-item.active {
            background-color: #b9d53f;
            color: #384679;
        }
        .offcanvas {
            background-color: #384679;
            color: #fff;
            width: 300px !important;
        }
        .offcanvas .nav-link {
            color: #fff !important;
            padding: 12px 20px !important;
            margin: 5px 0;
            transition: all 0.3s;
            border-radius: 20px;
        }
        .offcanvas .nav-link:hover,
        .offcanvas .nav-link.active {
            background-color: #b9d53f;
            color: #384679 !important;
        }
        .offcanvas .dropdown-menu {
            background-color: #384679;
            border: none;
            padding: 10px;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
        }
        .offcanvas .dropdown-item {
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s;
            padding: 10px 30px;
        }
        .offcanvas .dropdown-item:hover,
        .offcanvas .dropdown-item.active {
            background-color: #b9d53f;
            color: #384679;
        }
        .offcanvas .dropdown-toggle::after {
            float: right;
            margin-top: 10px;
            transition: transform 0.3s;
        }
        .offcanvas .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }
        .offcanvas-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        .offcanvas-header .navbar-brand {
            margin: 0 auto;
            padding: 0;
        }
        .offcanvas-header img {
            max-width: 120px;
            height: auto;
        }
        .offcanvas-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .offcanvas .navbar-nav {
            margin: 0;
            padding: 0;
            flex-grow: 1;
        }
        .offcanvas-footer {
            padding: 20px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
        }
        .btn-primary {
            background-color: #b9d53f;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: #384679;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #a0c32f;
            color: #384679;
            transform: translateY(-2px);
        }
        @media (max-width: 991.98px) {
            .navbar {
                padding: 10px 15px;
            }
            .navbar-brand img {
                max-height: 50px;
            }
            .navbar-collapse {
                display: none !important;
            }
            .offcanvas {
                width: 100% !important;
            }
        }
        <?php if (isset($additional_styles)) echo $additional_styles; ?>
    </style>
</head>
<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="GetFit Logo" class="me-3">
            </a>
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">Who We Are</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['gym.php', 'online-weight-loss.php', 'health-products.php']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'gym.php' ? 'active' : ''; ?>" href="gym.php">Gym</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'online-weight-loss.php' ? 'active' : ''; ?>" href="online-weight-loss.php">Online Weight Loss</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'health-products.php' ? 'active' : ''; ?>" href="health-products.php">Health Products</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="btn btn-primary ms-lg-2">Register Now</a>
                    </li>
                </ul>
            </div>

            <!-- Mobile Navigation -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
                        <a class="navbar-brand" href="index.php">
                            <img src="img/logo.png" alt="GetFit Logo">
                        </a>
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">Who We Are</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['gym.php', 'online-weight-loss.php', 'health-products.php']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">Services</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'gym.php' ? 'active' : ''; ?>" href="gym.php">Gym</a></li>
                                <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'online-weight-loss.php' ? 'active' : ''; ?>" href="online-weight-loss.php">Online Weight Loss</a></li>
                                <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'health-products.php' ? 'active' : ''; ?>" href="health-products.php">Health Products</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                        </li>
                    </ul>
                    <div class="offcanvas-footer">
                        <a href="register.php" class="btn btn-primary w-100">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
</body>
</html>