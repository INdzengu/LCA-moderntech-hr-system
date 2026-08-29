<?php
ini_set('session.cookie_httponly', 1);
session_start();
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://localhost:5173';
require_once __DIR__ . '/header.php';
include_once __DIR__ . '/../config/db-connect.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

// 1. FETCH ALL EMPLOYEES AND THEIR SALARY & HISTORY DETAILS
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT 
                e.id,
                CONCAT(e.first_name, ' ', e.last_name) AS name,
                'Staff Member' AS role,
                COALESCE(e.department_id, 'General') AS department,
                CAST(COALESCE(e.salary, e.salary, 0) AS UNSIGNED) AS monthlySalary
              FROM employees e
              ORDER BY e.id ASC";

    $result = $conn->query($query);
    if ($result) {
        $employees = $result->fetch_all(MYSQLI_ASSOC);
        
        // Fetch all salary history entries ordered by date descending
        $historyQuery = "SELECT id, employee_id, old_salary, new_salary, increase_percentage, effective_date, reason 
                        FROM salary_history 
                        ORDER BY effective_date DESC, id DESC";
        $historyResult = $conn->query($historyQuery);
        $historyData = $historyResult ? $historyResult->fetch_all(MYSQLI_ASSOC) : [];

        // Attach corresponding history list to each employee array
        foreach ($employees as &$emp) {
            $empId = $emp['id'];
            $emp['history'] = array_values(array_filter($historyData, function($h) use ($empId) {
                return (int)$h['employee_id'] === (int)$empId;
            }));
        }

        echo json_encode(["status" => "success", "data" => $employees]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit();
}

// 2. UPDATE EMPLOYEE SALARY, RECORD HISTORY & LOG ACTIVITY
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $rawId = $input['employee_id'] ?? null;
    $newSalary = $input['new_salary'] ?? null;
    $increasePercentage = $input['increase_percentage'] ?? 0;
    $reason = $input['reason'] ?? 'Annual Adjustment';

    if ($rawId === null || $newSalary === null) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing employee_id or new_salary."]);
        exit();
    }

    // Extract numbers only in case ID is passed as "E001" instead of 1
    $cleanId = preg_replace('/[^0-9]/', '', (string)$rawId);
    $employeeId = !empty($cleanId) ? intval($cleanId) : $rawId;

    // Check if column is 'monthly_salary' or 'salary' dynamically
    $checkCol = $conn->query("SHOW COLUMNS FROM employees LIKE 'monthly_salary'");
    $salaryCol = ($checkCol && $checkCol->num_rows > 0) ? "monthly_salary" : "salary";

    // Fetch employee info & current salary for logging and historical auditing
    $oldSalary = 0.00;
    $empName = "Employee #$employeeId";
    $empRole = "Staff Member";
    
    $infoStmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name, COALESCE(job_title, 'Staff Member') AS role, {$salaryCol} AS current_salary FROM employees WHERE id = ?");
    if ($infoStmt) {
        $infoStmt->bind_param("i", $employeeId);
        $infoStmt->execute();
        $res = $infoStmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $empName = $row['name'];
            $empRole = $row['role'];
            $oldSalary = (float)$row['current_salary'];
        }
        $infoStmt->close();
    }

    $stmt = $conn->prepare("UPDATE employees SET {$salaryCol} = ? WHERE id = ?");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("di", $newSalary, $employeeId);

    // In backend/routes/payroll.php (POST section)
if ($stmt->execute()) {
    $effectiveDate = date('Y-m-d');
    
    // Ensure createdBy resolves to a valid existing user ID in your users table
    $createdBy = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;

    // INSERT AUDIT ENTRY INTO salary_history TABLE
    $histStmt = $conn->prepare("INSERT INTO salary_history (employee_id, old_salary, new_salary, increase_percentage, effective_date, reason, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($histStmt) {
        $histStmt->bind_param("idddssi", $employeeId, $oldSalary, $newSalary, $increasePercentage, $effectiveDate, $reason, $createdBy);
        if (!$histStmt->execute()) {
            error_log("Salary history insert error: " . $histStmt->error);
        }
        $histStmt->close();
    }

    // AUTOMATIC ACTIVITY LOG FOR SALARY UPDATE
    $logStmt = $conn->prepare("INSERT INTO activity_logs (description, category, category_class, target_profile) VALUES (?, ?, ?, ?)");
    if ($logStmt) {
        $formattedSalary = number_format((float)$newSalary, 2);
        $description = "Salary Adjustment Updated (R{$formattedSalary})";
        $category = "Payroll";
        $categoryClass = "tag-payroll";
        $targetProfile = "{$empName} ({$empRole})";

        $logStmt->bind_param("ssss", $description, $category, $categoryClass, $targetProfile);
        $logStmt->execute();
        $logStmt->close();
    }

    echo json_encode(["status" => "success", "message" => "Salary and history updated successfully."]);
} else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    
    $stmt->close();
    exit();
}