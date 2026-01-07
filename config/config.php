<?php
// Application configuration
define('SITE_NAME', 'Billing System');
define('BASE_URL', 'http://localhost/billing-web/main/');
define('ASSETS_URL', BASE_URL . 'assets/');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/database.php';

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';
?>
