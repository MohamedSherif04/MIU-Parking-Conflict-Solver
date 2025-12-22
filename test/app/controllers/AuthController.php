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

            // Sanitize Basic Info
            $data = [
                'university_id' => trim($_POST['university_id']),
                'full_name' => filter_var($_POST['full_name'], FILTER_SANITIZE_SPECIAL_CHARS),
                'phone_number' => trim($_POST['phone_number']),
                'password' => $_POST['password'],
                // Vehicle Data
                'license_plate' => trim($_POST['license_plate'] ?? ''),
                'vehicle_model' => trim($_POST['vehicle_model'] ?? ''),
                'vehicle_color' => trim($_POST['vehicle_color'] ?? ''),
                'role' => 'Student' // Default
            ];

            // Validation Container
            $errors = [];

            // --- 1. DETERMINE ROLE (Check for #) ---
            if (strpos($data['university_id'], '#') !== false) {
                $data['role'] = 'Admin';
            } else {
                $data['role'] = 'Student';
            }

            // --- 2. VALIDATE ID ---
            if ($data['role'] == 'Student') {
                // Student: Strict Format (2023/01234)
                if (!preg_match('/^\d{4}\/\d{5}$/', $data['university_id'])) {
                    $errors[] = "Student ID must be in format YYYY/IDDDD (e.g. 2023/01234)";
                }
            } else {
                // Admin: Just check it's not empty
                if (empty($data['university_id'])) {
                    $errors[] = "Admin ID cannot be empty.";
                }
            }

            // --- 3. VALIDATE PHONE & PASSWORD ---
            if (!preg_match('/^\d{1,11}$/', $data['phone_number'])) {
                $errors[] = "Phone number must be digits only and max 11 characters.";
            }

            if (strlen($data['password']) < 8 || !preg_match('/[\W_]/', $data['password'])) {
                $errors[] = "Password must be at least 8 characters long and contain at least one special character.";
            }

            // --- 4. VALIDATE VEHICLE (MANDATORY FOR STUDENTS) ---
            if ($data['role'] == 'Student') {
                if (empty($data['license_plate']) || empty($data['vehicle_model']) || empty($data['vehicle_color'])) {
                    $errors[] = "<strong>Vehicle details are required</strong> for Students. You must register a car.";
                }
            }

            // Return Errors if any
            if (!empty($errors)) {
                $data['error'] = implode('<br>', $errors);
                $this->view('auth/register', $data);
                return;
            }

            // --- 5. EXECUTE REGISTRATION ---
            if ($userModel->register($data)) {
                
                // A. Handle Admin Role Update
                // (User model defaults to 'Student', so we update it manually if Admin)
                if ($data['role'] == 'Admin') {
                    $db = Database::getInstance();
                    $db->query("UPDATE users SET role = 'Admin' WHERE university_id = :uid");
                    $db->bind(':uid', $data['university_id']);
                    $db->execute();
                }

                // B. Handle Vehicle Addition (Only if Student)
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