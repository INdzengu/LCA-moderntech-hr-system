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
export default {
  name: "PayrollView",

  props: {
    // CURRENT USER: Name of logged-in HR staff member
    currentUser: {
      type: String,
      default: "HR Admin",
    },

    // EMPLOYEES: Central data source with all employee information
    // Passed from App.vue - contains all employee salary data
    employees: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      // searchQuery: Stores text user types in search field
      // Used to filter employee list by name or role
      searchQuery: "",

      // selectedEmployee: Stores the currently selected employee
      // Set when user clicks an employee from the left panel
      // Null when no employee is selected
      selectedEmployee: null,

      // salaryIncreasePercentage: Stores the percentage entered by user
      // Used in salary increase simulation (e.g., 5 for 5% increase)
      salaryIncreasePercentage: null,

      // successMessage: Stores success notification text
      // Shows when salary update completes successfully
      // Empty string means no message to display
      successMessage: "",

      // errorMessage: Stores error notification text
      // Shows validation errors or operation failures
      // Empty string means no error
      errorMessage: "",

      // isProcessing: Boolean for processing state
      // true = button disabled, shows "Processing..."
      // false = button enabled, shows normal text
      isProcessing: false,
    };
  },

  computed: {
    /**
     * FILTERED EMPLOYEES: Employee list filtered by search query
     *
     * WHAT IT DOES: Returns only employees matching the search term
     * USED FOR: Populates the left panel employee list
     *
     * HOW IT WORKS:
     *   1. Check if searchQuery is empty (if so, return all employees)
     *   2. Filter employees where name OR role matches search (case-insensitive)
     *   3. Return filtered array
     *
     * EXAMPLE:
     * If search = "developer", shows only employees with "developer" in name/role
     * If search = empty, shows all employees
     */
    filteredEmployees() {
      if (!this.employees) return [];
      return this.employees.filter((emp) => {
        const query = this.searchQuery ? this.searchQuery.toLowerCase() : "";
        return (
          (emp.name && emp.name.toLowerCase().includes(query)) ||
          (emp.role && emp.role.toLowerCase().includes(query))
        );
      });
    },

    /**
     * TOTAL COMPANY DEDUCTIONS: Sum of all employee deductions
     *
     * WHAT IT DOES: Calculates total deductions across all employees
     * USED FOR: Summary card showing total company deductions
     *
     * HOW IT WORKS:
     *   1. Loop through all employees
     *   2. For each employee, calculate their total deductions
     *   3. Add to running total (accumulator)
     *   4. Return sum
     *
     * FORMULA: Sum of (UIF + PAYE + Pension + Medical) for all employees
     */
    totalCompanyDeductions() {
      return this.employees.reduce(
        (total, employee) =>
          total + this.calculateTotalDeductions(employee.monthlySalary || 0),
        0,
      );
    },

    /**
     * TOTAL MONTHLY OUTFLOW: Total of all employee salaries
     *
     * WHAT IT DOES: Sums all monthly salaries company pays
     * USED FOR: Summary card showing total payroll cost per month
     *
     * HOW IT WORKS:
     *   1. Check if employees array exists and has data
     *   2. Loop through all employees
     *   3. Add each employee's monthlySalary to running total
     *   4. Return total sum
     *
     * EXAMPLE: If 3 employees earn R20k, R25k, R30k = R75k total
     */
    totalMonthlyOutflow() {
      if (!this.employees || this.employees.length === 0) return 0;
      return this.employees.reduce(
        (accumulator, item) => accumulator + (item.monthlySalary || 0),
        0,
      );
    },

    /**
     * ANNUAL PAYROLL PROJECTION: Estimated yearly payroll
     *
     * WHAT IT DOES: Multiplies monthly payroll by 12 for annual estimate
     * USED FOR: Summary card showing annual payroll cost
     *
     * CALCULATION: Total Monthly Outflow × 12 months = Annual Total
     *
     * EXAMPLE: If monthly = R75k, annual = R900k
     */
    annualPayrollProjection() {
      return this.totalMonthlyOutflow * 12;
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
     *   1. Split name by spaces
     *   2. Get first letter of each word
     *   3. Join together and make uppercase
     *
     * EXAMPLE:
     *   "John Doe" → ["John", "Doe"] → ["J", "D"] → "JD"
     */
    getInitials(fullName) {
      if (!fullName) return "";
      return fullName
        .split(" ")
        .map((segment) => segment[0])
        .join("")
        .toUpperCase();
    },

    /**
     * SET ACTIVE EMPLOYEE: Selects an employee and shows their payslip
     *
     * WHAT IT DOES: Updates selectedEmployee when clicked from list
     * CALLED: When user clicks on an employee in left panel
     *
     * HOW IT WORKS:
     *   1. Set selectedEmployee to the clicked employee
     *   2. Clear salary increase field (reset form)
     *
     * This updates the right panel to show payslip for selected employee
     */
    setActiveEmployee(employee) {
      this.selectedEmployee = employee;
      this.salaryIncreasePercentage = null;
    },

    /**
     * CALCULATE UIF: Unemployment Insurance Fund deduction
     *
     * WHAT IT DOES: Calculates 1% UIF deduction from salary
     * HOW IT WORKS: grossSalary × 0.01 = UIF amount
     *
     * EXAMPLE: R 50,000 × 0.01 = R 500 UIF deduction
     *
     * NOTE: This is South African deduction
     */
    calculateUIF(grossSalary) {
      return Math.round(grossSalary * 0.01);
    },

    /**
     * CALCULATE PENSION: Pension Fund contribution
     *
     * WHAT IT DOES: Calculates 7.5% pension fund deduction
     * HOW IT WORKS: grossSalary × 0.075 = Pension amount
     *
     * EXAMPLE: R 50,000 × 0.075 = R 3,750 pension deduction
     *
     * NOTE: Employee's contribution to retirement fund
     */
    calculatePension(grossSalary) {
      return Math.round(grossSalary * 0.075);
    },

    /**
     * CALCULATE MEDICAL AID: Health insurance contribution
     *
     * WHAT IT DOES: Calculates 2% medical aid deduction
     * HOW IT WORKS: grossSalary × 0.02 = Medical amount
     *
     * EXAMPLE: R 50,000 × 0.02 = R 1,000 medical deduction
     *
     * NOTE: Employee's share of health benefits
     */
    calculateMedicalAid(grossSalary) {
      return Math.round(grossSalary * 0.02);
    },

    /**
     * CALCULATE PAYE: Personal income tax estimation
     *
     * WHAT IT DOES: Estimates PAYE (income tax) using tax brackets
     * HOW IT WORKS: Uses tiered tax rates based on salary
     *
     * TAX BRACKETS (South African model):
     *   - Over R40,000: 25% tax rate
     *   - R25,000 to R40,000: 18% tax rate
     *   - Under R25,000: 10% tax rate
     *
     * EXAMPLES:
     *   R 50,000 × 0.25 = R 12,500 PAYE
     *   R 30,000 × 0.18 = R 5,400 PAYE
     *   R 15,000 × 0.10 = R 1,500 PAYE
     *
     * NOTE: This is simplified estimation, real PAYE is more complex
     */
    calculatePAYE(grossSalary) {
      if (grossSalary > 40000) {
        return Math.round(grossSalary * 0.25);
      } else if (grossSalary > 25000) {
        return Math.round(grossSalary * 0.18);
      } else {
        return Math.round(grossSalary * 0.1);
      }
    },

    /**
     * CALCULATE TOTAL DEDUCTIONS: Sum of all deductions
     *
     * WHAT IT DOES: Adds up all deductions from salary
     * HOW IT WORKS: UIF + PAYE + Pension + Medical = Total Deductions
     *
     * FORMULA: All deduction methods called and summed
     *
     * EXAMPLE:
     *   UIF (500) + PAYE (12,500) + Pension (3,750) + Medical (1,000)
     *   = R 17,750 total deductions
     */
    calculateTotalDeductions(grossSalary) {
      return (
        this.calculateUIF(grossSalary) +
        this.calculatePAYE(grossSalary) +
        this.calculatePension(grossSalary) +
        this.calculateMedicalAid(grossSalary)
      );
    },

    /**
     * CALCULATE NET PAY: Final take-home salary
     *
     * WHAT IT DOES: Calculates what employee actually receives
     * HOW IT WORKS: Gross Salary - Total Deductions = Net Pay
     *
     * FORMULA: grossSalary - calculateTotalDeductions(grossSalary)
     *
     * EXAMPLE:
     *   R 50,000 (gross) - R 17,750 (deductions) = R 32,250 (net pay)
     *
     * This is what gets deposited to employee's bank account
     */
    calculateNetPay(grossSalary) {
      return grossSalary - this.calculateTotalDeductions(grossSalary);
    },

    /**
     * APPLY LOCAL SALARY INCREASE: Updates salary with percentage increase
     *
     * WHAT IT DOES: Simulates a salary increase for selected employee
     * CALLED: When user clicks "Simulate Local Annual Increase" button
     *
     * VALIDATION FLOW:
     *   1. Clear previous error/success messages
     *   2. Check if employee is selected
     *   3. Check if percentage is entered
     *   4. Check if percentage is positive (> 0)
     *   5. Check if percentage doesn't exceed 100
     *   6. Check if value is a valid number
     *
     * If all checks pass:
     *   7. Set isProcessing = true (disable button, show "Processing...")
     *   8. Find employee in employees array by ID
     *   9. Calculate new salary: oldSalary + (oldSalary × percentage ÷ 100)
     *   10. Update employee's monthlySalary in both array and selectedEmployee
     *   11. Show success message with new salary
     *   12. Auto-hide success message after 4 seconds
     *   13. Clear input field
     *   14. Set isProcessing = false (enable button again)
     *
     * IMPORTANT: This updates the central employees array, so all views
     * see the change and App.vue's watch saves to localStorage
     *
     * EXAMPLE:
     * If salary = R50,000 and increase = 5%
     * New salary = 50,000 + (50,000 × 5 ÷ 100) = R52,500
     */
    applyLocalSalaryIncrease() {
      // RESET: Clear any previous messages
      this.successMessage = "";
      this.errorMessage = "";

      // VALIDATION 1: Check if employee is selected
      if (!this.selectedEmployee) {
        this.errorMessage = "❌ Please select an employee first";
        return;
      }

      // VALIDATION 2: Check if percentage field is filled
      if (
        this.salaryIncreasePercentage === null ||
        this.salaryIncreasePercentage === ""
      ) {
        this.errorMessage = "❌ Please enter a salary increase percentage";
        return;
      }

      // VALIDATION 3: Check if percentage is positive
      if (this.salaryIncreasePercentage <= 0) {
        this.errorMessage = "❌ Percentage must be greater than 0%";
        return;
      }

      // VALIDATION 4: Check if percentage doesn't exceed 100
      if (this.salaryIncreasePercentage > 100) {
        this.errorMessage = "❌ Salary increase cannot exceed 100%";
        return;
      }

      // VALIDATION 5: Check if value is a valid number
      if (!Number.isFinite(this.salaryIncreasePercentage)) {
        this.errorMessage = "❌ Please enter a valid number";
        return;
      }

      // ALL VALIDATIONS PASSED - Proceed with update
      // Set processing state (disables button, shows "Processing...")
      this.isProcessing = true;

      // SIMULATE API CALL: Wait 300ms for better UX
      setTimeout(() => {
        // FIND EMPLOYEE: Search employees array for matching ID
        const targetedIndex = this.employees.findIndex(
          (emp) => emp.id === this.selectedEmployee.id,
        );

        // UPDATE IF FOUND
        if (targetedIndex !== -1) {
          // STORE ORIGINAL SALARY: For calculation
          const historicalSalary = this.employees[targetedIndex].monthlySalary;

          // CALCULATE INCREASE AMOUNT: percentage of original salary
          const totalCalculatedIncrease =
            historicalSalary * (this.salaryIncreasePercentage / 100);

          // CALCULATE NEW SALARY: original + increase
          const updatedSalarySum = Math.round(
            historicalSalary + totalCalculatedIncrease,
          );

          // UPDATE BOTH LOCATIONS
          // 1. Update in central employees array
          this.employees[targetedIndex].monthlySalary = updatedSalarySum;
          // 2. Update in local selectedEmployee (for right panel display)
          this.selectedEmployee.monthlySalary = updatedSalarySum;

          // SHOW SUCCESS MESSAGE: Tells user what happened
          // Auto-hides after 4 seconds
          this.successMessage = `✅ ${this.selectedEmployee.name}'s salary updated to R${updatedSalarySum} (${this.salaryIncreasePercentage}% increase)`;

          // AUTO-HIDE SUCCESS MESSAGE
          setTimeout(() => {
            this.successMessage = "";
          }, 4000);

          // CLEAR INPUT FIELD
          this.salaryIncreasePercentage = null;
        }

        // STOP PROCESSING: Re-enable button
        this.isProcessing = false;
      }, 300);
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
  height: 600px;
  display: flex;
  flex-direction: column;
}

/* PANEL HEADING: Title of each panel */
.panel-heading {
  font-size: 1.1rem;
  color: #ffffff;
  margin-bottom: 15px;
  font-weight: bold;
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
</style>
