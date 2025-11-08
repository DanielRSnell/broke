# Scripts Directory

## Overview

This directory contains **JavaScript source files** that are bundled and compiled using Vite for both frontend and block editor use.

Think of this as **your JavaScript layer**—Alpine.js components, GSAP animations, and custom interactions.

---

## Philosophy

### The Problem This Solves

Traditional WordPress JavaScript development often involves:

- **jQuery dependency** - Heavy, outdated library
- **Global scope pollution** - No module system
- **Manual concatenation** - Build process headaches
- **No modern syntax** - Can't use ES6+ features
- **Separate editor/frontend** - Duplicate code or complexity

### The Solution

This directory provides **modern JavaScript development** with:

1. **Module system** - ES6 imports/exports
2. **Modern frameworks** - Alpine.js, GSAP built-in
3. **Vite bundling** - Fast builds, hot reload
4. **Single codebase** - Same code for frontend and editor
5. **Component organization** - Modular, reusable code

**Benefits:**
- Modern JavaScript (ES6+)
- Tree-shaking (only bundle what's used)
- Fast development with HMR
- Component-based architecture
- No jQuery dependency

### Core Principle

**"Write modern, modular JavaScript once—bundle for both frontend and block editor."**

---

## How It Works

### Build Process

```bash
# Development mode (watch)
npm run dev

# Production build
npm run build:js
```

**What happens:**
1. Vite reads `src/scripts/main.js` entry point
2. Bundles all imports (Alpine, GSAP, components)
3. Transpiles modern JavaScript for browser compatibility
4. Outputs to `_production/scripts.js`
5. WordPress enqueues the bundle

### File Flow

```
src/scripts/main.js
  ↓ (imports components)
src/scripts/components/*.js
  ↓ (npm run build:js)
_production/scripts.js
  ↓ (WordPress enqueues)
Frontend + Block Editor
```

---

## Directory Structure

### Typical Organization

```
src/scripts/
├── README.md               # This file
├── main.js                 # Entry point (imports everything)
├── components/             # Reusable JS components
│   ├── mobile-menu.js      # Mobile navigation
│   ├── scroll-animations.js # GSAP scroll effects
│   ├── form-validation.js  # Form handling
│   └── carousel.js         # Carousel functionality
└── highlight-config.js     # Syntax highlighting config
```

**Naming convention:** Use descriptive names that indicate component purpose.

---

## Entry Point: `main.js`

```javascript
// src/scripts/main.js

// Import Alpine.js and plugins
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';

// Register Alpine plugins
Alpine.plugin(collapse);
Alpine.plugin(intersect);

// Import GSAP
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

// Import custom components
import './components/mobile-menu.js';
import './components/scroll-animations.js';
import './components/form-validation.js';

// Import syntax highlighting (if needed)
import './highlight-config.js';

// Start Alpine
window.Alpine = Alpine;
Alpine.start();

// Export for global access (if needed)
window.gsap = gsap;
```

**Role:** The entry point imports all dependencies and components, then initializes them.

---

## Component Examples

### 1. Alpine.js Mobile Menu

```javascript
// src/scripts/components/mobile-menu.js

import Alpine from 'alpinejs';

// Mobile menu store
Alpine.store('mobile_menu', {
    open: false,

    toggle() {
        this.open = !this.open;

        // Prevent body scroll when menu is open
        if (this.open) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    },

    close() {
        this.open = false;
        document.body.style.overflow = '';
    }
});
```

**HTML usage:**
```html
<!-- Mobile menu button -->
<button
    @click="$store.mobile_menu.toggle()"
    class="md:hidden">
    Menu
</button>

<!-- Mobile menu overlay -->
<div
    x-show="$store.mobile_menu.open"
    @click.away="$store.mobile_menu.close()"
    x-transition
    class="fixed inset-0 bg-black/50 z-50">
    <nav class="bg-white h-full w-3/4 p-8">
        <!-- Menu items -->
    </nav>
</div>
```

### 2. GSAP Scroll Animations

```javascript
// src/scripts/components/scroll-animations.js

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Fade in elements on scroll
document.addEventListener('DOMContentLoaded', () => {
    // Find all elements with fade-in class
    const fadeElements = document.querySelectorAll('.fade-in-hidden');

    fadeElements.forEach((element) => {
        gsap.from(element, {
            scrollTrigger: {
                trigger: element,
                start: 'top 80%',
                end: 'top 20%',
                toggleActions: 'play none none reverse'
            },
            opacity: 0,
            y: 50,
            duration: 1,
            ease: 'power2.out'
        });
    });
});
```

**HTML usage:**
```html
<section class="fade-in-hidden">
    <!-- Content fades in on scroll -->
</section>
```

### 3. Form Validation (Alpine.js)

```javascript
// src/scripts/components/form-validation.js

import Alpine from 'alpinejs';

// Form validation component
Alpine.data('contactForm', () => ({
    loading: false,
    success: false,
    error: null,

    formData: {
        name: '',
        email: '',
        message: ''
    },

    async submit() {
        this.loading = true;
        this.error = null;

        try {
            const response = await fetch('/wp-json/contact/v1/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.formData)
            });

            if (!response.ok) {
                throw new Error('Submission failed');
            }

            this.success = true;
            this.formData = { name: '', email: '', message: '' };
        } catch (err) {
            this.error = 'Something went wrong. Please try again.';
        } finally {
            this.loading = false;
        }
    }
}));
```

**HTML usage:**
```html
<form x-data="contactForm" @submit.prevent="submit">
    <input
        type="text"
        x-model="formData.name"
        placeholder="Name"
        required>

    <input
        type="email"
        x-model="formData.email"
        placeholder="Email"
        required>

    <textarea
        x-model="formData.message"
        placeholder="Message"
        required></textarea>

    <button
        type="submit"
        :disabled="loading"
        class="btn btn-primary">
        <span x-show="!loading">Send</span>
        <span x-show="loading">Sending...</span>
    </button>

    <p x-show="success" class="text-green-600">Thank you! We'll be in touch.</p>
    <p x-show="error" class="text-red-600" x-text="error"></p>
</form>
```

### 4. Carousel Component (Alpine.js)

```javascript
// src/scripts/components/carousel.js

import Alpine from 'alpinejs';

Alpine.data('carousel', (autoplay = true, interval = 5000) => ({
    current: 0,
    total: 0,
    timer: null,

    init() {
        this.total = this.$refs.track.children.length;

        if (autoplay) {
            this.startAutoplay();
        }
    },

    next() {
        this.current = (this.current + 1) % this.total;
        this.resetAutoplay();
    },

    prev() {
        this.current = (this.current - 1 + this.total) % this.total;
        this.resetAutoplay();
    },

    goto(index) {
        this.current = index;
        this.resetAutoplay();
    },

    startAutoplay() {
        this.timer = setInterval(() => {
            this.next();
        }, interval);
    },

    resetAutoplay() {
        if (this.timer) {
            clearInterval(this.timer);
            this.startAutoplay();
        }
    }
}));
```

**HTML usage:**
```html
<div x-data="carousel(true, 5000)" class="relative">
    <!-- Slides -->
    <div x-ref="track" class="relative overflow-hidden">
        <div
            x-show="current === 0"
            x-transition
            class="slide">
            Slide 1
        </div>
        <div
            x-show="current === 1"
            x-transition
            class="slide">
            Slide 2
        </div>
    </div>

    <!-- Controls -->
    <button @click="prev">Previous</button>
    <button @click="next">Next</button>

    <!-- Indicators -->
    <div class="flex gap-2">
        <button
            @click="goto(0)"
            :class="current === 0 ? 'active' : ''">
        </button>
        <button
            @click="goto(1)"
            :class="current === 1 ? 'active' : ''">
        </button>
    </div>
</div>
```

---

## Structure by Project Type

### Marketing / Landing Page Site

**Focus:** Interactive UI, conversion optimization

```
src/scripts/
├── main.js
├── components/
│   ├── mobile-menu.js
│   ├── scroll-animations.js
│   ├── form-validation.js
│   ├── video-player.js
│   └── pricing-calculator.js
```

### Blog / Publication Site

**Focus:** Reading experience, syntax highlighting

```
src/scripts/
├── main.js
├── highlight-config.js       # Code syntax highlighting
├── components/
│   ├── mobile-menu.js
│   ├── table-of-contents.js  # Auto-generated TOC
│   ├── reading-progress.js   # Progress bar
│   └── social-share.js       # Share buttons
```

### E-commerce Site

**Focus:** Product interactions, cart management

```
src/scripts/
├── main.js
├── components/
│   ├── product-gallery.js    # Product image zoom/slider
│   ├── add-to-cart.js        # Cart functionality
│   ├── filters.js            # Product filtering
│   └── quantity-selector.js  # Quantity input
```

### Portfolio / Agency Site

**Focus:** Visual effects, project showcases

```
src/scripts/
├── main.js
├── components/
│   ├── portfolio-filter.js   # Filter projects by category
│   ├── lightbox.js           # Image lightbox
│   ├── parallax.js           # Parallax scrolling
│   └── case-study-nav.js     # Case study navigation
```

---

## Best Practices

### 1. Use ES6 Modules

**Good:**
```javascript
// src/scripts/components/utils.js
export function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// src/scripts/components/search.js
import { debounce } from './utils.js';

const handleSearch = debounce((query) => {
    // Search logic
}, 300);
```

**Bad:**
```javascript
// Global functions
window.debounce = function(func, wait) { ... };
window.handleSearch = function(query) { ... };
```

### 2. Lazy Load Heavy Dependencies

```javascript
// main.js - Don't import GSAP if not needed on every page
document.addEventListener('DOMContentLoaded', () => {
    // Only load on pages that need it
    if (document.querySelector('.has-animations')) {
        import('./components/scroll-animations.js');
    }

    if (document.querySelector('.carousel')) {
        import('./components/carousel.js');
    }
});
```

### 3. Avoid Inline Scripts

**Good:**
```javascript
// src/scripts/components/feature.js
Alpine.data('feature', () => ({
    // Component logic
}));
```

```html
<!-- HTML -->
<div x-data="feature">...</div>
```

**Bad:**
```html
<!-- Inline Alpine component -->
<div x-data="{ open: false, toggle() { this.open = !this.open } }">
    ...
</div>
```

### 4. Handle Errors Gracefully

```javascript
// src/scripts/components/api-client.js
export async function fetchData(endpoint) {
    try {
        const response = await fetch(endpoint);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        // Show user-friendly error message
        return { error: 'Failed to load data. Please try again.' };
    }
}
```

### 5. Use Alpine Stores for Global State

```javascript
// src/scripts/components/cart-store.js
import Alpine from 'alpinejs';

Alpine.store('cart', {
    items: [],
    total: 0,

    add(product) {
        this.items.push(product);
        this.calculateTotal();
    },

    remove(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.calculateTotal();
    },

    calculateTotal() {
        this.total = this.items.reduce((sum, item) => sum + item.price, 0);
    }
});
```

---

## Advanced Patterns

### Intersection Observer (Lazy Loading)

```javascript
// src/scripts/components/lazy-load.js

document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
});
```

**HTML:**
```html
<img data-src="/path/to/image.jpg" src="/path/to/placeholder.jpg" alt="...">
```

### GSAP Timeline Animations

```javascript
// src/scripts/components/hero-animation.js

import { gsap } from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    const tl = gsap.timeline();

    tl.from('.hero-title', {
        opacity: 0,
        y: -50,
        duration: 1,
        ease: 'power2.out'
    })
    .from('.hero-subtitle', {
        opacity: 0,
        y: -30,
        duration: 0.8,
        ease: 'power2.out'
    }, '-=0.5')
    .from('.hero-cta', {
        opacity: 0,
        scale: 0.8,
        duration: 0.6,
        ease: 'back.out'
    }, '-=0.4');
});
```

### WebSocket Integration

```javascript
// src/scripts/components/realtime-updates.js

class RealtimeUpdates {
    constructor(url) {
        this.ws = new WebSocket(url);
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.ws.onopen = () => {
            console.log('WebSocket connected');
        };

        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleUpdate(data);
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };
    }

    handleUpdate(data) {
        // Dispatch custom event for Alpine to handle
        window.dispatchEvent(new CustomEvent('realtime-update', {
            detail: data
        }));
    }
}

// Initialize if needed
if (document.querySelector('[data-realtime]')) {
    new RealtimeUpdates('wss://your-websocket-url');
}
```

---

## Integration with Theme Workflow

### With HTML Templates

```html
<!-- Alpine.js component -->
<div x-data="contactForm" @submit.prevent="submit">
    <input x-model="formData.email" type="email">
    <button :disabled="loading">Submit</button>
</div>

<!-- GSAP animation trigger -->
<section class="fade-in-hidden">
    Content animates on scroll
</section>
```

### With Context Filters

```javascript
// Access WordPress data in JavaScript
Alpine.data('postsList', () => ({
    posts: window.wpData.recent_posts || [],

    init() {
        console.log('Loaded posts:', this.posts);
    }
}));
```

```php
// Enqueue data from context
wp_localize_script('main-js', 'wpData', [
    'recent_posts' => $context['recent_posts'],
    'site_url' => home_url()
]);
```

---

## Troubleshooting

### Scripts Not Loading

1. Rebuild: `npm run build:js`
2. Check `_production/scripts.js` exists
3. Verify WordPress enqueues correctly
4. Check browser console for errors

### Alpine Not Working

1. Check `window.Alpine` is available
2. Verify `Alpine.start()` is called
3. Use `x-data` on parent element
4. Check browser console for Alpine errors

### GSAP Animations Not Running

1. Verify GSAP is imported
2. Check ScrollTrigger is registered
3. Ensure elements exist in DOM
4. Use `gsap.registerPlugin(ScrollTrigger)`

---

## Commands Reference

```bash
# Development mode (watch files)
npm run dev

# Production build
npm run build:js

# Build both CSS and JS
npm run build

# Format code
npm run format
```

---

## Related Documentation

- **[Alpine.js Docs](https://alpinejs.dev/)** - Alpine.js framework
- **[GSAP Docs](https://gsap.com/docs/v3/)** - GSAP animation library
- **[Vite Docs](https://vite.dev/)** - Vite bundler
- **[src/styles/README.md](../styles/README.md)** - CSS architecture
- **[CLAUDE.md](../../CLAUDE.md)** - Theme architecture

---

**Summary:**

This directory contains **JavaScript source files** bundled with Vite for modern, modular development. Alpine.js for reactive components, GSAP for animations, component-based architecture. Write once, bundle for frontend and block editor.

The structure you choose depends on your project's interactivity needs, but the principle remains the same: **modern, modular JavaScript with component organization.**

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
