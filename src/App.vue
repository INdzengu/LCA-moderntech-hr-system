<template>
  <div class="app-container">
    <!-- LOGIN VIEW: Shows when user is not authenticated -->
    <LoginView
      v-if="!isLoggedIn"
      @login-success="handleSuccessfulAuthentication"
    />

    <!-- MAIN APPLICATION SHELL: Shows when user is logged in -->
    <div v-else>
      <!-- NAVIGATION BAR: Sticky header with logo and menu links -->
      <nav class="top-nav">
        <!-- LOGO SECTION: Company branding with glowing dot accent -->
        <div class="logo">
          <div class="logo-dot"></div>
          <span>ModernTech Solutions</span>
        </div>

        <!-- HAMBURGER MENU: Mobile-only toggle button for navigation -->
        <button class="hamburger-menu" @click="mobileNavOpen = !mobileNavOpen">
          ☰
        </button>

        <!-- DESKTOP NAVIGATION: Navigation links shown on screens wider than 768px -->
        <!-- Links are dynamically highlighted based on currentView data property -->
        <div class="nav-links-desktop">
          <a
            href="#"
            :class="{ 'active-link': currentView === 'dashboard' }"
            @click.prevent="currentView = 'dashboard'"
            >Dashboard</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'people' }"
            @click.prevent="currentView = 'people'"
            >People</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'Calendar' }"
            @click.prevent="currentView = 'Calendar'"
            >Calendar</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'leave' }"
            @click.prevent="currentView = 'leave'"
            >Leave</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'Payroll' }"
            @click.prevent="currentView = 'Payroll'"
            >Payroll</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'Settings' }"
            @click.prevent="currentView = 'settings'"
            >Settings</a
          >
        </div>

        <!-- MOBILE NAVIGATION: Dropdown menu that appears when hamburger is clicked on mobile -->
        <!-- Uses v-show to toggle visibility based on mobileNavOpen boolean -->
        <!-- Closes after navigation by calling toggleMobileNav() -->
        <div v-show="mobileNavOpen" class="nav-links-mobile">
          <a
            href="#"
            :class="{ 'active-link': currentView === 'dashboard' }"
            @click.prevent="
              toggleMobileNav();
              currentView = 'dashboard';
            "
            >Dashboard</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'people' }"
            @click.prevent="
              toggleMobileNav();
              currentView = 'people';
            "
            >People</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'Calendar' }"
            @click.prevent="
              toggleMobileNav();
              currentView = 'Calendar';
            "
            >Calendar</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'leave' }"
            @click.prevent="
              toggleMobileNav();
              currentView = 'leave';
            "
            >Leave</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'Payroll' }"
            @click.prevent="
              toggleMobileNav();
              currentView = 'Payroll';
            "
            >Payroll</a
          >
          <a
            href="#"
            :class="{ 'active-link': currentView === 'Settings' }"
            @click.prevent="
              toggleMobileNav();
              currentView = 'settings';
            "
            >Settings</a
          >
        </div>

        <!-- USER SECTION: Displays current user name and logout button -->
        <div class="user-section">
          <span class="user-name">{{ currentUser }}</span>
          <button class="logout-btn" @click="executeSystemLogout">
            Logout
          </button>
        </div>
      </nav>

      <!-- PAGE CONTENT: Dynamic content area that switches between different views -->
      <!-- The view displayed depends on the currentView data property value -->
      <!-- All views receive the same employees array from central state (App.vue) -->
      <main class="page-content">
        <!-- DASHBOARD VIEW: Shows summary stats, charts, and activity logs -->
        <DashboardView
          v-if="currentView === 'dashboard'"
          :employees="employees"
          :currentUser="currentUser"
        />

        <!-- PEOPLE VIEW: Shows employee directory with search and filter functionality -->
        <PeopleView
          v-else-if="currentView === 'people'"
          :employees="employees"
          :currentUser="currentUser"
        />

        <!-- LEAVE VIEW: Manages leave requests with approval/rejection functionality -->
        <LeaveView
          v-else-if="currentView === 'leave'"
          :currentUser="currentUser"
          :employees="employees"
        />

        <!-- CALENDAR VIEW: Displays attendance heatmap and tracking -->
        <CalendarView
          v-else-if="currentView === 'Calendar'"
          :employees="employees"
          :currentUser="currentUser"
        />

        <!-- PAYROLL VIEW: Shows salary calculations and payslip generation -->
        <PayrollView
          v-else-if="currentView === 'Payroll'"
          :currentUser="currentUser"
          :employees="employees"
        />

        <!-- SETTINGS VIEW: Configuration panel for company info and preferences -->
        <SettingsView
          v-else-if="currentView === 'settings'"
          :currentUser="currentUser"
        />
      </main>

      <!-- FOOTER: Site information and quick links -->
      <footer class="app-footer">
        <!-- FOOTER CONTENT GRID: Multi-column layout with company info and links -->
        <div class="footer-content">
          <!-- COMPANY BRANDING SECTION -->
          <div class="footer-section">
            <h4>ModernTech Solutions</h4>
            <p>Intelligent HR Management System</p>
          </div>

          <!-- QUICK LINKS TO MAIN PAGES -->
          <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
              <li>
                <a href="#" @click.prevent="currentView = 'dashboard'"
                  >Dashboard</a
                >
              </li>
              <li>
                <a href="#" @click.prevent="currentView = 'people'">People</a>
              </li>
              <li>
                <a href="#" @click.prevent="currentView = 'leave'"
                  >Leave Management</a
                >
              </li>
              <li>
                <a href="#" @click.prevent="currentView = 'Payroll'">Payroll</a>
              </li>
            </ul>
          </div>

          <!-- SUPPORT RESOURCES -->
          <div class="footer-section">
            <h4>Support</h4>
            <ul>
              <li><a href="#">Contact Us</a></li>
              <li><a href="#">Documentation</a></li>
              <li><a href="#">FAQs</a></li>
              <li><a href="#">Report Issue</a></li>
            </ul>
          </div>

          <!-- LEGAL LINKS -->
          <div class="footer-section">
            <h4>Legal</h4>
            <ul>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Terms of Service</a></li>
              <li><a href="#">Security</a></li>
              <li><a href="#">Compliance</a></li>
            </ul>
          </div>
        </div>

        <!-- FOOTER BOTTOM: Copyright and version info -->
        <div class="footer-bottom">
          <p>&copy; 2026 ModernTech Solutions. All rights reserved.</p>
          <p class="version-info">v2.4.0 • Powered by Vue.js</p>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
// COMPONENT IMPORTS: Import all view components that make up the SPA
import LoginView from "./components/LoginView.vue";
import DashboardView from "./components/DashboardView.vue";
import PeopleView from "./components/PeopleView.vue";
import LeaveView from "./components/LeaveView.vue";
import PayrollView from "./components/PayrollView.vue";
import CalendarView from "./components/CalendarView.vue";
import SettingsView from "./components/SettingsView.vue";

// CENTRAL DATA SOURCE: Import dummy employee data from JSON file
// This is imported once and used throughout the app via props to child components
import employeeData from "./data/employees.json";

export default {
  name: "App",

  // COMPONENT REGISTRATION: Register all child components
  components: {
    LoginView,
    DashboardView,
    PeopleView,
    LeaveView,
    PayrollView,
    CalendarView,
    SettingsView,
  },

  data() {
    return {
      // AUTHENTICATION STATE: Controls whether login screen or main app is shown
      isLoggedIn: false,

      // CURRENT USER: Stores the logged-in user's name (hardcoded for proof of concept)
      currentUser: "HR Admin",

      // CURRENT VIEW: Controls which component is displayed in the page-content area
      // Values: 'dashboard', 'people', 'leave', 'Calendar', 'Payroll', 'settings'
      currentView: "dashboard",

      // CENTRAL STATE - EMPLOYEE DATA: Single source of truth for all employee information
      // All child components receive this data via props and cannot modify it directly
      // Changes to employees trigger the watcher which saves to localStorage
      employees: [],

      // MOBILE NAVIGATION STATE: Controls visibility of mobile menu dropdown
      // Set to false when user clicks a navigation link (via toggleMobileNav method)
      mobileNavOpen: false,
    };
  },

  // WATCHERS: Monitor data changes and trigger side effects
  watch: {
    // DEEP WATCH ON EMPLOYEES ARRAY: Triggers whenever any employee data changes
    // This includes nested properties like leave requests or attendance records
    // Calls saveEmployeeDataToStorage to persist changes to localStorage
    employees: {
      handler(newVal) {
        this.saveEmployeeDataToStorage();
      },
      deep: true,
    },
  },

  // LIFECYCLE HOOK: Runs when component is created but not yet rendered
  // Used to initialize employee data from localStorage or JSON file
  created() {
    this.initializeEmployeeData();
  },

  methods: {
    // INITIALIZE EMPLOYEE DATA: Called on app startup (in created hook)
    // Priority: Try localStorage first (for persistence), then fall back to imported JSON
    // This ensures employee data survives page refreshes
    initializeEmployeeData() {
      // Check if employee data exists in browser's localStorage
      const savedEmployees = localStorage.getItem("moderntech_employees");

      if (savedEmployees) {
        try {
          // Parse and load previously saved employee data from localStorage
          this.employees = JSON.parse(savedEmployees);
        } catch (error) {
          // If localStorage data is corrupted, log error and use imported JSON instead
          console.error("Error loading saved employee data:", error);
          this.employees = JSON.parse(JSON.stringify(employeeData));
        }
      } else {
        // First time loading app: use imported JSON employee data
        // JSON.parse(JSON.stringify()) creates a deep copy to avoid reference issues
        this.employees = JSON.parse(JSON.stringify(employeeData));
      }
    },

    // SAVE EMPLOYEE DATA TO STORAGE: Persists employee array to browser's localStorage
    // Called automatically by the watcher whenever employees data changes
    // This makes changes permanent - they survive page refreshes
    saveEmployeeDataToStorage() {
      try {
        // Convert employees array to JSON string and save to localStorage
        localStorage.setItem(
          "moderntech_employees",
          JSON.stringify(this.employees),
        );
      } catch (error) {
        // Log error if localStorage is full or unavailable
        console.error("Error saving employee data to localStorage:", error);
      }
    },

    // TOGGLE MOBILE NAVIGATION: Closes the mobile menu after user clicks a link
    // Used in mobile navigation to hide the dropdown after navigation
    toggleMobileNav() {
      this.mobileNavOpen = false;
    },

    // HANDLE SUCCESSFUL AUTHENTICATION: Called when user logs in successfully
    // Sets authentication state to true, sets user name, and shows dashboard
    handleSuccessfulAuthentication() {
      this.isLoggedIn = true;
      this.currentUser = "HR Admin";
      this.currentView = "dashboard";
    },

    // EXECUTE SYSTEM LOGOUT: Called when user clicks logout button
    // Sets authentication state to false, which shows login screen again
    executeSystemLogout() {
      this.isLoggedIn = false;
    },
  },
};
</script>

<style>
/* GLOBAL STYLES: Applied to entire page */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* HTML AND BODY: Reset default styles and set background */
body,
html {
  margin: 0 !important;
  padding: 0 !important;
  background: #050505;
  width: 100%;
  overflow-x: hidden;
}

/* APP CONTAINER: Main wrapper element for entire application */
#app {
  width: 100%;
  min-height: 100vh;
}

.app-container {
  width: 100%;
  min-height: 100vh;
  margin: 0;
  padding: 0;
  background: #050505;
  display: flex;
  flex-direction: column;
}

/* PAGE CONTENT: Takes up remaining space between navbar and footer */
.page-content {
  flex: 1;
}

/* TOP NAVIGATION BAR: Fixed sticky header with logo, menu, and user info */
.top-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  background: linear-gradient(135deg, #0a2818 0%, #051409 100%);
  border-bottom: 2px solid #44ff9a;
  padding: 18px 30px;
  margin: 0;
  width: 100%;
  box-shadow: 0 2px 15px rgba(68, 255, 154, 0.1);
}

/* LOGO: Company branding with glowing accent dot */
.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 1.3rem;
  font-weight: 700;
  color: #44ff9a;
}

.logo-dot {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #44ff9a;
  box-shadow: 0 0 10px rgba(68, 255, 154, 0.5);
}

/* DESKTOP NAVIGATION LINKS: Visible on screens wider than 768px */
.nav-links-desktop a {
  color: #b8b8b8;
  text-decoration: none;
  font-size: 1rem;
  transition: color 0.3s ease;
}

.nav-links-desktop a:hover {
  color: #44ff9a;
}

/* ACTIVE LINK HIGHLIGHTING: Shows which page user is currently viewing */
.nav-links-desktop .active-link {
  color: #44ff9a !important;
  font-weight: bold;
  text-shadow: 0 0 10px rgba(68, 255, 154, 0.3);
}

/* USER SECTION: Displays logged-in user name and logout button */
.user-section {
  display: flex;
  align-items: center;
  gap: 15px;
  color: white;
}

.user-name {
  color: #44ff9a;
  font-weight: 600;
}

/* LOGOUT BUTTON: Styled button with hover effects */
.logout-btn {
  background: transparent;
  border: 1px solid #44ff9a;
  color: #44ff9a;
  padding: 8px 15px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.logout-btn:hover {
  background: rgba(68, 255, 154, 0.1);
  box-shadow: 0 0 10px rgba(68, 255, 154, 0.3);
}

/* HAMBURGER MENU BUTTON: Hidden by default, shown on mobile (max-width: 768px) */
.hamburger-menu {
  display: none;
  background: transparent;
  border: none;
  color: #44ff9a;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 8px 12px;
  transition: all 0.3s ease;
}

.hamburger-menu:hover {
  opacity: 0.8;
  text-shadow: 0 0 10px rgba(68, 255, 154, 0.5);
}

/* DESKTOP NAVIGATION CONTAINER: Displayed as flex row on desktop */
.nav-links-desktop {
  display: flex;
  gap: 25px;
  flex-wrap: wrap;
}

/* MOBILE NAVIGATION CONTAINER: Dropdown menu that appears when hamburger clicked */
/* Hidden by default (display: none), shown when mobileNavOpen is true */
.nav-links-mobile {
  display: none;
  position: absolute;
  top: 60px;
  left: 0;
  right: 0;
  background: linear-gradient(135deg, #0a2818 0%, #051409 100%);
  border-bottom: 2px solid #44ff9a;
  flex-direction: column;
  gap: 15px;
  padding: 20px;
  z-index: 999;
}

/* MOBILE NAVIGATION LINKS: Styled with borders and hover effects */
.nav-links-mobile a {
  color: #b8b8b8;
  text-decoration: none;
  font-size: 1rem;
  padding: 10px 0;
  border-bottom: 1px solid #1a3a1a;
  transition: color 0.3s ease;
}

.nav-links-mobile a:last-child {
  border-bottom: none;
}

.nav-links-mobile a:hover {
  color: #44ff9a;
}

.nav-links-mobile .active-link {
  color: #44ff9a !important;
  font-weight: bold;
  text-shadow: 0 0 10px rgba(68, 255, 154, 0.3);
}

/* FOOTER: Site footer with company info and links */
.app-footer {
  background: linear-gradient(135deg, #0a2818 0%, #051409 100%);
  border-top: 2px solid #44ff9a;
  color: #b8b8b8;
  margin-top: 50px;
  padding: 40px 30px 20px;
  font-size: 0.9rem;
}

/* FOOTER CONTENT GRID: Multi-column layout for footer sections */
.footer-content {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 30px;
  margin-bottom: 30px;
}

/* FOOTER SECTION: Individual column in footer with heading and content */
.footer-section h4 {
  color: #44ff9a;
  margin-bottom: 15px;
  font-size: 1rem;
  font-weight: 600;
}

.footer-section p {
  color: #888888;
  line-height: 1.6;
}

/* FOOTER LINKS: Styled list of links with hover effects */
.footer-section ul {
  list-style: none;
}

.footer-section ul li {
  margin-bottom: 10px;
}

.footer-section ul li a {
  color: #b8b8b8;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-section ul li a:hover {
  color: #44ff9a;
  text-shadow: 0 0 8px rgba(68, 255, 154, 0.3);
}

/* FOOTER BOTTOM: Copyright and version info */
.footer-bottom {
  border-top: 1px solid #1a3a1a;
  padding-top: 20px;
  text-align: center;
  color: #666666;
  font-size: 0.85rem;
}

.footer-bottom p {
  margin: 5px 0;
}

.version-info {
  color: #555555;
  font-size: 0.8rem;
}

/* RESPONSIVE DESIGN: Tablets and smaller screens (max-width: 768px) */
@media (max-width: 768px) {
  /* Show hamburger menu on mobile */
  .hamburger-menu {
    display: block;
  }

  /* Hide desktop navigation on mobile */
  .nav-links-desktop {
    display: none;
  }

  /* Show mobile navigation dropdown on mobile */
  .nav-links-mobile {
    display: flex;
  }

  /* Position navbar relatively to stack dropdown below it */
  .top-nav {
    position: relative;
  }

  /* User section on mobile: full width with separator line */
  .user-section {
    width: 100%;
    justify-content: space-between;
    padding-top: 15px;
    border-top: 1px solid #1a3a1a;
  }

  /* Footer on tablet: 2 columns instead of 4 */
  .footer-content {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    padding: 0 10px;
  }
}

/* RESPONSIVE DESIGN: Mobile phones (max-width: 480px) */
@media (max-width: 480px) {
  /* Adjust mobile navigation position */
  .nav-links-mobile {
    top: 55px;
    padding: 15px;
  }

  /* User section stacks vertically on small mobile */
  .user-section {
    flex-direction: column;
    gap: 10px;
  }

  /* Logout button full width on mobile */
  .logout-btn {
    width: 100%;
  }

  /* Footer on mobile: single column */
  .footer-content {
    grid-template-columns: 1fr;
  }

  /* Footer padding on mobile */
  .app-footer {
    padding: 30px 15px 15px;
  }
}
</style>
