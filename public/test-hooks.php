<?php
/**
 * Test if Hooks are loaded
 */

echo "<h1>Hook System Test</h1>";

require_once __DIR__ . '/../config/constants.php';
echo "✅ Constants loaded<br>";

require_once __DIR__ . '/../vendor/autoload.php';
echo "✅ Autoloader loaded<br>";

echo "<h2>Function Checks:</h2>";

if (function_exists('add_action')) {
    echo "✅ add_action() exists<br>";
} else {
    echo "❌ add_action() NOT FOUND<br>";
}

if (function_exists('do_action')) {
    echo "✅ do_action() exists<br>";
} else {
    echo "❌ do_action() NOT FOUND<br>";
}

if (class_exists('\App\Core\Hooks')) {
    echo "✅ Hooks class exists<br>";
} else {
    echo "❌ Hooks class NOT FOUND<br>";
}

echo "<h2>Test Hook System:</h2>";

try {
    add_action('test_hook', function() {
        echo "Hook callback executed!<br>";
    });
    echo "✅ add_action() called successfully<br>";
    
    do_action('test_hook');
    echo "✅ do_action() called successfully<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>All Functions:</h2>";
$functions = get_defined_functions()['user'];
echo "Looking for hook functions...<br>";
foreach ($functions as $func) {
    if (strpos($func, 'action') !== false || strpos($func, 'filter') !== false) {
        echo "- " . $func . "<br>";
    }
}

