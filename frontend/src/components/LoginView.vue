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
      <div class="preview-card">
        <h3>Overview</h3>

        <div class="chart-area">
          <div class="line-chart"></div>

          <div class="circle-progress">
            <span>98%</span>
            <small>Capacity</small>
          </div>
        </div>

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
      <div class="logo-box">👥</div>

      <h2>Welcome Back</h2>

      <p class="subtitle">Sign in to your HR portal</p>

      <!-- ERROR MESSAGE -->
      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>

      <!-- LOADING MESSAGE -->
      <div v-if="isLoading" class="loading-message">⏳ Authenticating...</div>

      <!-- LOGIN FORM -->
      <form @submit.prevent="login">
        <!-- EMAIL INPUT GROUP -->
        <div class="input-group">
          <label for="email">Email Address</label>
          <input
            id="email"
            type="email"
            v-model="email"
            placeholder="admin@moderntech.com"
            @input="clearError"
            :class="{ 'input-error': errorMessage && !isValidEmail(email) }"
            :disabled="isLoading"
          />
          <span v-if="email && !isValidEmail(email)" class="input-hint">
            ℹ️ Invalid email format
          </span>
        </div>

        <!-- PASSWORD INPUT GROUP -->
        <div class="input-group">
          <label for="password">Password</label>
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
          <span v-if="password && password.length < 6" class="input-hint">
            ℹ️ Minimum 6 characters required
          </span>
        </div>

        <!-- SHOW PASSWORD CHECKBOX -->
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

        <!-- LOGIN BUTTON -->
        <button class="login-btn" :disabled="isLoading" type="submit">
          {{ isLoading ? "Signing In..." : "Sign In" }}
        </button>
      </form>

      <!-- DEMO CREDENTIALS -->
      <div class="demo-hint">
        <p><strong>Credentials example:</strong></p>
        <p>Email: <code>l.park@moderntech.com</code></p>
        <p>Password: <code>ParkAdmin#2026</code></p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "LoginView",

  data() {
    return {
      email: "",
      password: "",
      showPassword: false,
      errorMessage: "",
      isLoading: false,
    };
  },

  methods: {
    async login() {
      this.errorMessage = "";

      // VALIDATION 1: Check required fields
      if (this.email === "" || this.password === "") {
        this.errorMessage = "❌ Please complete all fields";
        return;
      }

      // VALIDATION 2: Email format check
      if (!this.isValidEmail(this.email)) {
        this.errorMessage =
          "❌ Please enter a valid email address (format: example@domain.com)";
        return;
      }

      // VALIDATION 3: Minimum password length
      if (this.password.length < 6) {
        this.errorMessage = "❌ Password must be at least 6 characters";
        return;
      }

      this.isLoading = true;

      try {
        const response = await axios.post(
          "http://localhost/lca-php/moderntech-hr-system/backend/routes/login.php",
          {
            email: this.email,
            password: this.password,
          },
          {
            withCredentials: true, // Crucial: Sends and receives PHP session cookies across origins
          },
        );

        if (response.data && response.data.status === "success") {
          // Store user info locally for UI convenience
          localStorage.setItem("isLoggedIn", "true");
          localStorage.setItem(
            "moderntech_session_user",
            JSON.stringify(response.data.user),
          );

          // Emit event for App.vue
          this.$emit("login-success", response.data.user);

          // Redirect to Dashboard
          this.$router.push("/dashboard");
        }
      } catch (error) {
        if (
          error.response &&
          error.response.data &&
          error.response.data.message
        ) {
          this.errorMessage = "❌ " + error.response.data.message;
        } else {
          this.errorMessage =
            "❌ Unable to connect to backend server. Check XAMPP/Apache status.";
        }
      } finally {
        this.isLoading = false;
      }
    },

    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },

    clearError() {
      this.errorMessage = "";
    },
  },
};
</script>

<style scoped>
/* ============ MAIN LOGIN PAGE CONTAINER ============ */
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
.hero-section {
  width: 50%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 600px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 18px;
  height: 18px;
  background: #38ef7d;
  border-radius: 4px;
}

.logo h2 {
  font-size: 1.3rem;
  color: white;
}

.hero-content h1 {
  font-size: 4rem;
  line-height: 1.1;
  margin-bottom: 20px;
}

.hero-content span {
  color: #38ef7d;
}

.hero-content p {
  max-width: 400px;
  color: #9e9e9e;
  line-height: 1.7;
}

/* ============ PREVIEW CARD ============ */
.preview-card {
  width: 420px;
  background: #111;
  border: 1px solid #222;
  border-radius: 25px;
  padding: 25px;
  margin-top: 50px;
  transform: rotate(-8deg);
  box-shadow: 0 0 30px rgba(56, 239, 125, 0.1);
}

.preview-card h3 {
  color: white;
  margin-bottom: 20px;
}

.chart-area {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.line-chart {
  width: 180px;
  height: 80px;
  border-bottom: 2px solid #38ef7d;
  border-radius: 50%;
}

.circle-progress {
  width: 120px;
  height: 120px;
  border: 10px solid #222;
  border-top: 10px solid #38ef7d;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
}

.circle-progress span {
  font-size: 2rem;
  font-weight: bold;
}

.circle-progress small {
  color: #999;
}

.bar-chart {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  margin-top: 25px;
}

.bar {
  width: 18px;
  background: #38ef7d;
  border-radius: 5px;
}

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
.login-card {
  width: 420px;
  background: #111111;
  border: 1px solid #222;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 0 25px rgba(56, 239, 125, 0.08);
}

.logo-box {
  width: 70px;
  height: 70px;
  background: #38ef7d;
  color: black;
  border-radius: 15px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 2rem;
  margin-bottom: 20px;
}

.subtitle {
  color: #9e9e9e;
  margin-bottom: 30px;
}

.input-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

.input-group label {
  margin-bottom: 8px;
}

.input-group input {
  padding: 12px;
  background: #1a1a1a;
  border: 1px solid #333;
  color: white;
  border-radius: 10px;
}

.options {
  margin-bottom: 20px;
}

.login-btn {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 10px;
  background: #38ef7d;
  color: black;
  font-weight: bold;
  cursor: pointer;
}

.login-btn:hover {
  opacity: 0.9;
}

/* ============ ERROR AND FEEDBACK STYLING ============ */
.error-message {
  background: rgba(255, 68, 68, 0.15);
  border: 1px solid #ff4444;
  color: #ff6b6b;
  padding: 12px 15px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 0.9rem;
  font-weight: 500;
  animation: slideIn 0.3s ease;
}

.loading-message {
  background: rgba(68, 255, 154, 0.15);
  border: 1px solid #44ff9a;
  color: #44ff9a;
  padding: 12px 15px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 0.9rem;
  font-weight: 500;
  animation: slideIn 0.3s ease;
}

.input-error {
  border-color: #ff4444 !important;
  background: rgba(255, 68, 68, 0.05) !important;
}

.input-hint {
  font-size: 0.75rem;
  color: #ff9999;
  margin-top: 4px;
  display: block;
}

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

.demo-hint code {
  background: #111;
  padding: 2px 6px;
  border-radius: 4px;
  color: #44ff9a;
  font-family: monospace;
}

.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* ============ ANIMATIONS ============ */
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

/* ============ RESPONSIVE DESIGN ============ */
@media (max-width: 900px) {
  .login-page {
    flex-direction: column;
    text-align: center;
  }

  .hero-section h1 {
    font-size: 3rem;
  }

  .login-card {
    width: 100%;
    max-width: 420px;
  }
}
</style>
