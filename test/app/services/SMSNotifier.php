<?php

require_once '../app/services/Observer.php';
require_once '../app/models/Vehicle.php';
require_once '../app/models/User.php';

class SMSNotifier implements Observer
{
    // PASTE YOUR N8N WEBHOOK URL HERE
    // Example: https://n8n.your-server.com/webhook/send-alert
    private $n8nWebhookUrl = 'https://mohamedsherifmiu.app.n8n.cloud/webhook/send-alert';

    public function update($reportData)
    {
        // 1. Find the Vehicle Owner
        $vehicleModel = new Vehicle();
        $vehicle = $vehicleModel->findVehicleByPlate($reportData['blocked_plate']);

        if ($vehicle) {
            // 2. Find the Owner's Phone Number
            $userModel = new User();
            $owner = $userModel->getUserById($vehicle['owner_id']);

            if (!empty($owner['phone_number'])) {
                // 3. Prepare Data for n8n
                $payload = [
                    'phone_number' => $owner['phone_number'],
                    'message'      => "URGENT: Your vehicle ({$reportData['blocked_plate']}) is blocking another car. Please move it immediately.",
                    'plate'        => $reportData['blocked_plate']
                ];

                // 4. Send to n8n via cURL
                $this->sendToN8n($payload);
            }
        }
    }

    private function sendToN8n($payload)
    {
        $ch = curl_init($this->n8nWebhookUrl);
        
        // Setup request to send JSON
        $jsonData = json_encode($payload);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            file_put_contents('../app/sms_log.txt', date('Y-m-d H:i:s') . " - n8n Error: " . $error_msg . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents('../app/sms_log.txt', date('Y-m-d H:i:s') . " - Sent to n8n. Code: $httpCode. Response: " . $response . PHP_EOL, FILE_APPEND);
        }

        curl_close($ch);
    }
}