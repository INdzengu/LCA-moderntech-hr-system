<template>
  <div class="leave-page">
    
    <section class="header">
      <p class="directory">MANAGEMENT</p>
      <h1>Leave Track</h1>
      <p class="subtitle">Manage and track employee time-off requests</p>
    </section>

    <section class="stats-grid">
      <div class="stat-card">
        <p class="stat-label">Total Employees on Leave</p>
        <p class="stat-number">{{ totalOnLeave }}</p>
      </div>
      
      <div class="stat-card">
        <p class="stat-label">Pending Approval</p>
        <p class="stat-number pink-text">{{ pendingApprovals }}</p>
      </div>
      
      <div class="stat-card">
        <p class="stat-label">Most Common Type</p>
        <p class="stat-number green-text">Sick</p>
      </div>
    </section>

    <section class="controls">
      <div class="search-row">
        <input
          type="text"
          placeholder="Search by employee name..."
          v-model="search"
        />
      </div>
    </section>

    <div class="timeline-container">
      
      <div class="timeline-section">
        <div class="section-title">
          <span class="dot pending-dot">●</span> Awaiting Your Action
        </div>
        <div class="divider"></div>

        <div v-if="pendingRequests.length > 0" class="timeline-headings">
          <span class="col-heading heading-emp">Employee</span>
          <span class="col-heading heading-type">Type of Leave</span>
          <span class="col-heading heading-duration">Duration</span>
          <span class="col-heading heading-action">Action</span>
        </div>

        <div v-if="pendingRequests.length === 0" class="empty-msg">
          No pending requests
        </div>
        
        <div
          v-for="item in pendingRequests"
          :key="item.employeeId + '-' + item.type"
          class="timeline-item"
        >
          <div class="emp-profile-col">
            <div class="row-avatar">{{ initials(item.name) }}</div>
            <div class="emp-name">{{ item.name }}</div>
          </div>
          <div class="leave-type">{{ item.type }} Leave</div>
          <div class="leave-days">{{ item.days }} days</div>
          
          <div class="action-buttons">
            <button
              class="view-btn"
              title="View Details"
              @click="showLeaveDetails(item)"
            >
              👁
            </button>
            
            <button
              class="approve-btn"
              title="Approve"
              @click="updateLeaveStatus(item.employeeId, item.type, 'Approved')"
            >
              ✓
            </button>
            
            <button
              class="reject-btn"
              title="Reject"
              @click="updateLeaveStatus(item.employeeId, item.type, 'Rejected')"
            >
              ✕
            </button>
          </div>
        </div>
      </div>

      <div class="timeline-section">
        <div class="section-title">
          <span class="dot approved-dot">●</span> Approved
        </div>
        <div class="divider"></div>

        <div v-if="approvedRequests.length > 0" class="timeline-headings">
          <span class="col-heading heading-emp">Employee</span>
          <span class="col-heading heading-type">Type of Leave</span>
          <span class="col-heading heading-duration">Duration</span>
          <span class="col-heading heading-action">Status</span>
        </div>

        <div v-if="approvedRequests.length === 0" class="empty-msg">
          No approved requests
        </div>
        
        <div
          v-for="item in approvedRequests"
          :key="item.employeeId + '-' + item.type"
          class="timeline-item"
        >
          <div class="emp-profile-col">
            <div class="row-avatar">{{ initials(item.name) }}</div>
            <div class="emp-name">{{ item.name }}</div>
          </div>
          <div class="leave-type">{{ item.type }} Leave</div>
          <div class="leave-days">{{ item.days }} days</div>
          <div class="action-buttons">
            <button
              class="view-btn"
              title="View Details"
              @click="showLeaveDetails(item)"
            >
              👁
            </button>
            <span class="status-text text-approved">Approved</span>
          </div>
        </div>
      </div>

      <div class="timeline-section">
        <div class="section-title">
          <span class="dot rejected-dot">●</span> Rejected
        </div>
        <div class="divider"></div>

        <div v-if="rejectedRequests.length > 0" class="timeline-headings">
          <span class="col-heading heading-emp">Employee</span>
          <span class="col-heading heading-type">Type of Leave</span>
          <span class="col-heading heading-duration">Duration</span>
          <span class="col-heading heading-action">Status</span>
        </div>

        <div v-if="rejectedRequests.length === 0" class="empty-msg">
          No rejected requests
        </div>
        
        <div
          v-for="item in rejectedRequests"
          :key="item.employeeId + '-' + item.type"
          class="timeline-item"
        >
          <div class="emp-profile-col">
            <div class="row-avatar">{{ initials(item.name) }}</div>
            <div class="emp-name">{{ item.name }}</div>
          </div>
          <div class="leave-type">{{ item.type }} Leave</div>
          <div class="leave-days">{{ item.days }} days</div>
          <div class="action-buttons">
            <button
              class="view-btn"
              title="View Details"
              @click="showLeaveDetails(item)"
            >
              👁
            </button>
            <span class="status-text text-rejected">Rejected</span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isModalOpen" class="modal-backdrop" @click.self="closeModal">
      <div class="details-card">
        <button class="close-card-btn" @click="closeModal">✕</button>

        <div class="card-header">
          <div class="card-avatar-large">
            {{ initials(selectedRequest.name) }}
          </div>
          <div>
            <h3>{{ selectedRequest.name }}</h3>
            <p class="card-sub">{{ selectedRequest.type }} Leave Request</p>
          </div>
        </div>

        <div class="card-body">
          <div class="info-row">
            <span class="info-label">Status</span>
            <span
              :class="[
                'status-badge',
                'badge-' + selectedRequest.status.toLowerCase(),
              ]"
            >
              {{ selectedRequest.status }}
            </span>
          </div>

          <div class="info-row">
            <span class="info-label">Date Submitted</span>
            <span class="info-value">{{ selectedRequest.dateSubmitted }}</span>
          </div>

          <div class="info-row">
            <span class="info-label">Duration</span>
            <span class="info-value text-highlight">{{ selectedRequest.days }} Days</span>
          </div>

          <div class="info-row">
            <span class="info-label">Start Date</span>
            <span class="info-value">{{ selectedRequest.startDate }}</span>
          </div>

          <div class="info-row">
            <span class="info-label">End Date</span>
            <span class="info-value">{{ selectedRequest.endDate }}</span>
          </div>

          <div class="reason-box">
            <p class="reason-title">Reason for Request</p>
            <p class="reason-text">
              "{{ selectedRequest.reason || "No reason provided." }}"
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LeaveView",
  
  // PROPS: Communications sent down from App.vue parent component
  props: {
    currentUser: {
      type: String,
      required: true,
    },
    // CENTRAL SHARED EMPLOYEES LINK: Direct pointer mapping straight to global state.
    // Any mutation applied here instantly updates other sub-view routes tracking this data source.
    employees: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      // INTERACTION STATES: Monitors search filter strings and tracking selections for the layout popups
      search: "",
      isModalOpen: false,
      selectedRequest: null, // Holds object references parsed to populate the details modal preview frame
    };
  },

  computed: {
    /**
     * ALL REQUESTS FLATTENING FUNCTION
     * * WHAT IT DOES: Iterates through nesting levels of arrays to return an organized single-level flat structure.
     * * WHY WE NEED IT: Employee arrays hold sub-arrays for requests. This structures everything cleanly for table lookups.
     * * HOW FILTER SEARCH WORKS: It inspects employee names against search field strings before loading objects into the stack.
     */
    allRequests() {
      let list = [];
      this.employees.forEach((employee) => {
        if (employee.leaveRequests && employee.leaveRequests.length > 0) {
          employee.leaveRequests.forEach((request) => {
            // Checks to see if typing matches string patterns inside the text input box
            if (
              employee.name.toLowerCase().includes(this.search.toLowerCase())
            ) {
              list.push({
                employeeId: employee.id,
                name: employee.name,
                type: request.type,
                days: request.days,
                status: request.status,
                reason: request.reason,
                dateSubmitted: request.dateSubmitted || "N/A",
                startDate: request.startDate || "N/A",
                endDate: request.endDate || "N/A",
              });
            }
          });
        }
      });
      return list;
    },

    // FILTER FEED METHODS: Subdivide our main flat array list into specific status segments
    pendingRequests() {
      return this.allRequests.filter((req) => req.status === "Pending");
    },
    approvedRequests() {
      return this.allRequests.filter((req) => req.status === "Approved");
    },
    rejectedRequests() {
      return this.allRequests.filter((req) => req.status === "Rejected");
    },

    // TOP COUNTER STATISTICS BLOCKS CALCULATORS
    totalOnLeave() {
      // Grabs items matching approved flags to determine total headcount away from the office
      return this.allRequests.filter((req) => req.status === "Approved").length;
    },
    pendingApprovals() {
      // Keeps score tracker totals for items requiring immediate attention inside Dashboard indicators
      return this.allRequests.filter((req) => req.status === "Pending").length;
    },
  },

  methods: {
    /**
     * ACTION UPDATE HANDLER SYSTEM
     * * WHAT IT DOES: Updates processing flags for leave statuses and syncs employee work availability.
     * * CENTRAL TIMELINE PIPELINE:
     * 1. Uses the ID parameter to point directly to the modified employee object map inside the main layout list.
     * 2. Pinpoints the matching leaf node request array tracking that targeted leave type.
     * 3. Alters the status string flag (Pending changes to Approved or Rejected based on choice clicked).
     * 4. APP-WIDE REACTION: If approved, updates employee.status to "On Leave". If rejected, resets back to "Active".
     * 5. APPVUE LOCAL STORAGE SYNC: App.vue senses this adjustment, autosaving changes across localStorage instantly.
     */
    updateLeaveStatus(employeeId, requestType, newStatus) {
      const employee = this.employees.find((emp) => emp.id === employeeId);

      if (employee && employee.leaveRequests) {
        const request = employee.leaveRequests.find(
          (req) => req.type === requestType,
        );

        if (request) {
          // Adjust status text value dynamically
          request.status = newStatus;

          // Toggle global system work status flags depending on selection choice action
          if (newStatus === "Approved") {
            employee.status = "On Leave";
          } else if (newStatus === "Rejected") {
            employee.status = "Active";
          }

          console.log(
            `✓ Leave request updated: ${employee.name} - ${requestType} - ${newStatus}`,
          );
        }
      }
    },

    /**
     * AVATAR NAME FORMATTING STRIPPER
     * * WHAT IT DOES: Converts full user string lines to neat short initial uppercase symbols.
     * INPUT EXAMPLE: "Sarah Mitchell" → "SM"
     */
    initials(name) {
      if (!name) return "";
      return name
        .split(" ")
        .map((word) => word[0])
        .join("")
        .toUpperCase();
    },

    /**
     * MODAL TOGGLE WINDOW ACTIONS
     * Loads selected object data into active focus view state loops and prompts display visibility
     */
    showLeaveDetails(item) {
      this.selectedRequest = item;
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
      this.selectedRequest = null;
    },
  },
};
</script>

<style scoped>
/* Page layout styles built for matching app standard spacing configurations */
.leave-page {
  background: #050505;
  min-height: 100vh;
  color: white;
  padding: 40px 20px;
  width: 100%;
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
  box-sizing: border-box;
}

/* HEADER ELEMENTS */
.header {
  text-align: center;
  margin: 20px 0 40px 0;
}

.directory {
  color: #44ff9a;
  letter-spacing: 4px;
  font-weight: 600;
  font-size: 0.85rem;
}

.header h1 {
  font-size: 3.5rem;
  margin: 10px 0;
}

.subtitle {
  color: #9f9f9f;
  font-size: 1.1rem;
}

/* STATS MATRIX LAYOUT CARDS */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: #0f0f0f;
  border: 1px solid #222;
  border-radius: 20px;
  padding: 25px;
  text-align: center;
}

.stat-label {
  color: #888;
  font-size: 0.9rem;
  margin: 0 0 10px 0;
}

.stat-number {
  font-size: 2.2rem;
  font-weight: bold;
  margin: 0;
}

.pink-text {
  color: #ff449a;
}

.green-text {
  color: #44ff9a;
}

/* SEARCH CONTROL INPUT STYLING */
.controls {
  margin-top: 30px;
  margin-bottom: 30px;
}

.search-row input {
  background: #121212;
  border: 1px solid #222;
  border-radius: 12px;
  color: white;
  padding: 12px 16px;
  font-size: 14px;
  width: 350px;
  outline: none;
}

.search-row input:focus {
  border-color: #44ff9a;
}

/* STACKED TIMELINE CONTAINERS */
.timeline-container {
  display: flex;
  flex-direction: column;
  gap: 40px;
  margin-top: 20px;
}

.timeline-section {
  display: flex;
  flex-direction: column;
}

.section-title {
  font-size: 1.15rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #ffffff;
}

.dot {
  font-size: 0.9rem;
}

.pending-dot {
  color: #ff449a;
}
.approved-dot {
  color: #44ff9a;
}
.rejected-dot {
  color: #ff4444;
}

.divider {
  height: 1px;
  background: #222;
  margin-top: 10px;
  margin-bottom: 10px;
}

/* ROW ALIGNMENTS HEADINGS */
.timeline-headings {
  display: flex;
  justify-content: space-between;
  padding: 5px 10px;
  margin-bottom: 5px;
}

.col-heading {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  font-size: 0.72rem;
  color: #555555;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.heading-emp {
  width: 25%;
}
.heading-type {
  width: 25%;
}
.heading-duration {
  width: 25%;
}
.heading-action {
  width: 25%;
  text-align: right;
}

.empty-msg {
  color: #555;
  font-size: 0.9rem;
  padding-left: 5px;
  font-style: italic;
  margin-top: 5px;
}

/* LIST ROWS HOVER INTERACTION DESIGN */
.timeline-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 10px;
  border-bottom: 1px solid #121212;
  font-size: 1rem;
}

.timeline-item:hover {
  background: #0a0a0a;
}

.emp-profile-col {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 25%;
}

.row-avatar {
  width: 32px;
  height: 32px;
  background: #1f2937;
  border: 1px solid #374151;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 0.8rem;
  font-weight: bold;
  color: #44ff9a;
  flex-shrink: 0;
}

.emp-name {
  font-weight: 500;
  color: #ffffff;
}

.leave-type {
  width: 25%;
  color: #888888;
}

.leave-days {
  width: 25%;
  color: #b8b8b8;
}

/* MANAGEMENT CONTROLS ALIGNMENTS */
.action-buttons {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  align-items: center;
  width: 25%;
}

.view-btn,
.approve-btn,
.reject-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 2px 6px;
  transition: transform 0.1s ease;
  font-size: 1rem;
}

.view-btn:hover,
.approve-btn:hover,
.reject-btn:hover {
  transform: scale(1.2);
}

.view-btn {
  color: #9f9f9f;
}

.approve-btn {
  color: #44ff9a;
  font-size: 1.1rem;
}

.reject-btn {
  color: #ff4444;
  font-size: 1.1rem;
}

.status-text {
  font-size: 0.95rem;
  text-align: right;
}

.text-approved {
  color: #44ff9a;
}

.text-rejected {
  color: #ff4444;
}

/* OVERLAY BACKDROP MODAL POPUP DESIGN SYSTEMS */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.details-card {
  background: #111827;
  border: 1px solid #1f2937;
  border-radius: 24px;
  width: 440px;
  max-width: 90%;
  padding: 30px;
  position: relative;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.close-card-btn {
  position: absolute;
  right: 20px;
  top: 15px;
  background: transparent;
  border: none;
  color: #888;
  font-size: 22px;
  cursor: pointer;
}

.close-card-btn:hover {
  color: white;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
}

.card-avatar-large {
  width: 55px;
  height: 55px;
  background: #1f2937;
  border: 2px solid #374151;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.1rem;
  font-weight: bold;
  color: #44ff9a;
  flex-shrink: 0;
}

.card-header h3 {
  margin: 0;
  font-size: 1.3rem;
  color: white;
}

.card-sub {
  margin: 2px 0 0 0;
  color: #888;
  font-size: 0.9rem;
}

.card-body {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #1f2937;
  padding-bottom: 10px;
}

.info-label {
  color: #888;
  font-size: 0.9rem;
}

.info-value {
  color: white;
  font-weight: 500;
}

.text-highlight {
  color: #44ff9a;
  font-weight: bold;
}

/* VISUAL COLOR STATUS STATUS BADGES */
.status-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: bold;
  text-transform: uppercase;
}

.badge-pending {
  background: rgba(255, 68, 154, 0.15);
  color: #ff449a;
}

.badge-approved {
  background: rgba(68, 255, 154, 0.15);
  color: #44ff9a;
}

.badge-rejected {
  background: rgba(255, 68, 68, 0.15);
  color: #ff4444;
}

.badge-denied {
  background: rgba(255, 68, 68, 0.15);
  color: #ff4444;
}

/* DETAIL TEXT WRITTEN STATEMENT CONTAINER BOX */
.reason-box {
  background: #0a0f1d;
  border: 1px solid #1f2937;
  border-radius: 14px;
  padding: 15px;
  margin-top: 5px;
}

.reason-title {
  color: #888;
  font-size: 0.85rem;
  margin: 0 0 6px 0;
  font-weight: bold;
}

.reason-text {
  color: #d1d5db;
  font-size: 0.9rem;
  margin: 0;
  line-height: 1.4;
  font-style: italic;
}

/* RESPONSIF MOBILE SPEC DESIGN RULES */
@media (max-width: 768px) {
  .header h1 {
    font-size: 2.5rem;
  }

  .search-row input {
    width: 100%;
  }

  .timeline-item {
    flex-wrap: wrap;
    gap: 10px;
  }

  .emp-profile-col,
  .leave-type,
  .leave-days,
  .action-buttons {
    width: 100%;
  }

  .action-buttons {
    justify-content: flex-start;
  }
}
</style>

