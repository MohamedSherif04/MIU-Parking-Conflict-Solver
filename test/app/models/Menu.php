<?php

class Menu extends Model
{
    public function getMenuItems($role)
    {
        // 1. Fetch items allowed for this Role OR 'All'
        // 2. Order them by 'order_index' so they appear in the right sequence
        $sql = "SELECT * FROM menu_items 
                WHERE (role_access = :role OR role_access = 'All') 
                ORDER BY order_index ASC";
        
        $this->db->query($sql);
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }
}