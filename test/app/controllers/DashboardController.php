<?php

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
    }

    public function index()
    {
        if ($_SESSION['role'] == 'Student') {
            $vehicleModel = $this->model('Vehicle');
            $reportModel = $this->model('Report');
            $notifModel = $this->model('Notification');

            $vehicles = $vehicleModel->getVehiclesByUserId($_SESSION['user_id']);
            $reports = $reportModel->getReportsByUserId($_SESSION['user_id']);
            $notifications = $notifModel->getUserNotifications($_SESSION['user_id']);

            $notifModel->markAsRead($_SESSION['user_id']);

            $this->view('dashboard/student', [
                'vehicles' => $vehicles, 
                'reports' => $reports, 
                'notifications' => $notifications
            ]);

            $this->view('dashboard/student', ['vehicles' => $vehicles, 'reports' => $reports]);
        } else {
            // Admin logic usually in AdminController or handled here
            header('Location: /admin/dashboard');
        }
    }

 // ... inside DashboardController class ...

    public function admin()
    {
        if ($_SESSION['role'] != 'Admin') {
            header('Location: ' . URLROOT . '/dashboard');
            exit();
        }

        $reportModel = $this->model('Report');
        $userModel = $this->model('User'); // Load User Model

        // Check 15-min timeout and auto-escalate
        $reportModel->sendTimeouts();

        // Fetch Data
        $reports = $reportModel->getAllReports();
        $users = $userModel->getAllUsers(); // You need to add this method to User.php

        $this->view('dashboard/admin', [
            'reports' => $reports, 
            'users' => $users
        ]);
    }

    public function deleteUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'Admin') {
            $userModel = $this->model('User');
            // Assuming deleteUser method exists in User model
            if ($userModel->deleteUser($_POST['user_id'])) {
                header('Location: ' . URLROOT . '/dashboard/admin');
            } else {
                die('Error deleting user');
            }
        }
    }
    // --- ADMIN: EDIT USER ---
    public function editUser($id)
    {
        if ($_SESSION['role'] != 'Admin') {
            header('Location: ' . URLROOT . '/dashboard');
            exit();
        }

        $userModel = $this->model('User');
        $vehicleModel = $this->model('Vehicle');

        $user = $userModel->getUserById($id); // You need to add this to User model
        $vehicles = $vehicleModel->getVehiclesByUserId($id);
        $vehicle = !empty($vehicles) ? $vehicles[0] : null; // Get primary car

        $this->view('dashboard/edit_user', [
            'user' => $user,
            'vehicle' => $vehicle
        ]);
    }

    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'Admin') {
            $userModel = $this->model('User');
            $vehicleModel = $this->model('Vehicle');

            // 1. Update User Info
            $userData = [
                'user_id' => $_POST['user_id'],
                'full_name' => filter_var($_POST['full_name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'phone_number' => $_POST['phone_number']
            ];
            $userModel->updateUser($userData); // Need to add to User Model

            // 2. Update Vehicle Info (if provided)
            if (isset($_POST['original_plate'])) {
                $vehicleData = [
                    'license_plate' => $_POST['license_plate'], // New Plate
                    'model' => $_POST['model'],
                    'color' => $_POST['color'],
                    'plate' => $_POST['original_plate'], // Old Plate (PK)
                    'owner_id' => $_POST['user_id'] // Needed for verification
                ];
                $vehicleModel->updateVehicleFull($vehicleData); // Need to add to Vehicle Model
            }

            header('Location: ' . URLROOT . '/dashboard/admin');
        }
    }
}