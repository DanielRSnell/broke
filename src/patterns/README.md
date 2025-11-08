# Patterns Directory

## Overview

This directory contains **reusable HTML section patterns** that are automatically converted into WordPress block patterns using the `html2pattern` CLI tool.

Think of this as your **component library for page sections**—build once, reuse everywhere.

---

## Philosophy

### The Problem This Solves

Building reusable sections in WordPress typically involves:

- **Copy/paste** - Duplicating block configurations across pages
- **Inconsistency** - Manually copied sections drift over time
- **No single source** - Can't update all instances of a section
- **Block editor tedium** - Rebuilding the same layouts repeatedly
- **No version control** - Design patterns live in database

### The Solution

This directory enables a **pattern library approach** where you:

1. **Build section once** - Write HTML for a reusable component
2. **Add dynamic content** - Use Universal Block/Twig syntax
3. **Convert to pattern** - Run `npm run parse:patterns`
4. **Insert anywhere** - Pattern appears in block inserter
5. **Customize per use** - Each insertion is independent

**Benefits:**
- Single source of truth for section designs
- Reusable across all pages
- Version controlled in git
- Dynamic content support
- No duplication or drift

### Core Principle

**"Build reusable sections as HTML patterns, not by copying blocks in the editor."**

### Patterns vs. Pages

| **Patterns** (`src/patterns/`) | **Pages** (`src/pages/`) |
|--------------------------------|--------------------------|
| **Reusable sections** | **Full page layouts** |
| Hero, CTA, features, testimonials | Home, About, Contact |
| Inserted multiple times | Inserted once per page |
| Component library | Page templates |
| `npm run parse:patterns` | `npm run parse:pages` |
| Category: `patterns,sections` | Category: `pages` |

---

## How It Works

### HTML to Pattern Conversion

```bash
# Convert all pattern HTML files to WordPress patterns
npm run parse:patterns
```

**What happens:**
1. Scans `src/patterns/*.html` for HTML files
2. Parses HTML structure into Universal Blocks
3. Converts to WordPress block markup
4. Generates PHP pattern files in `patterns/` directory
5. Patterns automatically register in WordPress

### File Flow

```
src/patterns/hero.html
    ↓ (npm run parse:patterns)
patterns/hero.php
    ↓ (WordPress auto-registers)
Block Inserter → "Hero" pattern
```

---

## Directory Structure

### Typical Organization

```
src/patterns/
├── README.md               # This file
├── hero.html               # Hero section variations
├── features.html           # Features grid
├── cta.html                # Call-to-action sections
├── testimonials.html       # Testimonial sections
├── pricing.html            # Pricing tables
├── team.html               # Team member grids
├── faq.html                # FAQ sections
└── related-posts.html      # Related content sections
```

**Naming convention:** Use descriptive names that indicate the section purpose.

---

## Pattern Template Structure

### Basic Section Pattern

```html
<!-- src/patterns/hero.html -->
<section class="section-hero dot-grid">
    <div class="content">
        <h1 class="h1">Your Compelling Headline</h1>
        <p class="lead">Supporting subheading that explains the value proposition.</p>
        <a href="/get-started" class="btn btn-primary">Get Started</a>
    </div>
</section>
```

### With Dynamic Content

```html
<!-- src/patterns/recent-posts.html -->
<section class="section">
    <div class="content">
        <h2 class="h2">Recent Articles</h2>

        <!-- Data from src/context/recent-posts.php -->
        <div loopsource="recent_posts" loopvariable="post" class="card-grid-3">
            <article class="v-card">
                <h3 class="h3">{{ post.title }}</h3>
                <p class="body-sm">{{ post.excerpt }}</p>
                <a href="{{ post.link }}" class="btn">Read More</a>
            </article>
        </div>
    </div>
</section>
```

### With Conditional Rendering

```html
<!-- src/patterns/related-posts.html -->
<section
    conditionalvisibility="true"
    conditionalexpression="related_posts|length > 0"
    class="section">
    <div class="content">
        <h2 class="h2">Related Articles</h2>

        <div loopsource="related_posts" loopvariable="post" class="card-grid">
            <article class="v-card">
                <h3 class="h3">{{ post.title }}</h3>
                <p class="body-sm">{{ post.excerpt }}</p>
                <a href="{{ post.link }}">Read More</a>
            </article>
        </div>
    </div>
</section>
```

**This section only renders if `related_posts` array has items.**

### With Multiple Variations

```html
<!-- src/patterns/cta-variants.html -->
<!-- Variation 1: Centered CTA -->
<section class="section bg-gradient-subtle" data-block-name="CTA - Centered">
    <div class="content text-center">
        <h2 class="h2">Ready to Get Started?</h2>
        <p class="lead">Join thousands of satisfied customers today.</p>
        <a href="/signup" class="btn btn-primary">Sign Up Free</a>
    </div>
</section>

<!-- Variation 2: Split CTA -->
<section class="section theme-night" data-block-name="CTA - Split">
    <div class="content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="h2">Ready to Get Started?</h2>
                <p class="body">Join thousands of satisfied customers today.</p>
            </div>
            <div class="text-right">
                <a href="/signup" class="btn btn-primary btn-lg">Sign Up Free</a>
            </div>
        </div>
    </div>
</section>
```

**Multiple section variants in one file** - each becomes a separate pattern.

---

## Common Pattern Types

### 1. Hero Sections

```html
<!-- src/patterns/hero.html -->
<section class="section-hero mesh-gradient">
    <div class="content">
        <h1 class="h1">{{ post.title|default('Welcome to Our Site') }}</h1>
        <p class="lead">{{ post.meta('subtitle')|default('Subheading goes here') }}</p>
        <div class="flex gap-4 mt-8">
            <a href="/get-started" class="btn btn-primary">Get Started</a>
            <a href="/learn-more" class="btn">Learn More</a>
        </div>
    </div>
</section>
```

**Use on:** Homepage, landing pages, major section introductions

### 2. Features Grid

```html
<!-- src/patterns/features.html -->
<section class="section">
    <div class="content">
        <h2 class="h2 text-center">Why Choose Us</h2>
        <p class="lead text-center">Everything you need to succeed</p>

        <div class="card-grid-3">
            <div class="v-card text-center">
                <h3 class="h3">Fast Performance</h3>
                <p class="body-sm">Lightning-fast load times for better conversions.</p>
            </div>
            <div class="v-card text-center">
                <h3 class="h3">Secure by Default</h3>
                <p class="body-sm">Enterprise-grade security built in.</p>
            </div>
            <div class="v-card text-center">
                <h3 class="h3">24/7 Support</h3>
                <p class="body-sm">Real humans ready to help anytime.</p>
            </div>
        </div>
    </div>
</section>
```

**Use on:** About pages, service pages, product pages

### 3. Call to Action (CTA)

```html
<!-- src/patterns/cta.html -->
<section class="section bg-brand-gradient text-white">
    <div class="content text-center">
        <h2 class="h2">Start Your Free Trial Today</h2>
        <p class="lead">No credit card required. Cancel anytime.</p>
        <a href="/signup" class="btn btn-lg">Get Started Free</a>
    </div>
</section>
```

**Use on:** End of pages, between sections, conversion points

### 4. Testimonials

```html
<!-- src/patterns/testimonials.html -->
<section class="section surface-card">
    <div class="content">
        <h2 class="h2 text-center">What Our Customers Say</h2>

        <div class="card-grid-3 mt-12">
            <div class="v-card">
                <p class="body">"This product changed how we work. Highly recommended!"</p>
                <div class="mt-4">
                    <p class="label">— John Doe</p>
                    <p class="meta">CEO, Company Inc</p>
                </div>
            </div>
            <!-- More testimonials -->
        </div>
    </div>
</section>
```

**Use on:** Homepage, about page, product pages

### 5. Pricing Tables

```html
<!-- src/patterns/pricing.html -->
<section class="section">
    <div class="content">
        <h2 class="h2 text-center">Simple, Transparent Pricing</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
            <div class="v-card text-center">
                <h3 class="h3">Starter</h3>
                <p class="text-5xl font-bold mt-4">$29</p>
                <p class="meta">per month</p>
                <ul class="mt-6 space-y-2">
                    <li>Feature one</li>
                    <li>Feature two</li>
                    <li>Feature three</li>
                </ul>
                <a href="/signup?plan=starter" class="btn btn-primary mt-8">Get Started</a>
            </div>
            <!-- More pricing tiers -->
        </div>
    </div>
</section>
```

**Use on:** Pricing pages, product pages, comparison pages

### 6. Team Members

```html
<!-- src/patterns/team.html -->
<section class="section">
    <div class="content">
        <h2 class="h2 text-center">Meet Our Team</h2>

        <div class="card-grid-3 mt-12">
            <div class="v-card text-center">
                <img src="/path/to/avatar.jpg" alt="Team member" class="rounded-full w-32 h-32 mx-auto">
                <h3 class="h3 mt-4">Jane Smith</h3>
                <p class="meta">Founder & CEO</p>
                <p class="body-sm mt-2">Brief bio about team member.</p>
            </div>
            <!-- More team members -->
        </div>
    </div>
</section>
```

**Use on:** About page, team page

### 7. FAQ

```html
<!-- src/patterns/faq.html -->
<section class="section">
    <div class="content max-w-3xl mx-auto">
        <h2 class="h2 text-center">Frequently Asked Questions</h2>

        <div class="stack-lg mt-12">
            <details class="v-card">
                <summary class="h3 cursor-pointer">How does pricing work?</summary>
                <p class="body-sm mt-4">Detailed answer to the question goes here.</p>
            </details>

            <details class="v-card">
                <summary class="h3 cursor-pointer">Can I cancel anytime?</summary>
                <p class="body-sm mt-4">Yes, you can cancel your subscription anytime.</p>
            </details>
            <!-- More FAQs -->
        </div>
    </div>
</section>
```

**Use on:** FAQ page, support pages, product pages

---

## Structure by Project Type

### Marketing / SaaS Site

**Focus:** Conversion-optimized sections

```
src/patterns/
├── hero-variants.html
├── features-grid.html
├── social-proof.html
├── pricing-tables.html
├── cta-variants.html
├── testimonials.html
└── faq.html
```

### Blog / Publication Site

**Focus:** Content display sections

```
src/patterns/
├── post-grid.html
├── featured-post.html
├── related-posts.html
├── author-bio.html
├── newsletter-signup.html
└── category-nav.html
```

### Portfolio / Agency Site

**Focus:** Visual showcases

```
src/patterns/
├── project-showcase.html
├── services-grid.html
├── case-study-preview.html
├── client-logos.html
├── process-steps.html
└── contact-cta.html
```

### E-commerce Site

**Focus:** Product displays

```
src/patterns/
├── product-grid.html
├── featured-products.html
├── category-banner.html
├── product-benefits.html
├── trust-badges.html
└── shipping-cta.html
```

---

## Workflow

### 1. Create Pattern

```bash
# Create new pattern file
touch src/patterns/new-section.html
```

Edit with your HTML structure.

### 2. Convert to Pattern

```bash
# Convert all patterns
npm run parse:patterns

# Or convert specific file
html2pattern convert src/patterns/new-section.html -o patterns
```

### 3. Insert in WordPress

1. Edit any page
2. Click "+" block inserter
3. Search for your pattern
4. Insert and customize

### 4. Iterate

```bash
# Edit HTML
# Re-convert
npm run parse:patterns

# Pattern updates
# Existing instances unchanged
```

---

## Best Practices

### 1. Make Patterns Self-Contained

**Good:**
```html
<!-- Complete section with all necessary markup -->
<section class="section">
    <div class="content">
        <h2 class="h2">Section Title</h2>
        <p class="body">Content</p>
    </div>
</section>
```

**Bad:**
```html
<!-- Incomplete - missing section wrapper -->
<div class="content">
    <h2>Section Title</h2>
    <p>Content</p>
</div>
```

### 2. Use Semantic Block Names

```html
<section data-block-name="Hero - Centered" class="section-hero">
<section data-block-name="Features - 3 Column" class="section">
<section data-block-name="CTA - Dark Theme" class="section theme-night">
```

This makes patterns easier to identify in the block list view.

### 3. Design for Flexibility

```html
<!-- Good - Easy to customize -->
<section class="section">
    <div class="content">
        <h2 class="h2">{{ section_title|default('Default Title') }}</h2>
        <p class="body">{{ section_content|default('Default content') }}</p>
    </div>
</section>
```

Provides defaults but allows customization.

### 4. Document Dependencies

```html
<!--
  Related Posts Section

  Dependencies:
  - Context: related_posts (from src/context/related-posts.php)
  - Minimum 1 related post required
  - Conditionally hidden if no posts

  Usage:
  - Insert on single post pages
  - Customize heading and card layout as needed
-->
<section conditionalvisibility="true" conditionalexpression="related_posts|length > 0">
    ...
</section>
```

### 5. Group Related Variations

```
src/patterns/
├── hero-centered.html
├── hero-split.html
├── hero-video.html
```

Or in one file:
```html
<!-- src/patterns/hero-variants.html -->
<section data-block-name="Hero - Centered">...</section>
<section data-block-name="Hero - Split">...</section>
<section data-block-name="Hero - Video Background">...</section>
```

### 6. Test Across Contexts

Insert pattern on:
- Homepage
- Interior page
- Single post
- Archive page

Ensure it looks good everywhere.

---

## Advanced Patterns

### Context-Aware Patterns

```html
<!-- src/patterns/archive-header.html -->
<!-- Different content based on archive type -->

<section class="section-hero">
    <div class="content">
        <!-- Category archive -->
        <div conditionalvisibility="true" conditionalexpression="term.taxonomy == 'category'">
            <h1 class="h1">{{ term.name }}</h1>
            <p class="lead">{{ term.description }}</p>
        </div>

        <!-- Author archive -->
        <div conditionalvisibility="true" conditionalexpression="author">
            <h1 class="h1">Posts by {{ author.name }}</h1>
            <p class="lead">{{ author.description }}</p>
        </div>

        <!-- Post type archive -->
        <div conditionalvisibility="true" conditionalexpression="post_type">
            <h1 class="h1">{{ post_type|title }} Archive</h1>
        </div>
    </div>
</section>
```

### Dynamic Data Loading

```html
<!-- src/patterns/stats.html -->
<section setvariable="stats" setexpression="timber.get_posts({'post_type': 'stat', 'posts_per_page': 4})">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div loopsource="stats" loopvariable="stat" class="text-center">
            <p class="text-5xl font-bold brand-gradient">{{ stat.meta('number') }}</p>
            <p class="meta">{{ stat.title }}</p>
        </div>
    </div>
</section>
```

**Note:** Prefer context filters over inline data loading for better MVC separation.

### Accessibility-First Patterns

```html
<!-- src/patterns/accessible-card-grid.html -->
<section class="section" aria-labelledby="features-heading">
    <div class="content">
        <h2 id="features-heading" class="h2">Our Features</h2>

        <div class="card-grid-3">
            <article class="v-card">
                <h3 class="h3">Feature Name</h3>
                <p class="body-sm">Description</p>
                <a href="/feature" class="btn" aria-label="Learn more about Feature Name">
                    Learn More
                </a>
            </article>
        </div>
    </div>
</section>
```

---

## Integration with Theme Workflow

### With Context Filters

```html
<!-- Pattern uses data from context -->
<div loopsource="recent_posts" loopvariable="post">
    <h3>{{ post.title }}</h3>
</div>
```

Context filter (`src/context/recent-posts.php`) provides the data.

### With Styles

```html
<!-- Pattern uses component classes -->
<section class="section dot-grid">
    <div class="content">
        <div class="v-card">
            <h3 class="h3">Title</h3>
            <a href="#" class="btn btn-primary">CTA</a>
        </div>
    </div>
</section>
```

CSS components (`src/styles/`) define the styling.

### With Pages

```
src/pages/home.html        ← Combines multiple patterns
  ↳ Uses hero.html pattern
  ↳ Uses features.html pattern
  ↳ Uses cta.html pattern
```

Pages compose patterns into full layouts.

---

## Troubleshooting

### Pattern Not in Inserter

1. Run `npm run parse:patterns`
2. Check `patterns/` directory for PHP file
3. Clear WordPress cache
4. Verify no PHP errors

### Dynamic Content Not Working

1. Check context filter exists
2. Variable name matches
3. Twig syntax correct
4. Enable Twig debug: `?twig=false`

### Styles Not Applying

1. Rebuild CSS: `npm run build:css`
2. Verify class names match components
3. Clear browser cache

---

## Commands Reference

```bash
# Convert all patterns
npm run parse:patterns

# Convert specific file
html2pattern convert src/patterns/hero.html -o patterns

# With custom options
html2pattern convert src/patterns/hero.html \
  --namespace=my-theme \
  --categories=patterns,sections \
  --description="Hero section pattern"

# Rebuild assets
npm run build:css
npm run build:js
```

---

## Related Documentation

- **[src/docs/block-markup-guide.md](../docs/block-markup-guide.md)** - Universal Block syntax (REQUIRED)
- **[src/pages/README.md](../pages/README.md)** - Full page templates
- **[src/styles/README.md](../styles/README.md)** - CSS components
- **[src/context/README.md](../context/README.md)** - Data layer
- **[CLAUDE.md](../../CLAUDE.md)** - Theme architecture

---

**Summary:**

This directory contains **reusable HTML section patterns** converted to WordPress block patterns via `npm run parse:patterns`. Build section designs once, insert anywhere, customize per use. Version controlled, dynamic content support, component library approach.

The structure you choose depends on your project's section needs, but the principle remains the same: **build reusable sections as patterns, not by copying blocks.**

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
