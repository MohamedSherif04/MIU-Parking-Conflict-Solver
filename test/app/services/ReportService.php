<?php

require_once '../app/models/Report.php';

class ReportService
{
    private $observers = [];
    private $reportModel;

    public function __construct()
    {
        $this->reportModel = new Report();
    }

    public function attach($observer)
    {
        $this->observers[] = $observer;
    }

    public function createReport($data)
    {
        // Create the report in DB
        if ($this->reportModel->createReport($data)) {
            // Notify observers
            $this->notify($data);
            return true;
        }
        return false;
    }

    public function notify($data)
    {
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }
}
