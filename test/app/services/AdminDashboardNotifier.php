<?php

require_once '../app/services/Observer.php';

class AdminDashboardNotifier implements Observer
{
    public function update($reportData)
    {
        // In a real real-time app, this might push to a websocket
        // Here, we might just ensure an Admin Alert is created or logged
        // For this assignment, we'll simulate it by logging to a distinct file
        // or effectively, the "Dashboard" just reads the Reports table, 
        // but this could trigger an "Alert" table entry if we had one.

        // We will log to an admin_alerts file as a simulation
        $alert = "ADMIN ALERT: New conflict reported. Reporter: " . $reportData['reporter_id'] . ", Blocked Plate: " . $reportData['blocked_plate'];
        file_put_contents('../app/admin_alerts.txt', date('Y-m-d H:i:s') . " - " . $alert . PHP_EOL, FILE_APPEND);
    }
}
