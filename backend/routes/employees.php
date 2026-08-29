<?php
ini_set('session.cookie_httponly', 1);
session_start();

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://localhost:5173';
require_once __DIR__ . '/header.php';
// Include database connection
include_once __DIR__ . '/../config/db-connect.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

// ==========================================
// 1. GET: Fetch All Employees
// 1. GET: Fetch All Employees with Performance Details
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT 
            e.id,
            e.first_name,
            e.last_name,
            CONCAT(e.first_name, ' ', e.last_name) AS name,
            e.email,
            e.job_title AS role,
            e.salary AS monthlySalary,
            e.hire_date AS joinedDate,
            e.status,
            COALESCE(pr.performance_score, 0.0) AS performanceScore,
            COALESCE(pr.rating_category, 'N/A') AS ratingCategory,
            COALESCE(pr.comments, 'No performance review recorded yet.') AS performanceComments,
            COALESCE(pr.review_date, '') AS lastReviewDate,
            COALESCE(d.name, 'Unassigned') AS department
          FROM employees e
          LEFT JOIN departments d 
            ON e.department_id = d.id
          LEFT JOIN performance_records pr 
            ON pr.id = (
                SELECT pr2.id 
                FROM performance_records pr2 
                WHERE pr2.employee_id = e.id 
                ORDER BY pr2.review_date DESC, pr2.id DESC 
                LIMIT 1
            )
          ORDER BY e.id ASC";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
        exit();
    }

    $employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
    http_response_code(200);
    echo json_encode($employees);
    exit();
}// ==========================================
// 2. POST: Create New Employee
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_data = file_get_contents("php://input");
    $data = json_decode($input_data, true);

    $first_name = $data['first_name'] ?? '';
    $last_name  = $data['last_name'] ?? '';
    $email      = $data['email'] ?? '';
    $job_title  = $data['job_title'] ?? $data['role'] ?? '';
    $dept_name  = $data['department'] ?? 'Software Development';
    $salary     = $data['salary'] ?? $data['monthlySalary'] ?? 0.00;
    $hire_date  = $data['hire_date'] ?? date('Y-m-d');
    $status     = $data['status'] ?? 'Active';

    if (empty($first_name) || empty($last_name) || empty($email)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "First name, last name, and email are required."]);
        exit();
    }

    // Map department string name to department_id foreign key
    $dept_id = 1; // Default fallback
    $dept_stmt = $conn->prepare("SELECT id FROM departments WHERE name = ? LIMIT 1");
    if ($dept_stmt) {
        $dept_stmt->bind_param("s", $dept_name);
        $dept_stmt->execute();
        $res = $dept_stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $dept_id = $row['id'];
        }
        $dept_stmt->close();
    }

    // Insert new employee record using prepared statements
    $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, email, job_title, department_id, salary, hire_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssssidss", $first_name, $last_name, $email, $job_title, $dept_id, $salary, $hire_date, $status);

    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;

        // --- AUTOMATIC ACTIVITY LOG FOR CREATION ---
        $logStmt = $conn->prepare("INSERT INTO activity_logs (description, category, category_class, target_profile) VALUES (?, ?, ?, ?)");
        if ($logStmt) {
            $description = "New Employee Added";
            $category = "Personnel";
            $categoryClass = "tag-system";
            $targetProfile = $first_name . " " . $last_name . " (" . $job_title . ")";

            $logStmt->bind_param("ssss", $description, $category, $categoryClass, $targetProfile);
            $logStmt->execute();
            $logStmt->close();
        }

        http_response_code(201);
        echo json_encode([
            "status" => "success", 
            "message" => "Employee created successfully.",
            "id" => $new_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database execute failed: " . $stmt->error]);
    }

    $stmt->close();
    exit();
}

// ==========================================
// 3. DELETE: Remove Employee Record
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing employee ID."]);
        exit();
    }

    // --- FETCH EMPLOYEE INFO BEFORE DELETION FOR ACTIVITY LOG ---
    $empName = "Employee #$id";
    $empRole = "Staff Member";

    $fetchStmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name, job_title FROM employees WHERE id = ?");
    if ($fetchStmt) {
        $fetchStmt->bind_param("i", $id);
        $fetchStmt->execute();
        $res = $fetchStmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $empName = $row['name'];
            $empRole = $row['job_title'];
        }
        $fetchStmt->close();
    }

    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // --- AUTOMATIC ACTIVITY LOG FOR DELETION ---
        $logStmt = $conn->prepare("INSERT INTO activity_logs (description, category, category_class, target_profile) VALUES (?, ?, ?, ?)");
        if ($logStmt) {
            $description = "Employee Profile Terminated";
            $category = "Personnel";
            $categoryClass = "tag-profile";
            $targetProfile = $empName . " (" . $empRole . ")";

            $logStmt->bind_param("ssss", $description, $category, $categoryClass, $targetProfile);
            $logStmt->execute();
            $logStmt->close();
        }

        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Employee deleted successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Delete failed: " . $stmt->error]);
    }

    $stmt->close();
    exit();
}
?>