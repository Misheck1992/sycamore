<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'getfit_db');



// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
// Function to check if the current page is active

// Define constants for branding
define('SITE_NAME', 'Getfit Gym');
define('BASE_URL', 'http://localhost/Getfit/');
// SMTP settings for PHPMailer
define('SMTP_HOST', 'premium262.web-hosting.com');
define('SMTP_USERNAME', 'info@getfitmw.com');
define('SMTP_PASSWORD', 'mwanafyare1999'); // Use app-specific password for Gmail
define('SMTP_PORT', 587);
define('SMTP_FROM_EMAIL', 'info@getfitmw.com');
define('SMTP_FROM_NAME', 'GetFit Gym');
?>