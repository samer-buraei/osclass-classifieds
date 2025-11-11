<?php

namespace App\Models;

use App\Core\Model;

class Listing extends Model
{
    protected $table = 'listings';

    /**
     * Get listing with user and category info
     */
    public function getWithDetails($id)
    {
        $sql = "SELECT l.*, u.name as user_name, u.email as user_email, 
                       c.name as category_name, c.slug as category_slug,
                       loc.name as location_name
                FROM {$this->table} l
                LEFT JOIN users u ON l.user_id = u.id
                LEFT JOIN categories c ON l.category_id = c.id
                LEFT JOIN locations loc ON l.location_id = loc.id
                WHERE l.id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Search listings
     */
    public function search($params)
    {
        $where = ['status = :status'];
        $queryParams = ['status' => 'active'];
        
        if (!empty($params['query'])) {
            $where[] = "MATCH(title, description) AGAINST(:query IN NATURAL LANGUAGE MODE)";
            $queryParams['query'] = $params['query'];
        }
        
        if (!empty($params['category_id'])) {
            $where[] = 'category_id = :category_id';
            $queryParams['category_id'] = $params['category_id'];
        }
        
        if (!empty($params['location_id'])) {
            $where[] = 'location_id = :location_id';
            $queryParams['location_id'] = $params['location_id'];
        }
        
        if (isset($params['min_price'])) {
            $where[] = 'price >= :min_price';
            $queryParams['min_price'] = $params['min_price'];
        }
        
        if (isset($params['max_price'])) {
            $where[] = 'price <= :max_price';
            $queryParams['max_price'] = $params['max_price'];
        }
        
        $whereClause = implode(' AND ', $where);
        
        return $this->paginate(
            $params['page'] ?? 1,
            $params['per_page'] ?? ITEMS_PER_PAGE,
            $whereClause,
            $queryParams
        );
    }

    /**
     * Get featured listings
     */
    public function getFeatured($limit = 6)
    {
        return $this->where(
            'status = :status AND featured = 1 ORDER BY created_at DESC LIMIT ' . $limit,
            ['status' => 'active']
        );
    }

    /**
     * Get recent listings
     */
    public function getRecent($limit = 12)
    {
        return $this->where(
            'status = :status ORDER BY created_at DESC LIMIT ' . $limit,
            ['status' => 'active']
        );
    }

    /**
     * Get listing attributes (for plugins)
     */
    public function getAttributes($listingId)
    {
        $sql = "SELECT * FROM listing_attributes WHERE listing_id = :id";
        $results = $this->db->fetchAll($sql, ['id' => $listingId]);
        
        $attributes = [];
        foreach ($results as $row) {
            $attributes[$row['attribute_key']] = $row['attribute_value'];
        }
        
        return $attributes;
    }

    /**
     * Set listing attribute
     */
    public function setAttribute($listingId, $key, $value)
    {
        $sql = "INSERT INTO listing_attributes (listing_id, attribute_key, attribute_value) 
                VALUES (:listing_id, :key, :value)
                ON DUPLICATE KEY UPDATE attribute_value = :value";
        return $this->db->query($sql, [
            'listing_id' => $listingId,
            'key' => $key,
            'value' => $value
        ]);
    }

    /**
     * Generate unique slug
     */
    public function generateSlug($title, $id = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        $where = 'slug = :slug';
        $params = ['slug' => $slug];
        
        if ($id) {
            $where .= ' AND id != :id';
            $params['id'] = $id;
        }
        
        $existing = $this->findWhere($where, $params);
        
        if ($existing) {
            $slug .= '-' . time();
        }
        
        return $slug;
    }

    /**
     * Mark as featured
     */
    public function markFeatured($id, $featured = true)
    {
        return $this->update($id, ['featured' => $featured ? 1 : 0]);
    }

    /**
     * Update status
     */
    public function updateStatus($id, $status)
    {
        $validStatuses = ['pending', 'active', 'expired', 'sold', 'rejected'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        return $this->update($id, ['status' => $status]);
    }
}

