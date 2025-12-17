<?php

require_once '../app/services/Observer.php';
require_once '../app/models/Vehicle.php';
require_once '../app/models/Notification.php';

class InAppNotifier implements Observer
{
    public function update($reportData)
    {
        // 1. Find the owner of the blocked plate
        $vehicleModel = new Vehicle();
        $vehicle = $vehicleModel->findVehicleByPlate($reportData['blocked_plate']);

        if ($vehicle) {
            // 2. Create the in-app notification for that owner
            $notifModel = new Notification();
            $message = "URGENT: Your vehicle (" . $reportData['blocked_plate'] . ") is reported as blocking. Please move it immediately.";
            
            $notifModel->create($vehicle['owner_id'], $message);
        }
    }
}