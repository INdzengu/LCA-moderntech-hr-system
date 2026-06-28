<template>
  <div class="people-page">
    <!-- PAGE HEADER: Title and employee overview -->
    <!-- Shows total employee count and department breakdown -->
    <section class="header">
      <p class="directory">DIRECTORY</p>

      <h1>People</h1>

      <!-- SUBTITLE: Shows key metrics about the workforce -->
      <!-- Displays: total employees and number of departments -->
      <p class="subtitle">
        {{ employees.length }} employees across
        {{ departments.length }} departments
      </p>
    </section>

    <!-- SEARCH AND FILTER BAR -->
    <!-- Allows HR staff to find specific employees quickly -->
    <section class="toolbar">
      <!-- SEARCH BOX: Filter by name, role, or email -->
      <!-- v-model connects to search data property -->
      <!-- Filters filteredEmployees computed property in real-time -->
      <div class="search-box">
        <input
          v-model="search"
          type="text"
          placeholder="Search by name, role or email..."
        />
      </div>

      <!-- FILTER BUTTONS: Filter by department -->
      <!-- Shows all departments as clickable filter buttons -->
      <div class="filters">
        <!-- ALL BUTTON: Shows all employees -->
        <!-- :class adds active styling if selected -->
        <button
          class="all-btn"
          :class="{ activeAll: selectedDepartment === 'All' }"
          @click="selectedDepartment = 'All'"
        >
          All
        </button>

        <!-- DEPARTMENT BUTTONS: One button per department -->
        <!-- v-for loops through departments array -->
        <!-- :style applies department-specific color -->
        <!-- @click sets selectedDepartment to the clicked department -->
        <button
          v-for="dept in departments"
          :key="dept"
          @click="selectedDepartment = dept"
          :style="departmentButtonStyle(dept)"
          class="department-btn"
        >
          {{ dept }}
        </button>
      </div>
    </section>

    <!-- EMPLOYEES TABLE: Shows all filtered employees -->
    <!-- Displays employee information in organized table format -->
    <div class="table-wrapper">
      <table>
        <!-- TABLE HEADER: Column titles -->
        <thead>
          <tr>
            <th>ID</th>
            <th>Employee</th>
            <th>Department</th>
            <th>Role</th>
            <th>Status</th>
            <th>Joined</th>
            <th>View</th>
          </tr>
        </thead>

        <!-- TABLE BODY: Employee rows -->
        <!-- v-for loops through filteredEmployees -->
        <!-- :key helps Vue track items for efficient updates -->
        <tbody>
          <tr v-for="employee in filteredEmployees" :key="employee.id">
            <!-- ID COLUMN: Employee ID (e.g., E001) -->
            <td>{{ employee.id }}</td>

            <!-- EMPLOYEE COLUMN: Avatar, name, and email -->
            <!-- Combines profile information in one column -->
            <td>
              <div class="employee-cell">
                <!-- AVATAR: Circle with employee initials -->
                <!-- :style applies department-specific background color -->
                <div
                  class="avatar"
                  :style="{
                    background: departmentColor(employee.department),
                  }"
                >
                  {{ initials(employee.name) }}
                </div>

                <!-- EMPLOYEE INFO: Name and email -->
                <div>
                  <!-- EMPLOYEE NAME: Full name -->
                  <div class="employee-name">
                    {{ employee.name }}
                  </div>

                  <!-- EMPLOYEE EMAIL: Email address -->
                  <div class="employee-email">
                    {{ employee.email }}
                  </div>
                </div>
              </div>
            </td>

            <!-- DEPARTMENT COLUMN: Department badge -->
            <!-- Shows department name with colored background -->
            <!-- :style applies department color dynamically -->
            <td>
              <span
                class="department-badge"
                :style="{
                  backgroundColor: departmentColor(employee.department) + '20',
                  color: departmentColor(employee.department),
                }"
              >
                ● {{ employee.department }}
              </span>
            </td>

            <!-- ROLE COLUMN: Job title -->
            <!-- Shows employee's position/role -->
            <td>{{ employee.role }}</td>

            <!-- STATUS COLUMN: Employment status with icon -->
            <!-- :class applies color based on status -->
            <!-- Shows emoji icon (✓, 🏖️, ❌, 🏥) for visual indication -->
            <td>
              <span class="status-badge" :class="getStatusClass(employee)">
                {{ getStatusIcon(employee.status) }} {{ employee.status }}
              </span>
            </td>

            <!-- JOINED DATE COLUMN: When employee started -->
            <!-- Shows date employee joined company -->
            <td>{{ employee.joinedDate }}</td>

            <!-- VIEW BUTTON COLUMN: Open employee details -->
            <!-- @click sets selectedEmployee and opens modal -->
            <td>
              <button class="view-btn" @click="selectedEmployee = employee">
                View
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- EMPLOYEE DETAIL MODAL: Popup showing full employee information -->
    <!-- v-if only shows when selectedEmployee has a value -->
    <!-- @click.self closes modal when clicking outside the card -->
    <div
      v-if="selectedEmployee"
      class="modal-overlay"
      @click.self="selectedEmployee = null"
    >
      <div class="employee-card-modal">
        <!-- CLOSE BUTTON: X button at top right -->
        <!-- @click sets selectedEmployee to null (closes modal) -->
        <button class="close-btn" @click="selectedEmployee = null">×</button>

        <!-- MODAL HEADER: Employee avatar and basic info -->
        <div class="card-top">
          <!-- LARGE AVATAR: Big initials circle -->
          <!-- :style applies department color -->
          <div
            class="card-avatar"
            :style="{
              background: departmentColor(selectedEmployee.department),
            }"
          >
            {{ initials(selectedEmployee.name) }}
          </div>

          <!-- EMPLOYEE HEADER INFO -->
          <div>
            <!-- FULL NAME: Employee's full name -->
            <h2>{{ selectedEmployee.name }}</h2>

            <!-- JOB ROLE: Employee's position -->
            <p class="card-role">
              {{ selectedEmployee.role }}
            </p>
          </div>
        </div>

        <!-- MODAL BODY: Detailed employee information -->
        <!-- Shows all relevant employee data -->
        <div class="card-info">
          <!-- EMPLOYEE ID -->
          <div class="info-item">
            <span>ID</span>
            <strong>{{ selectedEmployee.id }}</strong>
          </div>

          <!-- EMAIL ADDRESS -->
          <div class="info-item">
            <span>Email</span>
            <strong>{{ selectedEmployee.email }}</strong>
          </div>

          <!-- DEPARTMENT -->
          <div class="info-item">
            <span>Department</span>
            <strong>{{ selectedEmployee.department }}</strong>
          </div>

          <!-- CURRENT STATUS: Active, On Leave, Absent, etc -->
          <div class="info-item">
            <span>Status</span>
            <strong>{{ selectedEmployee.status }}</strong>
          </div>

          <!-- JOIN DATE: When employee started -->
          <div class="info-item">
            <span>Joined Date</span>
            <strong>{{ selectedEmployee.joinedDate }}</strong>
          </div>

          <!-- MONTHLY SALARY: Current salary amount -->
          <div class="info-item">
            <span>Salary</span>
            <strong> R{{ selectedEmployee.monthlySalary }} </strong>
          </div>

          <!-- PERFORMANCE SCORE: Rating out of 5 -->
          <!-- Ranges from 1-5 stars -->
          <div class="info-item">
            <span>Performance</span>
            <strong> {{ selectedEmployee.performanceScore }}/5 </strong>
          </div>

          <!-- ATTENDANCE: Number of days present -->
          <!-- Shows from attendance tracking data -->
          <div class="info-item">
            <span>Attendance</span>
            <strong>
              {{ selectedEmployee.attendance.present }}
              Present
            </strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PeopleView",

  props: {
    // EMPLOYEES: Central data source with all employee information
    // Passed from App.vue - contains all employee data
    employees: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      // search: Stores text user types in search field
      // Used to filter employees by name, role, or email
      search: "",

      // selectedDepartment: Stores selected department filter
      // "All" = show all employees, or specific department name
      // Used to filter filteredEmployees computed property
      selectedDepartment: "All",

      // selectedEmployee: Stores employee being viewed in modal
      // null = modal is closed
      // Specific employee = modal is open showing that employee
      selectedEmployee: null,
    };
  },

  computed: {
    /**
     * DEPARTMENTS: Unique list of all departments
     *
     * WHAT IT DOES: Extracts unique department names from employees
     * USED FOR: Creating department filter buttons
     *
     * HOW IT WORKS:
     *   1. map() gets department from each employee
     *   2. new Set() removes duplicates
     *   3. Spread operator (...) converts Set back to array
     *
     * EXAMPLE:
     * If employees work in: Dev, QA, Dev, HR, QA
     * Result: [Dev, QA, HR] (duplicates removed)
     */
    departments() {
      return [
        ...new Set(this.employees.map((employee) => employee.department)),
      ];
    },

    /**
     * FILTERED EMPLOYEES: Employees matching search and department filters
     *
     * WHAT IT DOES: Filters employees by two criteria:
     *   1. Search query (name, role, or email contains text)
     *   2. Department selection (All or specific department)
     *
     * HOW IT WORKS:
     *   1. Loop through all employees
     *   2. Check if name/role/email matches search (case-insensitive)
     *   3. Check if department matches selected department
     *   4. Keep only employees that pass both checks
     *
     * WHERE IT'S USED: Powers the table display
     *
     * EXAMPLE:
     * If search="developer" and dept="Software Development"
     * Shows only software developers with "developer" in name/role/email
     */
    filteredEmployees() {
      return this.employees.filter((employee) => {
        // SEARCH MATCH: Does name/role/email contain search term?
        const searchMatch =
          employee.name.toLowerCase().includes(this.search.toLowerCase()) ||
          employee.role.toLowerCase().includes(this.search.toLowerCase()) ||
          employee.email.toLowerCase().includes(this.search.toLowerCase());

        // DEPARTMENT MATCH: Is department selected or "All"?
        const departmentMatch =
          this.selectedDepartment === "All" ||
          employee.department === this.selectedDepartment;

        // KEEP ONLY IF BOTH CONDITIONS ARE TRUE
        return searchMatch && departmentMatch;
      });
    },
  },

  methods: {
    /**
     * GET INITIALS: Extract first letters from employee name
     *
     * WHAT IT DOES: Gets first letter of first and last name
     * USED FOR: Avatar display (e.g., "JD" for "John Doe")
     *
     * HOW IT WORKS:
     *   1. Split name by spaces: "John Doe" → ["John", "Doe"]
     *   2. Get first letter of each part: ["J", "D"]
     *   3. Join together: "JD"
     *
     * EXAMPLE:
     *   "Sarah Mitchell" → "SM"
     *   "John Doe" → "JD"
     */
    initials(name) {
      return name
        .split(" ")
        .map((word) => word[0])
        .join("");
    },

    /**
     * DEPARTMENT COLOR: Returns color for each department
     *
     * WHAT IT DOES: Maps department name to a consistent color
     * USED FOR: Avatar backgrounds and badges
     *
     * COLORS ASSIGNED:
     *   - Software Development: Blue (#3b82f6)
     *   - Quality Assurance: Purple (#8b5cf6)
     *   - Customer Support: Green (#10b981)
     *   - Human Resources: Cyan (#06b6d4)
     *   - Sales: Amber (#f59e0b)
     *   - Marketing: Red (#ef4444)
     *   - Unknown: Gray (#64748b)
     *
     * This creates visual department identification
     */
    departmentColor(department) {
      const colors = {
        "Software Development": "#3b82f6",
        "Quality Assurance": "#8b5cf6",
        "Customer Support": "#10b981",
        "Human Resources": "#06b6d4",
        Sales: "#f59e0b",
        Marketing: "#ef4444",
      };

      return colors[department] || "#64748b";
    },

    /**
     * DEPARTMENT BUTTON STYLE: Sets styling for department filter buttons
     *
     * WHAT IT DOES: Returns dynamic style object for department button
     * HOW IT WORKS:
     *   If department is selected (active):
     *     - Background color = department color
     *     - Text color = white
     *     - Shows button is active
     *   If department is not selected:
     *     - Background = transparent
     *     - Text color = department color
     *     - Border = department color
     *     - Shows button is clickable
     *
     * USED FOR: :style binding on department buttons
     */
    departmentButtonStyle(dept) {
      if (this.selectedDepartment === dept) {
        // ACTIVE STATE: Filled background with department color
        return {
          backgroundColor: this.departmentColor(dept),
          borderColor: this.departmentColor(dept),
          color: "#ffffff",
        };
      }

      // INACTIVE STATE: Transparent with colored border and text
      return {
        color: this.departmentColor(dept),
        borderColor: this.departmentColor(dept),
        backgroundColor: "transparent",
      };
    },

    /**
     * GET STATUS CLASS: Returns CSS class based on employee status
     *
     * WHAT IT DOES: Maps employee status to a CSS class for styling
     * HOW IT WORKS: Uses statusMap object to match status to class name
     *
     * STATUS CLASSES:
     *   - "Active" → "status-active" (green)
     *   - "On Leave" → "status-leave" (blue)
     *   - "Absent" → "status-absent" (red)
     *   - "Sick" → "status-sick" (amber)
     *
     * USED FOR: :class binding on status badges
     * This allows different colors for different statuses
     */
    getStatusClass(employee) {
      const statusMap = {
        Active: "status-active",
        "On Leave": "status-leave",
        Absent: "status-absent",
        Sick: "status-sick",
      };
      return statusMap[employee.status] || "status-unknown";
    },

    /**
     * GET STATUS ICON: Returns emoji icon for employee status
     *
     * WHAT IT DOES: Displays visual emoji indicator next to status text
     * HOW IT WORKS: Maps status to appropriate emoji
     *
     * STATUS ICONS:
     *   - "Active" → "✓" (checkmark)
     *   - "On Leave" → "🏖️" (beach emoji)
     *   - "Absent" → "❌" (X mark)
     *   - "Sick" → "🏥" (hospital emoji)
     *
     * USED FOR: Display before status text in table
     * Helps HR staff scan status at a glance
     */
    getStatusIcon(status) {
      const iconMap = {
        Active: "✓",
        "On Leave": "🏖️",
        Absent: "❌",
        Sick: "🏥",
      };
      return iconMap[status] || "◯";
    },
  },
};
</script>

<style scoped>
/* ============ MAIN PAGE CONTAINER ============ */
.people-page {
  color: white;
  width: 100%;
}

/* ============ PAGE HEADER ============ */
/* Centered header with title and stats */

.header {
  text-align: center;
  margin-bottom: 40px;
}

/* DIRECTORY TAG: Green label above title */
.directory {
  color: #44ff9a;
  letter-spacing: 4px;
  font-size: 0.8rem;
  font-weight: 600;
}

/* MAIN HEADING: "People" title */
.header h1 {
  font-size: 3rem;
  margin: 10px 0;
}

/* SUBTITLE: Shows employee and department count */
.subtitle {
  color: #94a3b8;
}

/* ============ TOOLBAR: SEARCH AND FILTERS ============ */

.toolbar {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  margin-bottom: 25px;
}

/* SEARCH BOX: Filter by name, role, email */
.search-box input {
  width: 300px;
  padding: 12px 16px;
  border-radius: 12px;
  border: 1px solid #1f2937;
  background: #111827;
  color: white;
  outline: none;
}

/* SEARCH INPUT FOCUS: Green border */
.search-box input:focus {
  border-color: #44ff9a;
}

/* FILTER BUTTONS ROW */
.filters {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

/* GENERIC BUTTON STYLING */
.filters button {
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid;
  cursor: pointer;
  transition: 0.2s;
  font-size: 0.9rem;
}

/* BUTTON HOVER: Slight lift effect */
.filters button:hover {
  transform: translateY(-2px);
}

/* ALL BUTTON: Gray button for showing all employees */
.all-btn {
  border-color: #64748b !important;
  color: #cbd5e1;
  background: transparent;
}

/* ALL BUTTON ACTIVE STATE: Green when selected */
.activeAll {
  background: #44ff9a;
  color: black;
  border-color: #44ff9a;
  font-weight: 600;
}

/* ============ EMPLOYEES TABLE ============ */

.table-wrapper {
  background: #111827;
  border: 1px solid #1f2937;
  border-radius: 18px;
  overflow: hidden;
  overflow-x: auto;
}

/* TABLE STYLING */
table {
  width: 100%;
  border-collapse: collapse;
}

/* TABLE HEADER */
thead {
  background: #0f172a;
}

/* HEADER CELLS */
th {
  color: #44ff9a;
  text-align: left;
  padding: 18px;
  font-size: 0.9rem;
  font-weight: 600;
}

/* TABLE DATA CELLS */
td {
  padding: 18px;
  border-top: 1px solid #1f2937;
}

/* ROW HOVER: Slight background change */
tbody tr:hover {
  background: rgba(255, 255, 255, 0.03);
}

/* ============ EMPLOYEE CELL ============ */
/* Avatar + name + email in one column */

.employee-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* AVATAR: Circle with department color and initials */
.avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  font-weight: bold;
  flex-shrink: 0;
}

/* EMPLOYEE NAME: Bold full name */
.employee-name {
  font-weight: 600;
}

/* EMPLOYEE EMAIL: Gray email address */
.employee-email {
  color: #94a3b8;
  font-size: 0.85rem;
}

/* ============ BADGES ============ */

/* DEPARTMENT BADGE: Colored department indicator */
.department-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

/* STATUS BADGE: Color-coded status indicator */
.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.2s ease;
}

/* ============ STATUS BADGE COLORS ============ */

/* ACTIVE STATUS: Green background and text */
.status-active {
  background: rgba(68, 255, 154, 0.15);
  color: #44ff9a;
  border: 1px solid rgba(68, 255, 154, 0.3);
}

/* ACTIVE HOVER: Brighter green on hover */
.status-active:hover {
  background: rgba(68, 255, 154, 0.25);
  box-shadow: 0 0 8px rgba(68, 255, 154, 0.2);
}

/* ON LEAVE STATUS: Blue background and text */
.status-leave {
  background: rgba(59, 130, 246, 0.15);
  color: #60a5fa;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

/* ON LEAVE HOVER: Brighter blue on hover */
.status-leave:hover {
  background: rgba(59, 130, 246, 0.25);
  box-shadow: 0 0 8px rgba(59, 130, 246, 0.2);
}

/* ABSENT STATUS: Red background and text */
.status-absent {
  background: rgba(239, 68, 68, 0.15);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

/* ABSENT HOVER: Brighter red on hover */
.status-absent:hover {
  background: rgba(239, 68, 68, 0.25);
  box-shadow: 0 0 8px rgba(239, 68, 68, 0.2);
}

/* SICK STATUS: Amber/orange background and text */
.status-sick {
  background: rgba(245, 158, 11, 0.15);
  color: #f59e0b;
  border: 1px solid rgba(245, 158, 11, 0.3);
}

/* SICK HOVER: Brighter amber on hover */
.status-sick:hover {
  background: rgba(245, 158, 11, 0.25);
  box-shadow: 0 0 8px rgba(245, 158, 11, 0.2);
}

/* UNKNOWN STATUS: Gray (fallback) */
.status-unknown {
  background: rgba(107, 114, 128, 0.15);
  color: #9ca3af;
  border: 1px solid rgba(107, 114, 128, 0.3);
}

/* ============ VIEW BUTTON ============ */
/* Opens employee detail modal */

.view-btn {
  border: none;
  background: transparent;
  color: #60a5fa;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}

/* VIEW BUTTON HOVER: Underline and lighter blue */
.view-btn:hover {
  color: #93c5fd;
  text-decoration: underline;
}

/* ============ MODAL OVERLAY ============ */
/* Dark backdrop behind modal */

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.85);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

/* ============ EMPLOYEE CARD MODAL ============ */
/* Popup showing detailed employee information */

.employee-card-modal {
  width: 450px;
  max-width: 90%;
  background: #111827;
  border: 1px solid #1f2937;
  border-radius: 24px;
  padding: 30px;
  position: relative;
}

/* CLOSE BUTTON: X button at top right */
.close-btn {
  position: absolute;
  right: 20px;
  top: 15px;
  border: none;
  background: transparent;
  color: white;
  font-size: 28px;
  cursor: pointer;
}

/* ============ MODAL HEADER ============ */
/* Employee avatar and basic info */

.card-top {
  display: flex;
  align-items: center;
  gap: 18px;
  margin-bottom: 30px;
}

/* LARGE AVATAR: Big initials circle with department color */
.card-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  color: white;
  font-weight: bold;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* EMPLOYEE ROLE: Job title under name */
.card-role {
  color: #94a3b8;
  margin-top: 5px;
}

/* ============ MODAL BODY ============ */
/* Detailed employee information */

.card-info {
  display: grid;
  gap: 14px;
}

/* INFORMATION ROW: Label + value pair */
.info-item {
  display: flex;
  justify-content: space-between;
  padding-bottom: 10px;
  border-bottom: 1px solid #1f2937;
}

/* LABEL: Gray text for field names */
.info-item span {
  color: #94a3b8;
}

/* VALUE: White text for information */
.info-item strong {
  color: white;
}

/* ============ RESPONSIVE DESIGN (MOBILE) ============ */
/* Adjusts layout for screens smaller than 768px */

@media (max-width: 768px) {
  /* TOOLBAR STACKS VERTICALLY */
  .toolbar {
    flex-direction: column;
    align-items: stretch;
  }

  /* SEARCH FULL WIDTH */
  .search-box input {
    width: 100%;
  }

  /* BUTTONS CENTERED */
  .filters {
    justify-content: center;
  }

  /* SMALLER HEADING */
  .header h1 {
    font-size: 2.3rem;
  }

  /* SMALLER TABLE PADDING */
  th,
  td {
    padding: 12px;
  }
}
</style>
