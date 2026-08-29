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

// -------------------------------------------------------------
// 1. GET ENDPOINT: FETCH ALL LEAVE REQUESTS
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT 
                l.id AS request_id,
                l.employee_id,
                COALESCE(CONCAT(e.first_name, ' ', e.last_name), 'Unknown Employee') AS name,
                COALESCE(l.request_type, 'Annual Leave') AS type,
                DATEDIFF(COALESCE(l.end_date, CURRENT_DATE), COALESCE(l.start_date, CURRENT_DATE)) + 1 AS days,
                l.status,
                COALESCE(l.reason, '') AS reason,
                COALESCE(l.created_at, CURRENT_TIMESTAMP) AS dateSubmitted,
                l.start_date AS startDate,
                l.end_date AS endDate
              FROM time_off_requests l
              LEFT JOIN employees e ON l.employee_id = e.id
              ORDER BY l.id DESC";

    $result = $conn->query($query);

    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode([
            "status" => "success",
            "count" => count($data),
            "data" => $data
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
    }
    exit();
}

// -------------------------------------------------------------
// 2. POST ENDPOINT: UPDATE STATUS WITH MULTI-TABLE TRANSACTION
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $requestId = $input['request_id'] ?? null;
    $status = $input['status'] ?? null;

    if (!$requestId || !$status) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing request_id or status."]);
        exit();
    }

    // 1. START DATABASE TRANSACTION
    $conn->begin_transaction();

    try {
        // Query 1: Fetch employee details and request info before updating for the activity log
        $fetchStmt = $conn->prepare("
            SELECT 
                l.employee_id, 
                COALESCE(l.request_type, 'Leave') AS request_type,
                CONCAT(e.first_name, ' ', e.last_name) AS emp_name,
                COALESCE(e.job_title, 'Staff Member') AS emp_role
            FROM time_off_requests l
            LEFT JOIN employees e ON l.employee_id = e.id
            WHERE l.id = ?
        ");
        $fetchStmt->bind_param("i", $requestId);
        $fetchStmt->execute();
        $res = $fetchStmt->get_result();
        $leaveData = $res->fetch_assoc();
        $fetchStmt->close();

        // Query 2: Update the leave request status in time_off_requests
        $stmt1 = $conn->prepare("UPDATE time_off_requests SET status = ? WHERE id = ?");
        $stmt1->bind_param("si", $status, $requestId);
        if (!$stmt1->execute()) {
            throw new Exception("Failed to update leave request: " . $stmt1->error);
        }
        $stmt1->close();

        if ($leaveData) {
            $empId = $leaveData['employee_id'];
            
            // Query 3: Sync employee status based on approval/rejection
            $empStatus = ($status === 'Approved') ? 'On Leave' : 'Active';
            
            $stmt3 = $conn->prepare("UPDATE employees SET status = ? WHERE id = ?");
            $stmt3->bind_param("si", $empStatus, $empId);
            if (!$stmt3->execute()) {
                throw new Exception("Failed to update employee status: " . $stmt3->error);
            }
            $stmt3->close();

            // Query 4: Insert record into activity_logs
            $logStmt = $conn->prepare("INSERT INTO activity_logs (description, category, category_class, target_profile) VALUES (?, ?, ?, ?)");
            if ($logStmt) {
                $reqType = $leaveData['request_type'];
                $empName = $leaveData['emp_name'] ?? "Employee #$empId";
                $empRole = $leaveData['emp_role'];

                $description = "{$reqType} Request {$status}";
                $category = "Leave Management";
                $categoryClass = "tag-leave";
                $targetProfile = "{$empName} ({$empRole})";

                $logStmt->bind_param("ssss", $description, $category, $categoryClass, $targetProfile);
                if (!$logStmt->execute()) {
                    throw new Exception("Failed to record activity log: " . $logStmt->error);
                }
                $logStmt->close();
            }
        }

        // 2. COMMIT TRANSACTION
        $conn->commit();

        echo json_encode([
            "status" => "success", 
            "message" => "Leave status and employee profile updated successfully."
        ]);

    } catch (Exception $e) {
        // 3. ROLLBACK TRANSACTION
        $conn->rollback();

        http_response_code(500);
        echo json_encode([
            "status" => "error", 
            "message" => "Transaction failed: " . $e->getMessage()
        ]);
    }

    exit();
}