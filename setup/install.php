<?php

/**
 * Installation Script
 * Run this once to set up the database and initial configuration
 */

// Colors for terminal output
function colorize($text, $color) {
    $colors = [
        'green' => "\033[0;32m",
        'red' => "\033[0;31m",
        'yellow' => "\033[1;33m",
        'blue' => "\033[0;34m",
        'reset' => "\033[0m"
    ];
    return $colors[$color] . $text . $colors['reset'];
}

echo "\n";
echo colorize("╔════════════════════════════════════════╗\n", 'blue');
echo colorize("║   Osclass Classifieds Installer      ║\n", 'blue');
echo colorize("╚════════════════════════════════════════╝\n", 'blue');
echo "\n";

// Load configuration
$configFile = __DIR__ . '/../config/database.php';
if (!file_exists($configFile)) {
    echo colorize("✗ Configuration file not found!\n", 'red');
    echo "Please copy config/config.sample.php to config/config.php\n";
    exit(1);
}

$config = require $configFile;

// Test database connection
echo colorize("→ Testing database connection...\n", 'yellow');

try {
    $dsn = "mysql:host={$config['host']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo colorize("✓ Database connection successful!\n", 'green');
} catch (PDOException $e) {
    echo colorize("✗ Database connection failed: " . $e->getMessage() . "\n", 'red');
    exit(1);
}

// Create database if it doesn't exist
echo colorize("→ Creating database...\n", 'yellow');

try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo colorize("✓ Database created!\n", 'green');
} catch (PDOException $e) {
    echo colorize("✗ Failed to create database: " . $e->getMessage() . "\n", 'red');
    exit(1);
}

// Connect to the database
$pdo->exec("USE `{$config['database']}`");

// Import schema
echo colorize("→ Importing database schema...\n", 'yellow');

$schemaFile = __DIR__ . '/../database/schema.sql';
if (!file_exists($schemaFile)) {
    echo colorize("✗ Schema file not found!\n", 'red');
    exit(1);
}

try {
    $sql = file_get_contents($schemaFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) { return !empty($stmt); }
    );
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo colorize("✓ Database schema imported!\n", 'green');
} catch (PDOException $e) {
    echo colorize("✗ Failed to import schema: " . $e->getMessage() . "\n", 'red');
    exit(1);
}

// Create required directories
echo colorize("→ Creating directories...\n", 'yellow');

$dirs = [
    __DIR__ . '/../public/uploads',
    __DIR__ . '/../cache',
    __DIR__ . '/../logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo colorize("  ✓ Created: " . basename($dir) . "/\n", 'green');
    } else {
        echo colorize("  ✓ Exists: " . basename($dir) . "/\n", 'green');
    }
}

// Set permissions
echo colorize("→ Setting permissions...\n", 'yellow');

foreach ($dirs as $dir) {
    chmod($dir, 0777);
}

echo colorize("✓ Permissions set!\n", 'green');

// Create admin user (optional)
echo "\n";
echo colorize("→ Create admin user? (y/n): ", 'yellow');
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) === 'y') {
    echo "Admin name: ";
    $handle = fopen("php://stdin", "r");
    $name = trim(fgets($handle));
    fclose($handle);
    
    echo "Admin email: ";
    $handle = fopen("php://stdin", "r");
    $email = trim(fgets($handle));
    fclose($handle);
    
    echo "Admin password: ";
    $handle = fopen("php://stdin", "r");
    $password = trim(fgets($handle));
    fclose($handle);
    
    try {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $hashedPassword]);
        
        echo colorize("\n✓ Admin user created!\n", 'green');
    } catch (PDOException $e) {
        echo colorize("\n✗ Failed to create admin user: " . $e->getMessage() . "\n", 'red');
    }
}

// Installation complete
echo "\n";
echo colorize("╔════════════════════════════════════════╗\n", 'green');
echo colorize("║   Installation Complete!             ║\n", 'green');
echo colorize("╚════════════════════════════════════════╝\n", 'green');
echo "\n";
echo colorize("Next steps:\n", 'blue');
echo "1. Configure payment gateways in config/config.php\n";
echo "2. Set up your web server to point to the public/ directory\n";
echo "3. Visit your site and start adding listings!\n";
echo "\n";
echo colorize("Documentation: README.md\n", 'yellow');
echo colorize("Quick Start: QUICKSTART.md\n", 'yellow');
echo colorize("Deployment: DEPLOYMENT.md\n", 'yellow');
echo "\n";
echo colorize("Happy selling! 🚀\n", 'green');
echo "\n";

