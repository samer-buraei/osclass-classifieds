<?php

namespace App\Models;

use App\Core\Model;

class Location extends Model
{
    protected $table = 'locations';

    /**
     * Get location tree
     */
    public function getTree()
    {
        $locations = $this->where('is_active = 1 ORDER BY type, name');
        
        $tree = [];
        $lookup = [];
        
        foreach ($locations as $location) {
            $location['children'] = [];
            $lookup[$location['id']] = $location;
        }
        
        foreach ($lookup as &$location) {
            if ($location['parent_id']) {
                $lookup[$location['parent_id']]['children'][] = &$location;
            } else {
                $tree[] = &$location;
            }
        }
        
        return $tree;
    }

    /**
     * Get countries
     */
    public function getCountries()
    {
        return $this->where("type = 'country' AND is_active = 1 ORDER BY name");
    }

    /**
     * Get states by country
     */
    public function getStates($countryId)
    {
        return $this->where("type = 'state' AND parent_id = :parent_id AND is_active = 1 ORDER BY name", 
                          ['parent_id' => $countryId]);
    }

    /**
     * Get cities by state
     */
    public function getCities($stateId)
    {
        return $this->where("type = 'city' AND parent_id = :parent_id AND is_active = 1 ORDER BY name", 
                          ['parent_id' => $stateId]);
    }

    /**
     * Find by slug
     */
    public function findBySlug($slug)
    {
        return $this->findWhere('slug = :slug', ['slug' => $slug]);
    }
}

