# Pattern Naming Guide

A comprehensive guide to naming WordPress block patterns for maximum reusability and maintainability.

---

## Table of Contents

1. [Philosophy](#philosophy)
2. [Naming Structure](#naming-structure)
3. [Reusability Assessment](#reusability-assessment)
4. [Naming Categories](#naming-categories)
5. [Examples](#examples)
6. [Common Pitfalls](#common-pitfalls)
7. [Quick Reference](#quick-reference)

---

## Philosophy

### Core Principles

1. **Generic over Specific** - If a pattern can be reused across multiple contexts, use a generic name
2. **Descriptive Names** - Pattern names should clearly indicate what the pattern contains
3. **Context When Needed** - Add context (page/post type prefix) only when the pattern is truly specific to that context
4. **Future-Proof** - Think about whether this pattern could be used elsewhere in 6 months

### The Reusability Question

Before naming a pattern, ask:

> "Could this exact layout/structure be useful on a different page or post type?"

- **Yes** → Use a generic name (e.g., `hero-centered`, `cta-split`)
- **No** → Use a context-specific name (e.g., `portfolio-grid`, `product-showcase`)

---

## Naming Structure

### Generic Patterns

```
{component}-{variant}
```

**Examples:**

- `hero-centered`
- `cta-gradient`
- `header-minimal`
- `grid-masonry`

### Context-Specific Patterns

```
{context}-{component}-{variant}
```

**Examples:**

- `portfolio-grid-featured`
- `product-showcase-cards`
- `team-bio-carousel`
- `blog-post-header`

### Numbered Components (Temporary/In Progress)

```
{context}-child-{number}
```

**Use Case:** Initial extraction before proper naming

**Examples:**

- `portfolio-child-1.html` → Rename to descriptive name
- `about-child-2.html` → Rename to descriptive name

---

## Reusability Assessment

### High Reusability (Use Generic Names)

✅ **Headers & Navigation**

- Can be used on multiple page types
- Name: `header-{variant}`, `nav-{variant}`

✅ **Call-to-Actions (CTAs)**

- Standard conversion elements
- Name: `cta-{variant}`, `banner-{variant}`

✅ **Content Sections**

- Text/media layouts that work anywhere
- Name: `section-{layout}`, `content-{variant}`

✅ **Cards & Grids**

- Generic display patterns
- Name: `grid-{layout}`, `card-{style}`

✅ **Footers**

- Site-wide elements
- Name: `footer-{variant}`

### Low Reusability (Use Context-Specific Names)

❌ **Custom Grids with Specific Data**

- Portfolio project grids
- Product catalogs
- Name: `{context}-grid-{variant}`

❌ **Specialized Forms**

- Contact forms with specific fields
- Application forms
- Name: `{context}-form-{variant}`

❌ **Custom Showcases**

- Team member displays
- Case study layouts
- Name: `{context}-showcase-{variant}`

❌ **Unique Page Elements**

- About page timelines
- Service breakdowns
- Name: `{context}-{component}-{variant}`

---

## Naming Categories

### Layout Patterns

**Generic Names:**

```
hero-{variant}        - Hero sections
section-{variant}     - Content sections
split-{variant}       - Split layouts
columns-{variant}     - Column layouts
grid-{variant}        - Grid layouts
```

**Examples:**

- `hero-centered` - Centered text hero
- `hero-split` - Split image/text hero
- `section-alternating` - Alternating content sections
- `grid-masonry` - Masonry-style grid
- `columns-three` - Three column layout

### Component Patterns

**Generic Names:**

```
cta-{variant}         - Call-to-action
card-{variant}        - Card components
button-{variant}      - Button groups
feature-{variant}     - Feature displays
testimonial-{variant} - Testimonial layouts
pricing-{variant}     - Pricing tables
```

**Examples:**

- `cta-gradient` - Gradient background CTA
- `cta-centered` - Centered CTA with button
- `card-minimal` - Minimal card design
- `feature-icons` - Features with icon grid
- `pricing-three-tier` - Three tier pricing

### Navigation Patterns

**Generic Names:**

```
header-{variant}      - Header sections
nav-{variant}         - Navigation menus
menu-{variant}        - Menu layouts
breadcrumb-{variant}  - Breadcrumb navigation
```

**Examples:**

- `header-transparent` - Transparent header overlay
- `header-sticky` - Sticky header
- `nav-sidebar` - Sidebar navigation
- `menu-mega` - Mega menu dropdown

### Context-Specific Patterns

**Use Context Prefix:**

```
portfolio-{component}  - Portfolio-specific
blog-{component}       - Blog-specific
product-{component}    - Product/WooCommerce
team-{component}       - Team/about page
service-{component}    - Services page
```

**Examples:**

- `portfolio-grid-featured` - Portfolio project grid
- `blog-post-header` - Blog post header
- `product-showcase-cards` - Product display cards
- `team-bio-carousel` - Team member carousel
- `service-breakdown-list` - Service details list

---

## Examples

### Example 1: Page Header Section

**Content:**

```html
<section class="...">
  <div class="text-center">
    <h1>Selected Work</h1>
    <p>A collection of digital experiences...</p>
  </div>
</section>
```

**Assessment:**

- ✅ Could be used for: Portfolio, Services, Blog Archive, any page
- ✅ Generic centered header layout
- ✅ No portfolio-specific content in structure

**Name:** `header-page-centered` or `page-header-centered`

---

### Example 2: Portfolio Project Grid

**Content:**

```html
<section class="...">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Project cards with specific portfolio fields -->
    <div>
      <img src="..." alt="E-commerce Platform" />
      <h3>E-commerce Platform</h3>
      <p>Complete marketplace redesign...</p>
      <span>React</span>
      <span>Node.js</span>
    </div>
  </div>
</section>
```

**Assessment:**

- ❌ Contains portfolio-specific structure (tech tags, project cards)
- ❌ Unlikely to be used outside portfolio context
- ❌ Would need significant modification for other uses

**Name:** `portfolio-grid-projects` or `portfolio-showcase-grid`

---

### Example 3: Call-to-Action Section

**Content:**

```html
<section class="...">
  <div class="text-center">
    <h2>Ready to Start Your Project?</h2>
    <p>Let's discuss how we can bring your vision to life...</p>
    <div class="flex gap-6 justify-center">
      <a class="btn-gradient" href="#work">
        <span>Book a call</span>
      </a>
      <a class="btn-secondary" href="#pricing">
        <span>View pricing</span>
      </a>
    </div>
  </div>
</section>
```

**Assessment:**

- ✅ Standard CTA layout with heading, text, buttons
- ✅ Could be used on: Services, About, Contact, any conversion point
- ✅ Generic dual-button CTA structure

**Name:** `cta-centered-dual` or `cta-two-button`

---

### Example 4: Service Breakdown List

**Content:**

```html
<section class="...">
  <div class="grid">
    <div class="service-item">
      <icon>Design</icon>
      <h3>UI/UX Design</h3>
      <p>User-centered design solutions...</p>
      <ul>
        <li>Wireframing</li>
        <li>Prototyping</li>
        <li>User Testing</li>
      </ul>
    </div>
  </div>
</section>
```

**Assessment:**

- ❌ Specific to services page
- ❌ Contains service-specific structure (icon, features list)
- ❌ Unlikely to be useful elsewhere without modification

**Name:** `service-breakdown-grid` or `services-feature-list`

---

## Common Pitfalls

### ❌ DON'T: Over-Specify Generic Patterns

**Bad:**

```
portfolio-header-section-centered.html
```

**Why:** If the header could work on any page, don't add "portfolio" prefix

**Good:**

```
page-header-centered.html
```

---

### ❌ DON'T: Use Page Names for Reusable Patterns

**Bad:**

```
about-page-hero.html
```

**Why:** If the hero layout works elsewhere, it's not about-specific

**Good:**

```
hero-image-left.html
```

---

### ❌ DON'T: Keep Generic Names for Specific Content

**Bad:**

```
grid-layout-1.html
```

**Why:** If it contains portfolio projects, it's portfolio-specific

**Good:**

```
portfolio-grid-projects.html
```

---

### ❌ DON'T: Use Numbers Instead of Descriptions

**Bad:**

```
section-1.html
section-2.html
section-3.html
```

**Why:** Numbers don't describe content or purpose

**Good:**

```
hero-centered.html
features-three-col.html
cta-gradient.html
```

---

## Quick Reference

### Decision Tree

```
Start: Look at pattern content
    ↓
Question 1: Does it contain context-specific data/structure?
    ├─ YES → Use context prefix (portfolio-, service-, team-)
    └─ NO → Continue
    ↓
Question 2: Could this layout work on multiple page types?
    ├─ YES → Use generic name (hero-, cta-, grid-)
    └─ NO → Use context prefix
    ↓
Question 3: What's the main component type?
    - Layout (hero, section, grid)
    - Component (cta, card, feature)
    - Navigation (header, nav, menu)
    ↓
Question 4: What's the variant/style?
    - Layout style (centered, split, alternating)
    - Visual style (gradient, minimal, bordered)
    - Quantity (two-col, three-tier)
    ↓
Final Name: {component}-{variant} OR {context}-{component}-{variant}
```

### Naming Checklist

Before finalizing a pattern name:

- [ ] Is the name descriptive of what the pattern contains?
- [ ] Would someone else understand what this pattern is?
- [ ] Does the reusability match the naming specificity?
- [ ] Are you using the most generic name possible while still being clear?
- [ ] Have you avoided numbers in favor of descriptions?

---

## Summary

### Key Takeaways

1. **Generic First** - Start with the most generic name possible
2. **Add Context When Needed** - Only add prefixes for truly specific patterns
3. **Descriptive Over Clever** - `hero-centered` beats `hero-1` every time
4. **Think Future** - Could this pattern be useful elsewhere?
5. **Consistency** - Follow the established naming patterns in your project

### Pattern Name Formula

```
Generic: {component}-{variant}
Specific: {context}-{component}-{variant}
```

**Examples:**

- ✅ `cta-gradient` (generic CTA, reusable)
- ✅ `portfolio-grid-projects` (portfolio-specific grid)
- ✅ `hero-split-image` (generic split hero)
- ✅ `team-bio-cards` (team-specific card layout)

---

**Remember:** Good pattern names make patterns easy to find, understand, and reuse. When in doubt, choose the more generic name and add context only when truly necessary.
