<?php

class Notification extends Model
{
    // [FIXED] Only ONE create method that handles both cases
    public function create($user_id, $message, $related_id = NULL)
    {
        $this->db->query("INSERT INTO notifications (user_id, message, related_id) VALUES (:uid, :msg, :rid)");
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':msg', $message);
        $this->db->bind(':rid', $related_id);
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

    // Check duplicate notification to prevent spamming
    public function notificationExistsForReport($report_id)
    {
        $this->db->query("SELECT * FROM notifications WHERE related_id = :rid");
        $this->db->bind(':rid', $report_id);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    // Delete notification (used when resolving/escalating)
    public function deleteByRelatedId($report_id)
    {
        $this->db->query("DELETE FROM notifications WHERE related_id = :rid");
        $this->db->bind(':rid', $report_id);
        return $this->db->execute();
    }
}