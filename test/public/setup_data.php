<?php
// public/setup_data.php

// 1. Set Error Reporting to see any issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>Starting Database Setup...</h3>";

// 2. Define Paths relative to 'public/'
// This mimics the environment of index.php so relative paths in Models work
define('APPROOT', dirname(__DIR__) . '/app');
define('URLROOT', 'http://localhost/test/public'); // Adjust if your folder name differs
define('SITENAME', 'MIU Parking Conflict Solver');

// 3. Require Core Files
// We need to verify these files exist before requiring
$files = [
    '../app/config/db.php',
    '../app/core/Model.php',
    '../app/models/User.php',
    '../app/models/Vehicle.php',
    '../app/models/Report.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        require_once $file;
    } else {
        die("<p style='color:red'>Error: Could not find file: $file. Please check your folder structure.</p>");
    }
}

echo "<p>Core files loaded successfully.</p>";

// 4. Initialize Database
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    die("<p style='color:red'>Database Connection Failed: " . $e->getMessage() . "<br>Make sure you have created the database 'miu_parking_solver' in phpMyAdmin.</p>");
}

// 5. Clear Existing Data (Optional - prevents duplicates)
try {
    // Disable foreign key checks to allow deleting users who own vehicles
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $conn->exec("TRUNCATE TABLE audit_log");
    $conn->exec("TRUNCATE TABLE reports");
    $conn->exec("TRUNCATE TABLE vehicles");
    $conn->exec("TRUNCATE TABLE users");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p>Existing data cleared.</p>";
} catch (PDOException $e) {
    die("<p style='color:red'>Error clearing tables: " . $e->getMessage() . "<br>Did you import the schema.sql file?</p>");
}

// 6. Insert Data
$userModel = new User();
$vehicleModel = new Vehicle();

// --- Create Admin ---
$adminData = [
    'university_id' => '2020/00001',
    'full_name' => 'System Admin',
    'phone_number' => '01000000000',
    'password' => 'Admin@123'
];

if ($userModel->register($adminData)) {
    // Manually promote to Admin
    $conn->query("UPDATE users SET role = 'Admin' WHERE university_id = '2020/00001'");
    echo "<p style='color:green'>&#10004; Admin Created: <b>2020/00001</b> (Pass: Admin@123)</p>";
} else {
    echo "<p style='color:red'>&#10008; Failed to create Admin.</p>";
}

// --- Create Car Owner ---
$ownerData = [
    'university_id' => '2023/00100',
    'full_name' => 'John Owner',
    'phone_number' => '01234567890',
    'password' => 'Owner@123'
];

if ($userModel->register($ownerData)) {
    $owner = $userModel->findUserByUniversityId('2023/00100');
    
    // Add Vehicle
    $vehicleData = [
        'license_plate' => 'ABC-123',
        'owner_id' => $owner['user_id'],
        'model' => 'Toyota Corolla',
        'color' => 'Black'
    ];
    
    if ($vehicleModel->addVehicle($vehicleData)) {
        echo "<p style='color:green'>&#10004; Owner Created: <b>2023/00100</b> (Pass: Owner@123) with Vehicle ABC-123</p>";
    } else {
        echo "<p style='color:orange'>Owner created but Vehicle failed.</p>";
    }
} else {
    echo "<p style='color:red'>&#10008; Failed to create Owner.</p>";
}

// --- Create Reporter ---
$reporterData = [
    'university_id' => '2023/00200',
    'full_name' => 'Jane Reporter',
    'phone_number' => '01122334455',
    'password' => 'Reporter@123'
];

if ($userModel->register($reporterData)) {
    echo "<p style='color:green'>&#10004; Reporter Created: <b>2023/00200</b> (Pass: Reporter@123)</p>";
} else {
    echo "<p style='color:red'>&#10008; Failed to create Reporter.</p>";
}

echo "<h3>Setup Complete! <a href='index.php'>Go to Login</a></h3>";
?>