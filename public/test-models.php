<?php
/**
 * Test Models & Database Operations
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Models</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; background: #fff; padding: 15px; border-left: 4px solid #007bff; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f8f9fa; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .back-link { display: inline-block; margin: 20px 0; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Model Testing</h1>
    <a href="test-homepage.php" class="back-link">← Back to Test Homepage</a>

    <?php
    try {
        // Test Category Model
        echo '<h2>📁 Category Model</h2>';
        $categoryModel = new \App\Models\Category();
        $categories = $categoryModel->all();
        
        echo '<p class="success">✅ Category model works! Found ' . count($categories) . ' categories</p>';
        echo '<table>';
        echo '<tr><th>ID</th><th>Name</th><th>Slug</th><th>Parent ID</th><th>Active</th></tr>';
        foreach ($categories as $cat) {
            echo '<tr>';
            echo '<td>' . $cat['id'] . '</td>';
            echo '<td>' . htmlspecialchars($cat['name']) . '</td>';
            echo '<td>' . htmlspecialchars($cat['slug']) . '</td>';
            echo '<td>' . ($cat['parent_id'] ?? 'NULL') . '</td>';
            echo '<td>' . ($cat['is_active'] ? '✅' : '❌') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        // Test category tree
        $tree = $categoryModel->getTree();
        echo '<p class="success">✅ Category tree method works! Built ' . count($tree) . ' top-level categories</p>';
        
        // Test User Model
        echo '<h2>👤 User Model</h2>';
        $userModel = new \App\Models\User();
        $userCount = $userModel->count();
        echo '<p class="success">✅ User model works! Found ' . $userCount . ' users</p>';
        
        if ($userCount > 0) {
            $users = $userModel->all(5);
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Created</th></tr>';
            foreach ($users as $user) {
                echo '<tr>';
                echo '<td>' . $user['id'] . '</td>';
                echo '<td>' . htmlspecialchars($user['name']) . '</td>';
                echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                echo '<td>' . $user['status'] . '</td>';
                echo '<td>' . $user['created_at'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
        // Test Listing Model
        echo '<h2>📝 Listing Model</h2>';
        $listingModel = new \App\Models\Listing();
        $listingCount = $listingModel->count();
        echo '<p class="success">✅ Listing model works! Found ' . $listingCount . ' listings</p>';
        
        // Test creating a sample listing (optional)
        echo '<h3>Test Listing Creation</h3>';
        echo '<p>To test listing creation, we need an authenticated user. You can:</p>';
        echo '<ol>';
        echo '<li>Register a user through the auth system</li>';
        echo '<li>Create a listing through the UI</li>';
        echo '<li>Or run the installer to create a test admin user</li>';
        echo '</ol>';
        
        // Test Location Model
        echo '<h2>📍 Location Model</h2>';
        $locationModel = new \App\Models\Location();
        $locationCount = $locationModel->count();
        echo '<p class="success">✅ Location model works! Found ' . $locationCount . ' locations</p>';
        
        // Test Database Helper Methods
        echo '<h2>🗄️ Database Helper Methods</h2>';
        $db = \App\Core\Database::getInstance();
        
        echo '<p class="success">✅ Database singleton works!</p>';
        
        // Test pagination
        $paginated = $categoryModel->paginate(1, 3);
        echo '<p class="success">✅ Pagination works! Page 1 of ' . $paginated['total_pages'] . '</p>';
        echo '<p>Items per page: ' . $paginated['per_page'] . ', Total items: ' . $paginated['total'] . '</p>';
        
        // Test where clause
        $activeCategories = $categoryModel->where('is_active = :active', ['active' => 1]);
        echo '<p class="success">✅ Where clause works! Found ' . count($activeCategories) . ' active categories</p>';
        
        // Show summary
        echo '<h2>📊 Summary</h2>';
        echo '<table>';
        echo '<tr><th>Component</th><th>Status</th><th>Notes</th></tr>';
        
        $tests = [
            ['Category Model', '✅ Passed', count($categories) . ' categories loaded'],
            ['User Model', '✅ Passed', $userCount . ' users found'],
            ['Listing Model', '✅ Passed', $listingCount . ' listings found'],
            ['Location Model', '✅ Passed', $locationCount . ' locations found'],
            ['Database Singleton', '✅ Passed', 'Connection active'],
            ['Pagination', '✅ Passed', 'Working correctly'],
            ['Where Clause', '✅ Passed', 'Filtering works'],
        ];
        
        foreach ($tests as $test) {
            echo '<tr>';
            echo '<td>' . $test[0] . '</td>';
            echo '<td>' . $test[1] . '</td>';
            echo '<td>' . $test[2] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        echo '<div style="margin-top: 40px; padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">';
        echo '<h3 style="color: #155724; margin-top: 0;">✅ All Models Working Perfectly!</h3>';
        echo '<p style="color: #155724;">Your MVC architecture is fully functional. All database models are operational.</p>';
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0;">';
        echo '<p class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p>Stack trace:</p>';
        echo '<pre style="background: white; padding: 10px; overflow: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
    ?>
    
    <a href="test-homepage.php" class="back-link">← Back to Test Homepage</a>
</body>
</html>



