<?php

namespace Plugins\RealEstate;

/**
 * Real Estate Plugin
 * 
 * Adds specialized fields for property listings:
 * - Property Type, Status
 * - Bedrooms, Bathrooms
 * - Area (sqft/sqm)
 * - Amenities, Features
 * - Year Built, Floor
 */

class RealEstatePlugin
{
    public $name = 'Real Estate Attributes';
    public $version = '1.0.0';
    public $description = 'Adds property-specific attributes to listings';
    public $author = 'Osclass';

    private $attributes = [
        'property_type' => ['label' => 'Property Type', 'type' => 'select'],
        'listing_type' => ['label' => 'Listing Type', 'type' => 'select'],
        'bedrooms' => ['label' => 'Bedrooms', 'type' => 'number'],
        'bathrooms' => ['label' => 'Bathrooms', 'type' => 'number'],
        'area' => ['label' => 'Area', 'type' => 'number'],
        'area_unit' => ['label' => 'Area Unit', 'type' => 'select'],
        'lot_size' => ['label' => 'Lot Size', 'type' => 'number'],
        'year_built' => ['label' => 'Year Built', 'type' => 'number'],
        'floor' => ['label' => 'Floor', 'type' => 'number'],
        'total_floors' => ['label' => 'Total Floors', 'type' => 'number'],
        'parking_spaces' => ['label' => 'Parking Spaces', 'type' => 'number'],
        'furnished' => ['label' => 'Furnished', 'type' => 'select'],
    ];

    /**
     * Initialize plugin
     */
    public function init()
    {
        add_action('listing_form_fields', [$this, 'renderFields']);
        add_action('listing_save', [$this, 'saveAttributes']);
        add_action('listing_display', [$this, 'displayAttributes']);
        add_action('listing_search_filters', [$this, 'renderSearchFilters']);
    }

    /**
     * Get property types
     */
    public function getPropertyTypes()
    {
        return [
            'house' => 'House',
            'apartment' => 'Apartment',
            'condo' => 'Condo',
            'townhouse' => 'Townhouse',
            'villa' => 'Villa',
            'land' => 'Land',
            'commercial' => 'Commercial',
            'office' => 'Office',
            'warehouse' => 'Warehouse',
            'building' => 'Building',
            'farm' => 'Farm',
            'studio' => 'Studio'
        ];
    }

    /**
     * Get listing types
     */
    public function getListingTypes()
    {
        return [
            'sale' => 'For Sale',
            'rent' => 'For Rent',
            'lease' => 'For Lease',
            'foreclosure' => 'Foreclosure',
            'short_sale' => 'Short Sale'
        ];
    }

    /**
     * Get area units
     */
    public function getAreaUnits()
    {
        return [
            'sqft' => 'Square Feet',
            'sqm' => 'Square Meters',
            'acres' => 'Acres',
            'hectares' => 'Hectares'
        ];
    }

    /**
     * Get furnished options
     */
    public function getFurnishedOptions()
    {
        return [
            'unfurnished' => 'Unfurnished',
            'semi_furnished' => 'Semi-Furnished',
            'fully_furnished' => 'Fully Furnished'
        ];
    }

    /**
     * Get amenities
     */
    public function getAmenities()
    {
        return [
            'pool' => 'Swimming Pool',
            'gym' => 'Gym/Fitness Center',
            'garden' => 'Garden',
            'balcony' => 'Balcony',
            'terrace' => 'Terrace',
            'elevator' => 'Elevator',
            'security' => '24/7 Security',
            'parking' => 'Parking',
            'ac' => 'Air Conditioning',
            'heating' => 'Central Heating',
            'fireplace' => 'Fireplace',
            'basement' => 'Basement',
            'attic' => 'Attic',
            'laundry' => 'Laundry Room',
            'storage' => 'Storage Room',
            'pet_friendly' => 'Pet Friendly',
            'wheelchair' => 'Wheelchair Accessible',
            'solar' => 'Solar Panels',
            'smart_home' => 'Smart Home',
            'internet' => 'High-Speed Internet'
        ];
    }

    /**
     * Render form fields
     */
    public function renderFields($categoryId)
    {
        if (!$this->isRealEstateCategory($categoryId)) {
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
        
        // Save regular attributes
        foreach ($this->attributes as $key => $config) {
            if (isset($data['re_' . $key])) {
                $value = $data['re_' . $key];
                
                if ($config['type'] === 'number') {
                    $value = is_numeric($value) ? $value : 0;
                } else {
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
                
                $listingModel->setAttribute($listingId, 're_' . $key, $value);
            }
        }
        
        // Save amenities as JSON
        if (isset($data['re_amenities']) && is_array($data['re_amenities'])) {
            $amenities = array_map('htmlspecialchars', $data['re_amenities']);
            $listingModel->setAttribute($listingId, 're_amenities', json_encode($amenities));
        }
    }

    /**
     * Display attributes
     */
    public function displayAttributes($listing)
    {
        if (!$this->isRealEstateCategory($listing['category_id'])) {
            return;
        }

        $listingModel = new \App\Models\Listing();
        $attributes = $listingModel->getAttributes($listing['id']);
        
        // Decode amenities
        if (isset($attributes['re_amenities'])) {
            $attributes['re_amenities'] = json_decode($attributes['re_amenities'], true);
        }
        
        include __DIR__ . '/views/display-attributes.php';
    }

    /**
     * Render search filters
     */
    public function renderSearchFilters($categoryId)
    {
        if (!$this->isRealEstateCategory($categoryId)) {
            return;
        }

        include __DIR__ . '/views/search-filters.php';
    }

    /**
     * Check if category is real estate category
     */
    private function isRealEstateCategory($categoryId)
    {
        $categoryModel = new \App\Models\Category();
        $category = $categoryModel->find($categoryId);
        
        if (!$category) {
            return false;
        }
        
        return strtolower($category['slug']) === 'real-estate' || 
               (isset($category['parent_id']) && $this->isRealEstateCategory($category['parent_id']));
    }
}

// Initialize plugin
$realEstatePlugin = new RealEstatePlugin();
$realEstatePlugin->init();

