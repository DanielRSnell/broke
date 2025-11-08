# Theme Variants Directory

## Overview

This directory contains **theme variant stylesheets**—alternative color schemes and design variations like dark mode.

Think of this as **your theming system**—different visual treatments that can be toggled via CSS classes.

---

## Philosophy

Theme variants provide:

1. **Alternative color schemes** - Dark mode, high contrast, brand variants
2. **Class-based activation** - Toggle via `.theme-name` class
3. **CSS variable overrides** - Redefine design tokens for different themes
4. **Component-level overrides** - Adjust specific components for themes

### Core Principle

**"Themes override CSS variables and component styles, activated via class on parent element."**

---

## Files

### `night.css` - Dark Mode Theme

**Purpose:** Dark mode color scheme activated via `.theme-night` class.

```css
.theme-night {
  /* Override color variables */
  --color-surface: var(--color-primary-900);
  --color-card: var(--color-primary-950);
  --color-border: var(--color-primary-800);

  /* Adjust text colors for dark background */
  --color-primary-500: var(--color-primary-400);
  --color-primary-600: var(--color-primary-300);
}

/* Component-specific overrides */
.theme-night .btn {
  background-color: var(--color-primary-800);
}

.theme-night .h1,
.theme-night .h2 {
  color: var(--color-white);
}
```

**Usage:**
```html
<!-- Dark entire page -->
<body class="theme-night">

<!-- Dark single section -->
<section class="section theme-night">
  <!-- Content -->
</section>
```

---

## Creating Theme Variants

### Step 1: Create Theme File

```bash
touch src/styles/themes/my-theme.css
```

### Step 2: Define Theme Overrides

```css
/* themes/my-theme.css */
@layer components {
  .theme-my-theme {
    /* Override color variables */
    --color-primary: #your-color;
    --color-surface: #your-background;
    --color-text: #your-text-color;

    /* Override component styles */
  }

  .theme-my-theme .btn-primary {
    background: var(--color-primary);
  }

  .theme-my-theme .h1 {
    color: var(--color-text);
  }
}
```

### Step 3: Import in `tailwind.css`

```css
/* tailwind.css */
@import './themes/my-theme.css' layer(components);
```

### Step 4: Apply Theme

```html
<body class="theme-my-theme">
  <!-- Content uses theme colors -->
</body>
```

---

## Common Theme Types

### Dark Mode

**Purpose:** Light text on dark backgrounds

```css
.theme-dark {
  --color-surface: #0f172a;
  --color-card: #1e293b;
  --color-text: #f1f5f9;
  --color-border: #334155;
}
```

### High Contrast

**Purpose:** Accessibility, WCAG AAA compliance

```css
.theme-high-contrast {
  --color-text: #000000;
  --color-surface: #ffffff;
  --color-border: #000000;
  /* Stronger color contrasts */
}
```

### Brand Variants

**Purpose:** Different brand colors for sub-brands

```css
.theme-brand-b {
  --color-primary: #your-alt-brand-color;
  --color-accent: #your-alt-accent;
}
```

### Seasonal Themes

**Purpose:** Holiday or seasonal variations

```css
.theme-holiday {
  --color-primary: #c41e3a;  /* Red */
  --color-accent: #165b33;   /* Green */
}
```

---

## Best Practices

### 1. Override Variables, Not Hardcode

**Good:**
```css
.theme-night {
  --color-surface: var(--color-primary-900);
}

.theme-night .card {
  background: var(--color-surface);  /* Uses override */
}
```

**Bad:**
```css
.theme-night .card {
  background: #1e293b;  /* Hard-coded */
}
```

### 2. Test Color Contrast

Ensure text remains readable:

```css
.theme-dark {
  --color-surface: #0f172a;
  --color-text: #f1f5f9;  /* Sufficient contrast */
}
```

Use tools like [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/).

### 3. Override at Root Level

Define variable overrides on theme class:

```css
.theme-name {
  /* Variables here */
  --color-primary: ...;
}
```

Then component styles automatically adapt:

```css
.btn {
  background: var(--color-primary);  /* Uses theme override */
}
```

### 4. Component-Specific Overrides

Only override components when necessary:

```css
.theme-night .btn {
  /* Only if button needs special treatment in dark mode */
  box-shadow: 0 0 0 1px rgba(255,255,255,0.1);
}
```

### 5. Keep Themes in Sync

When adding new components, test in all themes:

```css
/* New component */
.new-component {
  background: var(--color-card);  /* Uses theme variable */
}

/* Test in dark mode */
.theme-night .new-component {
  /* Add override if needed */
}
```

---

## Theme Switching with JavaScript

### Static Theme

Apply theme class directly in HTML:

```html
<body class="theme-night">
```

### Dynamic Theme Toggle

Toggle theme with JavaScript:

```html
<button onclick="document.body.classList.toggle('theme-night')">
  Toggle Dark Mode
</button>
```

### Save User Preference

```javascript
// Load saved theme
const theme = localStorage.getItem('theme');
if (theme) {
  document.body.classList.add(theme);
}

// Save theme on toggle
function toggleTheme() {
  document.body.classList.toggle('theme-night');

  const isDark = document.body.classList.contains('theme-night');
  localStorage.setItem('theme', isDark ? 'theme-night' : '');
}
```

### Alpine.js Theme Switcher

```html
<div x-data="{ dark: false }" x-init="dark = localStorage.getItem('theme') === 'dark'">
  <button
    @click="dark = !dark; localStorage.setItem('theme', dark ? 'dark' : 'light')"
    :class="dark && 'theme-night'">
    Toggle Dark Mode
  </button>
</div>
```

---

## System Preference Detection

Respect user's OS dark mode preference:

```css
/* Auto dark mode based on system preference */
@media (prefers-color-scheme: dark) {
  :root {
    --color-surface: var(--color-primary-900);
    --color-card: var(--color-primary-950);
    /* ... dark mode colors */
  }
}
```

Or with JavaScript:

```javascript
// Detect system preference
if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
  document.body.classList.add('theme-night');
}
```

---

## Removing Project-Specific Themes

For a clean boilerplate:

**Keep:**
- `night.css` - Universal dark mode (most projects need it)

**Remove:**
- Any custom brand themes
- Seasonal themes
- Project-specific color overrides

---

**Summary:**

This directory contains **theme variant stylesheets** that override CSS variables and component styles. Activated via class on parent element (e.g., `.theme-night`). Dark mode (`night.css`) is the core framework file—keep it. Remove project-specific themes for boilerplate.

**Core file to keep:**
- `night.css` (dark mode)

**Files to remove for boilerplate:**
- Custom brand themes
- Project-specific variants

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
