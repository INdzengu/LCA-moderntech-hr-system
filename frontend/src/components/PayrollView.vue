<template>
  <div class="payroll-page">
    <!-- PAGE HEADER: Title and description of Payroll section -->
    <section class="header-section">
      <p class="section-tag">FINANCIAL MANAGEMENT</p>
      <h1>Payroll Management</h1>
      <p class="subtitle-text">
        Review employee earnings, calculate local tax deductions, and simulate
        salary adjustments.
      </p>
    </section>

    <!-- SUMMARY CARDS: Key payroll statistics at top -->
    <!-- These cards show important financial information at a glance -->
    <section class="stats-summary-grid">
      <!-- CARD 1: Total Monthly Payroll Cost -->
      <!-- Shows total amount the company pays all employees per month -->
      <div class="summary-card">
        <div class="card-header">
          <span>💰</span>
          <p class="card-label">Total Monthly Outflow</p>
        </div>
        <!-- Shows total of all monthly salaries in green -->
        <p class="card-number accent-green">R {{ totalMonthlyOutflow }}</p>
      </div>

      <!-- CARD 2: Total Deductions -->
      <!-- Shows total amount deducted from all employees (taxes, benefits, etc) -->
      <div class="summary-card">
        <div class="card-header">
          <span>💸</span>
          <p class="card-label">Total Deductions</p>
        </div>
        <!-- Shows total deductions in red to indicate money going out -->
        <p class="card-number deduction-red">R {{ totalCompanyDeductions }}</p>
      </div>

      <!-- CARD 3: Annual Payroll Projection -->
      <!-- Shows estimated annual payroll (monthly × 12) -->
      <!-- Helps with budget planning for the year -->
      <div class="summary-card">
        <div class="card-header">
          <span>📈</span>
          <p class="card-label">Annual Payroll Projection</p>
        </div>

        <!-- SUCCESS NOTIFICATION: Shows when salary update succeeds -->
        <!-- Only displays if successMessage has content (v-if) -->
        <div v-if="successMessage" class="notification notification-success">
          {{ successMessage }}
        </div>

        <!-- Shows annual payroll in green -->
        <p class="card-number accent-green">R {{ annualPayrollProjection }}</p>
      </div>
    </section>

    <!-- ERROR NOTIFICATION: Shows validation errors or operation failures -->
    <!-- Only displays if errorMessage has content -->
    <div v-if="errorMessage" class="notification notification-error">
      {{ errorMessage }}
    </div>

    <!-- MAIN WORKSPACE: Two-column layout for employee list and payslip -->
    <div class="workspace-layout">
      <!-- LEFT PANEL: Employee List -->
      <!-- Scrollable list of employees to select from -->
      <div class="left-panel">
        <h3 class="panel-heading">Employee Payroll List</h3>

        <!-- SEARCH INPUT: Filter employees by name or role -->
        <!-- v-model connects to searchQuery data property -->
        <!-- Filters filteredEmployees computed property in real-time -->
        <div class="search-wrapper">
          <input
            type="text"
            placeholder="Search employee by name or role..."
            v-model="searchQuery"
          />
        </div>

        <!-- EMPLOYEE LIST: Scrollable container with employee items -->
        <div class="employee-scroll-container">
          <!-- LOOP THROUGH FILTERED EMPLOYEES -->
          <!-- v-for creates a clickable item for each employee -->
          <!-- :key helps Vue track items efficiently -->
          <!-- :class adds highlight when employee is selected -->
          <div
            v-for="employee in filteredEmployees"
            :key="employee.id"
            class="employee-list-item"
            :class="{
              'selected-active-item':
                selectedEmployee && selectedEmployee.id === employee.id,
            }"
            @click="setActiveEmployee(employee)"
          >
            <!-- AVATAR: Shows employee initials in circle -->
            <div class="initials-badge">
              {{ getInitials(employee.name) }}
            </div>

            <!-- EMPLOYEE META: Name, ID, and role -->
            <div class="meta-details">
              <!-- EMPLOYEE NAME AND ID: e.g., "John Doe (E001)" -->
              <p class="name-string">{{ employee.name }} ({{ employee.id }})</p>

              <!-- EMPLOYEE ROLE: e.g., "Software Developer" -->
              <p class="role-string">
                {{ employee.role }}
              </p>
            </div>
          </div>

          <!-- EMPTY STATE: Shows when search returns no results -->
          <div v-if="filteredEmployees.length === 0" class="empty-results">
            No matching profiles found.
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL: Payslip Display and Actions -->
      <!-- Shows detailed payroll information for selected employee -->
      <div class="right-panel">
        <h3 class="panel-heading">Payroll Interactive Canvas</h3>

        <!-- PAYSLIP CARD: Shows only if an employee is selected -->
        <!-- v-if prevents errors when no employee selected -->
        <div v-if="selectedEmployee" class="payslip-wrapper-card">
          <!-- PAYSLIP HEADER: Employee info and initials -->
          <div class="payslip-top-row">
            <!-- LARGE AVATAR: Employee initials circle -->
            <div class="large-initials-badge">
              {{ getInitials(selectedEmployee.name) }}
            </div>

            <!-- EMPLOYEE INFORMATION -->
            <div class="employee-header-text">
              <!-- HEADING: Shows employee name and ID -->
              <h4>
                SALARY BREAKDOWN:
                {{ selectedEmployee.name.toUpperCase() }}
                ({{ selectedEmployee.id }})
              </h4>

              <!-- JOB ROLE: Shows the employee's position -->
              <p class="sub-role">
                {{ selectedEmployee.role }}
              </p>

              <!-- DEPARTMENT: Shows which department they work in -->
              <p class="sub-dept">
                Department:
                {{ selectedEmployee.department || "General" }}
              </p>
            </div>
          </div>
          <div class="salary-history-section">
            <div class="dashed-section-heading">SALARY INCREMENT HISTORY</div>
            <div
              v-if="
                selectedEmployee.history && selectedEmployee.history.length > 0
              "
              class="history-list"
            >
              <div
                v-for="record in selectedEmployee.history"
                :key="record.id"
                class="history-item"
              >
                <div class="history-main-info">
                  <span class="history-date">{{
                    formatDate(record.effective_date)
                  }}</span>
                  <span class="history-reason">{{ record.reason }}</span>
                </div>
                <div class="history-amounts">
                  <span class="history-old" v-if="record.old_salary">
                    R{{ Number(record.old_salary).toLocaleString() }} &rarr;
                  </span>
                  <span class="history-new accent-green">
                    R{{ Number(record.new_salary).toLocaleString() }}
                  </span>
                  <span
                    class="history-badge"
                    v-if="record.increase_percentage > 0"
                  >
                    +{{ record.increase_percentage }}%
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="history-empty">
              No historical adjustments recorded.
            </div>
          </div>
          <!-- PAYSLIP DATA ROWS: Salary and deduction calculations -->
          <div class="payslip-data-rows">
            <!-- GROSS SALARY ROW -->
            <!-- Shows total monthly salary before any deductions -->
            <div class="invoice-data-row">
              <span class="muted-label"> Gross Monthly Salary </span>

              <span class="bright-value text-bold">
                R {{ selectedEmployee.monthlySalary }}
              </span>
            </div>

            <!-- DEDUCTIONS SECTION HEADING -->
            <!-- Indicates start of all deductions -->
            <div class="dashed-section-heading">DEDUCTIONS</div>

            <!-- DEDUCTION 1: UIF (Unemployment Insurance Fund) -->
            <!-- South African mandatory deduction: 1% of salary -->
            <!-- calculateUIF() method computes the amount -->
            <div class="invoice-data-row secondary-dim">
              <span>UIF (1%)</span>

              <span class="deduction-red">
                -R {{ calculateUIF(selectedEmployee.monthlySalary) }}
              </span>
            </div>

            <!-- DEDUCTION 2: PAYE (Personal Income Tax) -->
            <!-- South African income tax estimation -->
            <!-- calculatePAYE() uses tax brackets based on salary -->
            <!-- Higher salary = higher tax rate -->
            <div class="invoice-data-row secondary-dim">
              <span>PAYE Estimate</span>

              <span class="deduction-red">
                -R {{ calculatePAYE(selectedEmployee.monthlySalary) }}
              </span>
            </div>

            <!-- DEDUCTION 3: Pension Fund Contribution -->
            <!-- Standard employer-employee retirement contribution: 7.5% -->
            <!-- Employee contribution towards retirement savings -->
            <div class="invoice-data-row secondary-dim">
              <span>Pension Fund (7.5%)</span>

              <span class="deduction-red">
                -R {{ calculatePension(selectedEmployee.monthlySalary) }}
              </span>
            </div>

            <!-- DEDUCTION 4: Medical Aid -->
            <!-- Health insurance contribution: 2% of salary -->
            <!-- Employee's share of medical benefits -->
            <div class="invoice-data-row secondary-dim">
              <span>Medical Aid (2%)</span>

              <span class="deduction-red">
                -R {{ calculateMedicalAid(selectedEmployee.monthlySalary) }}
              </span>
            </div>

            <!-- TOTAL DEDUCTIONS ROW -->
            <!-- Sum of all deductions above -->
            <!-- Shows total amount taken from gross salary -->
            <div class="invoice-data-row deductions-total-line">
              <span class="muted-label"> Total Deductions </span>

              <span class="deduction-red">
                -R
                {{ calculateTotalDeductions(selectedEmployee.monthlySalary) }}
              </span>
            </div>

            <!-- NET PAY ROW: Final amount employee takes home -->
            <!-- Calculated as: Gross Salary - Total Deductions -->
            <!-- This is what actually gets paid to the employee -->
            <!-- Highlighted in green as the important takeaway number -->
            <div class="invoice-data-row final-net-payout-box">
              <span class="net-payout-label"> NET PAY </span>

              <span class="net-payout-value accent-green">
                R {{ calculateNetPay(selectedEmployee.monthlySalary) }}
              </span>
            </div>
          </div>

          <!-- SALARY ADJUSTMENT ACTIONS -->
          <!-- Allows HR to simulate salary increases -->
          <div class="payslip-actions-block">
            <!-- INPUT + BUTTON ROW: For salary increase simulation -->
            <div class="horizontal-input-row">
              <!-- PERCENTAGE INPUT: User enters increase percentage -->
              <!-- v-model.number converts to number type -->
              <!-- min/max attributes restrict values 1-100 -->
              <input
                type="number"
                placeholder="Increase % (e.g. 5)"
                v-model.number="salaryIncreasePercentage"
                min="1"
                max="100"
              />

              <!-- SIMULATE BUTTON: Applies the salary increase -->
              <!-- @click calls applyLocalSalaryIncrease() method -->
              <!-- :disabled prevents clicking while processing -->
              <!-- Button text changes during processing -->
              <button
                class="simulate-increase-btn"
                @click="applyLocalSalaryIncrease"
                :disabled="isProcessing"
              >
                {{
                  isProcessing
                    ? "Processing..."
                    : "Simulate Local Annual Increase"
                }}
              </button>
            </div>
          </div>
        </div>

        <!-- EMPTY STATE: Shows when no employee selected -->
        <!-- Appears before user selects an employee from left panel -->
        <div v-else class="blank-fallback-state">
          <div class="card-icon">💳</div>

          <p>
            Please select an employee from the left panel to view their detailed
            payroll breakdown.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  name: "PayrollView",
  data() {
    return {
      employees: [],
      selectedEmployee: null,
      searchQuery: "",
      salaryIncreasePercentage: null,
      isProcessing: false,
      successMessage: "",
      errorMessage: "",
      activeTab: "overview",
    };
  },

  computed: {
    filteredEmployees() {
      if (!this.searchQuery) return this.employees;
      const query = this.searchQuery.toLowerCase();
      return this.employees.filter((emp) => {
        return (
          emp.name?.toLowerCase().includes(query) ||
          (emp.department && emp.department.toLowerCase().includes(query)) ||
          (emp.role && emp.role.toLowerCase().includes(query))
        );
      });
    },

    totalMonthlyOutflow() {
      return this.employees.reduce(
        (sum, emp) => sum + (Number(emp.monthlySalary) || 0),
        0,
      );
    },

    totalCompanyDeductions() {
      return Math.round(this.totalMonthlyOutflow * 0.15);
    },

    annualPayrollProjection() {
      return this.totalMonthlyOutflow * 12;
    },

    totalMonthlyPayroll() {
      return this.totalMonthlyOutflow;
    },
  },

  mounted() {
    this.fetchPayrollData();
  },
  methods: {
    getApiUrl() {
      return (
        import.meta.env.VITE_API_URL ||
        "http://localhost/lca-php/moderntech-hr-system/backend/routes/payroll.php"
      );
    },

    formatDate(dateString) {
      if (!dateString) return "N/A";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-ZA", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },

    formatCurrency(amount) {
      return "R" + Number(amount || 0).toLocaleString();
    },

    // --- TEMPLATE HELPER METHODS ---
    getInitials(name) {
      if (!name) return "??";
      return name
        .split(" ")
        .map((word) => word[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
    },

    calculateUIF(salary) {
      const val = Number(salary) || 0;
      return Math.round(val * 0.01).toLocaleString();
    },

    calculatePAYE(salary) {
      const val = Number(salary) || 0;
      let tax = 0;
      if (val > 20000) tax = val * 0.18;
      else if (val > 10000) tax = val * 0.12;
      else tax = val * 0.05;
      return Math.round(tax).toLocaleString();
    },

    calculatePension(salary) {
      const val = Number(salary) || 0;
      return Math.round(val * 0.075).toLocaleString();
    },

    calculateMedicalAid(salary) {
      const val = Number(salary) || 0;
      return Math.round(val * 0.02).toLocaleString();
    },

    calculateTotalDeductions(salary) {
      const val = Number(salary) || 0;
      const uif = val * 0.01;
      const pension = val * 0.075;
      const medical = val * 0.02;
      let paye = 0;
      if (val > 20000) paye = val * 0.18;
      else if (val > 10000) paye = val * 0.12;
      else paye = val * 0.05;

      return Math.round(uif + paye + pension + medical).toLocaleString();
    },

    calculateNetPay(salary) {
      const val = Number(salary) || 0;
      const rawDeductions =
        val * 0.01 +
        val * 0.075 +
        val * 0.02 +
        (val > 20000 ? val * 0.18 : val > 10000 ? val * 0.12 : val * 0.05);

      return Math.round(val - rawDeductions).toLocaleString();
    },

    // --- SELECTION & DATA FETCHING ---
    setActiveEmployee(employee) {
      this.selectEmployee(employee);
    },

    selectEmployee(employee) {
      this.selectedEmployee = employee;
      this.successMessage = "";
      this.errorMessage = "";
    },

    parseSalary(value) {
      if (!value) return 0;
      // Strip non-numeric characters except decimals to handle formatting anomalies like "42000. 00"
      const cleaned = String(value).replace(/[^0-9.]/g, "");
      return parseFloat(cleaned) || 0;
    },

    async fetchPayrollData() {
      try {
        // Dynamically fetch from getApiUrl() (which returns payroll.php)
        const response = await axios.get(this.getApiUrl());

        if (response.data && response.data.status === "success") {
          // Map data with sanitization
          this.employees = response.data.data.map((emp) => ({
            ...emp,
            monthlySalary: this.parseSalary(emp.monthlySalary),
            departmentName: this.getDepartmentName(emp.department),
            jobRole:
              emp.history && emp.history.length > 0
                ? emp.history[0].reason.replace(/^Initial hire - /, "")
                : emp.role,
          }));

          if (this.selectedEmployee) {
            const current = this.employees.find(
              (e) => e.id === this.selectedEmployee.id,
            );
            if (current) this.selectedEmployee = current;
          } else if (this.employees.length > 0) {
            this.selectedEmployee = this.employees[0];
          }
        }
      } catch (error) {
        console.error("Error fetching payroll data:", error);
        this.errorMessage = "Failed to load employee payroll records.";
      }
    },
    getDepartmentName(deptId) {
      const departments = {
        1: "Engineering",
        2: "Quality Assurance",
        3: "Customer Support",
        4: "Human Resources",
        5: "Sales",
        6: "Marketing",
      };
      return departments[String(deptId)] || "General";
    },

    async applyLocalSalaryIncrease() {
      this.successMessage = "";
      this.errorMessage = "";

      if (!this.selectedEmployee) {
        this.errorMessage = "❌ Please select an employee first.";
        return;
      }

      if (
        !this.salaryIncreasePercentage ||
        this.salaryIncreasePercentage <= 0
      ) {
        this.errorMessage =
          "❌ Please enter a valid percentage greater than 0%.";
        return;
      }

      this.isProcessing = true;

      try {
        const currentSalary = Number(this.selectedEmployee.monthlySalary) || 0;
        const increaseAmount =
          currentSalary * (this.salaryIncreasePercentage / 100);
        const newSalarySum = Math.round(currentSalary + increaseAmount);

        const response = await axios.post(this.getApiUrl(), {
          employee_id: this.selectedEmployee.id,
          new_salary: newSalarySum,
          increase_percentage: this.salaryIncreasePercentage,
          reason: `Annual Increase (${this.salaryIncreasePercentage}%)`,
        });

        if (response.data && response.data.status === "success") {
          this.successMessage = `✅ Salary updated for ${this.selectedEmployee.name}!`;
          this.salaryIncreasePercentage = null;
          await this.fetchPayrollData();
        } else {
          this.errorMessage =
            response.data.message || "Failed to update salary.";
        }
      } catch (error) {
        console.error("Error updating salary:", error);
        this.errorMessage = "❌ Server error occurred while updating salary.";
      } finally {
        this.isProcessing = false;
      }
    },

    printPayslip() {
      if (!this.selectedEmployee) return;
      window.print();
    },
  },
};
</script>
<style scoped>
/* ============ MAIN PAYROLL PAGE ============ */
/* Dark background, full viewport height */
.payroll-page {
  background: #050505;
  min-height: 100vh;
  color: #ffffff;
  padding: 40px 20px;
  box-sizing: border-box;
}

/* ============ PAGE HEADER ============ */
/* Centered header with title and description */

.header-section {
  text-align: center;
  margin-bottom: 40px;
}

/* SECTION TAG: Green uppercase label (e.g., "FINANCIAL MANAGEMENT") */
.section-tag {
  color: #44ff9a;
  letter-spacing: 4px;
  font-weight: 600;
  font-size: 0.85rem;
  margin-bottom: 5px;
}

/* MAIN HEADING: Large title */
.header-section h1 {
  font-size: 3rem;
  margin: 10px 0;
}

/* SUBTITLE: Gray description text */
.subtitle-text {
  color: #9f9f9f;
  font-size: 1.05rem;
  max-width: 700px;
  margin: 0 auto;
  line-height: 1.5;
}

/* ============ SUMMARY STATISTICS GRID ============ */
/* Three summary cards in responsive grid */
/* Shows total payroll, deductions, and projections */

.stats-summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

/* INDIVIDUAL SUMMARY CARD */
.summary-card {
  background: #0f0f0f;
  border: 1px solid #222222;
  border-radius: 20px;
  padding: 25px;
  text-align: left;
}

/* CARD HEADER: Contains icon and label */
.summary-card .card-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.summary-card .card-header span {
  font-size: 1.5rem;
}

/* CARD LABEL: Description text */
.card-label {
  color: #888888;
  font-size: 0.9rem;
  margin-bottom: 12px;
}

/* CARD NUMBER: Large statistic value */
.card-number {
  font-size: 2rem;
  font-weight: bold;
  margin: 0;
}

/* GREEN TEXT: For income/positive numbers */
.accent-green {
  color: #44ff9a;
}

/* RED TEXT: For deductions/costs */
.deduction-red {
  color: #ef4444;
}

/* ============ WORKSPACE LAYOUT ============ */
/* Two-column layout: employee list (left) + payslip (right) */
/* Uses grid for responsive design */

.workspace-layout {
  display: grid;
  grid-template-columns: 380px 1fr;
  gap: 30px;
  align-items: start;
}

/* LEFT PANEL: Employee List */
.left-panel {
  background: #0f0f0f;
  border: 1px solid #222222;
  border-radius: 20px;
  padding: 20px;
  height: 600px;
  display: flex;
  flex-direction: column;
}

/* RIGHT PANEL: Payslip Display */
.right-panel {
  background: #0f0f0f;
  border: 1px solid #222222;
  border-radius: 20px;
  padding: 25px;
  /* FIX: Change fixed height: 600px to min-height and enable scrolling */
  min-height: 600px;
  max-height: 85vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

/* Add custom scrollbar styling for the right panel */
.right-panel::-webkit-scrollbar {
  width: 6px;
}
.right-panel::-webkit-scrollbar-thumb {
  background: #222222;
  border-radius: 4px;
}

/* ============ EMPLOYEE LIST SEARCH ============ */

.search-wrapper input {
  width: 100%;
  background: #161616;
  border: 1px solid #2a2a2a;
  border-radius: 12px;
  color: #ffffff;
  padding: 12px 14px;
  font-size: 0.9rem;
  outline: none;
  margin-bottom: 20px;
}

/* SEARCH INPUT FOCUS: Green border when focused */
.search-wrapper input:focus {
  border-color: #44ff9a;
}

/* ============ EMPLOYEE SCROLL CONTAINER ============ */
/* Scrollable list of employees */
.employee-scroll-container {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-right: 5px;
}

/* CUSTOM SCROLLBAR: Styled for dark theme */
.employee-scroll-container::-webkit-scrollbar {
  width: 6px;
}
.employee-scroll-container::-webkit-scrollbar-thumb {
  background: #222222;
  border-radius: 4px;
}

/* ============ EMPLOYEE LIST ITEM ============ */
/* Individual employee in the list */

.employee-list-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #121212;
  border: 1px solid #1a1a1a;
  border-radius: 14px;
  cursor: pointer;
}

/* HOVER EFFECT: Slight background change */
.employee-list-item:hover {
  background: #181818;
  border-color: #333333;
}

/* SELECTED STATE: Green border and background */
/* Applied when employee is clicked and selected */
.selected-active-item {
  background: #151e19 !important;
  border-color: #44ff9a !important;
}

/* AVATAR BADGE: Circle with initials */
.initials-badge {
  width: 38px;
  height: 38px;
  background: #1f2937;
  border: 1px solid #374151;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 0.85rem;
  font-weight: bold;
  color: #44ff9a;
}

/* EMPLOYEE META: Name, ID, role */
.meta-details {
  display: flex;
  flex-direction: column;
}

/* EMPLOYEE NAME: Full name and ID */
.name-string {
  font-size: 0.95rem;
  color: #ffffff;
  font-weight: 500;
  margin: 0;
}

/* EMPLOYEE ROLE: Job position */
.role-string {
  font-size: 0.8rem;
  color: #777777;
  margin: 2px 0 0 0;
}

/* EMPTY STATE: "No matching profiles found" */
.empty-results {
  color: #555555;
  font-style: italic;
  text-align: center;
  margin-top: 40px;
  font-size: 0.9rem;
}

/* ============ PAYSLIP CARD ============ */
/* Shows detailed salary breakdown for selected employee */

.payslip-wrapper-card {
  background: #111827;
  border: 1px solid #1f2937;
  border-radius: 18px;
  padding: 25px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* PAYSLIP HEADER: Employee avatar and info */
.payslip-top-row {
  display: flex;
  align-items: center;
  gap: 18px;
  border-bottom: 1px solid #1f2937;
  padding-bottom: 20px;
}

/* LARGE AVATAR: Big employee initials circle */
.large-initials-badge {
  width: 55px;
  height: 55px;
  background: #1f2937;
  border: 2px solid #374151;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.2rem;
  font-weight: bold;
  color: #44ff9a;
}

/* EMPLOYEE HEADER TEXT: Name, role, department */
.employee-header-text h4 {
  margin: 0;
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}

/* SUB-TEXT: Role and department labels */
.sub-role,
.sub-dept {
  margin: 3px 0 0 0;
  font-size: 0.85rem;
  color: #9ca3af;
}

/* ============ PAYSLIP DATA ROWS ============ */
/* Shows salary and deduction calculations */

.payslip-data-rows {
  margin-top: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* DATA ROW: Individual salary/deduction line */
.invoice-data-row {
  display: flex;
  justify-content: space-between;
  font-size: 1rem;
}

/* MUTED LABEL: Gray text for labels */
.muted-label {
  color: #d1d5db;
}

/* BRIGHT VALUE: White text for important amounts */
.bright-value {
  color: #ffffff;
}

/* TEXT BOLD: Bold font weight */
.text-bold {
  font-weight: bold;
}

/* SECONDARY DIM: Deduction rows in dim text */
.secondary-dim {
  color: #9ca3af;
  font-size: 0.95rem;
}

/* DEDUCTION SECTION HEADING: "DEDUCTIONS" divider */
.dashed-section-heading {
  font-size: 0.75rem;
  font-weight: bold;
  letter-spacing: 1px;
  color: #4b5563;
  margin: 10px 0 5px 0;
  border-bottom: 1px dashed #1f2937;
  padding-bottom: 5px;
}

/* DEDUCTIONS TOTAL LINE: Separates totals */
.deductions-total-line {
  border-top: 1px solid #1f2937;
  padding-top: 12px;
}

/* FINAL NET PAYOUT BOX: Highlighted final take-home */
.final-net-payout-box {
  background: #111e16;
  border: 1px solid #14532d;
  padding: 15px;
  border-radius: 12px;
  margin-top: auto;
  align-items: center;
}

/* NET PAYOUT LABEL: "NET PAY" text */
.net-payout-label {
  font-weight: bold;
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}

/* NET PAYOUT VALUE: Final take-home amount in large green text */
.net-payout-value {
  font-size: 1.5rem;
  font-weight: bold;
}

/* ============ SALARY ADJUSTMENT ACTIONS ============ */
/* Input field and button for salary increase */

.payslip-actions-block {
  margin-top: 20px;
  border-top: 1px solid #1f2937;
  padding-top: 18px;
}

/* INPUT ROW: Input field + button */
.horizontal-input-row {
  display: flex;
  gap: 15px;
}

/* INPUT FIELD: Percentage input */
.horizontal-input-row input {
  width: 150px;
  background: #0b0f19;
  border: 1px solid #1f2937;
  border-radius: 10px;
  color: #ffffff;
  padding: 10px;
  font-size: 0.9rem;
  outline: none;
}

/* INPUT FOCUS: Green border when focused */
.horizontal-input-row input:focus {
  border-color: #44ff9a;
}

/* SIMULATE BUTTON: Apply salary increase */
.simulate-increase-btn {
  flex: 1;
  background: transparent;
  border: 1px solid #44ff9a;
  border-radius: 10px;
  color: #44ff9a;
  font-weight: 500;
  cursor: pointer;
  font-size: 0.9rem;
}

/* BUTTON HOVER: Green background on hover */
.simulate-increase-btn:hover {
  background: #44ff9a;
  color: #050505;
}

/* BUTTON DISABLED STATE: Faded when processing */
.simulate-increase-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* ============ EMPTY STATE ============ */
/* Shows when no employee selected */

.blank-fallback-state {
  border: 2px dashed #222222;
  border-radius: 18px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 40px;
  text-align: center;
  color: #555555;
}

/* CARD ICON: Large emoji */
.card-icon {
  font-size: 3rem;
  margin-bottom: 15px;
}

/* EMPTY STATE TEXT */
.blank-fallback-state p {
  max-width: 320px;
  font-size: 0.95rem;
  line-height: 1.5;
}

/* ============ NOTIFICATIONS ============ */
/* Success and error messages */

.notification {
  padding: 15px 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  animation: slideInDown 0.3s ease;
  font-weight: 500;
}

/* SUCCESS NOTIFICATION: Green success message */
.notification-success {
  background: rgba(68, 255, 154, 0.15);
  border: 1px solid #44ff9a;
  color: #44ff9a;
}

/* ERROR NOTIFICATION: Red error message */
.notification-error {
  background: rgba(255, 68, 68, 0.15);
  border: 1px solid #ff4444;
  color: #ff6b6b;
}

/* SLIDE IN ANIMATION: Messages appear from top */
@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ============ RESPONSIVE DESIGN (MOBILE) ============ */
/* Adjusts layout for screens smaller than 900px */

@media (max-width: 900px) {
  /* STACK LAYOUT: One column on mobile */
  .workspace-layout {
    grid-template-columns: 1fr;
  }

  /* AUTO HEIGHT: Let panels size based on content */
  .left-panel,
  .right-panel {
    height: auto;
  }
}
.salary-history-section {
  margin-top: 15px;
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 120px;
  overflow-y: auto;
}

.history-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #0b0f19;
  border: 1px solid #1f2937;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
}

.history-main-info {
  display: flex;
  flex-direction: column;
}

.history-date {
  color: #9ca3af;
  font-size: 0.75rem;
}

.history-reason {
  color: #ffffff;
}

.history-amounts {
  display: flex;
  align-items: center;
  gap: 6px;
}

.history-old {
  color: #6b7280;
  text-decoration: line-through;
}

.history-badge {
  background: rgba(68, 255, 154, 0.15);
  color: #44ff9a;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
}

.history-empty {
  color: #6b7280;
  font-size: 0.85rem;
  font-style: italic;
  padding: 5px 0;
}
</style>
