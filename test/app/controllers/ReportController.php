<?php

require_once '../app/services/ReportService.php';
require_once '../app/services/SMSNotifier.php';
require_once '../app/services/AdminDashboardNotifier.php';
require_once '../app/services/InAppNotifier.php';

class ReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function submit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $plate = filter_var($_POST['blocked_plate'], FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);

            // 1. Search DB for the vehicle
            $vehicleModel = $this->model('Vehicle');
            $vehicle = $vehicleModel->findVehicleByPlate($plate);

            // 2. Alert if Not Found
            if (!$vehicle) {
                // CHANGED: Instead of die(), show a JS Alert and redirect back
                echo "<script>
                        alert('Error: License Plate ($plate) not found in database.');
                        window.history.back();
                      </script>";
                exit();
            }

            $data = [
                'reporter_id' => $_SESSION['user_id'],
                'blocked_plate' => $plate,
                'description' => $description
            ];

            // Use ReportService for Observer Pattern
            $reportService = new ReportService();
            $reportService->attach(new SMSNotifier());
            $reportService->attach(new AdminDashboardNotifier());
            $reportService->attach(new InAppNotifier());

            if ($reportService->createReport($data)) {
                // Log action
                $auditModel = $this->model('AuditLog');
                $auditModel->log($_SESSION['user_id'], 'Report Submitted', "Reported plate " . $data['blocked_plate']);

                header('Location: ' . URLROOT . '/dashboard/index');
            } else {
                die("Error submitting report");
            }
        }
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');
            $plate = $_POST['plate'];
            $vehicleModel = $this->model('Vehicle');
            $vehicle = $vehicleModel->findVehicleByPlate($plate);

            if ($vehicle) {
                echo json_encode(['status' => 'found', 'model' => $vehicle['model'], 'color' => $vehicle['color']]);
            } else {
                echo json_encode(['status' => 'not_found']);
            }
            exit();
        }
    }

    public function resolve()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'Admin') {
            $report_id = $_POST['report_id'];
            $reportModel = $this->model('Report');
            if ($reportModel->resolve($report_id)) {
                $auditModel = $this->model('AuditLog');
                $auditModel->log($_SESSION['user_id'], 'Report Resolved', "Resolved report " . $report_id);
                header('Location: ' . URLROOT . '/dashboard/admin');
            }
        }
    }

    public function addVehicle()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $vehicleModel = $this->model('Vehicle');
            $data = [
                'license_plate' => filter_var($_POST['license_plate'], FILTER_SANITIZE_SPECIAL_CHARS),
                'owner_id' => $_SESSION['user_id'],
                'model' => filter_var($_POST['model'], FILTER_SANITIZE_SPECIAL_CHARS),
                'color' => filter_var($_POST['color'], FILTER_SANITIZE_SPECIAL_CHARS)
            ];

            if ($vehicleModel->addVehicle($data)) {
                header('Location: ' . URLROOT . '/dashboard/index');
            } else {
                die("Error adding vehicle");
            }
        }
    }
    // Reporter clicks True/False on the notification
    public function verify()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $report_id = $_POST['report_id'];
            $status = $_POST['status']; // 'true' or 'false'
            
            $reportModel = $this->model('Report');

            if ($status == 'true') {
                // Reporter confirmed it's resolved
                $reportModel->resolve($report_id);
                // Optionally notify admin "Reporter Confirmed Resolution"
            } else {
                // Reporter says NOT resolved -> Escalate to Admin
                $reportModel->updateStatus($report_id, 'Escalated');
                
                // Add Notification for Admin
                $notifModel = $this->model('Notification');
                // Assuming Admin ID is 1 or finding admin ID logic
                $notifModel->create(1, "ESCALATION: Reporter denied resolution for Report #$report_id");
            }

            header('Location: ' . URLROOT . '/dashboard/index');
        }
    }
    public function verify()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $report_id = $_POST['report_id'];
            $reportModel = $this->model('Report');
            $notifModel = $this->model('Notification');

            // Check if checkbox is checked
            if (isset($_POST['is_solved']) && $_POST['is_solved'] == 'yes') {
                // TRUE -> RESOLVED
                $reportModel->resolve($report_id);
                // Delete the notification so it doesn't show again
                $notifModel->deleteByRelatedId($report_id); // Add this method to Notification model
            } else {
                // FALSE (Unchecked) -> ESCALATE
                $reportModel->updateStatus($report_id, 'Escalated');
                $notifModel->deleteByRelatedId($report_id); // Remove notification
                
                // Notify Admin
                $notifModel->create(1, "ESCALATION: Reporter marked Report #$report_id as NOT resolved.", $report_id);
            }

            header('Location: ' . URLROOT . '/dashboard/index');
        }
    }
}