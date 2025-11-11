<?php

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/constants.php';

// Autoload classes
require_once __DIR__ . '/../vendor/autoload.php';

// Error reporting (disable in production)
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Initialize app
$app = new App\Core\App();

