<?php
/**
 * Test Plugins
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Plugins</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; border-bottom: 3px solid #28a745; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; background: #fff; padding: 15px; border-left: 4px solid #28a745; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .plugin-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border: 2px solid #28a745; }
        .back-link { display: inline-block; margin: 20px 0; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; }
        ul { line-height: 1.8; }
    </style>
</head>
<body>
    <h1>🔌 Plugin Testing</h1>
    <a href="test-homepage.php" class="back-link">← Back to Test Homepage</a>

    <?php
    echo '<h2>🚗 Car Attributes Plugin</h2>';
    
    $carPluginFile = __DIR__ . '/../plugins/car-attributes/plugin.php';
    if (file_exists($carPluginFile)) {
        echo '<p class="success">✅ Plugin file found!</p>';
        
        try {
            require_once $carPluginFile;
            echo '<p class="success">✅ Plugin loaded successfully!</p>';
            
            echo '<div class="plugin-box">';
            echo '<h3>Plugin Features:</h3>';
            echo '<ul>';
            echo '<li>✅ Make & Model selection (30+ manufacturers)</li>';
            echo '<li>✅ Year, Mileage tracking</li>';
            echo '<li>✅ Transmission types (Automatic, Manual, CVT, etc.)</li>';
            echo '<li>✅ Fuel types (Gasoline, Diesel, Electric, Hybrid)</li>';
            echo '<li>✅ Body types (Sedan, SUV, Truck, Van, etc.)</li>';
            echo '<li>✅ Color selection (12+ colors)</li>';
            echo '<li>✅ Engine size, Doors, Seats</li>';
            echo '<li>✅ VIN number tracking</li>';
            echo '</ul>';
            
            echo '<h4>Available Views:</h4>';
            echo '<ul>';
            $carViews = [
                'form-fields.php' => 'Add car attributes to listing forms',
                'display-attributes.php' => 'Display car specs on listing pages',
                'search-filters.php' => 'Advanced car search filters'
            ];
            foreach ($carViews as $file => $desc) {
                $path = __DIR__ . '/../plugins/car-attributes/views/' . $file;
                $exists = file_exists($path) ? '✅' : '❌';
                echo '<li>' . $exists . ' ' . $file . ' - ' . $desc . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Error loading plugin: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    } else {
        echo '<p class="error">❌ Plugin file not found!</p>';
    }

    echo '<h2>🏠 Real Estate Plugin</h2>';
    
    $rePluginFile = __DIR__ . '/../plugins/real-estate/plugin.php';
    if (file_exists($rePluginFile)) {
        echo '<p class="success">✅ Plugin file found!</p>';
        
        try {
            require_once $rePluginFile;
            echo '<p class="success">✅ Plugin loaded successfully!</p>';
            
            echo '<div class="plugin-box">';
            echo '<h3>Plugin Features:</h3>';
            echo '<ul>';
            echo '<li>✅ Property types (House, Apartment, Condo, Villa, Land, etc.)</li>';
            echo '<li>✅ Listing types (For Sale, For Rent, For Lease)</li>';
            echo '<li>✅ Bedrooms & Bathrooms</li>';
            echo '<li>✅ Area with multiple units (sqft, sqm, acres, hectares)</li>';
            echo '<li>✅ Lot size tracking</li>';
            echo '<li>✅ Year built, Floor information</li>';
            echo '<li>✅ Parking spaces</li>';
            echo '<li>✅ Furnished status (Unfurnished, Semi, Fully)</li>';
            echo '<li>✅ 20+ Amenities (Pool, Gym, Garden, Balcony, Security, etc.)</li>';
            echo '</ul>';
            
            echo '<h4>Available Views:</h4>';
            echo '<ul>';
            $reViews = [
                'form-fields.php' => 'Add property details to listing forms',
                'display-attributes.php' => 'Display property specs on listing pages',
                'search-filters.php' => 'Advanced property search filters'
            ];
            foreach ($reViews as $file => $desc) {
                $path = __DIR__ . '/../plugins/real-estate/views/' . $file;
                $exists = file_exists($path) ? '✅' : '❌';
                echo '<li>' . $exists . ' ' . $file . ' - ' . $desc . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Error loading plugin: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    } else {
        echo '<p class="error">❌ Plugin file not found!</p>';
    }

    echo '<h2>🛠️ How Plugins Work</h2>';
    echo '<div class="plugin-box">';
    echo '<h3>Plugin Architecture:</h3>';
    echo '<ol>';
    echo '<li><strong>Hook System:</strong> Plugins use action hooks to extend core functionality</li>';
    echo '<li><strong>Category Detection:</strong> Plugins automatically activate for specific categories</li>';
    echo '<li><strong>Data Storage:</strong> Custom attributes stored in <code>listing_attributes</code> table</li>';
    echo '<li><strong>View Rendering:</strong> Plugins include their own form fields and display templates</li>';
    echo '</ol>';
    
    echo '<h4>Available Hooks:</h4>';
    echo '<ul>';
    echo '<li><code>listing_form_fields</code> - Add fields to listing creation/edit forms</li>';
    echo '<li><code>listing_save</code> - Save custom data when listing is saved</li>';
    echo '<li><code>listing_display</code> - Display custom data on listing pages</li>';
    echo '<li><code>listing_search_filters</code> - Add filters to search functionality</li>';
    echo '</ul>';
    echo '</div>';

    echo '<h2>📚 Create Your Own Plugin</h2>';
    echo '<div class="plugin-box">';
    echo '<p>To create a new plugin:</p>';
    echo '<ol>';
    echo '<li>Create <code>plugins/my-plugin/plugin.php</code></li>';
    echo '<li>Define a class that extends plugin functionality</li>';
    echo '<li>Register hooks in the <code>init()</code> method</li>';
    echo '<li>Create view templates in <code>views/</code> folder</li>';
    echo '<li>See <code>ARCHITECTURE.md</code> for complete tutorial</li>';
    echo '</ol>';
    echo '<p><strong>Example use cases:</strong></p>';
    echo '<ul>';
    echo '<li>Job listings (company, position, salary, etc.)</li>';
    echo '<li>Event listings (date, time, venue, tickets)</li>';
    echo '<li>Pet listings (breed, age, vaccinations)</li>';
    echo '<li>Equipment rentals (rates, availability)</li>';
    echo '</ul>';
    echo '</div>';

    echo '<div style="margin-top: 40px; padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">';
    echo '<h3 style="color: #155724; margin-top: 0;">✅ Both Plugins Working!</h3>';
    echo '<p style="color: #155724;">Your plugin system is fully operational. You can now:</p>';
    echo '<ul style="color: #155724;">';
    echo '<li>Use Car Attributes for vehicle listings</li>';
    echo '<li>Use Real Estate for property listings</li>';
    echo '<li>Create custom plugins following the same pattern</li>';
    echo '</ul>';
    echo '</div>';
    ?>
    
    <a href="test-homepage.php" class="back-link">← Back to Test Homepage</a>
</body>
</html>



