<?php

namespace Plugins\CarAttributes;

/**
 * Car Attributes Plugin
 * 
 * Adds specialized fields for vehicle listings:
 * - Make, Model, Year
 * - Mileage, Condition
 * - Transmission, Fuel Type
 * - Body Type, Color
 */

class CarAttributesPlugin
{
    public $name = 'Car Attributes';
    public $version = '1.0.0';
    public $description = 'Adds car-specific attributes to listings';
    public $author = 'Osclass';

    private $attributes = [
        'make' => ['label' => 'Make', 'type' => 'select'],
        'model' => ['label' => 'Model', 'type' => 'text'],
        'year' => ['label' => 'Year', 'type' => 'number'],
        'mileage' => ['label' => 'Mileage', 'type' => 'number'],
        'condition' => ['label' => 'Condition', 'type' => 'select'],
        'transmission' => ['label' => 'Transmission', 'type' => 'select'],
        'fuel_type' => ['label' => 'Fuel Type', 'type' => 'select'],
        'body_type' => ['label' => 'Body Type', 'type' => 'select'],
        'color' => ['label' => 'Color', 'type' => 'select'],
        'doors' => ['label' => 'Doors', 'type' => 'number'],
        'seats' => ['label' => 'Seats', 'type' => 'number'],
        'engine_size' => ['label' => 'Engine Size (L)', 'type' => 'text'],
        'vin' => ['label' => 'VIN', 'type' => 'text'],
    ];

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Hook into listing creation/edit forms
        \add_action('listing_form_fields', [$this, 'renderFields']);
        \add_action('listing_save', [$this, 'saveAttributes']);
        \add_action('listing_display', [$this, 'displayAttributes']);
        \add_action('listing_search_filters', [$this, 'renderSearchFilters']);
    }

    /**
     * Get car makes
     */
    public function getMakes()
    {
        return [
            'Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan', 
            'BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen', 'Hyundai',
            'Kia', 'Mazda', 'Subaru', 'Lexus', 'Jeep',
            'Ram', 'GMC', 'Dodge', 'Chrysler', 'Buick',
            'Cadillac', 'Tesla', 'Volvo', 'Porsche', 'Jaguar',
            'Land Rover', 'Mitsubishi', 'Acura', 'Infiniti', 'Genesis'
        ];
    }

    /**
     * Get conditions
     */
    public function getConditions()
    {
        return [
            'new' => 'New',
            'like_new' => 'Like New',
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'salvage' => 'Salvage'
        ];
    }

    /**
     * Get transmission types
     */
    public function getTransmissions()
    {
        return [
            'automatic' => 'Automatic',
            'manual' => 'Manual',
            'cvt' => 'CVT',
            'semi_automatic' => 'Semi-Automatic'
        ];
    }

    /**
     * Get fuel types
     */
    public function getFuelTypes()
    {
        return [
            'gasoline' => 'Gasoline',
            'diesel' => 'Diesel',
            'electric' => 'Electric',
            'hybrid' => 'Hybrid',
            'plug_in_hybrid' => 'Plug-in Hybrid',
            'hydrogen' => 'Hydrogen'
        ];
    }

    /**
     * Get body types
     */
    public function getBodyTypes()
    {
        return [
            'sedan' => 'Sedan',
            'suv' => 'SUV',
            'truck' => 'Truck',
            'van' => 'Van',
            'coupe' => 'Coupe',
            'convertible' => 'Convertible',
            'wagon' => 'Wagon',
            'hatchback' => 'Hatchback',
            'minivan' => 'Minivan'
        ];
    }

    /**
     * Get colors
     */
    public function getColors()
    {
        return [
            'black' => 'Black',
            'white' => 'White',
            'silver' => 'Silver',
            'gray' => 'Gray',
            'red' => 'Red',
            'blue' => 'Blue',
            'green' => 'Green',
            'yellow' => 'Yellow',
            'orange' => 'Orange',
            'brown' => 'Brown',
            'gold' => 'Gold',
            'beige' => 'Beige'
        ];
    }

    /**
     * Render form fields
     */
    public function renderFields($categoryId)
    {
        // Only show for vehicle category (adjust category ID as needed)
        if (!$this->isVehicleCategory($categoryId)) {
            return;
        }

        include __DIR__ . '/views/form-fields.php';
    }

    /**
     * Save attributes
     */
    public function saveAttributes($listingId, $data)
    {
        $listingModel = new \App\Models\Listing();
        
        foreach ($this->attributes as $key => $config) {
            if (isset($data['car_' . $key])) {
                $value = $data['car_' . $key];
                
                // Sanitize value
                if ($config['type'] === 'number') {
                    $value = intval($value);
                } else {
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
                
                $listingModel->setAttribute($listingId, 'car_' . $key, $value);
            }
        }
    }

    /**
     * Display attributes
     */
    public function displayAttributes($listing)
    {
        if (!$this->isVehicleCategory($listing['category_id'])) {
            return;
        }

        $listingModel = new \App\Models\Listing();
        $attributes = $listingModel->getAttributes($listing['id']);
        
        include __DIR__ . '/views/display-attributes.php';
    }

    /**
     * Render search filters
     */
    public function renderSearchFilters($categoryId)
    {
        if (!$this->isVehicleCategory($categoryId)) {
            return;
        }

        include __DIR__ . '/views/search-filters.php';
    }

    /**
     * Check if category is vehicle category
     */
    private function isVehicleCategory($categoryId)
    {
        // Check if category is "Vehicles" or child of vehicles
        // This is a simple check - you may want to make it more robust
        $categoryModel = new \App\Models\Category();
        $category = $categoryModel->find($categoryId);
        
        if (!$category) {
            return false;
        }
        
        return strtolower($category['slug']) === 'vehicles' || 
               (isset($category['parent_id']) && $this->isVehicleCategory($category['parent_id']));
    }
}

// Initialize plugin
$carAttributesPlugin = new CarAttributesPlugin();
$carAttributesPlugin->init();

