<?php

class Notification extends Model
{
    public function create($user_id, $message)
    {
        $this->db->query("INSERT INTO notifications (user_id, message) VALUES (:uid, :msg)");
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':msg', $message);
        return $this->db->execute();
    }

    public function getUnreadCount($user_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = :uid AND is_read = 0");
        $this->db->bind(':uid', $user_id);
        $row = $this->db->single();
        return $row['count'];
    }

    public function getUserNotifications($user_id)
    {
        $this->db->query("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC");
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    public function markAsRead($user_id)
    {
        $this->db->query("UPDATE notifications SET is_read = 1 WHERE user_id = :uid");
        $this->db->bind(':uid', $user_id);
        return $this->db->execute();
    }
}