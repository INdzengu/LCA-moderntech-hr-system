<?php
class AttendanceModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Fetch employee list dynamically for the visual target dropdown
     */
    public function getEmployeesList() {
        $query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM employees ORDER BY id ASC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Fetch accurate individual or aggregated monthly attendance logs
     */
    public function getMonthlyAttendance($year, $month, $employeeId = 'all') {
        $startDate = sprintf("%04d-%02d-01", $year, $month);
        $endDate = date("Y-m-t", strtotime($startDate));

        if ($employeeId !== 'all' && !empty($employeeId)) {
            // Individual Employee Exact Status Query
            $query = "SELECT 
                        a.attendance_date AS date,
                        a.status AS status
                      FROM attendance_records a
                      WHERE a.employee_id = ? 
                        AND a.attendance_date BETWEEN ? AND ?";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sss", $employeeId, $startDate, $endDate);
        } else {
            // Company-Wide Presence Ratio vs Total Active Employees
            $query = "SELECT 
                        a.attendance_date AS date,
                        COUNT(CASE WHEN a.status = 'present' THEN 1 END) AS present_count,
                        (SELECT COUNT(*) FROM employees e WHERE e.hire_date <= a.attendance_date) AS total_count
                      FROM attendance_records a
                      WHERE a.attendance_date BETWEEN ? AND ?
                      GROUP BY a.attendance_date";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $startDate, $endDate);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }
}