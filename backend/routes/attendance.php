<?php
ini_set('session.cookie_httponly', 1);
session_start();

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://localhost:5173';
require_once __DIR__ . '/header.php';
include_once __DIR__ . '/../config/db-connect.php';
require_once __DIR__ . '/../models/AttendanceModel.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

$attendanceModel = new AttendanceModel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return employee dropdown list
    if (isset($_GET['action']) && $_GET['action'] === 'get_employees') {
        try {
            $employees = $attendanceModel->getEmployeesList();
            http_response_code(200);
            echo json_encode(["status" => "success", "data" => $employees]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit();
    }

    // Monthly calendar heat-map data fetch
    $year = $_GET['year'] ?? 2026;
    $month = $_GET['month'] ?? 8;
    $employeeId = $_GET['employee_id'] ?? 'all';

    try {
        $data = $attendanceModel->getMonthlyAttendance($year, $month, $employeeId);
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $data]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit();
}