<!-- src/components/PeopleView.vue -->
<template>
  <div class="people-page">
    <!-- Hero Header Section -->
    <header class="page-header">
      <div class="header-content">
        <span class="sub-heading">DIRECTORY MANAGEMENT</span>
        <h1>People</h1>
        <p class="subtitle">
          Managing <strong>{{ employees.length }}</strong> active employees
          across <strong>{{ dynamicDepartmentCount }}</strong> departments.
        </p>
      </div>
    </header>

    <!-- Top Action & Filter Controls Bar -->
    <div class="controls-bar">
      <div class="search-box">
        <span class="search-icon">🔍</span>
        <input
          v-model="search"
          type="text"
          placeholder="Search by name, role or email address..."
        />
        <button v-if="search" class="clear-search" @click="search = ''">
          &times;
        </button>
      </div>

      <div class="department-filters">
        <button
          v-for="dept in departments"
          :key="dept"
          :class="['filter-btn', { active: selectedDepartment === dept }]"
          :style="departmentButtonStyle(dept)"
          @click="selectedDepartment = dept"
        >
          {{ dept }}
        </button>

        <!-- Only allow HR staff and Admins to add new employees -->
        <button
          v-if="['admin', 'hr_staff'].includes(userRole)"
          class="btn-add-plus"
          title="Add New Employee"
          @click="openAddModal"
        >
          +
        </button>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="table-container">
      <table class="employee-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Employee Details</th>
            <th>Department</th>
            <th>Role / Position</th>
            <th>Status</th>
            <th>Date Joined</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loading State -->
          <tr v-if="loading">
            <td colspan="7" class="state-cell">
              <div class="spinner"></div>
              <p>Fetching records from backend API...</p>
            </td>
          </tr>

          <!-- Empty Search/Filter State -->
          <tr v-else-if="filteredEmployees.length === 0">
            <td colspan="7" class="state-cell">
              <div class="empty-icon">📁</div>
              <p>No matching employee records found.</p>
              <button class="btn-reset" @click="resetFilters">
                Reset Filters
              </button>
            </td>
          </tr>

          <!-- Employee Rows -->
          <tr
            v-for="emp in filteredEmployees"
            :key="emp.id"
            v-else
            class="table-row"
          >
            <td class="id-cell">#{{ emp.id }}</td>

            <td class="user-cell">
              <div
                class="avatar"
                :style="{ backgroundColor: getDepartmentColor(emp.department) }"
              >
                {{ getInitials(emp.name) }}
              </div>
              <div class="user-info">
                <span class="user-name">{{ emp.name }}</span>
                <span class="user-email">{{ emp.email }}</span>
              </div>
            </td>

            <td>
              <span
                class="dept-pill"
                :style="getDepartmentPillStyle(emp.department)"
              >
                • {{ emp.department || "Unassigned" }}
              </span>
            </td>

            <td class="role-cell">{{ emp.role }}</td>

            <td>
              <span :class="['status-badge', getStatusClass(emp.status)]">
                <span class="status-dot"></span>
                {{ emp.status || "Active" }}
              </span>
            </td>

            <td class="date-cell">{{ formatDate(emp.joinedDate) }}</td>

            <td class="text-right">
              <div class="action-buttons">
                <button
                  class="btn-action-view"
                  title="View Details"
                  @click="openViewModal(emp)"
                >
                  View
                </button>

                <!-- RESTRICTION: Render Delete button ONLY for Admin users -->
                <button
                  v-if="userRole === 'admin'"
                  class="btn-action-delete"
                  title="Delete Record"
                  @click="deleteEmployeeRecord(emp.id)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add Employee Modal Overlay -->
    <transition name="fade">
      <div
        v-if="showAddModal"
        class="modal-backdrop"
        @click.self="closeAddModal"
      >
        <div class="modal-card">
          <div class="modal-card-header">
            <h3>Add New Staff Member</h3>
            <button class="close-btn" @click="closeAddModal">&times;</button>
          </div>

          <form @submit.prevent="submitNewEmployee" novalidate>
            <div class="form-grid">
              <div class="form-field">
                <label>First Name</label>
                <input
                  v-model="newEmployee.first_name"
                  type="text"
                  :class="{ 'input-error': errors.first_name }"
                  placeholder="e.g. Sarah"
                />
                <span v-if="errors.first_name" class="error-msg">{{
                  errors.first_name
                }}</span>
              </div>

              <div class="form-field">
                <label>Last Name</label>
                <input
                  v-model="newEmployee.last_name"
                  type="text"
                  :class="{ 'input-error': errors.last_name }"
                  placeholder="e.g. Mitchell"
                />
                <span v-if="errors.last_name" class="error-msg">{{
                  errors.last_name
                }}</span>
              </div>
            </div>

            <div class="form-field">
              <label>Work Email Address</label>
              <input
                v-model="newEmployee.email"
                type="email"
                :class="{ 'input-error': errors.email }"
                placeholder="s.mitchell@moderntech.com"
              />
              <span v-if="errors.email" class="error-msg">{{
                errors.email
              }}</span>
            </div>

            <div class="form-grid">
              <div class="form-field">
                <label>Role / Position</label>
                <input
                  v-model="newEmployee.role"
                  type="text"
                  :class="{ 'input-error': errors.role }"
                  placeholder="Senior Software Engineer"
                />
                <span v-if="errors.role" class="error-msg">{{
                  errors.role
                }}</span>
              </div>

              <div class="form-field">
                <label>Department</label>
                <select v-model="newEmployee.department">
                  <option value="Software Development">
                    Software Development
                  </option>
                  <option value="Quality Assurance">Quality Assurance</option>
                  <option value="Customer Support">Customer Support</option>
                  <option value="Human Resources">Human Resources</option>
                  <option value="Sales">Sales</option>
                  <option value="Marketing">Marketing</option>
                </select>
              </div>
            </div>

            <div class="form-grid">
              <div class="form-field">
                <label>Employment Status</label>
                <select v-model="newEmployee.status">
                  <option value="Active">Active</option>
                  <option value="On Leave">On Leave</option>
                  <option value="Absent">Absent</option>
                  <option value="Sick">Sick</option>
                </select>
              </div>

              <div class="form-field">
                <label>Monthly Salary (ZAR)</label>
                <input
                  v-model="newEmployee.monthlySalary"
                  type="number"
                  step="0.01"
                  :class="{ 'input-error': errors.monthlySalary }"
                  placeholder="45000"
                />
                <span v-if="errors.monthlySalary" class="error-msg">{{
                  errors.monthlySalary
                }}</span>
              </div>
            </div>

            <div class="modal-card-footer">
              <button
                type="button"
                class="btn-secondary"
                @click="closeAddModal"
              >
                Cancel
              </button>
              <button type="submit" class="btn-primary" :disabled="submitting">
                {{ submitting ? "Saving..." : "Save Employee" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- Detailed Inspection Modal Overlay -->
    <transition name="fade">
      <div
        v-if="selectedEmployee"
        class="modal-backdrop"
        @click.self="closeViewModal"
      >
        <div class="modal-card detail-card">
          <button class="close-btn" @click="closeViewModal">&times;</button>

          <div class="inspect-header">
            <div
              class="avatar-lg"
              :style="{
                backgroundColor: getDepartmentColor(
                  selectedEmployee.department,
                ),
              }"
            >
              {{ getInitials(selectedEmployee.name) }}
            </div>
            <h2>{{ selectedEmployee.name }}</h2>
            <span class="role-subtitle">{{ selectedEmployee.role }}</span>
          </div>

          <div class="inspect-body">
            <div class="detail-row">
              <span class="label">Employee ID:</span>
              <span class="value">#{{ selectedEmployee.id }}</span>
            </div>

            <div class="detail-row">
              <span class="label">Department:</span>
              <span class="value">{{
                selectedEmployee.department || "N/A"
              }}</span>
            </div>

            <div class="detail-row">
              <span class="label">Email Address:</span>
              <span class="value">{{ selectedEmployee.email }}</span>
            </div>

            <div class="detail-row">
              <span class="label">Monthly Salary:</span>
              <span class="value"
                >R {{ formatCurrency(selectedEmployee.monthlySalary) }}</span
              >
            </div>

            <div class="detail-row">
              <span class="label">Status:</span>
              <span
                :class="[
                  'status-badge',
                  getStatusClass(selectedEmployee.status),
                ]"
              >
                {{ selectedEmployee.status }}
              </span>
            </div>

            <div class="detail-row">
              <span class="label">Joined Date:</span>
              <span class="value">{{
                formatDate(selectedEmployee.joinedDate)
              }}</span>
            </div>

            <!-- NEW: Performance Details Section -->
            <hr class="divider" />
            <h4 class="section-title">Performance Summary</h4>

            <div class="detail-row">
              <span class="label">Score / Rating:</span>
              <span class="value">
                <strong>{{ selectedEmployee.performanceScore }} / 5.0</strong>
                ({{ selectedEmployee.ratingCategory }})
              </span>
            </div>

            <div class="detail-row">
              <span class="label">Last Review Date:</span>
              <span class="value">{{
                formatDate(selectedEmployee.lastReviewDate)
              }}</span>
            </div>

            <div class="performance-comments-box">
              <span class="label">Reviewer Comments:</span>
              <p class="comments-text">
                {{ selectedEmployee.performanceComments }}
              </p>
            </div>
          </div>
          <div class="modal-card-footer split-footer">
            <!-- RESTRICTION: Render Delete button inside modal ONLY for Admin users -->
            <button
              v-if="userRole === 'admin'"
              type="button"
              class="btn-danger-outline"
              @click="deleteEmployeeRecord(selectedEmployee.id)"
            >
              Delete Employee
            </button>
            <button type="button" class="btn-secondary" @click="closeViewModal">
              Close
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>
<script>
import axios from "axios";

export default {
  name: "PeopleView",

  props: {
    // Passed down from parent component (e.g., App.vue)
    userRole: {
      type: String,
      default: "employee",
    },
  },

  data() {
    return {
      employees: [],
      loading: true,
      submitting: false,
      search: "",
      selectedDepartment: "All",
      showAddModal: false,
      selectedEmployee: null,

      newEmployee: {
        first_name: "",
        last_name: "",
        email: "",
        role: "",
        department: "Software Development",
        status: "Active",
        monthlySalary: "",
      },
      errors: {},
    };
  },

  mounted() {
    this.fetchEmployees();
  },

  computed: {
    departments() {
      if (!Array.isArray(this.employees)) return ["All"];
      const extracted = this.employees
        .map((emp) => emp.department)
        .filter((dept) => dept && dept.trim() !== "");
      return ["All", ...new Set(extracted)];
    },

    dynamicDepartmentCount() {
      return Math.max(0, this.departments.length - 1);
    },

    filteredEmployees() {
      if (!Array.isArray(this.employees)) return [];

      return this.employees.filter((emp) => {
        const searchTerm = this.search.toLowerCase().trim();
        const empName = (emp.name || "").toLowerCase();
        const empRole = (emp.role || "").toLowerCase();
        const empEmail = (emp.email || "").toLowerCase();

        const matchesSearch =
          empName.includes(searchTerm) ||
          empRole.includes(searchTerm) ||
          empEmail.includes(searchTerm);

        const matchesDepartment =
          this.selectedDepartment === "All" ||
          emp.department === this.selectedDepartment;

        return matchesSearch && matchesDepartment;
      });
    },
  },

  methods: {
    getApiUrl() {
      return (
        import.meta.env.VITE_API_URL ||
        "http://localhost/lca-php/moderntech-hr-system/backend/routes/employees.php"
      );
    },

    async fetchEmployees() {
      this.loading = true;
      try {
        const response = await axios.get(this.getApiUrl(), {
          withCredentials: true,
        });
        if (Array.isArray(response.data)) {
          this.employees = response.data;
        } else {
          this.employees = [];
        }
      } catch (error) {
        console.error("Failed to read employee records from backend:", error);
        this.employees = [];
      } finally {
        this.loading = false;
      }
    },

    validateForm() {
      this.errors = {};
      const alphaRegex = /^[A-Za-z\s-]+$/; // Matches letters, spaces, and hyphens

      // First Name Validation (Letters only, 2-30 characters)
      const firstName = (this.newEmployee.first_name || "").trim();
      if (!firstName) {
        this.errors.first_name = "First name is required.";
      } else if (!alphaRegex.test(firstName)) {
        this.errors.first_name = "First name must contain letters only.";
      } else if (firstName.length < 2 || firstName.length > 30) {
        this.errors.first_name =
          "First name must be between 2 and 30 characters.";
      }

      // Last Name Validation (Letters only, 2-30 characters)
      const lastName = (this.newEmployee.last_name || "").trim();
      if (!lastName) {
        this.errors.last_name = "Last name is required.";
      } else if (!alphaRegex.test(lastName)) {
        this.errors.last_name = "Last name must contain letters only.";
      } else if (lastName.length < 2 || lastName.length > 30) {
        this.errors.last_name =
          "Last name must be between 2 and 30 characters.";
      }

      // Work Email Validation
      const email = (this.newEmployee.email || "").trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email) {
        this.errors.email = "Work email address is required.";
      } else if (!emailRegex.test(email)) {
        this.errors.email = "Please enter a valid email address.";
      }

      // Role / Position Validation (Letters, spaces, hyphens only, 2-50 characters)
      const role = (this.newEmployee.role || "").trim();
      if (!role) {
        this.errors.role = "Role/position title is required.";
      } else if (!alphaRegex.test(role)) {
        this.errors.role = "Role title must contain letters only.";
      } else if (role.length < 2 || role.length > 50) {
        this.errors.role = "Role title must be between 2 and 50 characters.";
      }

      // Salary Validation
      const salary = parseFloat(this.newEmployee.monthlySalary);
      if (
        this.newEmployee.monthlySalary === "" ||
        this.newEmployee.monthlySalary === null ||
        this.newEmployee.monthlySalary === undefined
      ) {
        this.errors.monthlySalary = "Monthly salary is required.";
      } else if (isNaN(salary) || salary <= 0) {
        this.errors.monthlySalary =
          "Salary must be a positive number greater than 0.";
      }

      return Object.keys(this.errors).length === 0;
    },

    async submitNewEmployee() {
      if (!this.validateForm()) {
        return;
      }

      this.submitting = true;

      try {
        const apiUrl = this.getApiUrl();

        const response = await axios.post(apiUrl, this.newEmployee, {
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.status === 200 || response.status === 201) {
          alert("Employee created successfully!");
          this.closeAddModal();
          this.resetNewEmployeeForm();
          await this.fetchEmployees();
        }
      } catch (error) {
        console.error("Failed to post new employee to PHP API:", error);
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          alert(
            `Server Error (${error.response.status}): ${error.response.data.message}`,
          );
        } else {
          alert("Network or Server error occurred.");
        }
      } finally {
        this.submitting = false;
      }
    },

    async deleteEmployeeRecord(id) {
      if (this.userRole !== "admin") {
        alert("Permission denied. Only administrators can delete records.");
        return;
      }

      if (!confirm(`Are you sure you want to delete employee record #${id}?`)) {
        return;
      }

      try {
        await axios.delete(`${this.getApiUrl()}?id=${id}`, {
          withCredentials: true,
        });
        if (this.selectedEmployee && this.selectedEmployee.id === id) {
          this.closeViewModal();
        }
        await this.fetchEmployees();
      } catch (error) {
        console.error("Failed to delete record via PHP backend:", error);
        alert(
          error.response?.data?.message ||
            "Delete failed. Check network tab for details.",
        );
      }
    },

    openAddModal() {
      this.errors = {};
      this.showAddModal = true;
    },

    closeAddModal() {
      this.showAddModal = false;
      this.errors = {};
    },

    openViewModal(emp) {
      this.selectedEmployee = emp;
    },

    closeViewModal() {
      this.selectedEmployee = null;
    },

    resetFilters() {
      this.search = "";
      this.selectedDepartment = "All";
    },

    resetNewEmployeeForm() {
      this.newEmployee = {
        first_name: "",
        last_name: "",
        email: "",
        role: "",
        department: "Software Development",
        status: "Active",
        monthlySalary: "",
      };
      this.errors = {};
    },

    getInitials(name) {
      if (!name) return "??";
      const parts = name.trim().split(" ");
      if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
      return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    },

    getDepartmentColor(department) {
      const palette = {
        "Software Development": "#3b82f6",
        "Quality Assurance": "#8b5cf6",
        "Customer Support": "#10b981",
        "Human Resources": "#06b6d4",
        Sales: "#f59e0b",
        Marketing: "#ef4444",
      };
      return palette[department] || "#64748b";
    },

    getDepartmentPillStyle(department) {
      const color = this.getDepartmentColor(department);
      return {
        color: color,
        borderColor: color,
        backgroundColor: "rgba(255, 255, 255, 0.03)",
      };
    },

    departmentButtonStyle(dept) {
      if (dept === "All") {
        return this.selectedDepartment === "All"
          ? {
              backgroundColor: "#10b981",
              color: "#000000",
              borderColor: "#10b981",
            }
          : {
              backgroundColor: "#1e293b",
              color: "#94a3b8",
              borderColor: "#334155",
            };
      }

      const activeColor = this.getDepartmentColor(dept);
      if (this.selectedDepartment === dept) {
        return {
          backgroundColor: activeColor,
          borderColor: activeColor,
          color: "#ffffff",
        };
      }

      return {
        backgroundColor: "transparent",
        borderColor: activeColor,
        color: activeColor,
      };
    },

    getStatusClass(status) {
      switch (status) {
        case "Active":
          return "status-active";
        case "On Leave":
          return "status-leave";
        case "Absent":
          return "status-absent";
        case "Sick":
          return "status-sick";
        default:
          return "status-default";
      }
    },

    formatCurrency(amount) {
      if (!amount) return "0.00";
      return parseFloat(amount).toLocaleString("en-ZA", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    formatDate(dateString) {
      if (!dateString) return "N/A";
      const parsed = new Date(dateString);
      if (isNaN(parsed.getTime())) return dateString;
      return parsed.toLocaleDateString("en-ZA", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
  },
};
</script>
<style scoped>
/* Core Page Layout */
.people-page {
  padding: 32px 24px;
  max-width: 1400px;
  margin: 0 auto;
  color: #f8fafc;
  font-family:
    system-ui,
    -apple-system,
    BlinkMacSystemFont,
    "Segoe UI",
    Roboto,
    sans-serif;
}

.page-header {
  margin-bottom: 32px;
  text-align: center;
}

.sub-heading {
  color: #10b981;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 2.5px;
  display: block;
  margin-bottom: 8px;
}

.page-header h1 {
  font-size: 2.5rem;
  font-weight: 800;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.subtitle {
  color: #94a3b8;
  font-size: 0.95rem;
  margin: 0;
}

.subtitle strong {
  color: #e2e8f0;
}

/* Controls Header Bar */
.controls-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 280px;
  max-width: 400px;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.85rem;
  color: #64748b;
}

.search-box input {
  width: 100%;
  background-color: #0f172a;
  border: 1px solid #1e293b;
  border-radius: 8px;
  padding: 10px 36px 10px 38px;
  color: #ffffff;
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s ease;
}

.search-box input:focus {
  border-color: #10b981;
}

.clear-search {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #64748b;
  font-size: 1.2rem;
  cursor: pointer;
}

.department-filters {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.filter-btn {
  border: 1px solid;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-add-plus {
  background-color: #10b981;
  color: #022c22;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  font-size: 1.4rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition:
    transform 0.15s ease,
    background-color 0.2s ease;
}

.btn-add-plus:hover {
  background-color: #34d399;
  transform: scale(1.05);
}

/* Table Design */
.table-container {
  background-color: #0b1329;
  border: 1px solid #1e293b;
  border-radius: 12px;
  overflow-x: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

.employee-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.9rem;
}

.employee-table th {
  background-color: #0f172a;
  color: #10b981;
  padding: 14px 20px;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 1px;
  border-bottom: 1px solid #1e293b;
}

.employee-table td {
  padding: 16px 20px;
  border-bottom: 1px solid #1e293b;
  vertical-align: middle;
}

.table-row:hover {
  background-color: rgba(30, 41, 59, 0.4);
}

.id-cell {
  color: #64748b;
  font-weight: 600;
  font-size: 0.85rem;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
  color: #ffffff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.avatar-lg {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 1.4rem;
  color: #ffffff;
  margin: 0 auto 12px auto;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  color: #f8fafc;
}

.user-email {
  font-size: 0.8rem;
  color: #64748b;
}

.dept-pill {
  border: 1px solid;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-block;
}

.role-cell {
  color: #cbd5e1;
}

.date-cell {
  color: #94a3b8;
}

/* Status Badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: currentColor;
}

.status-active {
  background: rgba(16, 185, 129, 0.15);
  color: #10b981;
}
.status-leave {
  background: rgba(245, 158, 11, 0.15);
  color: #f59e0b;
}
.status-absent {
  background: rgba(239, 68, 68, 0.15);
  color: #ef4444;
}
.status-sick {
  background: rgba(6, 182, 212, 0.15);
  color: #06b6d4;
}
.status-default {
  background: rgba(100, 116, 139, 0.15);
  color: #94a3b8;
}

/* Action Buttons */
.text-right {
  text-align: right;
}

.action-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.btn-action-view {
  background: #ffffff;
  color: #0f172a;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.8rem;
  cursor: pointer;
}

.btn-action-delete {
  background: rgba(239, 68, 68, 0.2);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.4);
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.8rem;
  cursor: pointer;
}

.btn-action-delete:hover {
  background: #ef4444;
  color: #ffffff;
}

/* States */
.state-cell {
  text-align: center;
  padding: 48px 20px !important;
  color: #94a3b8;
}

.empty-icon {
  font-size: 2.5rem;
  margin-bottom: 8px;
}

.btn-reset {
  margin-top: 12px;
  background: #1e293b;
  color: #10b981;
  border: 1px solid #10b981;
  padding: 6px 16px;
  border-radius: 6px;
  cursor: pointer;
}

/* Modals System */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-card {
  background: #0f172a;
  border: 1px solid #1e293b;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  padding: 24px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
  position: relative;
}

.modal-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.modal-card-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #f8fafc;
}
/* src/components/PeopleView.vue */

/* MODAL BACKDROP / OVERLAY */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
  box-sizing: border-box;
}

/* MAIN MODAL CONTAINER CARD */
.inspect-modal {
  background: #0b1329;
  border: 1px solid #1e293b;
  border-radius: 16px;
  width: 100%;
  max-width: 520px;

  /* FIX: Restrain height to viewport and enable internal scrolling */
  max-height: 70vh;
  display: flex;
  flex-direction: column;
  overflow: hidden; /* Prevents child elements from bleeding outside rounded corners */
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
}

/* MODAL HEADER: Remains fixed at the top */
.inspect-header {
  padding: 20px 24px 10px 24px;
  flex-shrink: 0;
}

/* MODAL BODY: Scrollable container for long content */
.inspect-body {
  padding: 10px 24px 24px 24px;
  overflow-y: auto; /* Adds scrollbar only inside body when content exceeds height */
  flex: 1;
}

/* CUSTOM SCROLLBAR FOR MODAL BODY */
.inspect-body::-webkit-scrollbar {
  width: 6px;
}

.inspect-body::-webkit-scrollbar-thumb {
  background: #334155;
  border-radius: 4px;
}

.close-btn {
  background: none;
  border: none;
  color: #64748b;
  font-size: 1.5rem;
  cursor: pointer;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-field {
  display: flex;
  flex-direction: column;
  margin-bottom: 14px;
}

.form-field label {
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 6px;
  font-weight: 600;
}

.form-field input,
.form-field select {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 6px;
  padding: 10px 12px;
  color: #ffffff;
  font-size: 0.88rem;
  outline: none;
}

.form-field input:focus,
.form-field select:focus {
  border-color: #10b981;
}

.modal-card-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
}

.split-footer {
  justify-content: space-between;
}

.btn-secondary {
  background: #334155;
  color: #ffffff;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.btn-primary {
  background: #10b981;
  color: #022c22;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 700;
}

.btn-danger-outline {
  background: transparent;
  color: #ef4444;
  border: 1px solid #ef4444;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

/* Inspection Modal Specifics */

.role-subtitle {
  color: #94a3b8;
  font-size: 0.9rem;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row .label {
  color: #94a3b8;
  font-size: 0.85rem;
}

.detail-row .value {
  font-weight: 600;
  color: #f8fafc;
  font-size: 0.85rem;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Loading Spinner */
.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(16, 185, 129, 0.2);
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 12px auto;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
/* Styling for performance section in modal */
.divider {
  border: 0;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  margin: 16px 0 12px 0;
}

.section-title {
  color: #10b981;
  font-size: 0.9rem;
  font-weight: 700;
  margin: 0 0 12px 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.performance-comments-box {
  margin-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.comments-text {
  font-size: 0.85rem;
  color: #cbd5e1;
  background: #0f172a;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #334155;
  margin: 4px 0 0 0;
  font-style: italic;
}
</style>
