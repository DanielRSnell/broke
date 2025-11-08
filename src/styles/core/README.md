# Theme Layer Directory

## Overview

This directory contains **theme layer CSS**—CSS custom properties (design tokens), global resets, and core styles that form the foundation of your design system.

Think of this as **your design system's foundation**—colors, spacing, and global styles that everything else references.

---

## Philosophy

The theme layer provides:

1. **CSS Custom Properties** - Design tokens for colors, spacing, typography
2. **Global Resets** - Minimal, non-intrusive base styles
3. **Design Token References** - All values point to WordPress `theme.json`
4. **Single Source of Truth** - Colors defined once, used everywhere

### Core Principle

**"Define design tokens once in WordPress `theme.json`, reference them via CSS variables everywhere."**

---

## Files

### `colors.css` - Color System

**Purpose:** Maps WordPress `theme.json` colors to CSS custom properties.

```css
@theme {
  /* Brand Colors */
  --color-primary: var(--wp--preset--color--primary);
  --color-accent: var(--wp--preset--color--accent);

  /* Neutral Scale */
  --color-primary-50: var(--wp--preset--color--primary-50);
  --color-primary-100: var(--wp--preset--color--primary-100);
  /* ... through 950 */

  /* Semantic Aliases */
  --color-surface: var(--wp--preset--color--surface);
  --color-card: var(--wp--preset--color--card);
  --color-border: var(--wp--preset--color--border);
}
```

**Why:** Single source of truth. Update colors in `theme.json`, they propagate everywhere.

### `core.css` - Global Resets

**Purpose:** Minimal global styles and resets.

```css
body {
  background: var(--color-surface);
}

a {
  text-decoration: none !important;
}

/* Reset lists outside content areas */
ul:where(:not(.prose *, .wp-block-* *)) {
  list-style: none;
  margin: 0;
  padding: 0;
}
```

**Why:** Provides sane defaults without being opinionated.

### `editor.css` - Block Editor Styles

**Purpose:** Block editor-specific styles to match frontend.

```css
.editor-styles-wrapper {
  background-color: var(--wp--preset--color--surface);
  padding: 2rem;
}
```

**Why:** Ensures WordPress block editor looks like your frontend.

---

## Adding Colors

### Step 1: Add to `theme.json`

```json
{
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "my-color",
          "color": "#ff6b6b",
          "name": "My Color"
        }
      ]
    }
  }
}
```

### Step 2: Reference in `colors.css`

```css
@theme {
  --color-my-color: var(--wp--preset--color--my-color);
}
```

### Step 3: Use in Components

```css
.my-component {
  background: var(--color-my-color);
}
```

---

## Best Practices

### 1. Never Hard-Code Colors

**Good:**
```css
.component {
  color: var(--color-text);
  background: var(--color-surface);
}
```

**Bad:**
```css
.component {
  color: #333333;  /* Hard-coded */
  background: #ffffff;
}
```

### 2. Use Semantic Aliases

**Good:**
```css
--color-surface: var(--wp--preset--color--primary-50);
--color-card: var(--wp--preset--color--white);
```

Then use:
```css
.component {
  background: var(--color-surface);  /* Semantic */
}
```

### 3. Keep core.css Minimal

Only add truly global styles:
- Body background
- Link resets
- List resets

Component-specific styles belong in `components/`.

---

## Integration with WordPress

WordPress `theme.json` generates CSS variables:

```
--wp--preset--color--primary
--wp--preset--color--accent
--wp--preset--spacing--40
--wp--preset--font-size--medium
```

This theme layer maps those to cleaner names:

```css
--color-primary
--color-accent
--spacing-40
--font-size-medium
```

---

**Summary:**

This directory provides the **foundation layer** of your design system. Colors reference WordPress `theme.json`, global resets are minimal, editor styles match frontend. Always define design tokens here, never hard-code values in components.

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
