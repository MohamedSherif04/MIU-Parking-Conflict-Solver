<?php

class VehicleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit();
        }
    }

    public function edit($plate)
    {
        $vehicleModel = $this->model('Vehicle');
        $vehicle = $vehicleModel->findVehicleByPlate($plate);

        // Security Check: Ensure vehicle exists and belongs to the logged-in user
        if (!$vehicle || $vehicle['owner_id'] != $_SESSION['user_id']) {
            die('Access Denied: You do not own this vehicle.');
        }

        // Handle Form Submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'license_plate' => $plate,
                'owner_id' => $_SESSION['user_id'],
                'model' => filter_var($_POST['model'], FILTER_SANITIZE_SPECIAL_CHARS),
                'color' => filter_var($_POST['color'], FILTER_SANITIZE_SPECIAL_CHARS),
                'vehicle' => $vehicle // Pass back vehicle data in case of error (not used here but good practice)
            ];

            if ($vehicleModel->updateVehicle($data)) {
                // Redirect back to Dashboard on success
                header('Location: ' . URLROOT . '/dashboard/index');
            } else {
                die('Error updating vehicle');
            }
        } else {
            // Load the View
            $this->view('vehicle/edit', ['vehicle' => $vehicle]);
        }
    }
}