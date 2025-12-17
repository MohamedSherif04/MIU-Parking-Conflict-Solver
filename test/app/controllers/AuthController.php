<?php

class AuthController extends Controller
{
    public function index()
    {
        $this->login();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            $user = $userModel->login($_POST['university_id'], $_POST['password']);

            if ($user) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['university_id'] = $user['university_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

              if ($user['role'] == 'Admin') {
    // CHANGED: Point to DashboardController -> admin()
    header('Location: ' . URLROOT . '/dashboard/admin'); 
} else {
    // CHANGED: Point to DashboardController -> index()
    header('Location: ' . URLROOT . '/dashboard/index');
}
exit();
            } else {
                $data = ['error' => 'Invalid credentials'];
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');

            // Sanitize
            $data = [
                'university_id' => trim($_POST['university_id']),
                'full_name' => filter_var($_POST['full_name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'phone_number' => trim($_POST['phone_number']),
                'password' => $_POST['password'],
                'license_plate' => trim($_POST['license_plate'] ?? ''),
                'vehicle_model' => trim($_POST['vehicle_model'] ?? ''),
                'vehicle_color' => trim($_POST['vehicle_color'] ?? '')
            ];

            // Validation
            $errors = [];

            // ID Validation: 2023/01234
            if (!preg_match('/^\d{4}\/\d{5}$/', $data['university_id'])) {
                $errors[] = "University ID must be in format YYYY/IDDDD (e.g. 2023/01234)";
            }

            // Phone Validation: numbers only, max 11
            if (!preg_match('/^\d{1,11}$/', $data['phone_number'])) {
                $errors[] = "Phone number must be digits only and max 11 characters.";
            }

            // Password Validation: Strong (8+ chars, special char)
            // Regex: At least 8 chars, at least one special char/symbol
            if (strlen($data['password']) < 8 || !preg_match('/[\W_]/', $data['password'])) {
                $errors[] = "Password must be at least 8 characters long and contain at least one special character.";
            }

            if (!empty($errors)) {
                $data['error'] = implode('<br>', $errors);
                $this->view('auth/register', $data);
                return;
            }

            // Attempt Registration
            // Note: Ideally wrap in transaction
            if ($userModel->register($data)) {
                // If vehicle info provided, add it
                if (!empty($data['license_plate'])) {
                    // Get the new user ID
                    $newUser = $userModel->findUserByUniversityId($data['university_id']);
                    if ($newUser) {
                        $vehicleModel = $this->model('Vehicle');
                        $vehicleData = [
                            'license_plate' => filter_var($data['license_plate'], FILTER_SANITIZE_SPECIAL_CHARS),
                            'owner_id' => $newUser['user_id'],
                            'model' => filter_var($data['vehicle_model'], FILTER_SANITIZE_SPECIAL_CHARS),
                            'color' => filter_var($data['vehicle_color'], FILTER_SANITIZE_SPECIAL_CHARS)
                        ];
                        // Try adding vehicle, ignore failure for now or log it
                        $vehicleModel->addVehicle($vehicleData);
                    }
                }

                header('Location: ' . URLROOT . '/auth/login');
            } else {
                $data['error'] = 'Registration failed. ID might exist.';
                $this->view('auth/register', $data);
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function logout()
{
    session_destroy();
    // FIXED: Use URLROOT to ensure the correct path regardless of folder structure
    header('Location: ' . URLROOT . '/auth/login'); 
    exit();
}}