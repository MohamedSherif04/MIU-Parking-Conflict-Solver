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

    // --- TIMEOUT NOTIFICATION LOGIC ---
    public function sendTimeouts()
    {
        // 1. Get reports pending for more than 15 minutes
        $this->db->query("SELECT * FROM reports WHERE status = 'Pending' AND created_at < (NOW() - INTERVAL 3 MINUTE)");
        $pendingReports = $this->db->resultSet();

        // [FIXED] REQUIRE THE NOTIFICATION MODEL HERE
        require_once '../app/models/Notification.php';
        
        $notifModel = new Notification(); 

        foreach ($pendingReports as $report) {
            // Check if notification already sent to avoid spamming
            if (!$notifModel->notificationExistsForReport($report['report_id'])) {
                
                $msg = "Timeout Check: Report #" . $report['report_id'] . " has been active for 15+ mins. Is the issue resolved?";
                // Create notification linked to the report
                $notifModel->create($report['reporter_id'], $msg, $report['report_id']);
            }
        }
    }

    public function resolve($report_id)
    {
        $this->db->query("UPDATE reports SET status = 'Resolved', resolved_at = NOW() WHERE report_id = :id");
        $this->db->bind(':id', $report_id);
        return $this->db->execute();
    }
}