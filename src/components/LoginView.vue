<template>
  <div class="login-page">
    <!-- LEFT SIDE: Hero section with company branding and visual overview -->
    <div class="hero-section">
      <!-- LOGO: Company name and icon at the top -->
      <div class="logo">
        <div class="logo-icon"></div>
        <h2>ModernTech Solutions</h2>
      </div>

      <!-- HERO CONTENT: Main tagline and value proposition -->
      <!-- This explains what the HR system does in simple terms -->
      <div class="hero-content">
        <h1>
          People.<br />
          Performance.<br />
          <span>Progress.</span>
        </h1>

        <p>
          Empower your team, streamline HR operations, and build a workplace
          where everyone thrives.
        </p>
      </div>

      <!-- PREVIEW CARD: Shows mockup of dashboard features -->
      <!-- This gives users a preview of what they'll see after login -->
      <div class="preview-card">
        <h3>Overview</h3>

        <!-- Chart area with fake line chart and progress circle -->
        <div class="chart-area">
          <div class="line-chart"></div>

          <!-- Circle showing 98% capacity (visual only) -->
          <div class="circle-progress">
            <span>98%</span>
            <small>Capacity</small>
          </div>
        </div>

        <!-- Bar chart showing dummy data (visual only) -->
        <div class="bar-chart">
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE: Login form card -->
    <div class="login-card">
      <!-- LOGO BOX: People emoji icon for the login form -->
      <div class="logo-box">👥</div>

      <h2>Welcome Back</h2>

      <p class="subtitle">Sign in to your HR portal</p>

      <!-- ERROR MESSAGE: Shows validation errors or incorrect credentials -->
      <!-- Only displays if errorMessage has a value (v-if directive) -->
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>

      <!-- LOADING MESSAGE: Shows while checking credentials -->
      <!-- Appears when isLoading is true (simulating login process) -->
      <div v-if="isLoading" class="loading-message">⏳ Authenticating...</div>

      <!-- LOGIN FORM: Collects email and password from user -->
      <!-- @submit.prevent prevents page reload and calls login() method instead -->
      <form @submit.prevent="login">
        <!-- EMAIL INPUT GROUP -->
        <div class="input-group">
          <label for="email">Email Address</label>

          <!-- EMAIL INPUT FIELD -->
          <!-- v-model connects input to email data (two-way binding) -->
          <!-- @input clears errors when user starts typing (better UX) -->
          <!-- :class adds red border if email format is invalid -->
          <!-- :disabled prevents typing while isLoading is true -->
          <input
            id="email"
            type="email"
            v-model="email"
            placeholder="admin@moderntech.com"
            @input="clearError"
            :class="{ 'input-error': errorMessage && !isValidEmail(email) }"
            :disabled="isLoading"
          />
          <!-- HINT TEXT: Shows if email format is wrong -->
          <!-- Uses isValidEmail() method to validate format -->
          <span v-if="email && !isValidEmail(email)" class="input-hint">
            ℹ️ Invalid email format
          </span>
        </div>

        <!-- PASSWORD INPUT GROUP -->
        <div class="input-group">
          <label for="password">Password</label>

          <!-- PASSWORD INPUT FIELD -->
          <!-- :type switches between "text" and "password" based on showPassword checkbox -->
          <!-- v-model connects input to password data -->
          <!-- @input clears errors when user types -->
          <!-- :class adds red border if password is less than 6 characters -->
          <!-- :disabled prevents typing while isLoading is true -->
          <input
            id="password"
            :type="showPassword ? 'text' : 'password'"
            v-model="password"
            placeholder="Enter Password"
            @input="clearError"
            :class="{
              'input-error':
                errorMessage && password.length > 0 && password.length < 6,
            }"
            :disabled="isLoading"
          />
          <!-- HINT TEXT: Shows if password is too short -->
          <!-- Minimum 6 characters required -->
          <span v-if="password && password.length < 6" class="input-hint">
            ℹ️ Minimum 6 characters required
          </span>
        </div>

        <!-- SHOW PASSWORD CHECKBOX -->
        <!-- Toggles between showing and hiding password -->
        <!-- v-model connects to showPassword boolean (true = show, false = hide) -->
        <!-- :disabled prevents clicking while isLoading is true -->
        <div class="options">
          <label>
            <input
              type="checkbox"
              v-model="showPassword"
              :disabled="isLoading"
            />
            Show Password
          </label>
        </div>

        <!-- LOGIN BUTTON: Submits the form -->
        <!-- :disabled prevents clicking while isLoading is true -->
        <!-- Button text changes from "Sign In" to "Signing In..." during loading -->
        <button class="login-btn" :disabled="isLoading" type="submit">
          {{ isLoading ? "Signing In..." : "Sign In" }}
        </button>
      </form>

      <!-- DEMO CREDENTIALS: Test login information -->
      <!-- Shows hardcoded credentials for testing the application -->
      <div class="demo-hint">
        <p><strong>Demo Credentials:</strong></p>
        <p>Email: <code>admin@moderntech.com</code></p>
        <p>Password: <code>Admin@2025</code></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LoginView",

  // DATA: Stores all reactive state for login form
  // These values update as user interacts with the form
  data() {
    return {
      // email: Stores email address entered by user
      email: "",

      // password: Stores password entered by user
      password: "",

      // showPassword: Boolean that controls if password is visible
      // true = show password as text, false = show as dots
      // Used in :type binding for password input
      showPassword: false,

      // errorMessage: Stores error text to display to user
      // Empty string = no error, non-empty string = show error
      // Displayed using v-if="errorMessage" in template
      errorMessage: "",

      // isLoading: Boolean for login loading state
      // true = disable button, show "Authenticating..." message
      // false = enable button, show "Sign In" text
      isLoading: false,
    };
  },

  methods: {
    /**
     * LOGIN METHOD: Main authentication validation
     *
     * FUNCTION: Validates user input and checks against hardcoded credentials
     * CALLED: When user clicks "Sign In" button (@submit.prevent="login")
     *
     * VALIDATION FLOW:
     * 1. Reset any previous error messages
     * 2. Check if email and password fields are not empty
     * 3. Check if email format is valid (something@domain.com)
     * 4. Check if password is at least 6 characters
     * 5. Compare against hardcoded credentials
     * 6. If credentials match: save to localStorage, emit event, show success
     * 7. If anything fails: show error message and stop
     */
    login() {
      // RESET: Clear any previous error messages before validation
      this.errorMessage = "";

      // VALIDATION 1: Check if both email and password have content
      // If either field is empty, show error and stop
      if (this.email === "" || this.password === "") {
        this.errorMessage = "❌ Please complete all fields";
        return;
      }

      // VALIDATION 2: Check if email format is correct
      // Calls isValidEmail() method which uses regex to validate
      // Email must have: username@domain.extension format
      if (!this.isValidEmail(this.email)) {
        this.errorMessage =
          "❌ Please enter a valid email address (format: example@domain.com)";
        return;
      }

      // VALIDATION 3: Check if password meets minimum length requirement
      // Password must be at least 6 characters long
      if (this.password.length < 6) {
        this.errorMessage = "❌ Password must be at least 6 characters";
        return;
      }

      // CREDENTIALS CHECK: Compare against hardcoded demo credentials
      // Email must be "admin@moderntech.com" AND password must be "Admin@2025"
      // This is a mock authentication (not real authentication)
      if (
        this.email === "admin@moderntech.com" &&
        this.password === "Admin@2025"
      ) {
        // SUCCESS PATH: Credentials are correct
        // Set isLoading to true to disable button and show loading message
        this.isLoading = true;

        // SIMULATE API CALL: Wait 500 milliseconds (simulates server response)
        // In real app, this would be actual API call to backend
        // setTimeout is JavaScript built-in function for delays
        setTimeout(() => {
          // SAVE TO LOCAL STORAGE: Remember user is logged in
          // localStorage persists data even after page refresh
          // isLoggedIn flag is checked by App.vue to show/hide views
          localStorage.setItem("isLoggedIn", "true");
          localStorage.setItem("moderntech_session_user", "Admin");

          // EMIT EVENT: Send "login-success" event to parent component (App.vue)
          // Parent component listens for this event and changes view
          // This is how child (LoginView) communicates with parent (App.vue)
          this.$emit("login-success");

          // STOP LOADING: Set isLoading back to false
          this.isLoading = false;
        }, 500);
      } else {
        // FAILURE PATH: Email and password don't match stored credentials
        this.errorMessage = "❌ Incorrect email or password";
      }
    },

    /**
     * IS VALID EMAIL METHOD: Validates email format using regex
     *
     * FUNCTION: Checks if email follows correct format
     * CALLED: In login() method during validation
     * RETURNS: true if format is correct, false if not
     *
     * VALID EMAIL EXAMPLES:
     * - admin@moderntech.com ✓
     * - user@example.co.za ✓
     *
     * INVALID EMAIL EXAMPLES:
     * - adminmodertech.com (missing @) ✗
     * - admin@domain (missing extension) ✗
     * - @moderntech.com (missing username) ✗
     */
    isValidEmail(email) {
      // REGEX PATTERN: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      // Explanation of pattern:
      // ^            = start of string
      // [^\s@]+      = one or more characters that are NOT space or @
      // @            = the @ symbol (must be present)
      // [^\s@]+      = one or more characters that are NOT space or @
      // \.           = the dot/period (escaped with backslash)
      // [^\s@]+      = one or more characters that are NOT space or @
      // $            = end of string
      //
      // test() method returns true if pattern matches, false if not
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },

    /**
     * CLEAR ERROR METHOD: Removes error message when user starts typing
     *
     * FUNCTION: Clears error display as user corrects their input
     * CALLED: @input event on email and password fields
     * IMPROVES: User experience - immediate feedback as they type
     *
     * HOW IT WORKS:
     * 1. User sees red error message
     * 2. User starts typing to fix their input
     * 3. @input event fires on that field
     * 4. This method runs and clears the error message
     * 5. Red error box disappears instantly
     */
    clearError() {
      // SET errorMessage to empty string
      // This hides error display because v-if="errorMessage" is false when empty
      this.errorMessage = "";
    },
  },
};
</script>
<style scoped>
/* ============ MAIN LOGIN PAGE CONTAINER ============ */
/* This is the outer wrapper that holds left side (hero) and right side (form) */
/* min-height: 100vh makes page fill entire screen height */
/* display: flex arranges child elements horizontally */
/* gap: 80px puts space between hero section and login card */
.login-page {
  min-height: 100vh;
  background: #0a0a0a;
  color: white;

  display: flex;
  justify-content: center;
  align-items: center;
  gap: 80px;

  padding: 40px;
}

/* ============ LEFT SIDE: HERO SECTION ============ */
/* Left side shows company branding, tagline, and dashboard preview */
/* Takes up 50% of screen width on desktop */
/* Stacks vertically using flex-direction: column */

.hero-section {
  width: 50%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 600px;
}

/* LOGO: Company name with icon */
/* Uses flexbox to arrange icon and text horizontally */
/* Located at top of hero section */
.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* LOGO ICON: Small green square before company name */
.logo-icon {
  width: 18px;
  height: 18px;
  background: #38ef7d;
  border-radius: 4px;
}

/* LOGO TEXT: "ModernTech Solutions" heading */
.logo h2 {
  font-size: 1.3rem;
  color: white;
}

/* HERO CONTENT: Main tagline and description */
/* Contains "People. Performance. Progress." heading and benefit text */

.hero-content h1 {
  font-size: 4rem;
  line-height: 1.1;
  margin-bottom: 20px;
}

/* GREEN ACCENT: Makes "Progress" word green to stand out */
/* Wrapped in <span> tag in template */
.hero-content span {
  color: #38ef7d;
}

/* DESCRIPTION TEXT: Explains HR system benefits in simple terms */
.hero-content p {
  max-width: 400px;
  color: #9e9e9e;
  line-height: 1.7;
}

/* ============ PREVIEW CARD: Dashboard Mockup ============ */
/* Shows fake charts and statistics to preview dashboard after login */
/* Rotated -8 degrees for visual interest */
/* Has green glow effect using box-shadow */

.preview-card {
  width: 420px;
  background: #111;
  border: 1px solid #222;
  border-radius: 25px;
  padding: 25px;
  margin-top: 50px;

  /* ROTATION: Tilts card 8 degrees left for stylish look */
  transform: rotate(-8deg);

  /* GLOW EFFECT: Creates subtle green shadow around edges */
  box-shadow: 0 0 30px rgba(56, 239, 125, 0.1);
}

.preview-card h3 {
  color: white;
  margin-bottom: 20px;
}

/* CHART AREA: Container for line chart and progress circle */
/* Arranges them side by side with space between */
.chart-area {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* LINE CHART: Fake visual chart (not real data) */
/* Shows curved line to look like attendance trend */

.line-chart {
  width: 180px;
  height: 80px;

  /* GREEN BOTTOM BORDER: Creates the chart line appearance */
  border-bottom: 2px solid #38ef7d;
  border-radius: 50%;
}

/* CIRCLE PROGRESS: Shows 98% capacity indicator */
/* Uses border styling to create circular progress ring */
.circle-progress {
  width: 120px;
  height: 120px;

  /* BORDER STYLING: Dark border with green top for progress look */
  border: 10px solid #222;
  border-top: 10px solid #38ef7d;
  border-radius: 50%;

  /* FLEXBOX: Centers percentage text inside circle */
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;

  color: white;
}

/* LARGE PERCENTAGE TEXT: The "98%" displayed in circle */
.circle-progress span {
  font-size: 2rem;
  font-weight: bold;
}

/* SMALL TEXT: "Capacity" label below percentage */
.circle-progress small {
  color: #999;
}

/* BAR CHART: Shows 6 green bars of different heights */
/* This is fake data just for visual preview */
.bar-chart {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  margin-top: 25px;
}

/* INDIVIDUAL BAR: Each bar in the bar chart */
.bar {
  width: 18px;
  background: #38ef7d;
  border-radius: 5px;
}

/* DIFFERENT HEIGHTS FOR EACH BAR: Creates visual chart pattern */
.bar:nth-child(1) {
  height: 35px;
}
.bar:nth-child(2) {
  height: 60px;
}
.bar:nth-child(3) {
  height: 45px;
}
.bar:nth-child(4) {
  height: 80px;
}
.bar:nth-child(5) {
  height: 50px;
}
.bar:nth-child(6) {
  height: 70px;
}

/* ============ RIGHT SIDE: LOGIN CARD ============ */
/* Right side contains the login form */
/* 420px wide card with dark background and green glow */

.login-card {
  width: 420px;
  background: #111111;
  border: 1px solid #222;
  border-radius: 20px;
  padding: 40px;

  /* GLOW EFFECT: Creates subtle green shadow around card */
  box-shadow: 0 0 25px rgba(56, 239, 125, 0.08);
}

/* LOGO BOX: Green box with people emoji at top of form */
.logo-box {
  width: 70px;
  height: 70px;

  /* GREEN BACKGROUND: Matches theme color */
  background: #38ef7d;
  color: black;
  border-radius: 15px;

  /* FLEXBOX: Centers emoji in box */
  display: flex;
  justify-content: center;
  align-items: center;

  font-size: 2rem;
  margin-bottom: 20px;
}

/* SUBTITLE: "Sign in to your HR portal" text under heading */
.subtitle {
  color: #9e9e9e;
  margin-bottom: 30px;
}

/* INPUT GROUP: Wrapper for label + input field */
/* Used for both email and password fields */
/* Arranges label above input vertically */
.input-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

/* INPUT LABEL: "Email Address" or "Password" text above input */
.input-group label {
  margin-bottom: 8px;
}

/* INPUT FIELD: Text input for email or password */
.input-group input {
  padding: 12px;
  background: #1a1a1a;
  border: 1px solid #333;
  color: white;
  border-radius: 10px;
}

/* OPTIONS: Container for show password checkbox */
.options {
  margin-bottom: 20px;
}

/* LOGIN BUTTON: Main submit button for form */
.login-btn {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 10px;

  /* GREEN BACKGROUND: Matches app theme */
  background: #38ef7d;
  color: black;
  font-weight: bold;
  cursor: pointer;
}

/* BUTTON HOVER: Slightly transparent when mouse over */
.login-btn:hover {
  opacity: 0.9;
}

/* ============ ERROR AND FEEDBACK STYLING ============ */

/* ERROR MESSAGE: Red alert box for validation errors or wrong credentials */
/* Shows at top of form if there's a problem */
/* Only displays when errorMessage has content (v-if directive) */
.error-message {
  background: rgba(255, 68, 68, 0.15);
  border: 1px solid #ff4444;
  color: #ff6b6b;
  padding: 12px 15px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 0.9rem;
  font-weight: 500;
  /* SLIDE IN ANIMATION: Message slides down from top when it appears */
  animation: slideIn 0.3s ease;
}

/* LOADING MESSAGE: Green alert box shown during authentication */
/* Appears while isLoading is true */
/* Tells user system is checking their credentials */
.loading-message {
  background: rgba(68, 255, 154, 0.15);
  border: 1px solid #44ff9a;
  color: #44ff9a;
  padding: 12px 15px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 0.9rem;
  font-weight: 500;
  /* SLIDE IN ANIMATION: Message slides down from top */
  animation: slideIn 0.3s ease;
}

/* INPUT ERROR STATE: Red border on invalid input */
/* Applied when :class="{ 'input-error': condition }" is true in template */
/* Shows user which field has the problem */
.input-error {
  border-color: #ff4444 !important;
  background: rgba(255, 68, 68, 0.05) !important;
}

/* INPUT HINT TEXT: Small helper text under input fields */
/* Shows when email format is wrong or password too short */
/* Conditional display using v-if in template */
.input-hint {
  font-size: 0.75rem;
  color: #ff9999;
  margin-top: 4px;
  display: block;
}

/* DEMO HINT BOX: Shows test login credentials for developers */
/* Tells users the demo email and password to use */
.demo-hint {
  background: #0a0a0a;
  border: 1px solid #222;
  border-radius: 10px;
  padding: 15px;
  margin-top: 20px;
  font-size: 0.8rem;
  color: #666;
}

.demo-hint p {
  margin: 4px 0;
}

/* CODE TEXT: Shows actual email and password values */
/* Displayed in green monospace font to stand out */
.demo-hint code {
  background: #111;
  padding: 2px 6px;
  border-radius: 4px;
  color: #44ff9a;
  font-family: monospace;
}

/* BUTTON DISABLED STATE: Button appearance when disabled */
/* Applied when :disabled="isLoading" is true */
/* Faded appearance and prevents user interaction */
.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* ============ ANIMATIONS ============ */

/* SLIDE IN ANIMATION: Error/loading messages slide down when they appear */
/* Runs for 0.3 seconds with ease timing */
/* Starts transparent and off-screen (translateY -10px) */
/* Ends visible and in final position (translateY 0) */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ============ RESPONSIVE DESIGN (MOBILE) ============ */
/* Adjusts layout for screens smaller than 900px */
@media (max-width: 900px) {
  /* STACK VERTICALLY: Changes from horizontal to vertical layout */
  .login-page {
    flex-direction: column;
    text-align: center;
  }

  /* SMALLER HEADING: Reduces heading size on mobile screens */
  .hero-section h1 {
    font-size: 3rem;
  }

  /* LOGIN CARD MOBILE: Takes full width but max 420px */
  .login-card {
    width: 100%;
    max-width: 420px;
  }
}
</style>
