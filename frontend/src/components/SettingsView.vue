<template>
  <div class="settings-page">
    <!-- PAGE HEADER: Settings title and description -->
    <!-- Explains that this page is for configuration -->
    <section class="settings-header">
      <h1>⚙️ Settings</h1>
      <p>Configure company details and system preferences</p>
    </section>

    <!-- SETTINGS WRAPPER: Grid of settings cards -->
    <!-- Each card represents a different settings category -->
    <div class="settings-wrapper">
      
      <!-- CARD 1: COMPANY INFORMATION -->
      <!-- Allows HR to configure company details -->
      <div class="settings-card">
        <h2>🏢 Company Information</h2>

        <!-- FORM GRID: Multiple input fields -->
        <div class="form-grid">
          <!-- COMPANY NAME FIELD -->
          <div class="form-group">
            <label>Company Name</label>
            <!-- v-model connects to companyInfo.name in data -->
            <!-- Updates in real-time as user types -->
            <input
              v-model="companyInfo.name"
              type="text"
              placeholder="Enter company name"
            />
          </div>

          <!-- INDUSTRY FIELD -->
          <div class="form-group">
            <label>Industry</label>
            <!-- v-model connects to companyInfo.industry -->
            <input
              v-model="companyInfo.industry"
              type="text"
              placeholder="e.g., Healthcare Technology"
            />
          </div>

          <!-- HR EMAIL FIELD -->
          <div class="form-group">
            <label>HR Email Address</label>
            <!-- v-model connects to companyInfo.hrEmail -->
            <!-- type="email" provides email validation -->
            <input
              v-model="companyInfo.hrEmail"
              type="email"
              placeholder="hr@company.com"
            />
          </div>

          <!-- COUNTRY FIELD -->
          <div class="form-group">
            <label>Country</label>
            <!-- v-model connects to companyInfo.country -->
            <!-- Dropdown select with predefined options -->
            <select v-model="companyInfo.country">
              <option>South Africa</option>
              <option>United States</option>
              <option>United Kingdom</option>
              <option>Canada</option>
              <option>Australia</option>
            </select>
          </div>
        </div>

        <!-- CARD ACTIONS: Buttons to save or reset -->
        <div class="card-actions">
          <!-- SAVE BUTTON: Saves company info to localStorage -->
          <!-- @click calls saveCompanyInfo() method -->
          <button class="btn-primary" @click="saveCompanyInfo">
            Save Changes
          </button>
          <!-- RESET BUTTON: Reverts to original values -->
          <!-- @click calls resetCompanyInfo() method -->
          <button class="btn-secondary" @click="resetCompanyInfo">Reset</button>
        </div>
      </div>

      <!-- CARD 2: NOTIFICATION PREFERENCES -->
      <!-- Allows HR to choose which notifications to receive -->
      <div class="settings-card">
        <h2>🔔 Notification Preferences</h2>

        <!-- NOTIFICATIONS LIST: Toggles for each notification type -->
        <div class="notifications-list">
          <!-- LOOP THROUGH NOTIFICATION SETTINGS -->
          <!-- v-for creates a row for each notification type -->
          <div
            v-for="notification in notificationSettings"
            :key="notification.id"
            class="notification-row"
          >
            <!-- NOTIFICATION INFO: Title and description -->
            <div>
              <!-- NOTIFICATION TITLE: e.g., "Leave Request Alerts" -->
              <h3>{{ notification.title }}</h3>
              <!-- NOTIFICATION DESCRIPTION: Explains what this notification does -->
              <p>{{ notification.description }}</p>
            </div>

            <!-- TOGGLE SWITCH: On/off control -->
            <!-- Custom toggle switch component -->
            <div class="toggle-switch">
              <!-- HIDDEN CHECKBOX: Real input for the toggle -->
              <!-- v-model connects to notification.enabled (boolean) -->
              <input
                type="checkbox"
                :id="'notif-' + notification.id"
                v-model="notification.enabled"
              />
              <!-- TOGGLE LABEL: Visual toggle switch -->
              <!-- Connected to checkbox via :for attribute -->
              <label :for="'notif-' + notification.id"></label>
            </div>
          </div>
        </div>

        <!-- CARD ACTIONS: Save button -->
        <div class="card-actions">
          <!-- SAVE PREFERENCES BUTTON: Saves notification settings -->
          <!-- @click calls saveNotifications() method -->
          <button class="btn-primary" @click="saveNotifications">
            Save Preferences
          </button>
        </div>
      </div>

      <!-- CARD 3: SECURITY STATUS -->
      <!-- Shows security features and status of system -->
      <div class="settings-card">
        <h2>🔒 Security Status</h2>

        <!-- SECURITY LIST: Shows security features -->
        <div class="security-list">
          <!-- LOOP THROUGH SECURITY SETTINGS -->
          <!-- v-for creates a row for each security feature -->
          <div
            v-for="security in securitySettings"
            :key="security.id"
            class="security-row"
          >
            <!-- SECURITY INFO: Feature name and description -->
            <div>
              <!-- SECURITY NAME: e.g., "Two-Factor Authentication" -->
              <h3>{{ security.name }}</h3>
              <!-- SECURITY DESCRIPTION: Explains what this feature does -->
              <p>{{ security.description }}</p>
            </div>
            <!-- STATUS BADGE: Shows "Active" or status -->
            <!-- Always green in this demo -->
            <span class="status-badge status-active">{{
              security.status
            }}</span>
          </div>
        </div>

        <!-- CARD ACTIONS: Security management buttons -->
        <div class="card-actions">
          <!-- CHANGE PASSWORD BUTTON: Would open password change modal -->
          <!-- Currently disabled (no functionality) -->
          <button class="btn-secondary">Change Password</button>
          <!-- VIEW SESSIONS BUTTON: Would show active sessions -->
          <!-- Currently disabled (no functionality) -->
          <button class="btn-secondary">View Sessions</button>
        </div>
      </div>

      <!-- CARD 4: SYSTEM INFORMATION -->
      <!-- Shows technical details about the system -->
      <div class="settings-card">
        <h2>ℹ️ System Information</h2>

        <!-- SYSTEM INFO: Technical details -->
        <div class="system-info">
          <!-- VERSION ROW: Application version -->
          <div class="info-row">
            <span class="info-label">Version</span>
            <span class="info-value">{{ systemInfo.version }}</span>
          </div>

          <!-- RELEASE DATE ROW: When this version was released -->
          <div class="info-row">
            <span class="info-label">Release Date</span>
            <span class="info-value">{{ systemInfo.releaseDate }}</span>
          </div>

          <!-- FRAMEWORK ROW: What framework powers the app -->
          <!-- Shows "Vue.js 3" -->
          <div class="info-row">
            <span class="info-label">Framework</span>
            <span class="info-value">{{ systemInfo.framework }}</span>
          </div>

          <!-- STYLING ROW: CSS framework used -->
          <!-- Shows "Tailwind CSS" or other styling library -->
          <div class="info-row">
            <span class="info-label">Styling</span>
            <span class="info-value">{{ systemInfo.styling }}</span>
          </div>

          <!-- COMPLIANCE ROW: Legal compliance standards -->
          <!-- Shows "POPIA 2021" (South African data protection law) -->
          <div class="info-row">
            <span class="info-label">Compliance</span>
            <span class="info-value">{{ systemInfo.compliance }}</span>
          </div>
        </div>

        <!-- CARD ACTIONS: Help and maintenance buttons -->
        <div class="card-actions">
          <!-- CHECK UPDATES BUTTON: Would check for new versions -->
          <!-- Currently disabled (no functionality) -->
          <button class="btn-secondary">Check Updates</button>
          <!-- DOCUMENTATION BUTTON: Would link to help docs -->
          <!-- Currently disabled (no functionality) -->
          <button class="btn-secondary">Documentation</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SettingsView",

  props: {
    // CURRENT USER: Name of logged-in HR staff member
    // Shows which user is viewing settings
    currentUser: {
      type: String,
      default: "HR Admin",
    },
  },

  data() {
    return {
      // COMPANY INFO: Configuration for ModernTech Solutions
      // All fields are editable by HR staff
      companyInfo: {
        // Company legal name
        name: "ModernTech Solutions",
        // Industry type (e.g., healthcare, tech, finance)
        industry: "Healthcare Technology",
        // Contact email for HR department
        hrEmail: "hr@moderntech.com",
        // Country where company is based
        country: "South Africa",
      },

      // NOTIFICATION SETTINGS: Array of notification preferences
      // Each notification can be enabled or disabled
      // HR chooses which notifications to receive
      notificationSettings: [
        {
          id: "leave-alerts",
          title: "Leave Request Alerts",
          description: "Notify when new leave requests are submitted",
          // true = notification is enabled, false = disabled
          enabled: true,
        },
        {
          id: "payroll-reminders",
          title: "Payroll Processing Reminders",
          description: "Receive reminders before monthly payroll deadlines",
          enabled: true,
        },
        {
          id: "employee-notifications",
          title: "New Employee Notifications",
          description: "Alert when a new employee profile is created",
          enabled: true,
        },
        {
          id: "weekly-summary",
          title: "Weekly Summary Report",
          description: "Receive an automated HR summary every Monday",
          // This one is disabled by default
          enabled: false,
        },
      ],

      // SECURITY SETTINGS: Shows system security features
      // All set to "Active" - demonstrates security status
      securitySettings: [
        {
          id: "2fa",
          name: "Two-Factor Authentication",
          description: "Additional security layer for account protection",
          status: "Active",
        },
        {
          id: "encryption",
          name: "Data Encryption",
          description: "All sensitive data is encrypted at rest and in transit",
          status: "Active",
        },
        {
          id: "compliance",
          name: "POPIA Compliance",
          description:
            "System complies with South African data protection laws",
          status: "Active",
        },
      ],

      // SYSTEM INFO: Technical details about the application
      // Read-only information showing system specs
      systemInfo: {
        // Current version number
        version: "v2.4.0",
        // When this version was released
        releaseDate: "January 2026",
        // Frontend framework used
        framework: "Vue.js 3",
        // CSS styling library
        styling: "Tailwind CSS",
        // Data protection law compliance
        compliance: "POPIA 2021",
      },
    };
  },

  methods: {
    /**
     * SAVE COMPANY INFO: Persists company settings to localStorage
     *
     * WHAT IT DOES: Saves company information to browser storage
     * CALLED: When user clicks "Save Changes" button
     *
     * HOW IT WORKS:
     *   1. JSON.stringify() converts companyInfo object to text
     *   2. localStorage.setItem() stores it with key "moderntech_company"
     *   3. Show alert confirming save was successful
     *
     * PERSISTENCE: Data survives page refresh and browser restart
     *
     * EXAMPLE:
     * Stores: {"name":"ModernTech","industry":"Healthcare",...}
     */
    saveCompanyInfo() {
      // CONVERT TO JSON: Object → text string
      localStorage.setItem(
        "moderntech_company",
        JSON.stringify(this.companyInfo),
      );

      // CONFIRM: Show user the save was successful
      alert("Company information saved!");
    },

    /**
     * RESET COMPANY INFO: Reverts to original/default values
     *
     * WHAT IT DOES: Undoes any changes and restores defaults
     * CALLED: When user clicks "Reset" button
     *
     * HOW IT WORKS:
     *   1. Replaces companyInfo object with original values
     *   2. Form displays original data
     *   3. No save to storage happens (must click Save to persist)
     */
    resetCompanyInfo() {
      // RESTORE DEFAULTS: Original values from page load
      this.companyInfo = {
        name: "ModernTech Solutions",
        industry: "Healthcare Technology",
        hrEmail: "hr@moderntech.com",
        country: "South Africa",
      };
    },

    /**
     * SAVE NOTIFICATIONS: Persists notification preferences to localStorage
     *
     * WHAT IT DOES: Saves which notifications user wants to receive
     * CALLED: When user clicks "Save Preferences" button
     *
     * HOW IT WORKS:
     *   1. JSON.stringify() converts notificationSettings to text
     *   2. localStorage.setItem() stores with key "moderntech_notifications"
     *   3. Show alert confirming save was successful
     *
     * PERSISTENCE: Preferences survive page refresh
     *
     * RESULT: System remembers which notifications to send
     */
    saveNotifications() {
      // CONVERT TO JSON: Array → text string
      localStorage.setItem(
        "moderntech_notifications",
        JSON.stringify(this.notificationSettings),
      );

      // CONFIRM: Show user the save was successful
      alert("Notification preferences saved!");
    },
  },
};
</script>

<style scoped>
/* ============ MAIN SETTINGS PAGE ============ */
/* Dark background, full width and height */
.settings-page {
  width: 100%;
  padding: 30px;
  background: #050505;
  color: #ffffff;
}

/* ============ PAGE HEADER ============ */
/* Green gradient header with title */

.settings-header {
  background: linear-gradient(135deg, #0e1e16 0%, #050b07 100%);
  border: 1px solid #113821;
  border-radius: 8px;
  padding: 40px;
  margin-bottom: 30px;
  text-align: center;
}

/* HEADER TITLE: Large green text */
.settings-header h1 {
  font-size: 2.5rem;
  color: #44ff9a;
  margin: 0 0 10px 0;
  font-weight: 700;
}

/* HEADER SUBTITLE: Gray description text */
.settings-header p {
  color: #a0b0a6;
  font-size: 1rem;
  margin: 0;
}

/* ============ SETTINGS WRAPPER ============ */
/* Grid layout for settings cards */
.settings-wrapper {
  max-width: 1000px;
  margin: 0 auto;
  display: grid;
  /* Two columns on desktop, one on mobile */
  grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
  gap: 30px;
}

/* ============ SETTINGS CARD ============ */
/* Individual settings category card */
.settings-card {
  background: #0f0f0f;
  border: 1px solid #1c1c1c;
  border-radius: 8px;
  padding: 25px;
}

/* CARD HEADING: Title with emoji and green underline */
.settings-card h2 {
  color: #44ff9a;
  font-size: 1.2rem;
  margin: 0 0 20px 0;
  font-weight: 600;
  border-bottom: 1px solid #1a3a1a;
  padding-bottom: 15px;
}

/* ============ FORM GRID ============ */
/* Layout for form input fields */
.form-grid {
  display: grid;
  gap: 15px;
  margin-bottom: 20px;
}

/* FORM GROUP: Individual input + label */
.form-group {
  display: flex;
  flex-direction: column;
}

/* FORM LABEL: Gray text above input */
.form-group label {
  color: #888888;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* FORM INPUT: Text input fields */
.form-group input,
.form-group select {
  padding: 10px 12px;
  background: #111827;
  border: 1px solid #1c1c1c;
  border-radius: 6px;
  color: #ffffff;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

/* INPUT FOCUS: Green border on focus */
.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #44ff9a;
  box-shadow: 0 0 8px rgba(68, 255, 154, 0.2);
}

/* ============ NOTIFICATIONS LIST ============ */
/* Container for notification preference rows */
.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 20px;
}

/* NOTIFICATION ROW: Individual notification setting */
.notification-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #111827;
  border: 1px solid #1c1c1c;
  border-radius: 6px;
  transition: all 0.3s ease;
}

/* HOVER EFFECT: Highlight when hovering */
.notification-row:hover {
  border-color: #44ff9a;
  background: rgba(68, 255, 154, 0.02);
}

/* NOTIFICATION TITLE: Name of notification */
.notification-row h3 {
  color: #ffffff;
  font-size: 0.9rem;
  margin: 0 0 4px 0;
}

/* NOTIFICATION DESCRIPTION: What this notification does */
.notification-row p {
  color: #888888;
  font-size: 0.8rem;
  margin: 0;
}

/* ============ TOGGLE SWITCH ============ */
/* Custom on/off toggle for notifications */
.toggle-switch {
  position: relative;
  width: 45px;
  height: 24px;
}

/* HIDDEN CHECKBOX: Real input hidden from view */
.toggle-switch input {
  display: none;
}

/* TOGGLE LABEL: Visual toggle switch element */
.toggle-switch label {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #333333;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.3s ease;
}

/* TOGGLE DOT: White circle inside toggle */
.toggle-switch label::after {
  content: "";
  position: absolute;
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  background: #ffffff;
  border-radius: 50%;
  transition: all 0.3s ease;
}

/* TOGGLE CHECKED STATE: Green background when enabled */
.toggle-switch input:checked + label {
  background: #44ff9a;
}

/* TOGGLE DOT POSITION: Move right when checked */
.toggle-switch input:checked + label::after {
  left: 23px;
}

/* ============ SECURITY LIST ============ */
/* Container for security feature rows */
.security-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 20px;
}

/* SECURITY ROW: Individual security feature */
.security-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #111827;
  border: 1px solid #1c1c1c;
  border-radius: 6px;
}

/* SECURITY FEATURE NAME */
.security-row h3 {
  color: #ffffff;
  font-size: 0.9rem;
  margin: 0 0 4px 0;
}

/* SECURITY DESCRIPTION: What this feature protects */
.security-row p {
  color: #888888;
  font-size: 0.8rem;
  margin: 0;
}

/* STATUS BADGE: Shows security status */
.status-badge {
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

/* ACTIVE STATUS: Green badge */
.status-active {
  background: rgba(68, 255, 154, 0.15);
  color: #44ff9a;
  border: 1px solid rgba(68, 255, 154, 0.3);
}

/* ============ SYSTEM INFO ============ */
/* Container for system technical details */
.system-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 20px;
}

/* INFO ROW: Label + value pair */
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  background: #111827;
  border-radius: 6px;
  border: 1px solid #1c1c1c;
}

/* INFO LABEL: Gray field name */
.info-label {
  color: #888888;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

/* INFO VALUE: Green technical detail */
.info-value {
  color: #44ff9a;
  font-size: 0.9rem;
  font-weight: 600;
}

/* ============ CARD ACTIONS ============ */
/* Button container at bottom of cards */
.card-actions {
  display: flex;
  gap: 10px;
  padding-top: 15px;
  border-top: 1px solid #1a3a1a;
}

/* PRIMARY BUTTON: Main action (Save, Apply, etc) */
.btn-primary,
.btn-secondary {
  padding: 10px 16px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid;
  font-size: 0.85rem;
  flex: 1;
  text-align: center;
}

/* PRIMARY BUTTON STYLING: Green background */
.btn-primary {
  background: #44ff9a;
  color: #000000;
  border-color: #44ff9a;
}

/* PRIMARY BUTTON HOVER: Darker green */
.btn-primary:hover {
  background: #3ae08a;
  border-color: #3ae08a;
  box-shadow: 0 0 12px rgba(68, 255, 154, 0.3);
}

/* SECONDARY BUTTON STYLING: Transparent green outline */
.btn-secondary {
  background: transparent;
  color: #44ff9a;
  border-color: #44ff9a;
}

/* SECONDARY BUTTON HOVER: Light green background */
.btn-secondary:hover {
  background: rgba(68, 255, 154, 0.1);
  box-shadow: 0 0 10px rgba(68, 255, 154, 0.2);
}

/* ============ RESPONSIVE DESIGN (MOBILE) ============ */
/* Adjusts layout for screens smaller than 768px */
@media (max-width: 768px) {
  /* REDUCE PAGE PADDING */
  .settings-page {
    padding: 20px;
  }

  /* REDUCE HEADER PADDING */
  .settings-header {
    padding: 30px 20px;
  }

  /* SMALLER HEADER TITLE */
  .settings-header h1 {
    font-size: 1.8rem;
  }

  /* SINGLE COLUMN LAYOUT */
  .settings-wrapper {
    grid-template-columns: 1fr;
    max-width: 100%;
  }

  /* REDUCE CARD PADDING */
  .settings-card {
    padding: 20px;
  }

  /* STACK BUTTONS VERTICALLY */
  .card-actions {
    flex-direction: column;
  }

  /* FULL WIDTH BUTTONS */
  .btn-primary,
  .btn-secondary {
    width: 100%;
  }

  /* NOTIFICATION AND SECURITY ROWS: VERTICAL STACK */
  .notification-row,
  .security-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  /* TOGGLE SWITCH: MOVE TO RIGHT */
  .toggle-switch {
    align-self: flex-end;
  }
}
</style>
