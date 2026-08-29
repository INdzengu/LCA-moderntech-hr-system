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
          <span class="panel-legend">Current Week Overview</span>
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
import axios from "axios";

export default {
  name: "DashboardView",

  props: {
    currentUser: {
      type: String,
      default: "HR Admin",
    },
    employees: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      metrics: {
        total_employees: 0,
        employees_present: 0,
        employees_on_leave: 0,
        pending_approvals: 0,
        weekly_attendance: [],
        department_breakdown: [],
        activity_log: [],
      },
      loading: false,
    };
  },

  mounted() {
    this.fetchDashboardData();
  },

  computed: {
    totalEmployees() {
      return this.metrics.total_employees;
    },

    employeesPresent() {
      return this.metrics.employees_present;
    },

    employeesOnLeave() {
      return this.metrics.employees_on_leave;
    },

    pendingLeaveCount() {
      return this.metrics.pending_approvals;
    },

    weeklyAttendanceData() {
      return this.metrics.weekly_attendance || [];
    },

    maxAttendance() {
      return this.totalEmployees > 0 ? this.totalEmployees : 10;
    },

    departmentBreakdown() {
      return this.metrics.department_breakdown || [];
    },

    pieChartStyle() {
      if (!this.departmentBreakdown.length) {
        return { background: "conic-gradient(#1c1c1c 0% 100%)" };
      }

      let gradientString = "conic-gradient(";
      let currentPercentage = 0;

      this.departmentBreakdown.forEach((dept, index) => {
        const percentage = (dept.count / this.totalEmployees) * 100;
        const startPercentage = currentPercentage;
        const endPercentage = currentPercentage + percentage;

        gradientString += `${dept.color} ${startPercentage}% ${endPercentage}%`;

        if (index < this.departmentBreakdown.length - 1) {
          gradientString += ",";
        }
        currentPercentage = endPercentage;
      });

      gradientString += ")";
      return { background: gradientString };
    },

    activityLog() {
      return this.metrics.activity_log || [];
    },
  },

  methods: {
    getApiUrl() {
      return "http://localhost/lca-php/moderntech-hr-system/backend/routes/dashboard.php";
    },

    async fetchDashboardData() {
      this.loading = true;
      try {
        const response = await axios.get(this.getApiUrl());
        if (response.data && response.data.status === "success") {
          this.metrics = response.data.data;
        }
      } catch (error) {
        console.error("Failed to load dashboard data from database:", error);
      } finally {
        this.loading = false;
      }
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
