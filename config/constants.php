<?php

// Environment
define('ENVIRONMENT', $_ENV['APP_ENV'] ?? 'development');

// Base URL
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost:8080');

// App info
define('APP_NAME', 'Osclass Classifieds');
define('APP_VERSION', '1.0.0');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('CACHE_PATH', ROOT_PATH . '/cache');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Create necessary directories
$dirs = [UPLOAD_PATH, CACHE_PATH, LOGS_PATH];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// File upload settings
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 7200); // 2 hours

// SEO
define('DEFAULT_META_DESCRIPTION', 'Find great deals on classified ads');
define('DEFAULT_META_KEYWORDS', 'classifieds,ads,buy,sell,cars,real estate');

