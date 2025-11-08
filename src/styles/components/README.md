# Components Layer Directory

## Overview

This directory contains **component layer CSS**—reusable component classes that form your design system's building blocks.

Think of this as **your component library**—buttons, cards, typography, layouts extracted from repeated patterns.

---

## Philosophy

Components are extracted when:

1. **Pattern repeats 3+ times** - Same HTML structure used multiple times
2. **Semantic meaning** - `.btn` is clearer than `.px-4 py-2 rounded-lg bg-primary`
3. **Team velocity** - Faster to use `.card` than rebuild utilities each time

### Core Principle

**"Extract components from utilities when patterns repeat, not before."**

---

## Core Framework Files

These files are part of the framework and should remain:

### `typography.css` - Text Component Classes

Semantic text classes for headings, paragraphs, labels:

```css
.h1 { font-size: var(--text-5xl); font-weight: 900; }
.h2 { font-size: var(--text-3xl); font-weight: 700; }
.lead { font-size: var(--text-lg); line-height: 1.6; }
.body { font-size: var(--text-base); }
```

**Why keep:** Every project needs typography components.

### `buttons.css` - Button Component Classes

Button variants with states:

```css
.btn {
  display: inline-flex;
  padding: 0.75rem 1.5rem;
  border-radius: 9999px;
  transition: all 200ms;
}

.btn-primary {
  background: var(--color-primary);
  color: white;
}
```

**Why keep:** Buttons are universal across projects.

### `cards.css` - Card Layout Components

Vertical and horizontal card layouts:

```css
.v-card {
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  background: var(--color-card);
  border-radius: 1rem;
}
```

**Why keep:** Cards are common UI patterns.

### `layout.css` - Layout Utilities

Content containers and section spacing:

```css
.content {
  max-width: var(--container-3xl);
  margin: 0 auto;
  padding: 0 2rem;
}

.section {
  padding: 3rem 0;
}
```

**Why keep:** Every site needs layout utilities.

---

## Project-Specific Files

These can be removed or customized per project:

- `badges.css` - Badge components (`.badge`)
- `carousel.css` - Legacy carousel styles
- `css-carousel.css` - Pure CSS carousel
- `forms.css` - Form components
- `gradients.css` - Gradient utilities
- `navigation.css` - Header/footer navigation
- `patterns.css` - Background patterns
- `syntax-highlighting.css` - Code syntax highlighting
- `utilities.css` - Custom utility classes

**Why remove:** These are specific to certain project types.

---

## Creating New Components

### Step 1: Identify the Pattern

If you're using this HTML 3+ times:

```html
<div class="flex items-center gap-4 p-6 bg-white rounded-lg shadow">
  <img src="..." class="w-16 h-16 rounded-full">
  <div>
    <h3 class="text-lg font-bold">Title</h3>
  </div>
</div>
```

### Step 2: Extract to Component

Create `components/profile-card.css` using CSS custom properties:

```css
@layer components {
  .profile-card {
    display: flex;
    align-items: center;
    gap: var(--spacing-4);
    padding: var(--spacing-6);
    background: var(--color-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
  }

  .profile-card__avatar {
    width: var(--spacing-16);
    height: var(--spacing-16);
    border-radius: var(--radius-full);
  }

  .profile-card__title {
    font-size: var(--text-lg);
    font-weight: 700;
  }
}
```

### Step 3: Import in `tailwind.css`

```css
@import './components/profile-card.css' layer(components);
```

### Step 4: Use the Component

```html
<div class="profile-card">
  <img src="..." class="profile-card__avatar">
  <div>
    <h3 class="profile-card__title">Title</h3>
  </div>
</div>
```

---

## Component Naming

### BEM-Style Naming

```css
.component { }              /* Block */
.component__element { }     /* Element */
.component--modifier { }    /* Modifier */
```

**Example:**

```css
.card { }                   /* Base component */
.card__header { }           /* Child element */
.card__body { }             /* Child element */
.card--featured { }         /* Variant */
```

### Utility-Based Naming

```css
.btn { }
.btn-primary { }
.btn-lg { }
.btn-sm { }
```

**Choose the style that fits your team.**

---

## Best Practices

### 1. Use CSS Custom Properties (Not @apply)

**Tailwind v4 Recommended Approach:**
```css
.btn {
  padding: var(--spacing-2) var(--spacing-4);
  border-radius: var(--radius-lg);
  background: var(--color-primary);
  color: var(--color-white);
}
```

**Why not @apply?**
Tailwind CSS v4 recommends using CSS custom properties for better performance, maintainability, and explicit theming. The `@apply` directive is no longer the preferred pattern.

**Available theme variables:**
- **Colors:** `var(--color-primary)`, `var(--color-neutral-500)`, `var(--color-white)`
- **Spacing:** `var(--spacing-2)`, `var(--spacing-4)`, `var(--spacing-6)`
- **Radius:** `var(--radius-sm)`, `var(--radius-lg)`, `var(--radius-full)`
- **Shadows:** `var(--shadow)`, `var(--shadow-lg)`, `var(--shadow-xl)`
- **Typography:** `var(--text-sm)`, `var(--text-base)`, `var(--text-lg)`

See [Tailwind CSS: Adding Custom Styles](https://tailwindcss.com/docs/adding-custom-styles) for details.

### 2. Reference Theme Variables

**Good:**
```css
.card {
  background: var(--color-card);
  border: 1px solid var(--color-border);
}
```

**Bad:**
```css
.card {
  background: #ffffff;  /* Hard-coded */
  border: 1px solid #e5e7eb;
}
```

### 3. Mobile-First Responsive

```css
.grid {
  grid-template-columns: 1fr;  /* Mobile */
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);  /* Tablet */
  }
}
```

### 4. Keep Components Focused

One component per file:
```
components/
├── buttons.css        # Just buttons
├── cards.css          # Just cards
├── forms.css          # Just forms
```

### 5. Document Complex Components

```css
/**
 * Dropdown Component
 *
 * Requires Alpine.js for interactivity.
 *
 * Usage:
 * <div x-data="{ open: false }" class="dropdown">
 *   <button @click="open = !open" class="dropdown__trigger">Toggle</button>
 *   <div x-show="open" class="dropdown__menu">...</div>
 * </div>
 */
.dropdown {
  position: relative;
}
```

---

## Component Examples

### Minimal Button

```css
@layer components {
  .btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-2);
    padding: var(--spacing-2) var(--spacing-4);
    border-radius: var(--radius-full);
    font-weight: 500;
    font-size: var(--text-sm);
    transition: all 200ms;
  }

  .btn-primary {
    background: var(--color-primary);
    color: var(--color-white);
  }

  .btn-primary:hover {
    background: var(--color-primary-600);
  }
}
```

### Minimal Card

```css
@layer components {
  .card {
    padding: var(--spacing-6);
    border-radius: var(--radius-lg);
    background: var(--color-white);
    box-shadow: var(--shadow);
  }

  .card-hover {
    transition: box-shadow 200ms;
  }

  .card-hover:hover {
    box-shadow: var(--shadow-lg);
  }
}
```

---

## Customizing Theme Variables

Components should reference theme variables defined in the `@theme` directive. This ensures consistency across your design system.

### Using @theme for Custom Design Tokens

Define custom properties in your theme files:

```css
/* theme/colors.css */
@theme {
  /* Custom color creates bg-brand, text-brand utilities */
  --color-brand: oklch(0.72 0.11 221.19);

  /* Custom spacing creates p-huge, m-huge utilities */
  --spacing-huge: 10rem;

  /* Custom radius creates rounded-pill utility */
  --radius-pill: 9999px;
}
```

### Accessing Theme Variables in Components

```css
@layer components {
  .custom-button {
    padding: var(--spacing-4);
    background: var(--color-brand);
    border-radius: var(--radius-pill);
  }
}
```

### Variable Naming Conventions

| Namespace | Purpose | Example |
|-----------|---------|---------|
| `--color-*` | Colors | `var(--color-primary)` |
| `--spacing-*` | Spacing/sizing | `var(--spacing-4)` |
| `--text-*` | Font sizes | `var(--text-lg)` |
| `--radius-*` | Border radius | `var(--radius-lg)` |
| `--shadow-*` | Box shadows | `var(--shadow-xl)` |

See [Tailwind CSS: Theme](https://tailwindcss.com/docs/theme) for complete documentation.

---

## When to Extract Components

### Extract:
- Used 3+ times across the site
- Has semantic meaning (`.btn`, `.card`, `.nav`)
- Team velocity benefit

### Don't Extract:
- Used once or twice (keep as utilities)
- No clear semantic meaning
- Utilities are already clear enough

---

## Integration with Utilities

Components and utilities work together:

```html
<!-- Component provides base, utilities override -->
<button class="btn btn-primary px-8 py-4">
  Custom Size
</button>

<!-- Utility classes (higher layer) override component padding -->
```

---

**Summary:**

This directory contains **component layer CSS**—reusable classes extracted from repeated patterns. Core framework files (typography, buttons, cards, layout) should remain. Project-specific files can be customized or removed. Extract components when patterns repeat 3+ times.

**Core files to keep:**
- `typography.css`
- `buttons.css`
- `cards.css`
- `layout.css`

**Files to customize per project:**
- Everything else

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
