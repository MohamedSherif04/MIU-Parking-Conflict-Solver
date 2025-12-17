<?php

require_once '../app/services/Observer.php';

class SMSNotifier implements Observer
{
    public function update($reportData)
    {
        // Simulate sending SMS
        // In a real app, this would use Twilio or similar
        // For now, we'll just log it or maybe write to a file
        $logDetails = "SMS sent to owner of vehicle " . $reportData['blocked_plate'] . ": You have been reported for blocking.";

        // Optionally allow logging via AuditLog if passed or instantiated
        // For simplicity, we assume this action is just a simulation output
        // or we can write to a specific log file in the system
        file_put_contents('../app/sms_log.txt', date('Y-m-d H:i:s') . " - " . $logDetails . PHP_EOL, FILE_APPEND);
    }
}
