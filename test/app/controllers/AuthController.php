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
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['university_id'] = $user['university_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                if ($user['role'] == 'Admin') {
                    header('Location: ' . URLROOT . '/dashboard/admin'); 
                } else {
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

            $data = [
                'university_id' => trim($_POST['university_id']),
                'full_name' => filter_var($_POST['full_name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'phone_number' => trim($_POST['phone_number']), // No sanitizing digits here, we need the +
                'password' => $_POST['password'],
                'license_plate' => trim($_POST['license_plate'] ?? ''),
                'vehicle_model' => trim($_POST['vehicle_model'] ?? ''),
                'vehicle_color' => trim($_POST['vehicle_color'] ?? ''),
                'role' => 'Student'
            ];

            $errors = [];

            // Admin Check
            if (strpos($data['university_id'], '#') !== false) {
                $data['role'] = 'Admin';
            } else {
                $data['role'] = 'Student';
            }

            // ID Validation
            if ($data['role'] == 'Student') {
                if (!preg_match('/^\d{4}\/\d{5}$/', $data['university_id'])) {
                    $errors[] = "Student ID must be in format YYYY/IDDDD (e.g. 2023/01234)";
                }
            } else {
                if (empty($data['university_id'])) {
                    $errors[] = "Admin ID cannot be empty.";
                }
            }

            // [CHANGED] STRICT PHONE VALIDATION
            // Must start with +20 and be followed by exactly 10 digits
            // Example matches: +201012345678
            // Example rejects: 01012345678, +1555..., +20123 (too short)
            if (!preg_match('/^\+20\d{10}$/', $data['phone_number'])) {
                $errors[] = "Phone number must be in format <b>+20</b> followed by 10 digits (e.g., +201012345678).";
            }

            // Password Validation
            if (strlen($data['password']) < 8 || !preg_match('/[\W_]/', $data['password'])) {
                $errors[] = "Password must be at least 8 characters long and contain at least one special character.";
            }

            // Vehicle Validation
            if ($data['role'] == 'Student') {
                if (empty($data['license_plate']) || empty($data['vehicle_model']) || empty($data['vehicle_color'])) {
                    $errors[] = "<strong>Vehicle details are required</strong> for Students.";
                }
            }

            if (!empty($errors)) {
                $data['error'] = implode('<br>', $errors);
                $this->view('auth/register', $data);
                return;
            }

            if ($userModel->register($data)) {
                
                if ($data['role'] == 'Admin') {
                    $db = Database::getInstance();
                    $db->query("UPDATE users SET role = 'Admin' WHERE university_id = :uid");
                    $db->bind(':uid', $data['university_id']);
                    $db->execute();
                }

                if ($data['role'] == 'Student') {
                    $newUser = $userModel->findUserByUniversityId($data['university_id']);
                    if ($newUser) {
                        $vehicleModel = $this->model('Vehicle');
                        $vehicleData = [
                            'license_plate' => filter_var($data['license_plate'], FILTER_SANITIZE_SPECIAL_CHARS),
                            'owner_id' => $newUser['user_id'],
                            'model' => filter_var($data['vehicle_model'], FILTER_SANITIZE_SPECIAL_CHARS),
                            'color' => filter_var($data['vehicle_color'], FILTER_SANITIZE_SPECIAL_CHARS)
                        ];
                        $vehicleModel->addVehicle($vehicleData);
                    }
                }

                header('Location: ' . URLROOT . '/auth/login');
            } else {
                $data['error'] = 'Registration failed. ID or Phone might already exist.';
                $this->view('auth/register', $data);
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . URLROOT . '/auth/login');
        exit();
    }
}