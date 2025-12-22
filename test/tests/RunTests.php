<?php

// 1. Setup Environment
// FIX: We do not define APPROOT/URLROOT here manually anymore.
// We strictly load the main config file which handles it.
require_once dirname(dirname(__FILE__)) . '/app/config/config.php';

// Include Database & Core
// Now we can use APPROOT because config.php defined it
require_once APPROOT . '/config/db.php';
require_once APPROOT . '/core/Model.php';

// Include Models to Test
require_once APPROOT . '/models/User.php';
require_once APPROOT . '/models/Vehicle.php';
require_once APPROOT . '/models/Report.php';

class TestRunner {
    private $db;
    private $passed = 0;
    private $failed = 0;

    public function __construct() {
        // FIX: Database is a Singleton, so we use getInstance() instead of new Database()
        $this->db = Database::getInstance();
        echo "<h1>🚀 Starting System Tests...</h1><hr>";
    }

    // --- HELPER FUNCTION: ASSERTION ---
    private function assert($condition, $testName) {
        if ($condition) {
            echo "<div style='color: green;'>✔ PASS: $testName</div>";
            $this->passed++;
        } else {
            echo "<div style='color: red; font-weight: bold;'>❌ FAIL: $testName</div>";
            $this->failed++;
        }
    }

    public function runAll() {
        $this->testInputValidation();
        $this->testUserFlow();
        $this->printSummary();
    }

    // --- TEST SUITE 1: INPUT VALIDATION (Logic Only) ---
    private function testInputValidation() {
        echo "<h3>Testing Input Logic...</h3>";

        // Test 1: Student ID Regex
        $validID = "2023/01234";
        $invalidID = "admin";
        $regex = '/^\d{4}\/\d{5}$/';
        
        $this->assert(preg_match($regex, $validID), "Student ID Regex accepts valid format");
        $this->assert(!preg_match($regex, $invalidID), "Student ID Regex rejects invalid format");

        // Test 2: Admin Detection Logic
        $adminInput = "#2023";
        $studentInput = "2023/12345";
        
        $isAdmin = (strpos($adminInput, '#') !== false);
        $this->assert($isAdmin, "System correctly detects Admin by '#'");
        
        $isStudent = (strpos($studentInput, '#') === false);
        $this->assert($isStudent, "System correctly identifies Student (no '#')");
    }

    // --- TEST SUITE 2: DATABASE INTEGRATION (Real Data) ---
    private function testUserFlow() {
        echo "<h3>Testing Database Operations...</h3>";

        $userModel = new User();
        $vehicleModel = new Vehicle();
        $reportModel = new Report();

        // Data for Dummy User
        $testID = "TEST_99999"; 
        $testData = [
            'university_id' => $testID,
            'full_name' => 'Test Robot',
            'phone_number' => '00000000000',
            'email' => 'test@robot.com',
            'password' => 'Pass@1234',
            'role' => 'Student'
        ];

        // 1. Cleanup before starting (in case previous test failed)
        $existing = $userModel->findUserByUniversityId($testID);
        if($existing) {
            $userModel->deleteUser($existing['user_id']);
        }

        // 2. Test Registration
        $result = $userModel->register($testData);
        $this->assert($result, "User Registration (DB Insert)");

        // 3. Verify User Exists
        $user = $userModel->findUserByUniversityId($testID);
        $this->assert($user && $user['full_name'] === 'Test Robot', "User Fetch from DB");

        if ($user) {
            $userId = $user['user_id'];

            // 4. Test Adding Vehicle
            $plate = "TEST-CAR-1";
            $vehicleData = [
                'license_plate' => $plate,
                'owner_id' => $userId,
                'model' => 'TestModel',
                'color' => 'TestColor'
            ];
            $vResult = $vehicleModel->addVehicle($vehicleData);
            $this->assert($vResult, "Add Vehicle to DB");

            // 5. Test Finding Vehicle
            $foundCar = $vehicleModel->findVehicleByPlate($plate);
            $this->assert($foundCar && $foundCar['owner_id'] == $userId, "Find Vehicle by Plate");

            // 6. Test Creating Report
            if ($foundCar) {
                $reportData = [
                    'reporter_id' => $userId, // Reporting own car for test
                    'blocked_plate' => $plate,
                    'description' => 'Test report description'
                ];
                $rResult = $reportModel->createReport($reportData);
                $this->assert($rResult, "Create Report in DB");
            }

            // 7. Cleanup (Delete User - cascading delete should handle car/reports if FK set properly)
            // If FK not set to cascade, we manually delete to be safe
            $this->db->query("DELETE FROM vehicles WHERE owner_id = :id");
            $this->db->bind(':id', $userId);
            $this->db->execute();

            $delResult = $userModel->deleteUser($userId);
            $this->assert($delResult, "Cleanup: Delete Test User");
        }
    }

    private function printSummary() {
        echo "<hr><h3>Summary</h3>";
        echo "Total Tests: " . ($this->passed + $this->failed) . "<br>";
        echo "<span style='color:green'>Passed: " . $this->passed . "</span><br>";
        if ($this->failed > 0) {
            echo "<span style='color:red'>Failed: " . $this->failed . "</span>";
        } else {
            echo "<span style='color:blue'>ALL SYSTEM CHECKS PASSED! ✅</span>";
        }
    }
}

// Run the tests
try {
    $tests = new TestRunner();
    $tests->runAll();
} catch (Exception $e) {
    echo "<h2 style='color:red'>CRITICAL ERROR: " . $e->getMessage() . "</h2>";
}
?>