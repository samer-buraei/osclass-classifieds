<?php

namespace App\Core;

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Find all records
     */
    public function all($limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Find records by condition
     */
    public function where($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Find first record by condition
     */
    public function findWhere($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions LIMIT 1";
        return $this->db->fetch($sql, $params);
    }

    /**
     * Create new record
     */
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update record
     */
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    /**
     * Count records
     */
    public function count($where = '', $params = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if ($where) {
            $sql .= " WHERE $where";
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Execute raw query
     */
    public function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Paginate results
     */
    public function paginate($page = 1, $perPage = 20, $where = '', $params = [])
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE $where";
        }
        $sql .= " LIMIT $perPage OFFSET $offset";
        
        $items = $this->db->fetchAll($sql, $params);
        $total = $this->count($where, $params);
        
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}

