/**
 * Component Initializers
 * Import and initialize all interactive components
 */

import { initializeButtonInteractions } from './button-interactions.js';
import { initializeContactForm } from './contact-form.js';

export function initComponents() {
  // Initialize button hover and press effects
  initializeButtonInteractions();

  // Initialize contact form
  initializeContactForm();

  console.log('✓ All components initialized');
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initComponents);
} else {
  initComponents();
}
