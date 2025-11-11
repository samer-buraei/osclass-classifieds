<?php
/**
 * Database Schema Checker
 * Verifies database structure and adds sample data if needed
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

$db = Database::getInstance();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Check - Osclass</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f7fa; 
            padding: 40px 20px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 30px; }
        .section { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 { color: #3a7bd5; margin-bottom: 20px; font-size: 20px; }
        .status { 
            display: inline-block; 
            padding: 6px 12px; 
            border-radius: 6px; 
            font-weight: 600;
            margin-left: 10px;
        }
        .status.ok { background: #10b981; color: white; }
        .status.error { background: #ef4444; color: white; }
        .status.warning { background: #f59e0b; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f5f7fa; font-weight: 600; color: #333; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3a7bd5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .btn:hover { background: #2d5ea8; }
        code {
            background: #f5f7fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #e55a2a;
        }
        .info { 
            background: #dbeafe; 
            border-left: 4px solid #3a7bd5; 
            padding: 15px; 
            margin: 15px 0;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🔍 Database Schema Check</h1>

    <!-- Tables Check -->
    <div class="section">
        <h2>📊 Database Tables</h2>
        <?php
        try {
            $tables = $db->fetchAll("SHOW TABLES", []);
            echo '<p><span class="status ok">✓ Connected</span> Found ' . count($tables) . ' tables</p>';
            
            echo '<table>';
            echo '<tr><th>Table Name</th><th>Rows</th><th>Status</th></tr>';
            
            $expectedTables = ['users', 'categories', 'locations', 'listings', 'listing_images', 
                             'listing_attributes', 'favorites', 'messages', 'reviews', 'payments', 
                             'pages', 'settings'];
            
            foreach ($expectedTables as $table) {
                $exists = false;
                foreach ($tables as $t) {
                    if (in_array($table, $t)) {
                        $exists = true;
                        break;
                    }
                }
                
                if ($exists) {
                    $count = $db->fetchOne("SELECT COUNT(*) as count FROM $table", [])['count'];
                    echo '<tr>';
                    echo '<td><code>' . $table . '</code></td>';
                    echo '<td>' . number_format($count) . '</td>';
                    echo '<td><span class="status ok">✓ Exists</span></td>';
                    echo '</tr>';
                } else {
                    echo '<tr>';
                    echo '<td><code>' . $table . '</code></td>';
                    echo '<td>-</td>';
                    echo '<td><span class="status error">✗ Missing</span></td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
            
        } catch (Exception $e) {
            echo '<p><span class="status error">✗ Error</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <!-- Listings Table Structure -->
    <div class="section">
        <h2>📋 Listings Table Structure</h2>
        <?php
        try {
            $columns = $db->fetchAll("DESCRIBE listings", []);
            echo '<table>';
            echo '<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
            foreach ($columns as $col) {
                echo '<tr>';
                echo '<td><code>' . htmlspecialchars($col['Field']) . '</code></td>';
                echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
                echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
                echo '<td>' . htmlspecialchars($col['Key']) . '</td>';
                echo '<td>' . htmlspecialchars($col['Default'] ?? 'NULL') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
            // Check for important columns
            $hasStatus = false;
            $hasFeatured = false;
            foreach ($columns as $col) {
                if ($col['Field'] === 'status') $hasStatus = true;
                if ($col['Field'] === 'featured') $hasFeatured = true;
            }
            
            echo '<div class="info">';
            echo '<strong>Column Status:</strong><br>';
            echo '• <code>status</code> column: ' . ($hasStatus ? '<span class="status ok">✓ Found</span>' : '<span class="status error">✗ Missing</span>') . '<br>';
            echo '• <code>featured</code> column: ' . ($hasFeatured ? '<span class="status ok">✓ Found</span>' : '<span class="status error">✗ Missing</span>');
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<p><span class="status error">✗ Error</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <!-- Sample Data Check -->
    <div class="section">
        <h2>📦 Sample Data</h2>
        <?php
        try {
            $categoryCount = $db->fetchOne("SELECT COUNT(*) as count FROM categories", [])['count'];
            $locationCount = $db->fetchOne("SELECT COUNT(*) as count FROM locations", [])['count'];
            $listingCount = $db->fetchOne("SELECT COUNT(*) as count FROM listings", [])['count'];
            $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users", [])['count'];
            
            echo '<table>';
            echo '<tr><th>Data Type</th><th>Count</th><th>Status</th></tr>';
            
            echo '<tr>';
            echo '<td>Categories</td>';
            echo '<td>' . number_format($categoryCount) . '</td>';
            echo '<td>' . ($categoryCount > 0 ? '<span class="status ok">✓ Has data</span>' : '<span class="status warning">⚠ Empty</span>') . '</td>';
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>Locations</td>';
            echo '<td>' . number_format($locationCount) . '</td>';
            echo '<td>' . ($locationCount > 0 ? '<span class="status ok">✓ Has data</span>' : '<span class="status warning">⚠ Empty</span>') . '</td>';
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>Listings</td>';
            echo '<td>' . number_format($listingCount) . '</td>';
            echo '<td>' . ($listingCount > 0 ? '<span class="status ok">✓ Has data</span>' : '<span class="status warning">⚠ Empty</span>') . '</td>';
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>Users</td>';
            echo '<td>' . number_format($userCount) . '</td>';
            echo '<td>' . ($userCount > 0 ? '<span class="status ok">✓ Has data</span>' : '<span class="status warning">⚠ Empty</span>') . '</td>';
            echo '</tr>';
            
            echo '</table>';
            
            if ($categoryCount == 0 || $locationCount == 0) {
                echo '<div class="info">';
                echo '<strong>⚠ Warning:</strong> Your database has no sample data. ';
                echo 'Visit <a href="test-setup.php">test-setup.php</a> to add sample data.';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<p><span class="status error">✗ Error</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <!-- Active Listings Check -->
    <div class="section">
        <h2>✅ Active Listings</h2>
        <?php
        try {
            $activeListings = $db->fetchAll("SELECT id, title, status, created_at FROM listings ORDER BY created_at DESC LIMIT 10", []);
            
            if (empty($activeListings)) {
                echo '<div class="info">';
                echo '<strong>ℹ No listings found.</strong><br>';
                echo 'The database has no listings yet. This is normal for a new installation.';
                echo '</div>';
            } else {
                echo '<table>';
                echo '<tr><th>ID</th><th>Title</th><th>Status</th><th>Created</th></tr>';
                foreach ($activeListings as $listing) {
                    echo '<tr>';
                    echo '<td>' . $listing['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($listing['title']) . '</td>';
                    echo '<td><code>' . htmlspecialchars($listing['status']) . '</code></td>';
                    echo '<td>' . date('Y-m-d H:i', strtotime($listing['created_at'])) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            
        } catch (Exception $e) {
            echo '<p><span class="status error">✗ Error</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <!-- Recommendations -->
    <div class="section">
        <h2>💡 Recommendations</h2>
        <div class="info">
            <strong>For Halooglasi Theme to work properly:</strong><br><br>
            
            1. <strong>Listings table must have:</strong><br>
            &nbsp;&nbsp;&nbsp;• <code>status</code> column with values: 'pending', 'active', 'expired', 'sold', 'rejected'<br>
            &nbsp;&nbsp;&nbsp;• <code>featured</code> column (BOOLEAN)<br><br>
            
            2. <strong>Sample data needed:</strong><br>
            &nbsp;&nbsp;&nbsp;• At least 8 categories<br>
            &nbsp;&nbsp;&nbsp;• At least 5 locations<br>
            &nbsp;&nbsp;&nbsp;• Some test listings (optional)<br><br>
            
            3. <strong>If you see errors:</strong><br>
            &nbsp;&nbsp;&nbsp;• Run <code>database/schema.sql</code> to create tables<br>
            &nbsp;&nbsp;&nbsp;• Visit <a href="test-setup.php">test-setup.php</a> to add sample data<br>
            &nbsp;&nbsp;&nbsp;• Check <a href="diagnose.php">diagnose.php</a> for detailed diagnostics
        </div>
    </div>

    <!-- Quick Links -->
    <div class="section">
        <h2>🔗 Quick Links</h2>
        <a href="index-halooglasi.php" class="btn">View Homepage</a>
        <a href="category-halooglasi.php?slug=vehicles" class="btn">View Category</a>
        <a href="test-setup.php" class="btn">Test Setup</a>
        <a href="diagnose.php" class="btn">Full Diagnostic</a>
    </div>

</div>

</body>
</html>


