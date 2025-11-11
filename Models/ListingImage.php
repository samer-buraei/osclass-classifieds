<?php

namespace App\Models;

use App\Core\Model;

class ListingImage extends Model
{
    protected $table = 'listing_images';

    /**
     * Get images for listing
     */
    public function getByListing($listingId)
    {
        return $this->where('listing_id = :id ORDER BY sort_order', ['id' => $listingId]);
    }

    /**
     * Get primary image
     */
    public function getPrimary($listingId)
    {
        return $this->findWhere('listing_id = :id ORDER BY sort_order LIMIT 1', ['id' => $listingId]);
    }

    /**
     * Delete images by listing
     */
    public function deleteByListing($listingId)
    {
        $images = $this->getByListing($listingId);
        
        foreach ($images as $image) {
            $filePath = PUBLIC_PATH . $image['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $sql = "DELETE FROM {$this->table} WHERE listing_id = :id";
        return $this->db->query($sql, ['id' => $listingId]);
    }
}

