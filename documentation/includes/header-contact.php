<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Contact Us - GetFit</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="fitness, gym, contact, support, getfit malawi" name="keywords">
    <meta content="Get in touch with GetFit for inquiries about our gym, online weight loss programs, or health products." name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding-top: 80px; /* Space for fixed navbar */
        }

        /* Navbar Styles */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .navbar-brand img {
            max-height: 50px;
        }

        .navbar-nav .nav-link {
            color: #384679 !important;
            padding: 8px 20px !important;
            margin: 0 5px;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: #b9d53f;
            color: #384679 !important;
        }

        /* Desktop Navigation */
        @media (min-width: 992px) {
            .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
            }

            .dropdown-menu {
                border: none;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                padding: 10px;
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
                display: none;
            }
        }

        /* Mobile Navigation */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                display: none !important;
            }

            .offcanvas {
                background-color: #384679;
                width: 100% !important;
            }

            .offcanvas-header {
                background-color: #fff;
                padding: 15px;
            }

            .offcanvas-header .btn-close {
                background-color: #b9d53f;
                opacity: 1;
                padding: 10px;
                margin: 0;
            }

            .offcanvas-body {
                padding: 20px;
            }

            .offcanvas .nav-link {
                color: #fff !important;
                padding: 12px 20px !important;
                border-radius: 10px;
                margin-bottom: 5px;
            }

            .offcanvas .nav-link:hover,
            .offcanvas .nav-link.active {
                background-color: #b9d53f;
                color: #384679 !important;
            }

            .offcanvas .dropdown-menu {
                background-color: transparent;
                border: none;
                padding-left: 20px;
            }

            .offcanvas .dropdown-item {
                color: #fff;
                padding: 8px 20px;
                border-radius: 5px;
            }

            .offcanvas .dropdown-item:hover,
            .offcanvas .dropdown-item.active {
                background-color: #b9d53f;
                color: #384679;
            }
        }

        /* Button Styles */
        .btn-primary {
            background-color: #b9d53f;
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: #384679;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #384679;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="GetFit Logo" class="img-fluid">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="fa fa-bars"></span>
            </button>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">Who We Are</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="gym.php">Gym</a></li>
                            <li><a class="dropdown-item" href="online-weight-loss.php">Online Weight Loss</a></li>
                            <li><a class="dropdown-item" href="health-products.php">Health Products</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="register.php">Register Now</a>
                    </li>
                </ul>
            </div>

            <!-- Mobile Navigation -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header">
                    <a class="navbar-brand" href="index.php">
                        <img src="img/logo.png" alt="GetFit Logo" class="img-fluid">
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">Who We Are</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Services</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="gym.php">Gym</a></li>
                                <li><a class="dropdown-item" href="online-weight-loss.php">Online Weight Loss</a></li>
                                <li><a class="dropdown-item" href="health-products.php">Health Products</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contact.php">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary w-100 mt-3" href="register.php">Register Now</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
</body>
</html>