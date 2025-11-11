<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected $table = 'categories';

    /**
     * Get category tree
     */
    public function getTree()
    {
        $categories = $this->where('is_active = 1 ORDER BY sort_order, name');
        
        $tree = [];
        $lookup = [];
        
        foreach ($categories as $category) {
            $category['children'] = [];
            $lookup[$category['id']] = $category;
        }
        
        foreach ($lookup as &$category) {
            if ($category['parent_id']) {
                $lookup[$category['parent_id']]['children'][] = &$category;
            } else {
                $tree[] = &$category;
            }
        }
        
        return $tree;
    }

    /**
     * Get category with listing count
     */
    public function getWithCount()
    {
        $sql = "SELECT c.*, COUNT(l.id) as listing_count 
                FROM {$this->table} c
                LEFT JOIN listings l ON c.id = l.category_id AND l.status = 'active'
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.sort_order, c.name";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get parent categories
     */
    public function getParents()
    {
        return $this->where('parent_id IS NULL AND is_active = 1 ORDER BY sort_order, name');
    }

    /**
     * Get children categories
     */
    public function getChildren($parentId)
    {
        return $this->where('parent_id = :parent_id AND is_active = 1 ORDER BY sort_order, name', 
                          ['parent_id' => $parentId]);
    }

    /**
     * Find by slug
     */
    public function findBySlug($slug)
    {
        return $this->findWhere('slug = :slug', ['slug' => $slug]);
    }
}

