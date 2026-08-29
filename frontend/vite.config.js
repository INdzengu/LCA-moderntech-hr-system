// frontend/vite.config.js
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

// Configuration export that tells Vite how to process Vue Single File Components
export default defineConfig({
  plugins: [
    vue(), // Registers the Vue plugin to parse .vue files
  ],
  server: {
    port: 5173, // Default Vite development server port
  },
});
