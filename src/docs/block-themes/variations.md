# Block Style Variations

A complete guide to creating custom style variations for WordPress blocks - providing design options while maintaining consistency.

---

## Table of Contents

- [What Are Style Variations?](#what-are-style-variations)
- [Why Use Them?](#why-use-them)
- [When to Use Variations](#when-to-use-variations)
- [How They Work](#how-they-work)
- [Creating Style Variations](#creating-style-variations)
- [CSS Implementation](#css-implementation)
- [Best Practices](#best-practices)
- [Advanced Techniques](#advanced-techniques)
- [Troubleshooting](#troubleshooting)

---

## What Are Style Variations?

Style variations are **pre-defined design options** for existing WordPress blocks. They appear in the block inspector as selectable styles that users can apply with one click.

### Visual Example

```
Block Inspector Sidebar:
┌─────────────────────┐
│ Styles              │
│ ○ Default          │
│ ● Card Style       │ ← User selects this
│ ○ Hero Section     │
│ ○ Glass Effect     │
└─────────────────────┘
```

When selected, WordPress adds a class like `is-style-card` to the block, triggering your custom CSS.

---

## Why Use Them?

### Benefits

✅ **User-Friendly** - Simple dropdown, no custom CSS needed
✅ **Design Consistency** - Pre-approved styles maintain brand
✅ **No Code Required** - Users don't touch CSS
✅ **Flexible** - Multiple options per block type
✅ **Performance** - Pure CSS, no JavaScript overhead
✅ **Maintainable** - Update all instances by changing one CSS rule

### Real-World Example

Instead of users writing custom CSS for each button:

```css
/* User has to know CSS */
.my-custom-button {
  background: linear-gradient(...);
  border-radius: 50px;
}
```

They select "Gradient Button" from a dropdown - done! ✨

---

## When to Use Variations

### ✅ Use Variations When:

- **Providing design options** - Multiple looks for same block type
- **Maintaining brand consistency** - Approved design system styles
- **Simplifying user experience** - Non-technical users need options
- **Creating design patterns** - Reusable components (cards, sections, CTAs)

### ❌ Don't Use Variations When:

- **One-off custom styling** - Use block settings or custom CSS instead
- **Complex interactivity** - Use block extensions or custom blocks
- **Content-dependent** - Use conditional logic or templates
- **Requires JavaScript** - Create a block extension instead

---

## How They Work

### The Flow

```
1. Register variation (PHP or theme.json)
   ↓
2. WordPress adds it to block inspector UI
   ↓
3. User selects style from dropdown
   ↓
4. WordPress adds class: is-style-{name}
   ↓
5. Your CSS targets that class
   ↓
6. Block displays with custom styling
```

### Example Output

**User selects "Card" style on Group block:**

```html
<!-- WordPress automatically adds: -->
<div class="wp-block-group is-style-card">
  <!-- Block content -->
</div>
```

**Your CSS activates:**

```css
.wp-block-group.is-style-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
```

---

## Creating Style Variations

### Step 1: Register the Variation

**In `functions.php` or a dedicated file:**

```php
<?php
/**
 * Register block style variations
 */
add_action('init', 'mytheme_register_block_styles');
function mytheme_register_block_styles() {

    // Card style for Group blocks
    register_block_style('core/group', array(
        'name'  => 'card',
        'label' => __('Card', 'textdomain'),
    ));

    // Hero style for Group blocks
    register_block_style('core/group', array(
        'name'  => 'hero',
        'label' => __('Hero Section', 'textdomain'),
    ));

    // Gradient button
    register_block_style('core/button', array(
        'name'  => 'gradient',
        'label' => __('Gradient Button', 'textdomain'),
    ));

    // Rounded image
    register_block_style('core/image', array(
        'name'  => 'rounded',
        'label' => __('Rounded', 'textdomain'),
    ));
}
```

### Step 2: Create the CSS

**In your theme's CSS file:**

```css
/* Group - Card Style */
.wp-block-group.is-style-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Group - Hero Style */
.wp-block-group.is-style-hero {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-align: center;
}

/* Button - Gradient */
.wp-block-button.is-style-gradient .wp-block-button__link {
  background: linear-gradient(45deg, #667eea, #764ba2);
  border: none;
  border-radius: 50px;
  padding: 1rem 2rem;
}

/* Image - Rounded */
.wp-block-image.is-style-rounded img {
  border-radius: 50%;
}
```

### Step 3: Enqueue Your CSS

**In `functions.php`:**

```php
<?php
/**
 * Enqueue block styles
 */
add_action('wp_enqueue_scripts', 'mytheme_enqueue_block_styles');
add_action('enqueue_block_editor_assets', 'mytheme_enqueue_block_styles');

function mytheme_enqueue_block_styles() {
    wp_enqueue_style(
        'mytheme-block-styles',
        get_template_directory_uri() . '/assets/css/block-styles.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
```

**Note:** Both hooks ensure styles load in editor AND frontend.

---

## CSS Implementation

### Basic Structure

```css
/* Pattern: .wp-block-{type}.is-style-{name} */

.wp-block-group.is-style-card {
  /* Your styles */
}

.wp-block-button.is-style-outline .wp-block-button__link {
  /* Button variations need to target the __link */
}

.wp-block-image.is-style-polaroid {
  /* Image styles */
}
```

### Common Patterns

#### 1. Card Variations

```css
/* Basic Card */
.wp-block-group.is-style-card {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Glass Card */
.wp-block-group.is-style-glass {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  padding: 2rem;
}

/* Featured Card */
.wp-block-group.is-style-featured {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  padding: 3rem 2rem;
}
```

#### 2. Button Variations

```css
/* Outline Button */
.wp-block-button.is-style-outline .wp-block-button__link {
  background: transparent;
  border: 2px solid currentColor;
  color: #667eea;
}

.wp-block-button.is-style-outline .wp-block-button__link:hover {
  background: #667eea;
  color: white;
}

/* Arrow Button */
.wp-block-button.is-style-arrow .wp-block-button__link {
  position: relative;
  padding-right: 3rem;
}

.wp-block-button.is-style-arrow .wp-block-button__link::after {
  content: '→';
  position: absolute;
  right: 1rem;
  transition: transform 0.3s;
}

.wp-block-button.is-style-arrow .wp-block-button__link:hover::after {
  transform: translateX(4px);
}
```

#### 3. Image Variations

```css
/* Rounded Image */
.wp-block-image.is-style-rounded img {
  border-radius: 50%;
  object-fit: cover;
}

/* Polaroid Effect */
.wp-block-image.is-style-polaroid {
  background: white;
  padding: 1rem 1rem 3rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: rotate(-2deg);
  transition: transform 0.3s;
}

.wp-block-image.is-style-polaroid:hover {
  transform: rotate(0) scale(1.05);
}

/* Hover Zoom */
.wp-block-image.is-style-zoom {
  overflow: hidden;
  border-radius: 8px;
}

.wp-block-image.is-style-zoom img {
  transition: transform 0.5s;
}

.wp-block-image.is-style-zoom:hover img {
  transform: scale(1.1);
}
```

### Responsive Styling

```css
.wp-block-group.is-style-hero {
  padding: 2rem;
  min-height: 40vh;
}

@media (min-width: 768px) {
  .wp-block-group.is-style-hero {
    padding: 4rem;
    min-height: 60vh;
  }
}

@media (min-width: 1024px) {
  .wp-block-group.is-style-hero {
    padding: 6rem;
    min-height: 80vh;
  }
}
```

---

## Best Practices

### 1. Naming Conventions

```php
// ✅ Good - Descriptive, kebab-case
register_block_style('core/group', array(
    'name'  => 'glass-card',
    'label' => __('Glass Card', 'textdomain'),
));

register_block_style('core/button', array(
    'name'  => 'outline-primary',
    'label' => __('Outline Primary', 'textdomain'),
));

// ❌ Bad - Unclear, inconsistent
register_block_style('core/group', array(
    'name'  => 'gc1',        // Not descriptive
    'label' => 'GlassCard',  // PascalCase
));
```

### 2. CSS Specificity

```css
/* ✅ Good - Just specific enough */
.wp-block-group.is-style-card {
  background: white;
}

/* ❌ Bad - Too specific */
body .site-content .wp-block-group.is-style-card {
  background: white;
}

/* ❌ Bad - Not specific enough */
.is-style-card {
  background: white;
}
```

### 3. Accessibility

```css
/* Respect user preferences */
@media (prefers-reduced-motion: reduce) {
  .wp-block-image.is-style-zoom img,
  .wp-block-button.is-style-arrow .wp-block-button__link::after {
    transition: none;
  }
}

/* High contrast support */
@media (prefers-contrast: high) {
  .wp-block-group.is-style-glass {
    background: white;
    border: 2px solid black;
  }
}

/* Focus states */
.wp-block-button.is-style-outline .wp-block-button__link:focus {
  outline: 2px solid currentColor;
  outline-offset: 2px;
}
```

### 4. Performance

```css
/* ✅ Good - Use transforms */
.wp-block-image.is-style-zoom:hover img {
  transform: scale(1.1); /* GPU-accelerated */
  will-change: transform;
}

/* ❌ Bad - Causes layout shift */
.wp-block-image.is-style-zoom:hover img {
  width: 110%; /* Forces reflow */
  height: 110%; /* Forces repaint */
}
```

### 5. Organization

**File Structure:**

```
theme/
├── assets/css/
│   └── block-styles.css        # All block variations
├── inc/
│   └── block-styles.php        # Registration code
└── functions.php               # Requires inc/block-styles.php
```

**In block-styles.css, group by block type:**

```css
/* ==========================================================================
   GROUP BLOCK VARIATIONS
   ========================================================================== */

.wp-block-group.is-style-card {
}
.wp-block-group.is-style-hero {
}
.wp-block-group.is-style-glass {
}

/* ==========================================================================
   BUTTON VARIATIONS
   ========================================================================== */

.wp-block-button.is-style-outline {
}
.wp-block-button.is-style-gradient {
}
.wp-block-button.is-style-arrow {
}

/* ==========================================================================
   IMAGE VARIATIONS
   ========================================================================== */

.wp-block-image.is-style-rounded {
}
.wp-block-image.is-style-polaroid {
}
.wp-block-image.is-style-zoom {
}
```

---

## Advanced Techniques

### 1. CSS Variables for Theming

```css
.wp-block-group.is-style-themed {
  --card-bg: white;
  --card-text: #333;
  --card-border: #ddd;

  background: var(--card-bg);
  color: var(--card-text);
  border: 1px solid var(--card-border);
  padding: 2rem;
  border-radius: 8px;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .wp-block-group.is-style-themed {
    --card-bg: #222;
    --card-text: #fff;
    --card-border: #444;
  }
}
```

### 2. Pseudo-Element Enhancements

```css
.wp-block-group.is-style-corner-accent {
  position: relative;
  padding: 2rem;
  background: white;
}

.wp-block-group.is-style-corner-accent::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, transparent 100%);
  border-radius: 0 0 100% 0;
}
```

### 3. Child Element Styling

```css
.wp-block-group.is-style-feature-box {
  text-align: center;
  padding: 3rem 2rem;
  background: #f9fafb;
  border-radius: 12px;
}

/* Style child headings */
.wp-block-group.is-style-feature-box h1,
.wp-block-group.is-style-feature-box h2,
.wp-block-group.is-style-feature-box h3 {
  color: #667eea;
  margin-bottom: 1rem;
}

/* Style child images */
.wp-block-group.is-style-feature-box .wp-block-image {
  margin: 0 auto 2rem;
  max-width: 200px;
}

/* Style child buttons */
.wp-block-group.is-style-feature-box .wp-block-button {
  margin-top: 2rem;
}
```

### 4. Unregister Default Styles

```php
<?php
/**
 * Remove default WordPress block styles you don't want
 */
add_action('init', 'mytheme_unregister_default_styles');
function mytheme_unregister_default_styles() {
    // Remove default button styles
    unregister_block_style('core/button', 'fill');
    unregister_block_style('core/button', 'outline');

    // Remove default quote styles
    unregister_block_style('core/quote', 'large');
}
```

---

## Troubleshooting

### Style Not Appearing in Editor

**Check registration:**

```php
add_action('init', 'debug_block_styles', 999);
function debug_block_styles() {
    if (WP_DEBUG) {
        $styles = WP_Block_Styles_Registry::get_instance()->get_all_registered();
        error_log('Registered styles: ' . print_r($styles, true));
    }
}
```

**Verify block name is correct:**

```php
// ✅ Correct
register_block_style('core/group', ...);

// ❌ Wrong
register_block_style('group', ...);        // Missing core/
register_block_style('core/groups', ...);  // Typo
```

### CSS Not Applying

**Check class in inspector:**

Open browser DevTools and verify the class was added:

```html
<!-- Should see: -->
<div class="wp-block-group is-style-card"></div>
```

**Check CSS specificity:**

```css
/* If your styles aren't applying, increase specificity */
.wp-block-group.is-style-card {
  background: red !important; /* Temporary debug */
}
```

**Check CSS is loaded:**

View source and verify your stylesheet is enqueued:

```html
<link rel="stylesheet" href=".../block-styles.css?ver=1.0.0" />
```

### Works in Editor But Not Frontend

**Ensure both hooks are used:**

```php
// ✅ Both hooks needed
add_action('wp_enqueue_scripts', 'mytheme_enqueue_styles');           // Frontend
add_action('enqueue_block_editor_assets', 'mytheme_enqueue_styles');  // Editor

// ❌ Only one hook
add_action('wp_enqueue_scripts', 'mytheme_enqueue_styles');  // Missing editor
```

### Style Shows But CSS Doesn't Load

**Check file path:**

```php
// Debug the file path
function mytheme_enqueue_block_styles() {
    $file = get_template_directory() . '/assets/css/block-styles.css';

    if (WP_DEBUG && !file_exists($file)) {
        error_log('Block styles CSS not found: ' . $file);
        return;
    }

    wp_enqueue_style(...);
}
```

---

## Quick Reference

### Common Block Types

| Block Type | Target Class                                              |
| ---------- | --------------------------------------------------------- |
| Group      | `.wp-block-group.is-style-{name}`                         |
| Button     | `.wp-block-button.is-style-{name} .wp-block-button__link` |
| Image      | `.wp-block-image.is-style-{name}`                         |
| Heading    | `.wp-block-heading.is-style-{name}`                       |
| Paragraph  | `.wp-block-paragraph.is-style-{name}`                     |
| Cover      | `.wp-block-cover.is-style-{name}`                         |
| Column     | `.wp-block-column.is-style-{name}`                        |

### Registration Template

```php
register_block_style('core/{block-type}', array(
    'name'  => 'style-name',           // kebab-case
    'label' => __('Style Label', 'textdomain'),
));
```

### CSS Template

```css
.wp-block-{type}.is-style-{name} {
    /* Your styles */
}
```

---

## Summary

**Block Style Variations:**

- ✅ Easy way to provide design options
- ✅ User-friendly dropdown selection
- ✅ CSS-only, no JavaScript needed
- ✅ Works with any WordPress block
- ✅ Maintains design consistency

**Process:**

1. Register variation in PHP
2. Write CSS for `.is-style-{name}` class
3. Enqueue stylesheet
4. Users select from dropdown

**Best Practices:**

- Use descriptive, kebab-case names
- Target with appropriate specificity
- Support accessibility features
- Use performant CSS properties
- Organize by block type

---

**Version:** 1.0.0
**Last Updated:** 2025-01-26
