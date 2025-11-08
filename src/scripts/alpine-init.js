/**
 * Alpine.js Initialization
 *
 * Initializes Alpine.js with plugins and starts the framework
 */

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Register Alpine plugins
Alpine.plugin(collapse);

// Start Alpine
Alpine.start();

// Make Alpine globally available (for debugging)
window.Alpine = Alpine;

console.log('Alpine.js initialized successfully!');
