<?php
/**
 * Setup Verification Script
 */

echo "<h1>🧪 Osclass Setup Test</h1>";

// Test 1: Check if files exist
echo "<h2>1. File Check</h2>";
$files = [
    '../config/constants.php' => file_exists(__DIR__ . '/../config/constants.php'),
    '../config/database.php' => file_exists(__DIR__ . '/../config/database.php'),
    '../vendor/autoload.php' => file_exists(__DIR__ . '/../vendor/autoload.php'),
    '../app/Core/Database.php' => file_exists(__DIR__ . '/../app/Core/Database.php'),
];

foreach ($files as $file => $exists) {
    echo $exists ? "✅ " : "❌ ";
    echo $file . "<br>";
}

// Test 2: Load configuration
echo "<h2>2. Configuration</h2>";
try {
    require_once __DIR__ . '/../config/constants.php';
    echo "✅ Constants loaded<br>";
    echo "- Environment: " . ENVIRONMENT . "<br>";
    echo "- Base URL: " . BASE_URL . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading constants: " . $e->getMessage() . "<br>";
}

// Test 3: Autoloader
echo "<h2>3. Autoloader</h2>";
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "✅ Autoloader loaded<br>";
} catch (Exception $e) {
    echo "❌ Error loading autoloader: " . $e->getMessage() . "<br>";
}

// Test 4: Database Connection
echo "<h2>4. Database Connection</h2>";
try {
    $config = require __DIR__ . '/../config/database.php';
    echo "Database Config:<br>";
    echo "- Host: " . $config['host'] . "<br>";
    echo "- Database: " . $config['database'] . "<br>";
    echo "- Username: " . $config['username'] . "<br>";
    
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connected!<br>";
    
    // Test 5: Check tables
    echo "<h2>5. Database Tables</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✅ Found " . count($tables) . " tables:<br>";
        foreach ($tables as $table) {
            echo "- $table<br>";
        }
    } else {
        echo "❌ No tables found. Please import database/schema.sql<br>";
    }
    
    // Test 6: Check categories
    echo "<h2>6. Sample Data</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "✅ Found " . $result['count'] . " categories<br>";
        
        $stmt = $pdo->query("SELECT name, slug FROM categories LIMIT 5");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($categories as $cat) {
            echo "<li>" . htmlspecialchars($cat['name']) . " (/" . htmlspecialchars($cat['slug']) . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "⚠️ No categories found<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "<br><strong>Fix:</strong><br>";
    echo "1. Check that MySQL is running in XAMPP<br>";
    echo "2. Verify credentials in config/database.php<br>";
    echo "3. Make sure you imported database/schema.sql<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>✅ Next Steps</h2>";
echo "<p>If all tests pass, visit: <a href='index.php'>Homepage</a></p>";
echo "<p>Or create a test listing: <a href='listing/create'>Create Listing</a></p>";
?>

<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
    h2 { color: #555; margin-top: 30px; background: #fff; padding: 10px; border-left: 4px solid #007bff; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
</style>



