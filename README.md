# ModernTech HR System

## Description

ModernTech HR System is a web-based Human Resources management application designed to help organisations manage employee information, attendance, leave requests, payroll, salary history, and employee performance records in one centralised system. The application provides different views and permissions for administrators, HR staff, managers, and employees through role-based access control.

The system consists of a Vue.js frontend connected to a PHP REST-style backend and a MySQL database. HR staff can manage organisation-wide HR information, while managers have access to information relating to their teams and employees have access to their own HR information.

## Tech Stack

- **Vue.js 3** – Used to build the interactive single-page application and user interface.
- **Vite** – Used as the frontend development server and build tool for the Vue.js application.
- **PHP** – Used to build the backend API, handle business logic, authentication, validation, and role-based access control.
- **MySQL** – Used to store employee, department, attendance, leave, payroll, salary history, performance, and user authentication data.
- **XAMPP** – Provides the local Apache web server and MySQL database environment required to run the PHP backend.
- **phpMyAdmin** – Used to create, manage, and populate the MySQL database.
- **HTML** – Used through Vue components to structure the application's user interface.
- **CSS** – Used to style the application and provide responsive layouts.
- **Bootstrap / Bootstrap Icons** – Used for interface components, responsive styling, and icons.

## Prerequisites

Before running the project, make sure the following software is installed:

- [Node.js](https://nodejs.org/)
- [XAMPP](https://www.apachefriends.org/)
- A modern web browser such as Google Chrome
- Git
- phpMyAdmin

### Required XAMPP Configuration

The following services must be running in the XAMPP Control Panel:

- **Apache**
- **MySQL**

The MySQL server must be configured to run on **port 3307**.

The project uses PHP for its backend API, so Apache must be running before making requests to the backend.

## Database Setup

The ModernTech HR System uses a MySQL database containing seven main HR tables and an activity log table.

### Step 1: Start XAMPP

Open the XAMPP Control Panel and start:

Apache
MySQL

Confirm that MySQL is running on port:

3307

### Step 2: Open phpMyAdmin

Open the following address in your browser:

http://localhost/phpmyadmin

### Step 3: Create the Database

Create a new database called:

moderntech_hr

Select the newly created database.

### Step 4: Create the Tables

DROP TABLE IF EXISTS performance_records; DROP TABLE IF EXISTS salary_history; DROP TABLE IF EXISTS attendance_records; DROP TABLE IF EXISTS time_off_requests; DROP TABLE IF EXISTS users; DROP TABLE IF EXISTS employees; DROP TABLE IF EXISTS departments; CREATE TABLE departments ( id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100) NOT NULL UNIQUE, location VARCHAR(100) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ); CREATE TABLE employees ( id INT PRIMARY KEY AUTO_INCREMENT, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE, job_title VARCHAR(100) NOT NULL, department_id INT NOT NULL, salary DECIMAL(10,2) NOT NULL, hire_date DATE NOT NULL, status ENUM('Active', 'On Leave', 'Inactive') DEFAULT 'Active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT, INDEX idx_employee_department (department_id), INDEX idx_employee_status (status) ); CREATE TABLE users ( id INT PRIMARY KEY AUTO_INCREMENT, email VARCHAR(255) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, role ENUM('admin', 'hr_staff', 'manager', 'employee') NOT NULL DEFAULT 'employee', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_user_email (email) ); CREATE TABLE attendance_records ( id INT PRIMARY KEY AUTO_INCREMENT, employee_id INT NOT NULL, attendance_date DATE NOT NULL, status ENUM('present', 'late', 'absent', 'leave', 'sick') NOT NULL, notes VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE, UNIQUE KEY unique_employee_date (employee_id, attendance_date), INDEX idx_attendance_employee (employee_id), INDEX idx_attendance_date (attendance_date), INDEX idx_attendance_status (status) ); CREATE TABLE time_off_requests ( id INT PRIMARY KEY AUTO_INCREMENT, employee_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, reason VARCHAR(255), request_type ENUM( 'Sick', 'Annual', 'Personal', 'Paternity', 'Maternity' ) NOT NULL, status ENUM( 'Pending', 'Approved', 'Denied' ) NOT NULL DEFAULT 'Pending', date_submitted DATE NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE, CHECK (end_date >= start_date), INDEX idx_timeoff_employee (employee_id), INDEX idx_timeoff_status (status), INDEX idx_timeoff_dates (start_date, end_date) ); CREATE TABLE salary_history ( id INT PRIMARY KEY AUTO_INCREMENT, employee_id INT NOT NULL, old_salary DECIMAL(10,2), new_salary DECIMAL(10,2) NOT NULL, increase_percentage DECIMAL(5,2) DEFAULT 0.00, effective_date DATE NOT NULL, reason VARCHAR(255) NOT NULL, created_by INT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE, FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL, INDEX idx_salary_employee (employee_id), INDEX idx_salary_date (effective_date) ); CREATE TABLE performance_records ( id INT PRIMARY KEY AUTO_INCREMENT, employee_id INT NOT NULL, performance_score DECIMAL(2,1) NOT NULL, review_date DATE NOT NULL, reviewer_id INT, comments TEXT, rating_category ENUM( 'Excellent', 'Good', 'Satisfactory', 'Needs Improvement', 'Poor' ) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE, FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE SET NULL, CHECK (performance_score >= 0.0 AND performance_score <= 5.0), INDEX idx_performance_employee (employee_id), INDEX idx_performance_date (review_date) );

/\* =========================================================

1.  DEPARTMENTS
    ========================================================= \*/

INSERT INTO departments (name, location) VALUES
('Software Development', 'Main Office'),
('Quality Assurance', 'Main Office'),
('Customer Support', 'Support Center'),
('Human Resources', 'Main Office'),
('Sales', 'Sales Hub'),
('Marketing', 'Main Office');

/_ ========================================================= 2. USERS
========================================================= _/

INSERT INTO users (email, password_hash, role) VALUES
('l.park@moderntech.com',
'$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36MM4YAu',
'admin'),

('i.larsson@moderntech.com',
'$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36MM4YAu',
'hr_staff'),

('j.okafor@moderntech.com',
'$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36MM4YAu',
'manager'),

('c.rivera@moderntech.com',
'$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36MM4YAu',
'manager'),

('t.brandt@moderntech.com',
'$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36MM4YAu',
'manager');

/_ ========================================================= 3. EMPLOYEES - 32 EMPLOYEES
========================================================= _/

INSERT INTO employees (
first_name,
last_name,
email,
job_title,
department_id,
salary,
hire_date,
status
) VALUES

/_ Software Development _/
('Sarah', 'Mitchell', 's.mitchell@moderntech.com',
'Senior Developer', 1, 45000.00, '2015-03-12', 'On Leave'),

('James', 'Okafor', 'j.okafor@moderntech.com',
'Engineering Manager', 1, 55000.00, '2013-07-08', 'Active'),

('Fatima', 'Al-Rashid', 'f.alrashid@moderntech.com',
'Full Stack Developer', 1, 42000.00, '2020-01-06', 'Active'),

('Sophie', 'Dubois', 's.dubois@moderntech.com',
'Frontend Developer', 1, 36000.00, '2021-03-22', 'Active'),

('Marcus', 'Chen', 'm.chen@moderntech.com',
'DevOps Engineer', 1, 43000.00, '2019-09-30', 'Active'),

('Thabo', 'Mokoena', 't.mokoena@moderntech.com',
'Backend Developer', 1, 39000.00, '2022-02-14', 'Active'),

('Emily', 'Williams', 'e.williams@moderntech.com',
'Junior Developer', 1, 28000.00, '2024-01-08', 'Active'),

('Daniel', 'Smith', 'd.smith@moderntech.com',
'Software Architect', 1, 62000.00, '2016-06-20', 'Active'),

/_ Quality Assurance _/
('Priya', 'Sharma', 'p.sharma@moderntech.com',
'QA Lead', 2, 38000.00, '2016-11-20', 'Active'),

('Ryan', 'Moodley', 'r.moodley@moderntech.com',
'QA Engineer', 2, 29000.00, '2020-06-18', 'Active'),

('Aisha', 'Khan', 'a.khan@moderntech.com',
'QA Analyst', 2, 27000.00, '2023-03-10', 'Active'),

('Michael', 'Brown', 'm.brown@moderntech.com',
'Senior QA Engineer', 2, 35000.00, '2019-08-05', 'Active'),

/_ Customer Support _/
('David', 'Ferreira', 'd.ferreira@moderntech.com',
'Support Specialist', 3, 22000.00, '2019-02-14', 'Active'),

('Amara', 'Nwosu', 'a.nwosu@moderntech.com',
'Support Manager', 3, 32000.00, '2016-08-15', 'Active'),

('Zanele', 'Dlamini', 'z.dlamini@moderntech.com',
'Support Specialist', 3, 21000.00, '2022-06-12', 'Active'),

('Liam', 'Johnson', 'l.johnson@moderntech.com',
'Customer Support Agent', 3, 20000.00, '2023-09-18', 'Active'),

('Nomsa', 'Khumalo', 'n.khumalo@moderntech.com',
'Customer Success Specialist', 3, 26000.00, '2021-05-03', 'Active'),

('Peter', 'Anderson', 'p.anderson@moderntech.com',
'Technical Support Engineer', 3, 31000.00, '2020-10-11', 'Active'),

/ _ Human Resources _ /
('Linda', 'Park', 'l.park@moderntech.com',
'HR Director', 4, 60000.00, '2012-09-01', 'Active'),

('Ingrid', 'Larsson', 'i.larsson@moderntech.com',
'HR Specialist', 4, 28000.00, '2022-01-17', 'Active'),

('Noah', 'Patterson', 'n.patterson@moderntech.com',
'HR Specialist', 4, 27000.00, '2024-03-15', 'Active'),

('Grace', 'Mthembu', 'g.mthembu@moderntech.com',
'Recruitment Officer', 4, 30000.00, '2021-07-22', 'Active'),

/ _ Sales _ /
('Tom', 'Brandt', 't.brandt@moderntech.com',
'Sales Manager', 5, 35000.00, '2017-05-22', 'Active'),

('Kwame', 'Asante', 'k.asante@moderntech.com',
'Sales Executive', 5, 24000.00, '2021-08-09', 'Active'),

('Olivia', 'Taylor', 'o.taylor@moderntech.com',
'Account Executive', 5, 32000.00, '2020-04-17', 'Active'),

('Brian', 'Miller', 'b.miller@moderntech.com',
'Business Development Representative', 5, 25000.00, '2023-01-30', 'Active'),

('Lerato', 'Nkosi', 'l.nkosi@moderntech.com',
'Sales Consultant', 5, 23000.00, '2022-11-07', 'On Leave'),

/ _ Marketing _ /
('Lena', 'Hoffmann', 'l.hoffmann@moderntech.com',
'Marketing Analyst', 6, 26000.00, '2018-04-03', 'Active'),

('Carlos', 'Rivera', 'c.rivera@moderntech.com',
'Marketing Director', 6, 50000.00, '2014-12-10', 'Active'),

('Hannah', 'Wilson', 'h.wilson@moderntech.com',
'Content Strategist', 6, 33000.00, '2020-02-25', 'Active'),

('Jason', 'Naidoo', 'j.naidoo@moderntech.com',
'Digital Marketing Specialist', 6, 31000.00, '2021-09-13', 'Active'),

('Mei', 'Lin', 'm.lin@moderntech.com',
'Graphic Designer', 6, 29000.00, '2023-06-01', 'Active');

/_ ========================================================= 4. SALARY HISTORY
Initial salary record for each employee
========================================================= _/

INSERT INTO salary_history (
employee_id,
old_salary,
new_salary,
increase_percentage,
effective_date,
reason,
created_by
)
SELECT
id,
NULL,
salary,
0.00,
hire_date,
CONCAT('Initial hire - ', job_title),
1
FROM employees;

/_ Salary increases _/

INSERT INTO salary_history (
employee_id,
old_salary,
new_salary,
increase_percentage,
effective_date,
reason,
created_by
) VALUES

(1, 42000.00, 45000.00, 7.14,
'2026-01-01', 'Annual salary adjustment', 2),

(3, 39000.00, 42000.00, 7.69,
'2026-01-01', 'Performance-based increase', 2),

(6, 36000.00, 39000.00, 8.33,
'2026-04-01', 'Annual salary adjustment', 2),

(9, 35000.00, 38000.00, 8.57,
'2026-01-01', 'Annual salary adjustment', 2),

(14, 30000.00, 32000.00, 6.67,
'2026-02-01', 'Performance-based increase', 2),

(23, 32000.00, 35000.00, 9.38,
'2026-01-01', 'Annual salary adjustment', 2),

(28, 47000.00, 50000.00, 6.38,
'2026-01-01', 'Annual salary adjustment', 2);

/_ ========================================================= 5. PERFORMANCE RECORDS
August 2026 reviews
========================================================= _/

INSERT INTO performance_records (
employee_id,
performance_score,
review_date,
reviewer_id,
comments,
rating_category
) VALUES

(1, 4.7, '2026-08-25', 3,
'Excellent technical leadership and consistent delivery.',
'Excellent'),

(2, 4.9, '2026-08-25', 1,
'Outstanding engineering leadership and strategic decision-making.',
'Excellent'),

(3, 4.5, '2026-08-24', 3,
'Strong quality assurance leadership and attention to detail.',
'Good'),

(4, 3.8, '2026-08-24', 3,
'Solid support performance with opportunities for technical growth.',
'Satisfactory'),

(5, 4.8, '2026-08-25', 1,
'Excellent strategic HR leadership and execution.',
'Excellent'),

(6, 4.2, '2026-08-24', 3,
'Consistently strong backend development performance.',
'Good'),

(7, 3.6, '2026-08-24', 3,
'Shows good potential and continues developing technical skills.',
'Satisfactory'),

(8, 5.0, '2026-08-25', 1,
'Exceptional architectural leadership and technical vision.',
'Excellent'),

(9, 4.4, '2026-08-23', 3,
'Strong QA leadership and process improvement.',
'Good'),

(10, 3.7, '2026-08-23', 3,
'Reliable QA engineer with room to improve automation skills.',
'Satisfactory'),

(11, 4.1, '2026-08-23', 3,
'Good analytical skills and consistent testing performance.',
'Good'),

(12, 4.6, '2026-08-23', 3,
'Excellent testing practices and mentorship of junior staff.',
'Excellent'),

(13, 3.9, '2026-08-22', 3,
'Strong customer service and communication skills.',
'Satisfactory'),

(14, 4.4, '2026-08-22', 3,
'Effective support leadership and team coordination.',
'Good'),

(15, 3.5, '2026-08-22', 3,
'Developing technical skills and maintaining positive customer feedback.',
'Satisfactory'),

(16, 3.6, '2026-08-21', 3,
'Reliable customer support with improving resolution times.',
'Satisfactory'),

(17, 4.3, '2026-08-21', 3,
'Strong customer success performance and relationship management.',
'Good'),

(18, 4.5, '2026-08-21', 3,
'Excellent technical troubleshooting and customer support.',
'Good'),

(19, 4.8, '2026-08-20', 2,
'Excellent HR leadership and organizational strategy.',
'Excellent'),

(20, 4.0, '2026-08-20', 2,
'Consistent HR support and strong administrative performance.',
'Satisfactory'),

(21, 4.2, '2026-08-20', 2,
'Good progress and strong compliance knowledge.',
'Good'),

(22, 4.4, '2026-08-20', 2,
'Excellent recruitment coordination and candidate engagement.',
'Good'),

(23, 4.2, '2026-08-19', 5,
'Strong sales leadership and consistent team performance.',
'Good'),

(24, 3.8, '2026-08-19', 5,
'Good sales results with opportunities to improve client retention.',
'Satisfactory'),

(25, 4.5, '2026-08-19', 5,
'Excellent account management and revenue performance.',
'Good'),

(26, 3.7, '2026-08-18', 5,
'Developing business pipeline and showing positive progress.',
'Satisfactory'),

(27, 4.0, '2026-08-18', 5,
'Consistent sales performance and strong customer relationships.',
'Satisfactory'),

(28, 3.9, '2026-08-17', 4,
'Creative marketing analysis with improved campaign performance.',
'Satisfactory'),

(29, 4.7, '2026-08-17', 4,
'Excellent marketing leadership and campaign strategy.',
'Excellent'),

(30, 4.3, '2026-08-17', 4,
'Strong content strategy and consistently high-quality work.',
'Good'),

(31, 4.1, '2026-08-16', 4,
'Good digital campaign performance and analytical skills.',
'Good'),

(32, 4.4, '2026-08-16', 4,
'Excellent creative work and strong collaboration.',
'Good');

/_ ========================================================= 6. TIME-OFF REQUESTS
Pending = September dates, submitted in August
Approved = August dates
Denied = August dates
========================================================= _/

INSERT INTO time_off_requests (
employee_id,
start_date,
end_date,
reason,
request_type,
status,
date_submitted
) VALUES

/_ APPROVED - August _/

(1, '2026-08-10', '2026-08-14',
'Annual leave', 'Annual', 'Approved', '2026-07-20'),

(13, '2026-08-17', '2026-08-18',
'Medical recovery', 'Sick', 'Approved', '2026-08-16'),

(27, '2026-08-24', '2026-08-28',
'Family holiday', 'Annual', 'Approved', '2026-07-30'),

(21, '2026-08-06', '2026-08-07',
'Personal commitments', 'Personal', 'Approved', '2026-07-25'),

/_ DENIED - August _/

(7, '2026-08-20', '2026-08-22',
'Personal travel request during project deadline',
'Personal', 'Denied', '2026-08-05'),

(24, '2026-08-12', '2026-08-15',
'Annual leave request during peak sales period',
'Annual', 'Denied', '2026-08-01'),

(30, '2026-08-27', '2026-08-29',
'Personal event',
'Personal', 'Denied', '2026-08-10'),

/_ PENDING - September dates, submitted in August _/

(3, '2026-09-07', '2026-09-11',
'Family vacation',
'Annual', 'Pending', '2026-08-18'),

(10, '2026-09-14', '2026-09-15',
'Medical appointments',
'Personal', 'Pending', '2026-08-22'),

(18, '2026-09-21', '2026-09-25',
'Annual family holiday',
'Annual', 'Pending', '2026-08-25'),

(32, '2026-09-03', '2026-09-04',
'Personal commitments',
'Personal', 'Pending', '2026-08-26'),

(6, '2026-09-28', '2026-09-30',
'Professional development',
'Personal', 'Pending', '2026-08-27');

/\* ========================================================= 7. ATTENDANCE RECORDS
1 AUGUST 2026 - 29 AUGUST 2026

Generates one record for every employee for every weekday.
========================================================= \*/

INSERT INTO attendance_records (
employee_id,
attendance_date,
status,
notes
)

SELECT
e.id,
d.attendance_date,

    CASE

        /* Employee 1 - approved annual leave */
        WHEN e.id = 1
             AND d.attendance_date BETWEEN '2026-08-10'
                                     AND '2026-08-14'
        THEN 'leave'

        /* Employee 13 - approved sick leave */
        WHEN e.id = 13
             AND d.attendance_date BETWEEN '2026-08-17'
                                     AND '2026-08-18'
        THEN 'sick'

        /* Employee 21 - approved personal leave */
        WHEN e.id = 21
             AND d.attendance_date BETWEEN '2026-08-06'
                                     AND '2026-08-07'
        THEN 'leave'

        /* Employee 27 - approved annual leave */
        WHEN e.id = 27
             AND d.attendance_date BETWEEN '2026-08-24'
                                     AND '2026-08-28'
        THEN 'leave'

        /* Selected absences */
        WHEN e.id = 4
             AND d.attendance_date = '2026-08-05'
        THEN 'absent'

        WHEN e.id = 7
             AND d.attendance_date = '2026-08-12'
        THEN 'absent'

        WHEN e.id = 10
             AND d.attendance_date = '2026-08-19'
        THEN 'absent'

        WHEN e.id = 16
             AND d.attendance_date = '2026-08-26'
        THEN 'absent'

        WHEN e.id = 24
             AND d.attendance_date = '2026-08-11'
        THEN 'absent'

        /* Selected late arrivals */
        WHEN e.id = 3
             AND DAYOFWEEK(d.attendance_date) = 3
        THEN 'late'

        WHEN e.id = 6
             AND DAYOFWEEK(d.attendance_date) = 5
        THEN 'late'

        WHEN e.id = 11
             AND DAYOFWEEK(d.attendance_date) = 2
        THEN 'late'

        WHEN e.id = 17
             AND DAYOFWEEK(d.attendance_date) = 4
        THEN 'late'

        WHEN e.id = 28
             AND DAYOFWEEK(d.attendance_date) = 6
        THEN 'late'

        ELSE 'present'

    END,

    CASE
        WHEN e.id = 1
             AND d.attendance_date BETWEEN '2026-08-10'
                                     AND '2026-08-14'
        THEN 'Approved annual leave'

        WHEN e.id = 13
             AND d.attendance_date BETWEEN '2026-08-17'
                                     AND '2026-08-18'
        THEN 'Approved sick leave'

        WHEN e.id = 27
             AND d.attendance_date BETWEEN '2026-08-24'
                                     AND '2026-08-28'
        THEN 'Approved annual leave'

        ELSE NULL
    END

FROM employees e

CROSS JOIN (

    SELECT '2026-08-01' AS attendance_date UNION ALL
    SELECT '2026-08-02' UNION ALL
    SELECT '2026-08-03' UNION ALL
    SELECT '2026-08-04' UNION ALL
    SELECT '2026-08-05' UNION ALL
    SELECT '2026-08-06' UNION ALL
    SELECT '2026-08-07' UNION ALL
    SELECT '2026-08-08' UNION ALL
    SELECT '2026-08-09' UNION ALL
    SELECT '2026-08-10' UNION ALL
    SELECT '2026-08-11' UNION ALL
    SELECT '2026-08-12' UNION ALL
    SELECT '2026-08-13' UNION ALL
    SELECT '2026-08-14' UNION ALL
    SELECT '2026-08-15' UNION ALL
    SELECT '2026-08-16' UNION ALL
    SELECT '2026-08-17' UNION ALL
    SELECT '2026-08-18' UNION ALL
    SELECT '2026-08-19' UNION ALL
    SELECT '2026-08-20' UNION ALL
    SELECT '2026-08-21' UNION ALL
    SELECT '2026-08-22' UNION ALL
    SELECT '2026-08-23' UNION ALL
    SELECT '2026-08-24' UNION ALL
    SELECT '2026-08-25' UNION ALL
    SELECT '2026-08-26' UNION ALL
    SELECT '2026-08-27' UNION ALL
    SELECT '2026-08-28' UNION ALL
    SELECT '2026-08-29'

) d

WHERE DAYOFWEEK(d.attendance_date) NOT IN (1, 7);

-- Index foreign keys and search filter columns in time_off_requests table
ALTER TABLE time_off_requests ADD INDEX idx_employee_id (employee_id);
ALTER TABLE time_off_requests ADD INDEX idx_status (status);

-- Composite index for status lookups (improves queries filtering pending/approved requests)
ALTER TABLE time_off_requests ADD INDEX idx_employee_status (employee_id, status);

-- Index frequently searched employee fields (useful if filtering by name in SQL)
ALTER TABLE employees ADD INDEX idx_first_last_name (first_name, last_name);

CREATE TABLE IF NOT EXISTS activity_logs (
id INT AUTO_INCREMENT PRIMARY KEY,
description VARCHAR(255) NOT NULL,
category VARCHAR(50) NOT NULL,
category_class VARCHAR(50) NOT NULL,
target_profile VARCHAR(255) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

## Installation

### Step 1: Clone the Repository

Clone the project from GitHub:

git clone https://github.com/INdzengu/LCA-moderntech-hr-system-course2.git

### Step 2: Move the Project into XAMPP

Copy the project folder into the XAMPP `htdocs` directory.

For example:

C:\xampp\lca-php\htdocs\LCA-moderntech-hr-system

The backend should then be located inside the project directory:

C:\xampp\htdocs\LCA-moderntech-hr-system\backend

### Step 3: Configure the Backend

Inside the `backend` directory, create a `.env` file based on the provided `.env.example` file.

The `.env` file should contain the local database connection information.

Example:

```env
DB_HOST=127.0.0.1
DB_PORT=3307
DB_NAME=moderntech_hr
DB_USER=root
DB_PASSWORD=
JWT_SECRET=your_secret_key
```

**Important:** The `.env` file contains private configuration information and must not be committed to GitHub.

The `.env.example` file should be committed instead so that another developer can see which environment variables are required.

## How to Run

### Backend

1. Open the XAMPP Control Panel.
2. Start **Apache**.
3. Start **MySQL**.
4. Confirm that MySQL is running on port **3307**.
5. Confirm that the database has been created in phpMyAdmin.
6. Confirm that the PHP backend is located inside the XAMPP `htdocs` directory.

The backend API can then be accessed through:

http://localhost/lca-php/LCA-moderntech-hr-system/backend/

### Frontend

Open the project in Visual Studio Code and install the required dependencies:

npm install

Start the Vue development server:

npm run dev

Vite will provide a local development address, normally similar to:

http://localhost:5173/

Open this address in your browser.

## Authentication and Role-Based Access Control

The system implements role-based access control to provide different views and permissions depending on the authenticated user's role.

### Administrator

Administrators have full access to the HR system.

Permissions include:

- View and manage employees
- Manage departments
- Manage attendance
- Manage leave requests
- Manage payroll information
- View salary history
- Manage performance records
- Access system settings
- Manage user access

### HR Staff

HR staff manage HR information across the organisation.

Permissions include:

- View all employees
- Add employees
- Edit employee information
- Manage attendance
- View and manage leave requests
- Approve or deny leave requests
- View payroll information
- View salary history
- Manage performance records
- View HR-related information

### Manager

Managers have access to information relating to their own department or team.

Permissions include:

- View team members
- View team attendance
- Update team attendance where permitted
- View team leave requests
- Approve or deny team leave requests
- View team performance
- Create performance reviews for team members

Managers cannot access or modify HR information belonging to other departments.

## API Structure

The PHP backend uses separate routes for the main HR system functions.

```text
POST   /api/auth/login

GET    /api/employees
POST   /api/employees
PUT    /api/employees/{id}
DELETE /api/employees/{id}

GET    /api/time-off
POST   /api/time-off
PUT    /api/time-off/{id}

GET    /api/attendance
POST   /api/attendance

GET    /api/payroll
POST   /api/payroll

GET    /api/departments
```

Authentication is handled through JWT tokens. Protected routes verify the user's token before allowing access to restricted resources.

## Project Structure

```text
LCA-moderntech-hr-system/
│
├── backend/
│   ├── config/
│   │   └── db-connect.php
│   │
│   ├── helpers/
│   │   ├── Response.php
│   │

│   ├── models/
│   │   ├── EmployeeModel.php
│   │   ├── AttendanceModel.php
│   │
│   ├── routes/
│   │   ├── attendance.php
│   │   ├── check-session.php
│   │   ├── dashboard.php
│   │   ├── employees.php
│   │   ├── header.php
│   │   ├── leave.php
│   │   ├── login.php
│   │   └── logout.php
├── │   └── payroll.php
│   │
│   │

│   ├── .env
│   ├── .gitignore
│   └── index.php
│
├── src/
│   ├── components/
│   ├── views/
│   │   ├── LoginView.vue
│   │   ├── DashboardView.vue
│   │   ├── PeopleView.vue
│   │   ├── LeaveView.vue
│   │   ├── PayrollView.vue
│   │   ├── CalendarView.vue
│   │   └── SettingsView.vue
│   │
│   ├── App.vue
│   ├── main.js
│   └── style.css
│
├── public/
│
├── package.json
├── package-lock.json
├── vite.config.js
└── README.md



## Author

**Iviwe Ndzengu**

Life Choices Academy YouthCode Off-Site
Cohort 2

## Repository

GitHub Repository:


https://github.com/INdzengu/LCA-moderntech-hr-system-course2.git
```
