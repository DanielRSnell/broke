/**
 * Main JavaScript Entry Point
 *
 * This file is the entry point for all JavaScript in the theme.
 * Import and initialize your components here.
 */

import { initComponents } from './components/index';

// Initialize all components on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  initComponents();
  console.log('Theme components initialized successfully!');
});
