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
// 1. GET ENDPOINT: FETCH ALL DASHBOARD METRICS & REAL-TIME LOGS
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = [];

    // 1. Total Employees Count
    $resTotal = $conn->query("SELECT COUNT(*) AS total FROM employees");
    $response['total_employees'] = $resTotal ? (int)$resTotal->fetch_assoc()['total'] : 0;

    // 2. Employees Present Today (from attendance_records)
    $today = date('Y-m-d');
    $resPresent = $conn->query("SELECT COUNT(DISTINCT employee_id) AS present FROM attendance_records WHERE attendance_date = '$today' AND status IN ('present', 'late')");
    $response['employees_present'] = $resPresent ? (int)$resPresent->fetch_assoc()['present'] : 0;

    // 3. Employees Currently On Leave (from time_off_requests)
    // 3. Employees Currently On Leave (from time_off_requests or employees table)
$resOnLeave = $conn->query("
    SELECT COUNT(DISTINCT employee_id) AS on_leave 
    FROM time_off_requests 
    WHERE LOWER(status) = 'approved' 
");

$response['employees_on_leave'] = $resOnLeave ? (int)$resOnLeave->fetch_assoc()['on_leave'] : 0;

// Fallback: If no date-bound request matches, count employees with status = 'On Leave'
if ($response['employees_on_leave'] === 0) {
    $resStatusOnLeave = $conn->query("
        SELECT COUNT(*) AS on_leave 
        FROM employees 
        WHERE LOWER(status) = 'on leave'
    ");
    if ($resStatusOnLeave) {
        $response['employees_on_leave'] = (int)$resStatusOnLeave->fetch_assoc()['on_leave'];
    }
}
    // 4. Pending Leave Approvals
    $resPending = $conn->query("SELECT COUNT(*) AS pending FROM time_off_requests WHERE status = 'Pending'");
    $response['pending_approvals'] = $resPending ? (int)$resPending->fetch_assoc()['pending'] : 0;

    // 5. Weekly Attendance Trend (Past 5 Weekdays)
    $weeklyData = [];
    for ($i = 4; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayLabel = date('D d', strtotime($date));
        
        $resDay = $conn->query("SELECT COUNT(DISTINCT employee_id) AS cnt FROM attendance_records WHERE attendance_date = '$date' AND status IN ('present', 'late')");
        $cnt = $resDay ? (int)$resDay->fetch_assoc()['cnt'] : 0;
        
        $pct = ($response['total_employees'] > 0) ? round(($cnt / $response['total_employees']) * 100) : 0;
        
        $weeklyData[] = [
            'label' => $dayLabel,
            'present' => $cnt,
            'percentage' => $pct
        ];
    }
    $response['weekly_attendance'] = $weeklyData;

    // 6. Department Breakdown (Pie Chart)
    $resDept = $conn->query("
        SELECT COALESCE(d.name, 'Unassigned') AS name, COUNT(e.id) AS count 
        FROM employees e 
        LEFT JOIN departments d ON e.department_id = d.id 
        GROUP BY e.department_id, d.name 
        ORDER BY count DESC
    ");
    
    $deptData = [];
    $deptColors = [
        "Software Development" => "#3b82f6",
        "Quality Assurance"    => "#8b5cf6",
        "Customer Support"     => "#10b981",
        "Human Resources"      => "#06b6d4",
        "Sales"                => "#f59e0b",
        "Marketing"            => "#ef4444"
    ];

    if ($resDept) {
        while ($row = $resDept->fetch_assoc()) {
            $deptData[] = [
                'name'  => $row['name'],
                'count' => (int)$row['count'],
                'color' => $deptColors[$row['name']] ?? '#64748b'
            ];
        }
    }
    $response['department_breakdown'] = $deptData;

    // 7. Recent Administrative Activities Log (Real-time Audit Trail)
    $resLogs = $conn->query("SELECT id, description, category, category_class AS categoryClass, target_profile AS targetProfile, DATE_FORMAT(created_at, '%Y-%m-%d • %h:%i %p') AS timestamp FROM activity_logs ORDER BY id DESC LIMIT 10");
    $logs = [];
    if ($resLogs) {
        $logs = $resLogs->fetch_all(MYSQLI_ASSOC);
    }
    $response['activity_log'] = $logs;

    echo json_encode(["status" => "success", "data" => $response]);
    exit();
}

// -------------------------------------------------------------
// 2. POST ENDPOINT: LOG NEW ADMINISTRATIVE ACTIVITY
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $description   = $input['description'] ?? null;
    $category      = $input['category'] ?? 'System';
    $categoryClass = $input['category_class'] ?? 'tag-system';
    $targetProfile = $input['target_profile'] ?? 'N/A';

    if (!$description) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Description is required."]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO activity_logs (description, category, category_class, target_profile) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $description, $category, $categoryClass, $targetProfile);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Activity logged successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    $stmt->close();
    exit();
}