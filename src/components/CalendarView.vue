<template>
  <div class="attendance-page">
    <!-- PAGE HEADER: Title and description of the attendance tracking feature -->
    <section class="header-section">
      <p class="section-tag">OPERATIONS TRACKING</p>
      <h1>Team Attendance Heatmap</h1>
      <p class="subtitle-text">
        Track daily presence ratios, explore historic trends, and view
        individual weekly logs. Simulated current date: 19 June 2026.
      </p>
    </section>

    <!-- CONTROL PANEL: Filter options and legend for the calendar -->
    <!-- This section lets users switch between company-wide and individual views -->
    <section class="controls-card">
      <!-- EMPLOYEE FILTER: Dropdown to select which employee to view -->
      <!-- When user selects an employee, the selectedEmployeeId changes -->
      <!-- This triggers the watcher which regenerates the calendar display -->
      <div class="control-group">
        <label for="employee-select">Filter Visual Target:</label>
        <div class="select-dropdown-container">
          <!-- V-MODEL: Two-way binding between dropdown and selectedEmployeeId -->
          <!-- Default value "all" shows company overview with heatmap colors -->
          <!-- Individual employee ID shows that person's daily attendance status -->
          <select id="employee-select" v-model="selectedEmployeeId">
            <option value="all">All Employees (Company Overview)</option>
            <!-- V-FOR: Loop through employees array to populate dropdown options -->
            <!-- Each option displays employee name and ID for easy identification -->
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.name }} ({{ emp.id }})
            </option>
          </select>
        </div>
      </div>

      <!-- LEGEND: Color key explaining what the calendar colors mean -->
      <!-- Legend changes based on whether viewing company-wide or individual data -->
      <div class="legend-container">
        <span class="legend-label">Legend:</span>

        <!-- COMPANY OVERVIEW LEGEND: Shows heatmap color meanings -->
        <!-- V-IF: Only shown when selectedEmployeeId is "all" -->
        <!-- Heatmap colors represent percentage of employees present each day -->
        <div v-if="selectedEmployeeId === 'all'" class="legend-cells">
          <div class="legend-item">
            <div class="cell-box heat-0"></div>
            0% Present
          </div>
          <div class="legend-item">
            <div class="cell-box heat-1"></div>
            Low Ratio
          </div>
          <div class="legend-item">
            <div class="cell-box heat-2"></div>
            Mid Ratio
          </div>
          <div class="legend-item">
            <div class="cell-box heat-3"></div>
            High Ratio
          </div>
          <div class="legend-item">
            <div class="cell-box heat-4"></div>
            Peak Heat (100%)
          </div>
        </div>

        <!-- INDIVIDUAL EMPLOYEE LEGEND: Shows attendance status colors -->
        <!-- V-ELSE: Shown when a specific employee is selected -->
        <!-- Status colors represent that person's daily attendance state -->
        <div v-else class="legend-cells">
          <div class="legend-item">
            <div class="cell-box status-present"></div>
            Present
          </div>
          <div class="legend-item">
            <div class="cell-box status-late"></div>
            Late
          </div>
          <div class="legend-item">
            <div class="cell-box status-absent"></div>
            Absent
          </div>
          <div class="legend-item">
            <div class="cell-box status-leave"></div>
            Approved Leave
          </div>
        </div>

        <!-- FUTURE DAY INDICATOR: Always shown in both views -->
        <div class="legend-item">
          <div class="cell-box status-future"></div>
          Future Day
        </div>
      </div>
    </section>

    <!-- MAIN CALENDAR DISPLAY: The attendance heatmap grid -->
    <main class="calendar-workspace">
      <div class="calendar-card">
        <!-- CALENDAR HEADER: Shows month and current view mode -->
        <!-- View mode text changes based on selectedEmployeeId value -->
        <div class="calendar-header-meta">
          <h3>June 2026</h3>
          <!-- COMPUTED PROPERTY: viewModeHeading shows user what they're viewing -->
          <p class="view-indicator-text">
            Currently Viewing: <strong>{{ viewModeHeading }}</strong>
          </p>
        </div>

        <!-- DAY OF WEEK LABELS: Shows Mon, Tue, Wed, etc. -->
        <!-- Static grid that aligns with the calendar cells below -->
        <div class="days-of-week-grid">
          <div>Mon</div>
          <div>Tue</div>
          <div>Wed</div>
          <div>Thu</div>
          <div>Fri</div>
          <div>Sat</div>
          <div>Sun</div>
        </div>

        <!-- CALENDAR GRID: The main heatmap with one tile per day -->
        <!-- V-FOR: Loops through calendarDays array created in generateMonthMatrix() -->
        <!-- Each tile represents one day of June 2026 (days 1-30) -->
        <div class="calendar-cells-grid">
          <div
            v-for="day in calendarDays"
            :key="day.dateString"
            class="calendar-tile"
            :class="getDayClass(day)"
            :title="getDayTooltip(day)"
          >
            <!-- DAY NUMBER: The number shown in the tile (1-30) -->
            <span class="day-number-label">
              {{ day.dayNumber }}
            </span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: "CalendarView",

  // PROPS: Receive employees array from parent component (App.vue)
  // This is the central data source shared between all components
  // We use this to look up attendance data for each day
  props: {
    employees: {
      type: Array,
      required: true,
    },
  },

  // DATA: Local state for this component
  data() {
    return {
      // SELECTED EMPLOYEE ID: Controls which view mode is active
      // Value "all" = show company-wide heatmap with percentage colors
      // Value = employee ID = show that specific person's attendance status
      selectedEmployeeId: "all",

      // CALENDAR DAYS ARRAY: Generated by generateMonthMatrix() method
      // Contains 30 objects, one for each day of June 2026
      // Each object has: dateString, dayNumber, isPast, presentCount, status
      calendarDays: [],

      // SIMULATED TODAY: Hard-coded date to represent "current date"
      // Used to determine which days are in the past (have data)
      // and which are in the future (no data yet)
      simulatedTodayStr: "2026-06-19",
    };
  },

  // COMPUTED PROPERTIES: Dynamic values that update when dependencies change
  computed: {
    // VIEW MODE HEADING: Changes based on which view is active
    // Shows HR staff what they're currently looking at
    viewModeHeading() {
      // If "all" is selected: show company overview message
      if (this.selectedEmployeeId === "all")
        return "Company-Wide Presence Overview";

      // If specific employee selected: find their name and show it
      const target = this.employees.find(
        (e) => e.id === this.selectedEmployeeId,
      );
      return target ? `${target.name}'s File Profile` : "Individual File";
    },
  },

  // WATCHERS: Monitor data changes and trigger automatic updates
  watch: {
    // WATCH SELECTED EMPLOYEE ID: When user changes dropdown selection
    // Automatically regenerate the calendar with new data
    selectedEmployeeId() {
      this.generateMonthMatrix();
    },
  },

  // LIFECYCLE HOOK: Runs when component first loads
  // Generates the initial calendar on page load
  created() {
    this.generateMonthMatrix();
  },

  methods: {
    // GENERATE MONTH MATRIX: Creates the 30-day calendar data structure
    // Called on component load and whenever selectedEmployeeId changes (via watcher)
    // Builds calendarDays array with attendance data for each day
    generateMonthMatrix() {
      // LOOP THROUGH JUNE: Days 1-30
      const daysInJune = 30;
      const matrixBuild = [];

      for (let d = 1; d <= daysInJune; d++) {
        // FORMAT DATE STRING: "2026-06-01", "2026-06-02", etc.
        // Pad single-digit days with leading zero (day 5 becomes "05")
        const paddingDayStr = d < 10 ? `0${d}` : `${d}`;
        const targetDateKey = `2026-06-${paddingDayStr}`;

        // CHECK IF DAY IS IN PAST: Today is June 19, so days 1-19 have data
        // Future days (20-30) haven't happened yet, so no attendance data
        const isPastDay = d <= 19;

        // CREATE DAY OBJECT: Template with default values
        let dayObj = {
          dateString: targetDateKey,
          dayNumber: d,
          isPast: isPastDay,
          presentCount: 0,
          status: "",
        };

        // LOAD ATTENDANCE DATA: Only if day is in the past
        if (isPastDay) {
          // COMPANY OVERVIEW MODE: Calculate how many employees were present today
          if (this.selectedEmployeeId === "all") {
            let totalPresentForDay = 0;

            // LOOP THROUGH EMPLOYEES: Count how many were present or late
            // Skip absent employees and those on approved leave
            this.employees.forEach((emp) => {
              const status = emp.prevWeekAttendance?.[targetDateKey];
              // Count as "present" if they were either present or late
              if (status === "present" || status === "late") {
                totalPresentForDay++;
              }
            });

            // STORE PRESENT COUNT: Used to determine heatmap color intensity
            dayObj.presentCount = totalPresentForDay;
          } 
          // INDIVIDUAL EMPLOYEE MODE: Get that specific person's status
          else {
            // FIND SELECTED EMPLOYEE: Look up their record
            const selectedEmpObj = this.employees.find(
              (e) => e.id === this.selectedEmployeeId,
            );

            if (selectedEmpObj) {
              // LOOK UP STATUS FOR THIS DAY: From prevWeekAttendance object
              // Status can be: "present", "late", "absent", or "leave"
              // Default to "absent" if no data found
              dayObj.status =
                selectedEmpObj.prevWeekAttendance?.[targetDateKey] || "absent";
            }
          }
        }

        // ADD TO CALENDAR: Push completed day object to array
        matrixBuild.push(dayObj);
      }

      // UPDATE COMPONENT STATE: Replace calendar with newly generated data
      this.calendarDays = matrixBuild;
    },

    // GET DAY CLASS: Determines CSS class for tile styling
    // Different colors for different data based on view mode
    getDayClass(day) {
      // FUTURE DAYS: Grayed out, no attendance data yet
      if (!day.isPast) return "status-future";

      // INDIVIDUAL VIEW MODE: Show single person's status (present/late/absent/leave)
      if (this.selectedEmployeeId !== "all") return `status-${day.status}`;

      // COMPANY OVERVIEW MODE: Show heatmap intensity based on attendance percentage
      // Calculate what percentage of employees were present today
      const ratio = day.presentCount / this.employees.length;

      // RETURN HEAT CLASS: Based on percentage (0% = heat-0, 100% = heat-4)
      if (ratio === 0) return "heat-0";         // No one present
      if (ratio < 0.4) return "heat-1";         // Less than 40% present
      if (ratio < 0.7) return "heat-2";         // 40-70% present
      if (ratio < 1) return "heat-3";           // 70-99% present
      return "heat-4";                          // 100% present (peak heat)
    },

    // GET DAY TOOLTIP: Creates hover text that appears when mouse over tile
    // Shows helpful information based on view mode
    getDayTooltip(day) {
      // FUTURE DAYS: Simple message
      if (!day.isPast) return `${day.dateString} (Future Day)`;

      // COMPANY OVERVIEW: Show how many employees were present
      if (this.selectedEmployeeId === "all") {
        return `${day.dateString}: ${day.presentCount} out of ${this.employees.length} employees present`;
      }

      // INDIVIDUAL EMPLOYEE: Show their attendance status for the day
      return `${day.dateString} Status: ${day.status.toUpperCase()}`;
    },
  },
};
</script>

<style scoped>
/* ATTENDANCE PAGE: Main container for calendar view */
.attendance-page {
  background: #050505;
  min-height: 100vh;
  color: #ffffff;
  padding: 40px 20px;
  box-sizing: border-box;
}

/* HEADER SECTION: Title and description at top of page */
.header-section {
  text-align: center;
  margin-bottom: 40px;
}

/* SECTION TAG: Small uppercase label above main title */
.section-tag {
  color: #44ff9a;
  letter-spacing: 4px;
  font-weight: 600;
  font-size: 0.85rem;
  margin-bottom: 5px;
}

/* PAGE TITLE: Main heading */
.header-section h1 {
  font-size: 3rem;
  margin: 10px 0;
}

/* SUBTITLE: Description text under title */
.subtitle-text {
  color: #9f9f9f;
  font-size: 1.05rem;
  max-width: 700px;
  margin: 0 auto;
  line-height: 1.5;
}

/* CONTROLS CARD: Panel with filter dropdown and legend */
.controls-card {
  background: #0f0f0f;
  border: 1px solid #222222;
  border-radius: 20px;
  padding: 25px;
  max-width: 1000px;
  margin: 0 auto 30px auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

/* CONTROL GROUP: Wrapper for filter label and dropdown */
.control-group {
  display: flex;
  align-items: center;
  gap: 15px;
}

.control-group label {
  color: #888888;
  font-size: 0.95rem;
  font-weight: 500;
}

/* DROPDOWN SELECT: Styled select element for employee filter */
.select-dropdown-container select {
  background: #161616;
  border: 1px solid #2a2a2a;
  border-radius: 12px;
  color: #ffffff;
  padding: 12px 18px;
  font-size: 0.95rem;
  outline: none;
  cursor: pointer;
}

.select-dropdown-container select:focus {
  border-color: #44ff9a;
}

/* LEGEND CONTAINER: Displays color meanings for calendar */
.legend-container {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.legend-label {
  font-size: 0.85rem;
  color: #666666;
  font-weight: bold;
  text-transform: uppercase;
}

/* LEGEND CELLS: Grid of color boxes with labels */
.legend-cells {
  display: flex;
  gap: 15px;
  align-items: center;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  color: #b3b3b3;
}

/* COLOR BOX: Small colored square in legend */
.cell-box {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

/* CALENDAR WORKSPACE: Container for calendar grid */
.calendar-workspace {
  max-width: 1000px;
  margin: 0 auto;
}

/* CALENDAR CARD: Main card containing calendar */
.calendar-card {
  background: #0f0f0f;
  border: 1px solid #222222;
  border-radius: 20px;
  padding: 30px;
}

/* CALENDAR HEADER: Title and view mode indicator */
.calendar-header-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #222222;
  padding-bottom: 15px;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 15px;
}

.calendar-header-meta h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

/* VIEW INDICATOR: Text showing what data is displayed */
.view-indicator-text {
  margin: 0;
  font-size: 0.9rem;
  color: #888888;
}

.view-indicator-text strong {
  color: #44ff9a;
}

/* DAY OF WEEK LABELS: Mon, Tue, Wed, etc. */
.days-of-week-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  font-size: 0.85rem;
  font-weight: bold;
  text-transform: uppercase;
  color: #555555;
  letter-spacing: 1px;
  margin-bottom: 10px;
}

/* CALENDAR CELLS GRID: 7x5 grid for June dates (30 days) */
.calendar-cells-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 12px;
}

/* CALENDAR TILE: Individual day cell */
.calendar-tile {
  background: #121212;
  border: 1px solid #1a1a1a;
  border-radius: 12px;
  min-height: 85px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-sizing: border-box;
  position: relative;
  transition: transform 0.2s ease;
}

.calendar-tile:hover {
  transform: translateY(-2px);
}

/* DAY NUMBER: The number displayed in tile (1, 2, 3, etc.) */
.day-number-label {
  font-size: 1rem;
  font-weight: bold;
  color: #ffffff;
}

/* HEATMAP COLORS: Company overview mode - show % of employees present */
.heat-0 {
  background: #1a1a1a;        /* 0% present - dark gray */
}

.heat-1 {
  background: #0c3520;        /* Low % - dark green */
}

.heat-2 {
  background: #15653d;        /* Mid % - medium green */
}

.heat-3 {
  background: #20a061;        /* High % - light green */
}

.heat-4 {
  background: #44ff9a;        /* 100% present - bright green (peak heat) */
}

/* STATUS COLORS: Individual employee mode - show daily status */
.status-present {
  background: #16a34a;        /* Green - employee was present */
  border: 1px solid #22c55e;
}

.status-late {
  background: #ca8a04;        /* Orange - employee was late */
  border: 1px solid #eab308;
}

.status-absent {
  background: #dc2626;        /* Red - employee was absent */
  border: 1px solid #ef4444;
}

.status-leave {
  background: #7c3aed;        /* Purple - employee on approved leave */
  border: 1px solid #a855f7;
}

.status-future {
  background: #0f0f0f;        /* Dark - future date, no data yet */
  border: 1px solid #1a1a1a;
  opacity: 0.25;
}

/* RESPONSIVE DESIGN: Tablets (768px and below) */
@media (max-width: 768px) {
  .attendance-page {
    padding: 20px 15px;
  }

  /* Adjust title size for smaller screens */
  .header-section h1 {
    font-size: 2rem;
  }

  /* Stack controls vertically on mobile */
  .controls-card {
    flex-direction: column;
    align-items: stretch;
    padding: 20px;
  }

  .control-group {
    flex-direction: column;
    align-items: stretch;
  }

  .select-dropdown-container select {
    width: 100%;
  }

  /* Legend stacks vertically */
  .legend-container {
    justify-content: flex-start;
    flex-direction: column;
    align-items: flex-start;
  }

  .legend-cells {
    width: 100%;
  }

  .calendar-card {
    padding: 20px;
  }

  /* Header stacks vertically */
  .calendar-header-meta {
    flex-direction: column;
    align-items: flex-start;
  }

  /* Reduce calendar grid to 4 columns on tablet */
  .calendar-cells-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
  }

  .calendar-tile {
    min-height: 60px;
    padding: 8px;
  }

  .day-number-label {
    font-size: 0.85rem;
  }

  .days-of-week-grid {
    font-size: 0.75rem;
  }
}

/* RESPONSIVE DESIGN: Mobile phones (480px and below) */
@media (max-width: 480px) {
  .attendance-page {
    padding: 15px 10px;
  }

  .header-section h1 {
    font-size: 1.5rem;
  }

  .subtitle-text {
    font-size: 0.9rem;
  }

  .controls-card {
    padding: 15px;
  }

  /* Legend displays as 2-column grid on mobile */
  .legend-cells {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  /* Calendar displays as 2-column grid on small mobile */
  .calendar-cells-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }

  .calendar-tile {
    min-height: 50px;
    padding: 6px;
  }

  .day-number-label {
    font-size: 0.75rem;
  }

  .days-of-week-grid {
    font-size: 0.65rem;
    margin-bottom: 8px;
  }
}
</style>
