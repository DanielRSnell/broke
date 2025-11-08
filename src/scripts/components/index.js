/**
 * Component Initializer
 *
 * This file imports and initializes all JavaScript components.
 * Add your component imports and initialization calls here.
 *
 * Pattern:
 * 1. Import component initialization functions
 * 2. Call them in the initComponents() function
 * 3. Export initComponents to be called from main.js
 */

import { initFormHandler } from './form-handler';

/**
 * Initialize all components
 * Called from main.js on DOMContentLoaded
 */
export function initComponents() {
  // Initialize form handler (example pattern)
  initFormHandler();

  // Add more component initializations here
  // Example:
  // initMobileMenu();
  // initCarousels();
  // initModals();
}
