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
        $reportModel->checkTimeout();

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
}
