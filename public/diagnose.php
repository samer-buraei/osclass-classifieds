<?php
/**
 * Comprehensive Diagnostic Tool
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>System Diagnostic</title>
    <style>
        body { font-family: monospace; max-width: 1200px; margin: 20px auto; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        h1 { color: #4ec9b0; border-bottom: 2px solid #4ec9b0; }
        h2 { color: #dcdcaa; margin-top: 30px; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #ce9178; }
        .info { color: #9cdcfe; }
        pre { background: #2d2d2d; padding: 15px; border-left: 3px solid #4ec9b0; overflow-x: auto; }
        .step { background: #252526; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .code { background: #1e1e1e; padding: 3px 6px; border-radius: 3px; color: #ce9178; }
    </style>
</head>
<body>
    <h1>🔍 System Diagnostic Tool</h1>
    
    <?php
    $errors = [];
    $warnings = [];
    $passed = [];
    
    // Step 1: Check file paths
    echo '<div class="step">';
    echo '<h2>Step 1: File System Checks</h2>';
    
    $files = [
        'constants.php' => __DIR__ . '/../config/constants.php',
        'database.php' => __DIR__ . '/../config/database.php',
        'autoload.php' => __DIR__ . '/../vendor/autoload.php',
        'Hooks.php' => __DIR__ . '/../app/Core/Hooks.php',
        'Database.php' => __DIR__ . '/../app/Core/Database.php',
        'Car Plugin' => __DIR__ . '/../plugins/car-attributes/plugin.php',
        'Real Estate Plugin' => __DIR__ . '/../plugins/real-estate/plugin.php',
    ];
    
    foreach ($files as $name => $path) {
        if (file_exists($path)) {
            echo "<span class='success'>✅ $name:</span> <span class='info'>$path</span><br>";
            $passed[] = $name;
        } else {
            echo "<span class='error'>❌ $name:</span> <span class='warning'>NOT FOUND at $path</span><br>";
            $errors[] = "$name not found";
        }
    }
    echo '</div>';
    
    // Step 2: Load constants
    echo '<div class="step">';
    echo '<h2>Step 2: Load Constants</h2>';
    try {
        require_once __DIR__ . '/../config/constants.php';
        echo "<span class='success'>✅ Constants loaded</span><br>";
        echo "Environment: <span class='code'>" . ENVIRONMENT . "</span><br>";
        echo "Base URL: <span class='code'>" . BASE_URL . "</span><br>";
        $passed[] = 'Constants loaded';
    } catch (Exception $e) {
        echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span><br>";
        $errors[] = 'Constants failed to load';
    }
    echo '</div>';
    
    // Step 3: Check Hooks.php content
    echo '<div class="step">';
    echo '<h2>Step 3: Analyze Hooks.php File</h2>';
    $hooksPath = __DIR__ . '/../app/Core/Hooks.php';
    if (file_exists($hooksPath)) {
        $hooksContent = file_get_contents($hooksPath);
        echo "<span class='success'>✅ Hooks.php readable</span><br>";
        echo "File size: <span class='code'>" . strlen($hooksContent) . " bytes</span><br>";
        
        // Check for class definition
        if (strpos($hooksContent, 'class Hooks') !== false) {
            echo "<span class='success'>✅ Hooks class definition found</span><br>";
        } else {
            echo "<span class='error'>❌ Hooks class definition NOT found</span><br>";
            $errors[] = 'Hooks class missing';
        }
        
        // Check for function definitions
        $functions = ['add_action', 'do_action', 'add_filter', 'apply_filters', 'has_action', 'remove_action'];
        foreach ($functions as $func) {
            if (strpos($hooksContent, "function $func") !== false) {
                echo "<span class='success'>✅ Function '$func' definition found</span><br>";
            } else {
                echo "<span class='error'>❌ Function '$func' NOT found</span><br>";
                $errors[] = "Function $func missing";
            }
        }
    } else {
        echo "<span class='error'>❌ Hooks.php not found</span><br>";
        $errors[] = 'Hooks.php missing';
    }
    echo '</div>';
    
    // Step 4: Load autoloader
    echo '<div class="step">';
    echo '<h2>Step 4: Load Autoloader</h2>';
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    
    echo "<pre>";
    echo "Autoloader path: $autoloadPath\n";
    echo "File exists: " . (file_exists($autoloadPath) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($autoloadPath)) {
        $autoloadContent = file_get_contents($autoloadPath);
        echo "Autoloader size: " . strlen($autoloadContent) . " bytes\n";
        echo "\nAutoloader content (first 500 chars):\n";
        echo substr($autoloadContent, 0, 500) . "\n...";
    }
    echo "</pre>";
    
    try {
        require_once $autoloadPath;
        echo "<span class='success'>✅ Autoloader executed</span><br>";
        $passed[] = 'Autoloader loaded';
    } catch (Exception $e) {
        echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span><br>";
        $errors[] = 'Autoloader failed';
    }
    echo '</div>';
    
    // Step 5: Check if functions exist NOW
    echo '<div class="step">';
    echo '<h2>Step 5: Function Existence Check (After Autoloader)</h2>';
    
    $functions = ['add_action', 'do_action', 'add_filter', 'apply_filters', 'has_action', 'remove_action'];
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "<span class='success'>✅ $func() EXISTS</span><br>";
            $passed[] = "$func exists";
        } else {
            echo "<span class='error'>❌ $func() NOT FOUND</span><br>";
            $errors[] = "$func missing";
        }
    }
    echo '</div>';
    
    // Step 6: Check class existence
    echo '<div class="step">';
    echo '<h2>Step 6: Class Existence Check</h2>';
    
    if (class_exists('App\Core\Hooks')) {
        echo "<span class='success'>✅ App\\Core\\Hooks class EXISTS</span><br>";
        
        // Try to call static methods directly
        try {
            \App\Core\Hooks::addAction('test', function() {});
            echo "<span class='success'>✅ Can call Hooks::addAction() directly</span><br>";
        } catch (Exception $e) {
            echo "<span class='error'>❌ Cannot call Hooks::addAction(): " . $e->getMessage() . "</span><br>";
        }
    } else {
        echo "<span class='error'>❌ App\\Core\\Hooks class NOT FOUND</span><br>";
        $errors[] = 'Hooks class not loaded';
    }
    echo '</div>';
    
    // Step 7: List all loaded files
    echo '<div class="step">';
    echo '<h2>Step 7: Loaded Files</h2>';
    $loadedFiles = get_included_files();
    echo "Total files loaded: <span class='code'>" . count($loadedFiles) . "</span><br><br>";
    echo "<pre>";
    foreach ($loadedFiles as $file) {
        if (strpos($file, 'Hooks.php') !== false) {
            echo "<span class='success'>>>> $file (HOOKS FILE!)</span>\n";
        } else {
            echo "$file\n";
        }
    }
    echo "</pre>";
    echo '</div>';
    
    // Step 8: Get all defined functions
    echo '<div class="step">';
    echo '<h2>Step 8: Search All Defined Functions</h2>';
    $allFunctions = get_defined_functions()['user'];
    echo "Total user functions: <span class='code'>" . count($allFunctions) . "</span><br><br>";
    
    echo "Functions containing 'action' or 'filter':<br>";
    $found = false;
    foreach ($allFunctions as $func) {
        if (stripos($func, 'action') !== false || stripos($func, 'filter') !== false) {
            echo "<span class='info'>- $func</span><br>";
            $found = true;
        }
    }
    if (!$found) {
        echo "<span class='warning'>No hook functions found!</span><br>";
    }
    echo '</div>';
    
    // Step 9: Manual require test
    echo '<div class="step">';
    echo '<h2>Step 9: Manual Require Test</h2>';
    echo "Attempting to require Hooks.php manually...<br>";
    
    $hooksPath = __DIR__ . '/../app/Core/Hooks.php';
    try {
        // Force reload
        include $hooksPath;
        echo "<span class='success'>✅ Manual include executed</span><br>";
        
        // Check again
        if (function_exists('add_action')) {
            echo "<span class='success'>✅ add_action() NOW EXISTS!</span><br>";
        } else {
            echo "<span class='error'>❌ add_action() STILL NOT FOUND</span><br>";
        }
    } catch (Exception $e) {
        echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span><br>";
    }
    echo '</div>';
    
    // Summary
    echo '<div class="step">';
    echo '<h2>📊 Summary</h2>';
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;'>";
    
    echo "<div style='background: #264f36; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #4ec9b0; margin-top: 0;'>✅ Passed (" . count($passed) . ")</h3>";
    foreach ($passed as $item) {
        echo "- $item<br>";
    }
    echo "</div>";
    
    echo "<div style='background: #4d3319; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #ce9178; margin-top: 0;'>⚠️ Warnings (" . count($warnings) . ")</h3>";
    foreach ($warnings as $item) {
        echo "- $item<br>";
    }
    echo "</div>";
    
    echo "<div style='background: #4b1818; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #f48771; margin-top: 0;'>❌ Errors (" . count($errors) . ")</h3>";
    foreach ($errors as $item) {
        echo "- $item<br>";
    }
    echo "</div>";
    
    echo "</div>";
    echo '</div>';
    
    // Recommendation
    echo '<div class="step">';
    echo '<h2>💡 Recommendation</h2>';
    if (count($errors) > 0) {
        echo "<p class='error'>System has errors that need to be fixed.</p>";
        echo "<p>Primary issue: Functions are not being defined by Hooks.php</p>";
        echo "<p>Next step: Check PHP error logs and verify Hooks.php syntax</p>";
    } else {
        echo "<p class='success'>All checks passed! System is operational.</p>";
    }
    echo '</div>';
    ?>
    
    <div style="margin-top: 40px; padding: 20px; background: #264f36; border-radius: 5px;">
        <h3 style="color: #4ec9b0;">🔧 Debug Commands</h3>
        <p>If functions still don't exist, try these in a fresh PHP file:</p>
        <pre>
&lt;?php
// Test 1: Direct include
include 'C:\xampp\htdocs\osclass\app\Core\Hooks.php';
var_dump(function_exists('add_action'));

// Test 2: Check namespace
var_dump(class_exists('App\Core\Hooks'));

// Test 3: Call directly
\App\Core\Hooks::addAction('test', function() { echo 'Works!'; });
\App\Core\Hooks::doAction('test');
?&gt;
        </pre>
    </div>
</body>
</html>

