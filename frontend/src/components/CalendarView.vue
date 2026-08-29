<template>
  <div class="page-wrapper">
    <!-- Top Header -->
    <header class="header-section">
      <div>
        <h1 class="page-title">Attendance & Calendar Matrix</h1>
        <p class="page-subtitle">
          Track company presence trends and detailed employee attendance
          profiles.
        </p>
      </div>
      <div class="header-badge">
        <span class="live-indicator"></span>
        <span>August 2026 Sync</span>
      </div>
    </header>

    <!-- Stat Overview Cards -->
    <section class="stats-grid">
      <div class="stat-card">
        <span class="stat-label">Total Workdays</span>
        <span class="stat-value">19</span>
        <span class="stat-sub">Tracked in August</span>
      </div>
      <div class="stat-card">
        <span class="stat-label">Company Presence</span>
        <span class="stat-value text-emerald">88%</span>
        <span class="stat-sub">Average turnout</span>
      </div>
      <div class="stat-card">
        <span class="stat-label">Active Mode</span>
        <span class="stat-value text-indigo">
          {{ selectedEmployeeId === "all" ? "Overview" : "Individual" }}
        </span>
        <span class="stat-sub">Target selection</span>
      </div>
    </section>

    <!-- Controls Bar -->
    <div class="controls-card">
      <div class="control-group">
        <label for="visual-target" class="control-label">Filter Target</label>
        <div class="select-wrapper">
          <select
            id="visual-target"
            v-model="selectedEmployeeId"
            @change="fetchMonthData"
            class="custom-select"
          >
            <option value="all">Company Overview (All Employees)</option>
            <option
              v-for="emp in employeeOptions"
              :key="emp.id"
              :value="emp.id"
            >
              {{ emp.name }} ({{ emp.id }})
            </option>
          </select>
        </div>
      </div>

      <!-- Legends -->
      <div class="legend-container" v-if="selectedEmployeeId === 'all'">
        <span class="legend-title">PRESENCE RATIO:</span>
        <div class="legend-items">
          <span class="legend-chip"><i class="dot level-0"></i> 0%</span>
          <span class="legend-chip"><i class="dot level-1"></i> Low</span>
          <span class="legend-chip"><i class="dot level-2"></i> Mid</span>
          <span class="legend-chip"><i class="dot level-3"></i> High</span>
          <span class="legend-chip"><i class="dot level-4"></i> 100%</span>
          <span class="legend-chip"><i class="dot future"></i> Future</span>
        </div>
      </div>
      <div class="legend-container" v-else>
        <span class="legend-title">STATUS:</span>
        <div class="legend-items">
          <span class="legend-chip"
            ><i class="dot status-present"></i> Present</span
          >
          <span class="legend-chip"><i class="dot status-late"></i> Late</span>
          <span class="legend-chip"
            ><i class="dot status-absent"></i> Absent</span
          >
          <span class="legend-chip"
            ><i class="dot status-leave"></i> Leave</span
          >
          <span class="legend-chip"><i class="dot future"></i> Future</span>
        </div>
      </div>
    </div>

    <!-- Main Calendar Display -->
    <main class="calendar-card">
      <div class="calendar-card-header">
        <div class="month-heading">
          <h2>{{ monthName }} {{ currentYear }}</h2>
        </div>
        <div class="view-indicator">
          <span>Viewing: </span>
          <strong class="view-highlight">
            {{
              selectedEmployeeId === "all"
                ? "Company-Wide Presence Heatmap"
                : selectedEmployeeName + "'s Profile"
            }}
          </strong>
        </div>
      </div>

      <!-- Weekdays Bar -->
      <div class="weekdays-grid">
        <div
          v-for="day in ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN']"
          :key="day"
          class="weekday-cell"
        >
          {{ day }}
        </div>
      </div>

      <!-- Days Grid -->
      <div class="days-grid">
        <!-- Offset Blank Cells -->
        <div
          v-for="padding in paddingDays"
          :key="'pad-' + padding"
          class="day-cell pad-cell"
        ></div>

        <!-- Active Month Days -->
        <div
          v-for="day in calendarDays"
          :key="day.dateString"
          :class="['day-cell', getDayClass(day)]"
        >
          <div class="cell-top">
            <span class="day-num">{{ day.dayNumber }}</span>
            <span v-if="day.dateString === todayString" class="today-tag"
              >TODAY</span
            >
          </div>
          <div class="cell-body">
            <span
              v-if="selectedEmployeeId === 'all' && day.record && !day.isFuture"
              class="cell-sub"
            >
              {{ day.record.present_count || 0 }}/{{
                day.record.total_count || 0
              }}
              Present
            </span>
            <span
              v-else-if="
                selectedEmployeeId !== 'all' && day.record && !day.isFuture
              "
              class="cell-sub capitalize"
            >
              {{ day.record.status }}
            </span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CalendarView",
  data() {
    return {
      selectedEmployeeId: "all",
      employeeOptions: [],
      currentYear: 2026,
      currentMonth: 8,
      todayString: "2026-08-27",
      monthlyData: {},
    };
  },
  computed: {
    monthName() {
      const names = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
      return names[this.currentMonth - 1];
    },
    selectedEmployeeName() {
      const emp = this.employeeOptions.find(
        (e) => e.id == this.selectedEmployeeId,
      );
      return emp ? emp.name : "Employee";
    },
    paddingDays() {
      const firstDayIndex = new Date(
        this.currentYear,
        this.currentMonth - 1,
        1,
      ).getDay();
      return firstDayIndex === 0 ? 6 : firstDayIndex - 1;
    },
    calendarDays() {
      const days = [];
      const totalDays = new Date(
        this.currentYear,
        this.currentMonth,
        0,
      ).getDate();

      for (let i = 1; i <= totalDays; i++) {
        const dateStr = `${this.currentYear}-${String(this.currentMonth).padStart(2, "0")}-${String(i).padStart(2, "0")}`;
        const isFuture = dateStr > this.todayString;
        days.push({
          dayNumber: i,
          dateString: dateStr,
          isFuture: isFuture,
          record: this.monthlyData[dateStr] || null,
        });
      }
      return days;
    },
  },
  mounted() {
    this.fetchEmployees();
    this.fetchMonthData();
  },
  methods: {
    getApiUrl() {
      return "http://localhost/lca-php/moderntech-hr-system/backend/routes/attendance.php";
    },

    async fetchEmployees() {
      try {
        const res = await axios.get(`${this.getApiUrl()}?action=get_employees`);
        if (res.data && res.data.status === "success") {
          this.employeeOptions = res.data.data;
        }
      } catch (err) {
        console.error("Failed to fetch employees:", err);
      }
    },

    async fetchMonthData() {
      try {
        const url = `${this.getApiUrl()}?year=${this.currentYear}&month=${this.currentMonth}&employee_id=${this.selectedEmployeeId}`;
        const res = await axios.get(url);
        if (res.data && res.data.status === "success") {
          const map = {};
          res.data.data.forEach((item) => {
            map[item.date] = item;
          });
          this.monthlyData = map;
        }
      } catch (err) {
        console.error("Failed to load monthly attendance:", err);
      }
    },

    getDayClass(day) {
      if (day.isFuture) return "future-day";

      if (this.selectedEmployeeId === "all") {
        if (
          !day.record ||
          !day.record.total_count ||
          day.record.total_count == 0
        )
          return "heat-level-0";
        const ratio = day.record.present_count / day.record.total_count;
        if (ratio >= 0.9) return "heat-level-4";
        if (ratio >= 0.7) return "heat-level-3";
        if (ratio >= 0.4) return "heat-level-2";
        if (ratio > 0) return "heat-level-1";
        return "heat-level-0";
      } else {
        if (!day.record) return "heat-level-0";
        return "status-" + day.record.status;
      }
    },
  },
};
</script>

<style scoped>
/* Page Layout */
.page-wrapper {
  padding: 32px 40px;
  max-width: 1300px;
  margin: 0 auto;
  color: #f8fafc;
  font-family:
    "Inter",
    system-ui,
    -apple-system,
    sans-serif;
}

/* Header */
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 28px;
}
.page-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.025em;
  margin-bottom: 6px;
}
.page-subtitle {
  color: #94a3b8;
  font-size: 0.95rem;
}
.header-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(30, 41, 59, 0.6);
  border: 1px solid #334155;
  padding: 8px 14px;
  border-radius: 20px;
  font-size: 0.85rem;
  color: #cbd5e1;
}
.live-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #10b981;
  box-shadow: 0 0 8px #10b981;
}

/* Overview Stat Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}
.stat-card {
  background: #0f172a;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
}
.stat-label {
  font-size: 0.825rem;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
}
.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 4px 0;
  color: #fff;
}
.stat-sub {
  font-size: 0.8rem;
  color: #94a3b8;
}
.text-emerald {
  color: #10b981;
}
.text-indigo {
  color: #818cf8;
}

/* Control Box */
.controls-card {
  background: #0f172a;
  border: 1px solid #1e293b;
  border-radius: 12px;
  padding: 18px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}
.control-group {
  display: flex;
  align-items: center;
  gap: 14px;
}
.control-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #cbd5e1;
}
.custom-select {
  background: #1e293b;
  color: #f8fafc;
  border: 1px solid #334155;
  padding: 9px 16px;
  border-radius: 8px;
  font-size: 0.9rem;
  outline: none;
  cursor: pointer;
  transition: border-color 0.2s;
}
.custom-select:focus {
  border-color: #3b82f6;
}

/* Legend */
.legend-container {
  display: flex;
  align-items: center;
  gap: 12px;
}
.legend-title {
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.05em;
}
.legend-items {
  display: flex;
  gap: 12px;
}
.legend-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.8rem;
  color: #94a3b8;
}
.dot {
  width: 10px;
  height: 10px;
  border-radius: 3px;
  display: inline-block;
}

/* Main Calendar Card */
.calendar-card {
  background: #0f172a;
  border: 1px solid #1e293b;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
}
.calendar-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid #1e293b;
}
.month-heading h2 {
  font-size: 1.35rem;
  font-weight: 700;
  color: #ffffff;
}
.view-indicator {
  font-size: 0.9rem;
  color: #94a3b8;
}
.view-highlight {
  color: #38bdf8;
  font-weight: 600;
}

/* Weekdays Header Grid */
.weekdays-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 12px;
  text-align: center;
  font-size: 0.8rem;
  font-weight: 700;
  color: #64748b;
  margin-bottom: 12px;
}

/* Days Grid */
.days-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 12px;
}
.day-cell {
  height: 85px;
  border-radius: 10px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition:
    transform 0.15s ease,
    box-shadow 0.15s ease;
  border: 1px solid transparent;
}
.day-cell:hover:not(.pad-cell) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

.pad-cell {
  background: transparent;
  border: none;
}
.cell-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.day-num {
  font-size: 1rem;
  font-weight: 700;
}
.today-tag {
  font-size: 0.6rem;
  font-weight: 800;
  background: #3b82f6;
  color: #fff;
  padding: 2px 5px;
  border-radius: 4px;
}
.cell-sub {
  font-size: 0.725rem;
  font-weight: 500;
  opacity: 0.9;
}
.capitalize {
  text-transform: capitalize;
}

/* Heatmap & Status Colors */
.future-day {
  background: #161e2e;
  color: #475569;
  border-color: #1e293b;
}
.dot.future {
  background: #161e2e;
  border: 1px solid #334155;
}

/* Company Heat Levels */
.dot.level-0,
.heat-level-0 {
  background: #1e293b;
  color: #64748b;
}
.dot.level-1,
.heat-level-1 {
  background: #064e3b;
  color: #a7f3d0;
  border-color: #047857;
}
.dot.level-2,
.heat-level-2 {
  background: #047857;
  color: #ecfdf5;
  border-color: #059669;
}
.dot.level-3,
.heat-level-3 {
  background: #059669;
  color: #ffffff;
  border-color: #10b981;
}
.dot.level-4,
.heat-level-4 {
  background: #10b981;
  color: #022c22;
  border-color: #34d399;
}

/* Individual Employee Statuses */
.dot.status-present,
.status-present {
  background: #10b981;
  color: #022c22;
}
.dot.status-late,
.status-late {
  background: #f59e0b;
  color: #451a03;
}
.dot.status-absent,
.status-absent {
  background: #ef4444;
  color: #450a0a;
}
.dot.status-leave,
.status-leave {
  background: #8b5cf6;
  color: #2e1065;
}
</style>
