<?php
// backend/models/EmployeeModel.php

class EmployeeModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Fetch all employees with joined department names
     */
    public function getAll() {
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
                    e.performance_score AS performanceScore,
                    COALESCE(d.name, 'Unassigned') AS department
                  FROM employees e
                  LEFT JOIN departments d ON e.department_id = d.id
                  ORDER BY e.id ASC";

        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            return [];
        }

        // Fetch all rows as an associative array
        $employees = mysqli_fetch_all($result, MYSQLI_ASSOC);


        return $employees;
    }

    /**
     * Create a new employee record using MySQLi prepared statement
     */
    public function create($data) {
        $query = "INSERT INTO employees 
                    (first_name, last_name, email, job_title, department_id, salary, hire_date, status, performance_score) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $query);

        if (!$stmt) {
            return false;
        }

        // Types: s = string, i = integer, d = double/decimal
        // 's s s s i d s s d'
        mysqli_stmt_bind_param(
            $stmt, 
            "ssssidssd", 
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['job_title'],
            $data['department_id'],
            $data['salary'],
            $data['hire_date'],
            $data['status'],
            $data['performance_score']
        );

        $executed = mysqli_stmt_execute($stmt);

        if ($executed) {
            $insertId = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt);
            return $insertId;
        }

        mysqli_stmt_close($stmt);
        return false;
    }
}
?>