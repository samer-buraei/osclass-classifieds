<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->findWhere('email = :email', ['email' => $email]);
    }

    /**
     * Get user's listings
     */
    public function getListings($userId, $status = null)
    {
        $where = 'user_id = :user_id';
        $params = ['user_id' => $userId];
        
        if ($status) {
            $where .= ' AND status = :status';
            $params['status'] = $status;
        }
        
        $sql = "SELECT * FROM listings WHERE $where ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get user's favorites
     */
    public function getFavorites($userId)
    {
        $sql = "SELECT l.* FROM listings l 
                INNER JOIN favorites f ON l.id = f.listing_id 
                WHERE f.user_id = :user_id 
                ORDER BY f.created_at DESC";
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Get user rating
     */
    public function getRating($userId)
    {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                FROM reviews 
                WHERE reviewed_user_id = :user_id AND status = 'approved'";
        return $this->db->fetch($sql, ['user_id' => $userId]);
    }

    /**
     * Verify email
     */
    public function verifyEmail($userId)
    {
        return $this->update($userId, ['email_verified_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId)
    {
        $sql = "UPDATE {$this->table} SET last_login_at = NOW() WHERE id = :id";
        return $this->db->query($sql, ['id' => $userId]);
    }
}

