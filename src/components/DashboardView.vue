<template>
  <div class="dashboard-page">
    <!-- WELCOME BANNER: Hero section at the top -->
    <!-- Shows greeting, current user, and system status -->
    <!-- This is the first thing HR staff sees when they log in -->
    <header class="welcome-banner">
      <div class="banner-content">
        <!-- GREETING: "GOOD MORNING" text in green -->
        <!-- This changes based on time of day in a real app -->
        <span class="system-greeting">GOOD MORNING</span>

        <!-- WELCOME MESSAGE: Uses current user name from prop -->
        <!-- currentUser is passed from App.vue and displays logged-in user -->
        <h1>Welcome Back, {{ currentUser }}</h1>

        <!-- BANNER SUBTITLE: Describes what's on the dashboard -->
        <!-- Tells HR staff what information they can see today -->
        <p class="banner-sub">
          Here is what's happening with your workspace metrics and employee
          performance trends today.
        </p>
      </div>

      <!-- STATUS PILL: Shows system is working -->
      <!-- In a real app, this would check if backend is running -->
      <div class="banner-stats-pill">
        <span class="pill-title">System Status</span>
        <span class="pill-value">&bull; Operational</span>
      </div>
    </header>

    <!-- QUICK STATS ROW: Four stat boxes showing key numbers -->
    <!-- These are computed properties that automatically update as data changes -->
    <!-- This section shows HR staff the important numbers at a glance -->
    <section class="quick-stats-row">
      <!-- TOTAL EMPLOYEES STAT BOX -->
      <!-- Shows count of all employees in the system -->
      <!-- Uses totalEmployees computed property -->
      <div class="stat-box">
        <span class="stat-icon">👥</span>
        <span class="stat-label">Total Employees</span>
        <span class="stat-value">{{ totalEmployees }}</span>
      </div>

      <!-- EMPLOYEES PRESENT TODAY STAT BOX -->
      <!-- Calculates how many employees are working today -->
      <!-- Formula: Total employees minus those on leave and absent -->
      <!-- Updates live as leave requests are approved -->
      <div class="stat-box">
        <span class="stat-icon">✓</span>
        <span class="stat-label">Present Today</span>
        <span class="stat-value">{{ employeesPresent }}</span>
      </div>

      <!-- EMPLOYEES ON LEAVE STAT BOX -->
      <!-- Counts employees with "On Leave" status -->
      <!-- This status is set when HR approves a leave request -->
      <!-- Updates instantly when HR approves leave in LeaveView -->
      <div class="stat-box">
        <span class="stat-icon">🏖️</span>
        <span class="stat-label">On Leave</span>
        <span class="stat-value">{{ employeesOnLeave }}</span>
      </div>

      <!-- PENDING LEAVE APPROVALS STAT BOX -->
      <!-- Counts leave requests waiting for HR approval -->
      <!-- Shows HR staff how many decisions they need to make -->
      <!-- Updates when HR approves or rejects requests in LeaveView -->
      <div class="stat-box">
        <span class="stat-icon">⏳</span>
        <span class="stat-label">Pending Approvals</span>
        <span class="stat-value">{{ pendingLeaveCount }}</span>
      </div>
    </section>

    <!-- ANALYTICS SECTION: Two columns with charts -->
    <!-- Left chart: Weekly attendance trend (bar chart) -->
    <!-- Right chart: Team distribution (pie chart) -->
    <!-- These charts visualize data using dummy employee records -->
    <section class="analytics-split-grid">
      <!-- CHART 1: WEEKLY ATTENDANCE TREND (BAR CHART) -->
      <!-- Shows how many employees were present each day last week -->
      <div class="chart-panel">
        <div class="panel-header">
          <h3>Weekly Attendance Trend</h3>
          <span class="panel-legend">June Past Week Overview</span>
        </div>

        <!-- BAR CHART CONTAINER: Main chart area -->
        <div class="bar-chart-container">
          <!-- Y-AXIS: Shows numbers on left side (0 to max employees) -->
          <!-- These are scale markers to read the chart -->
          <div class="chart-axis-y">
            <span>{{ maxAttendance }}</span>
            <span>{{ Math.round(maxAttendance * 0.75) }}</span>
            <span>{{ Math.round(maxAttendance * 0.5) }}</span>
            <span>0</span>
          </div>

          <!-- BARS AREA: Contains the actual bar columns -->
          <!-- Each bar represents one day of the week -->
          <div class="chart-bars-area">
            <!-- GENERATE A BAR FOR EACH DAY OF THE WEEK -->
            <!-- v-for loops through weeklyAttendanceData computed property -->
            <!-- Creates 5 bars for Monday through Friday -->
            <!-- Each bar's height = (employees present / total employees) * 100 -->
            <div
              v-for="(day, index) in weeklyAttendanceData"
              :key="index"
              class="bar-column"
            >
              <!-- INDIVIDUAL BAR: Height changes based on attendance percentage -->
              <!-- :style dynamically sets the height based on day.percentage -->
              <!-- This connects the data to the visual height of the bar -->
              <div class="bar-fill" :style="{ height: day.percentage + '%' }">
                <!-- TOOLTIP: Shows exact number when user hovers over bar -->
                <!-- Displays how many employees were present that day -->
                <span class="bar-tooltip">{{ day.present }} Present</span>
              </div>
              <!-- DAY LABEL: Mon 15, Tue 16, etc -->
              <!-- Shows which day of the week this bar represents -->
              <span class="bar-label">{{ day.label }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- CHART 2: TEAM DISTRIBUTION PIE CHART -->
      <!-- Shows how many employees are in each department -->
      <div class="chart-panel">
        <div class="panel-header">
          <h3>Team Distribution</h3>
          <span class="panel-legend">Departmental Segments</span>
        </div>

        <!-- PIE CHART CONTAINER: Main pie chart area -->
        <div class="pie-chart-container">
          <!-- PIE CIRCLE: The actual pie chart visualization -->
          <!-- Uses conic-gradient CSS to create colored segments -->
          <!-- Each segment size represents percentage of employees in that department -->
          <!-- :style binding uses pieChartStyle computed property -->
          <div class="simulated-pie-circle" :style="pieChartStyle"></div>

          <!-- PIE LEGEND: List showing department names and employee counts -->
          <div class="pie-legend-list">
            <!-- LOOP THROUGH DEPARTMENTS -->
            <!-- v-for creates legend item for each department -->
            <div
              v-for="dept in departmentBreakdown"
              :key="dept.name"
              class="legend-item"
            >
              <!-- COLORED DOT: Matches color of pie chart segment -->
              <!-- :style sets background color based on dept.color -->
              <span
                class="legend-color-dot"
                :style="{ backgroundColor: dept.color }"
              ></span>
              <!-- DEPARTMENT NAME AND COUNT: e.g., "Software Development (5)" -->
              <!-- Shows which department and how many people are in it -->
              <span class="legend-text"
                >{{ dept.name }} ({{ dept.count }})</span
              >
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ACTIVITY LOG SECTION: Table of recent events -->
    <!-- Shows recent leave requests that have been processed -->
    <!-- This demonstrates how data flows from employees array to display -->
    <section class="activity-log-section">
      <div class="panel-header padding-box">
        <h3>Recent Administrative Activities</h3>
        <p class="panel-sub-desc">
          Latest system modifications, leave processing logs, and personnel
          status shifts.
        </p>
      </div>

      <!-- ACTIVITY TABLE: Shows log of recent leave requests -->
      <div class="table-responsive-wrapper">
        <table class="activity-data-table">
          <!-- TABLE HEADER: Column titles explaining each column -->
          <thead>
            <tr>
              <th>ACTIVITY DETAILS</th>
              <th>OPERATIONAL CATEGORY</th>
              <th>TARGET PROFILE</th>
              <th>TIMESTAMP</th>
            </tr>
          </thead>

          <!-- TABLE BODY: Rows of activity data -->
          <!-- Generated from activityLog computed property -->
          <!-- Shows leave requests from all employees -->
          <tbody>
            <!-- LOOP THROUGH ACTIVITY LOG -->
            <!-- v-for creates a row for each leave request -->
            <!-- Sorted by most recent first -->
            <tr v-for="activity in activityLog" :key="activity.id">
              <!-- ACTIVITY DESCRIPTION: What happened -->
              <!-- Examples: "Sick Leave Request Approved", "Annual Leave Request Pending" -->
              <td>
                <div class="activity-main-text">
                  {{ activity.description }}
                </div>
              </td>

              <!-- CATEGORY TAG: Color-coded label showing type of activity -->
              <!-- :class applies different color based on activity.categoryClass -->
              <!-- Different colors for leave, system, payroll, etc. -->
              <td>
                <span :class="['category-tag', activity.categoryClass]">
                  {{ activity.category }}
                </span>
              </td>

              <!-- TARGET PROFILE: Which employee this affects -->
              <!-- Shows employee name and their job role -->
              <td>{{ activity.targetProfile }}</td>

              <!-- TIMESTAMP: When this activity occurred -->
              <!-- Shows date and time of the leave request -->
              <td class="timestamp-col">{{ activity.timestamp }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "DashboardView",

  props: {
    // CURRENT USER: Name of logged-in user
    // Passed from App.vue - shows who is using the dashboard
    currentUser: {
      type: String,
      default: "HR Admin",
    },

    // EMPLOYEES: Array of all employees in the system
    // Passed from App.vue - this is the main data source
    // All computed properties below use this array
    // When employees array changes, all computed properties update automatically
    employees: {
      type: Array,
      required: true,
    },
  },

  computed: {
    /**
     * TOTAL EMPLOYEES COUNT
     *
     * WHAT IT DOES: Counts all employees in the system
     * HOW IT WORKS: Returns length of employees array
     * WHERE IT'S USED: Stat box showing total employees
     *
     * AUTOMATIC UPDATE: If employees array changes (employee added/removed),
     * this count updates automatically - that's what computed properties do
     */
    totalEmployees() {
      return this.employees.length;
    },

    /**
     * EMPLOYEES ON LEAVE
     *
     * WHAT IT DOES: Counts how many employees have "On Leave" status
     * HOW IT WORKS:
     *   1. filter() loops through employees array
     *   2. Checks each employee's status property
     *   3. Keeps only employees where status === "On Leave"
     *   4. Returns count of matching employees
     *
     * WHERE IT'S USED: Stat box showing employees on leave
     *
     * WHEN IT UPDATES: When HR approves a leave request in LeaveView,
     * that employee's status changes to "On Leave", this count updates
     */
    employeesOnLeave() {
      return this.employees.filter((emp) => emp.status === "On Leave").length;
    },

    /**
     * EMPLOYEES PRESENT TODAY
     *
     * WHAT IT DOES: Calculates how many employees are actually working
     * HOW IT WORKS:
     *   Total employees - Employees on leave - Employees absent = Present today
     *
     * WHERE IT'S USED: Stat box showing employees present
     *
     * EXAMPLE:
     * If total = 15, on leave = 2, absent = 1
     * Then present = 15 - 2 - 1 = 12
     *
     * UPDATES: When leave is approved or when attendance status changes
     */
    employeesPresent() {
      return (
        this.employees.length -
        this.employeesOnLeave -
        this.employees.filter((emp) => emp.status === "Absent").length
      );
    },

    /**
     * PENDING LEAVE REQUESTS COUNT
     *
     * WHAT IT DOES: Counts how many leave requests are waiting for approval
     * HOW IT WORKS:
     *   1. Loop through all employees (forEach)
     *   2. For each employee, check if they have leaveRequests array
     *   3. Count only requests with status === "Pending"
     *   4. Add to total count
     *
     * WHERE IT'S USED: Stat box showing pending approvals
     *
     * UPDATES: When HR approves or rejects a leave request in LeaveView,
     * the request status changes from "Pending" to "Approved" or "Rejected",
     * so this count changes automatically
     */
    pendingLeaveCount() {
      let count = 0;
      this.employees.forEach((emp) => {
        if (emp.leaveRequests && emp.leaveRequests.length > 0) {
          count += emp.leaveRequests.filter(
            (r) => r.status === "Pending",
          ).length;
        }
      });
      return count;
    },

    /**
     * WEEKLY ATTENDANCE DATA
     *
     * WHAT IT DOES: Processes attendance for each day of the week
     * Creates data for the attendance bar chart
     *
     * HOW IT WORKS:
     *   1. Create array of 5 weekdays (Mon-Fri, June 15-19)
     *   2. For each day, count employees who were "present" or "late"
     *   3. Calculate percentage for bar height (present / total * 100)
     *   4. Return array with all day data
     *
     * WHERE IT'S USED: Powers the bar chart visualization
     *
     * RETURNS: Array like this:
     * [
     *   { label: "Mon 15", present: 13, percentage: 87 },
     *   { label: "Tue 16", present: 14, percentage: 93 },
     *   ...
     * ]
     *
     * DATA SOURCE: Uses prevWeekAttendance property from each employee
     * This property contains attendance status for each date
     */
    weeklyAttendanceData() {
      // DEFINE WEEK DAYS: Monday through Friday of June 15-19
      // These are the dates we're showing attendance for
      const weekDays = [
        { date: "2026-06-15", label: "Mon 15" },
        { date: "2026-06-16", label: "Tue 16" },
        { date: "2026-06-17", label: "Wed 17" },
        { date: "2026-06-18", label: "Thu 18" },
        { date: "2026-06-19", label: "Fri 19" },
      ];

      // MAP OVER WEEK DAYS: Create attendance data for each day
      // map() transforms weekDays array into attendance data array
      return weekDays.map((day) => {
        // COUNT EMPLOYEES PRESENT OR LATE: Loop through all employees
        let presentCount = 0;

        this.employees.forEach((emp) => {
          // GET ATTENDANCE FOR THIS DATE: Look up status for this specific date
          // emp.prevWeekAttendance is an object like: { "2026-06-15": "present", ... }
          const status = emp.prevWeekAttendance?.[day.date];

          // COUNT AS PRESENT: Only count "present" or "late" employees
          // Don't count "absent" or "leave" (those don't count as present)
          if (status === "present" || status === "late") {
            presentCount++;
          }
        });

        // CALCULATE PERCENTAGE FOR BAR HEIGHT
        // Percentage = (present employees / total employees) * 100
        // This percentage becomes the bar height (0% to 100%)
        return {
          label: day.label,
          present: presentCount,
          percentage: (presentCount / this.employees.length) * 100,
        };
      });
    },

    /**
     * MAXIMUM ATTENDANCE VALUE
     *
     * WHAT IT DOES: Returns the highest possible attendance number
     * HOW IT WORKS: Returns total number of employees
     * WHERE IT'S USED: Y-axis scale on bar chart
     *
     * EXAMPLE:
     * If 15 employees total, maxAttendance = 15
     * Y-axis shows: 15, 11, 8, 0
     * (These scale marks help read the chart)
     */
    maxAttendance() {
      return this.employees.length;
    },

    /**
     * DEPARTMENT BREAKDOWN
     *
     * WHAT IT DOES: Groups employees by department and counts each group
     * Creates data for the pie chart legend
     *
     * HOW IT WORKS:
     *   1. Define colors for each department (color coding)
     *   2. Create empty object to store departments
     *   3. Loop through all employees
     *   4. For each employee, add them to their department group
     *   5. Count how many employees in each department
     *   6. Sort departments by size (largest first)
     *
     * WHERE IT'S USED: Pie chart legend and pieChartStyle calculation
     *
     * RETURNS: Array like this:
     * [
     *   { name: "Software Development", count: 5, color: "#3b82f6" },
     *   { name: "Quality Assurance", count: 2, color: "#8b5cf6" },
     *   ...
     * ]
     *
     * Sorted from most employees to least employees
     */
    departmentBreakdown() {
      // DEPARTMENT COLORS: Assign consistent color to each department
      // These colors are used in pie chart and legend
      const departmentColors = {
        "Software Development": "#3b82f6", // Blue
        "Quality Assurance": "#8b5cf6", // Purple
        "Customer Support": "#10b981", // Green
        "Human Resources": "#06b6d4", // Cyan
        Sales: "#f59e0b", // Amber
        Marketing: "#ef4444", // Red
      };

      // GROUP EMPLOYEES BY DEPARTMENT
      // Create empty object to store department data
      const departments = {};

      // LOOP THROUGH ALL EMPLOYEES: Add each to their department group
      this.employees.forEach((emp) => {
        const dept = emp.department;

        // CHECK IF DEPARTMENT ALREADY EXISTS
        // If first time seeing this department, create it
        if (!departments[dept]) {
          departments[dept] = {
            name: dept,
            count: 0,
            color: departmentColors[dept] || "#64748b",
          };
        }

        // INCREMENT COUNT: Add this employee to department count
        departments[dept].count++;
      });

      // CONVERT TO ARRAY AND SORT
      // Object.values() converts department object into array
      // sort() arranges by count (largest count first)
      return Object.values(departments).sort((a, b) => b.count - a.count);
    },

    /**
     * PIE CHART STYLE
     *
     * WHAT IT DOES: Generates CSS conic-gradient to create pie chart
     * HOW IT WORKS:
     *   1. Loop through each department
     *   2. Calculate what percentage of employees are in that department
     *   3. Create CSS gradient segment for that department
     *   4. Combine all segments into one gradient string
     *
     * WHERE IT'S USED: :style binding on simulated-pie-circle div
     * This converts the gradient string into a pie chart visualization
     *
     * CSS CONIC-GRADIENT EXPLANATION:
     * Creates a circle divided into colored wedges/slices
     * Each slice size = percentage of that department
     *
     * EXAMPLE OUTPUT:
     * conic-gradient(
     *   #3b82f6 0% 30%,        <- Blue slice from 0% to 30% of circle
     *   #8b5cf6 30% 50%,       <- Purple slice from 30% to 50%
     *   #10b981 50% 100%       <- Green slice from 50% to 100%
     * )
     *
     * COOL PART: Entire pie chart is pure CSS - no chart library needed!
     */
    pieChartStyle() {
      // START GRADIENT STRING: Build the conic-gradient from scratch
      let gradientString = "conic-gradient(";
      let currentPercentage = 0;

      // LOOP THROUGH EACH DEPARTMENT: Create a segment for each
      this.departmentBreakdown.forEach((dept, index) => {
        // CALCULATE PERCENTAGE FOR THIS DEPARTMENT
        // percentage = (employees in dept / total employees) * 100
        const percentage = (dept.count / this.employees.length) * 100;

        // CALCULATE START AND END POSITIONS
        // Where does this department's slice start and end in the circle?
        const startPercentage = currentPercentage;
        const endPercentage = currentPercentage + percentage;

        // ADD TO GRADIENT STRING
        // Format: "color startPosition% endPosition%"
        // Example: "#3b82f6 0% 30%"
        gradientString += `${dept.color} ${startPercentage}% ${endPercentage}%`;

        // ADD COMMA BETWEEN SEGMENTS
        // Comma separates each color segment
        // Don't add comma after the last segment
        if (index < this.departmentBreakdown.length - 1) {
          gradientString += ",";
        }

        // UPDATE CURRENT POSITION FOR NEXT SEGMENT
        // Next segment starts where this one ends
        currentPercentage = endPercentage;
      });

      // CLOSE GRADIENT STRING: Add closing parenthesis
      gradientString += ")";

      // RETURN STYLE OBJECT: This gets applied as inline style
      return {
        background: gradientString,
      };
    },

    /**
     * ACTIVITY LOG
     *
     * WHAT IT DOES: Creates list of recent activities from leave requests
     * Shows approved and pending leave requests as system events
     *
     * HOW IT WORKS:
     *   1. Create empty activities array
     *   2. Loop through all employees
     *   3. For each employee's leave request, create activity entry
     *   4. Sort by most recent first (by date submitted)
     *   5. Return sorted activity array
     *
     * WHERE IT'S USED: Powers the activity log table
     *
     * RETURNS: Array like this:
     * [
     *   {
     *     id: "emp1-annual",
     *     description: "Annual Leave Request Approved",
     *     category: "Leave Management",
     *     categoryClass: "tag-leave",
     *     targetProfile: "John Doe (Software Developer)",
     *     timestamp: "2026-06-24 • 08:14 AM"
     *   },
     *   ...
     * ]
     *
     * DATA FLOW: Employee JSON → Computed Property → Table Display
     * This demonstrates how data flows through the application
     */
    activityLog() {
      // CREATE EMPTY ACTIVITIES ARRAY: Will store all activities
      const activities = [];

      // LOOP THROUGH ALL EMPLOYEES: Get their leave requests
      this.employees.forEach((emp) => {
        // CHECK IF EMPLOYEE HAS ANY LEAVE REQUESTS
        // leaveRequests might be undefined, so check first
        if (emp.leaveRequests && emp.leaveRequests.length > 0) {
          // LOOP THROUGH EACH LEAVE REQUEST: Create activity for each
          emp.leaveRequests.forEach((request) => {
            // CREATE ACTIVITY ENTRY FOR THIS LEAVE REQUEST
            activities.push({
              // UNIQUE ID: Combines employee ID and request type
              id: `${emp.id}-${request.type}`,

              // DESCRIPTION: What happened
              // Changes based on status: "Approved", "Pending", "Rejected"
              description:
                request.status === "Approved"
                  ? `${request.type} Leave Request Approved`
                  : `${request.type} Leave Request ${request.status}`,

              // CATEGORY: Type of activity (all leave requests are "Leave Management")
              category: "Leave Management",

              // CATEGORY CLASS: CSS class for color coding
              // Used in :class binding to color the tag
              categoryClass: "tag-leave",

              // TARGET PROFILE: Shows which employee (name and role)
              targetProfile: `${emp.name} (${emp.role})`,

              // TIMESTAMP: When the activity happened
              // Split by space to get just the date part for sorting
              timestamp: `${request.dateSubmitted} • 08:14 AM`,
            });
          });
        }
      });

      // SORT BY MOST RECENT FIRST
      // Sorts by date in timestamp string
      // Most recent dates come first
      return activities.sort(
        (a, b) =>
          new Date(b.timestamp.split(" ")[0]) -
          new Date(a.timestamp.split(" ")[0]),
      );
    },
  },
};
</script>

<style scoped>
/* ============ MAIN DASHBOARD PAGE CONTAINER ============ */
/* This is the main wrapper for the entire dashboard */
/* padding: 30px adds space around all edges */
/* max-width: 1400px limits width on very wide screens */
/* display: flex with flex-direction: column stacks sections vertically */
.dashboard-page {
  padding: 30px;
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* ============ WELCOME BANNER: Hero section at top ============ */
/* Shows greeting, user name, and system status */
/* Green gradient background */
/* Uses flexbox to arrange content and status pill side by side */

.welcome-banner {
  background: linear-gradient(135deg, #0e1e16 0%, #051409 100%);
  border: 1px solid #113821;
  border-radius: 8px;
  padding: 35px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

/* SYSTEM GREETING: "GOOD MORNING" text in green */
.system-greeting {
  color: #44ff9a;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 2px;
  display: block;
  margin-bottom: 8px;
}

/* WELCOME MESSAGE: Main heading with user name */
.welcome-banner h1 {
  color: #ffffff;
  font-size: 2.2rem;
  font-weight: 700;
  margin: 0 0 10px 0;
}

/* BANNER SUBTITLE: Description text */
.banner-sub {
  color: #a0b0a6;
  font-size: 0.95rem;
  margin: 0;
  max-width: 650px;
  line-height: 1.5;
}

/* STATUS PILL: Shows "Operational" status on right side */
.banner-stats-pill {
  background: rgba(68, 255, 154, 0.05);
  border: 1px solid rgba(68, 255, 154, 0.2);
  padding: 10px 20px;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.pill-title {
  font-size: 0.7rem;
  color: #6a8274;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.pill-value {
  font-size: 0.9rem;
  color: #44ff9a;
  font-weight: 600;
  margin-top: 2px;
}

/* ============ QUICK STATS ROW: Four stat boxes ============ */
/* Shows key numbers: total employees, present, on leave, pending */
/* Uses CSS Grid to arrange in responsive layout */
/* On mobile, wraps to 2 columns; on desktop shows 4 columns */

.quick-stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 10px;
}

/* INDIVIDUAL STAT BOX */
/* Each stat box contains icon, label, and value */
/* Flexbox centers content */
.stat-box {
  background: #0f0f0f;
  border: 1px solid #1c1c1c;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: all 0.3s ease;
}

/* STAT BOX HOVER: Adds glow effect when mouse over */
.stat-box:hover {
  border-color: #44ff9a;
  box-shadow: 0 0 12px rgba(68, 255, 154, 0.1);
}

.stat-icon {
  font-size: 2rem;
  margin-bottom: 12px;
}

.stat-label {
  color: #888888;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

/* STAT VALUE: The big number (12, 2, 3, etc) */
.stat-value {
  color: #44ff9a;
  font-size: 2rem;
  font-weight: 700;
}

/* ============ ANALYTICS GRID: Two columns for charts ============ */
/* Left column: bar chart (weekly attendance) */
/* Right column: pie chart (department distribution) */
/* Uses grid with auto-fit to stack on mobile */

.analytics-split-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 30px;
}

/* CHART PANEL: Container for each chart */
.chart-panel {
  background: #0f0f0f;
  border: 1px solid #1c1c1c;
  border-radius: 8px;
  padding: 25px;
}

.panel-header {
  margin-bottom: 25px;
}

.panel-header h3 {
  color: #ffffff;
  font-size: 1.15rem;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.panel-legend {
  color: #666666;
  font-size: 0.8rem;
}

/* ============ BAR CHART STYLING ============ */
/* Container and layout for attendance bar chart */

.bar-chart-container {
  display: flex;
  height: 220px;
  gap: 20px;
  padding-top: 10px;
}

/* Y-AXIS LABELS: Scale markers on left side (0, 8, 11, 15) */
.chart-axis-y {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  color: #444444;
  font-size: 0.75rem;
  font-weight: 600;
  text-align: right;
  width: 25px;
  padding-bottom: 25px;
}

/* BARS AREA: Contains all the bar columns */
/* Uses flexbox to distribute bars evenly */
.chart-bars-area {
  flex: 1;
  display: flex;
  justify-content: space-around;
  align-items: flex-end;
  border-bottom: 1px solid #222222;
  padding-bottom: 10px;
}

/* INDIVIDUAL BAR COLUMN: Container for one bar */
/* Arranges bar vertically with label below */
.bar-column {
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
  justify-content: flex-end;
  width: 60px;
}

/* THE BAR ITSELF: Height varies based on attendance percentage */
/* :style binding sets height dynamically from day.percentage */
.bar-fill {
  width: 32px;
  border-radius: 4px 4px 0 0;
  position: relative;
  cursor: pointer;
  transition: opacity 0.2s ease;
  background: linear-gradient(to top, #44ff9a, #127c45);
  border: 1px solid #44ff9a;
}

.bar-fill:hover {
  opacity: 0.8;
}

/* TOOLTIP: Shows count on hover */
/* Appears above bar when user hovers */
.bar-tooltip {
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translateX(-50%);
  background: #1c1c1c;
  color: #ffffff;
  font-size: 0.7rem;
  padding: 4px 8px;
  border-radius: 4px;
  white-space: nowrap;
  border: 1px solid #333;
}

/* BAR LABEL: Day of week label under bar (Mon 15, Tue 16, etc) */
.bar-label {
  color: #777777;
  font-size: 0.75rem;
  margin-top: 10px;
}

/* ============ PIE CHART STYLING ============ */

/* PIE CHART CONTAINER: Arranges circle and legend side by side */
.pie-chart-container {
  display: flex;
  align-items: center;
  justify-content: space-around;
  height: 220px;
  flex-wrap: wrap;
  gap: 20px;
}

/* PIE CIRCLE: The actual pie chart visualization */
/* Uses conic-gradient from pieChartStyle computed property */
/* :style binding applies the gradient */
.simulated-pie-circle {
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid #333333;
}

/* PIE LEGEND: List of departments and their colors */
.pie-legend-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* LEGEND ITEM: One department in the list */
.legend-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* LEGEND COLOR DOT: Small colored square matching pie chart segment */
.legend-color-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

/* LEGEND TEXT: Department name and employee count */
.legend-text {
  color: #bbbbbb;
  font-size: 0.85rem;
}

/* ============ ACTIVITY LOG TABLE ============ */

/* ACTIVITY LOG SECTION: Container for activity table */
.activity-log-section {
  background: #0f0f0f;
  border: 1px solid #1c1c1c;
  border-radius: 8px;
  overflow: hidden;
}

.padding-box {
  padding: 25px 25px 15px 25px;
}

.panel-sub-desc {
  color: #666666;
  font-size: 0.85rem;
  margin: 4px 0 0 0;
}

/* TABLE WRAPPER: Makes table scrollable on mobile */
.table-responsive-wrapper {
  width: 100%;
  overflow-x: auto;
}

/* TABLE STYLING: Main activity log table */
.activity-data-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.9rem;
}

/* TABLE HEADER: Column titles */
.activity-data-table th {
  background: #141414;
  color: #555555;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  padding: 14px 25px;
  border-bottom: 1px solid #1c1c1c;
}

/* TABLE DATA CELLS */
.activity-data-table td {
  padding: 18px 25px;
  border-bottom: 1px solid #161616;
  color: #cccccc;
}

/* LAST ROW: Remove bottom border from last row */
.activity-data-table tr:last-child td {
  border-bottom: none;
}

/* ACTIVITY MAIN TEXT: The description column */
.activity-main-text {
  color: #ffffff;
  font-weight: 500;
}

/* ============ CATEGORY TAGS: Color-coded labels ============ */
/* Tags show what type of activity it is (Leave, System, Payroll, etc) */

.category-tag {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 4px;
}

/* TAG COLOR: Leave Management activities (blue) */
.tag-leave {
  background: rgba(26, 58, 95, 0.2);
  color: #5fa4ff;
  border: 1px solid rgba(26, 58, 95, 0.4);
}

/* TAG COLOR: System activities (green) */
.tag-system {
  background: rgba(18, 124, 69, 0.15);
  color: #44ff9a;
  border: 1px solid rgba(18, 124, 69, 0.3);
}

/* TAG COLOR: Payroll activities (gold) */
.tag-payroll {
  background: rgba(191, 161, 0, 0.15);
  color: #ffd700;
  border: 1px solid rgba(191, 161, 0, 0.3);
}

/* TAG COLOR: Profile activities (gray) */
.tag-profile {
  background: rgba(42, 42, 42, 0.4);
  color: #b8b8b8;
  border: 1px solid #333;
}

/* TIMESTAMP COLUMN: Date/time display */
.timestamp-col {
  color: #555555;
  font-size: 0.8rem;
}

/* ============ RESPONSIVE DESIGN (MOBILE) ============ */
/* Adjusts dashboard layout for screens smaller than 768px */

@media (max-width: 768px) {
  /* REDUCE PADDING: Less space on mobile */
  .dashboard-page {
    padding: 20px;
  }

  /* STACK SECTIONS: Hero content below welcome banner on mobile */
  .welcome-banner {
    flex-direction: column;
    align-items: flex-start;
  }

  /* SMALLER HEADING: Reduces heading size on mobile */
  .welcome-banner h1 {
    font-size: 1.8rem;
  }

  /* STAT BOXES: 2 columns on mobile instead of 4 */
  .quick-stats-row {
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
  }

  /* STAT BOX MOBILE: Reduce padding on small screens */
  .stat-box {
    padding: 15px;
  }

  /* STAT VALUE MOBILE: Smaller numbers on mobile */
  .stat-value {
    font-size: 1.5rem;
  }

  /* CHARTS STACK: One column on mobile */
  .analytics-split-grid {
    grid-template-columns: 1fr;
  }

  /* SMALLER CHARTS: Reduce bar and pie chart heights */
  .bar-chart-container {
    height: 180px;
  }

  .pie-chart-container {
    height: 180px;
  }
}
</style>
