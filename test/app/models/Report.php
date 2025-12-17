<?php

class Report extends Model
{
    public function createReport($data)
    {
        $this->db->query("INSERT INTO reports (reporter_id, blocked_plate, description, status) VALUES (:reporter, :plate, :desc, 'Pending')");
        $this->db->bind(':reporter', $data['reporter_id']);
        $this->db->bind(':plate', $data['blocked_plate']);
        $this->db->bind(':desc', $data['description']);
        if ($this->db->execute()) {
            return true;
        }
        return false;
    }

    public function getReportsByUserId($user_id)
    {
        $this->db->query("SELECT * FROM reports WHERE reporter_id = :uid ORDER BY created_at DESC");
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    public function getAllReports()
    {
        $this->db->query("SELECT reports.*, users.full_name as reporter_name FROM reports JOIN users ON reports.reporter_id = users.user_id ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function updateStatus($report_id, $status)
    {
        $this->db->query("UPDATE reports SET status = :status WHERE report_id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $report_id);
        return $this->db->execute();
    }

    // Logic: If created_at > 15 mins AND status is Pending, update status to Escalated
    public function checkTimeout()
    {
        $this->db->query("UPDATE reports SET status = 'Escalated' WHERE status = 'Pending' AND created_at < (NOW() - INTERVAL 3 MINUTE)");
        return $this->db->execute();
    }

    public function resolve($report_id)
    {
        $this->db->query("UPDATE reports SET status = 'Resolved', resolved_at = NOW() WHERE report_id = :id");
        $this->db->bind(':id', $report_id);
        return $this->db->execute();
    }
}
