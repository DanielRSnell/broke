# Pages Directory

## Overview

This directory contains **full HTML page templates** that are automatically converted into WordPress block patterns using the `html2pattern` CLI tool.

Think of this as **your pattern library for complete pages**—write HTML once, convert to WordPress blocks, insert anywhere.

---

## Philosophy

### The Problem This Solves

Building pages in WordPress typically means:

- **Block editor limitations** - Repetitive clicking and dragging
- **No version control** - Page designs live in database
- **Hard to prototype** - Can't design full pages in HTML
- **Difficult collaboration** - Designers can't work in WordPress
- **Copy/paste reuse** - No systematic way to reuse page layouts

### The Solution

This directory enables an **HTML-first workflow** where you:

1. **Design in HTML** - Write full page layouts with proper markup
2. **Use Universal Block syntax** - Add dynamic Twig content
3. **Convert to patterns** - Run `npm run parse:pages`
4. **Insert in WordPress** - Patterns appear in block inserter
5. **Customize per page** - Each insertion is independent

**Benefits:**
- Version control for page designs
- Design in your IDE, not WordPress editor
- Reusable page templates
- Dynamic content via Twig
- No PHP knowledge required for page creation

### Core Principle

**"Page designs should be HTML files that automatically become WordPress patterns."**

---

## How It Works

### HTML to Pattern Conversion

```bash
# Convert all page HTML files to WordPress patterns
npm run parse:pages
```

**What happens:**
1. Scans `src/pages/*.html` for HTML files
2. Parses HTML structure into Universal Blocks
3. Converts to WordPress block markup (comments + HTML)
4. Generates PHP pattern files in `patterns/` directory
5. Patterns automatically register in WordPress

### File Flow

```
src/pages/home.html
    ↓ (npm run parse:pages)
patterns/home.php
    ↓ (WordPress auto-registers)
Block Inserter → "Home" pattern
```

---

## Directory Structure

### Typical Organization

```
src/pages/
├── README.md               # This file
├── home.html               # Homepage template
├── about.html              # About page template
├── services.html           # Services page template
├── contact.html            # Contact page template
├── pricing.html            # Pricing page template
└── blog.html               # Blog listing page template
```

**Naming convention:** Use descriptive names that indicate the page purpose. Filename becomes pattern name.

---

## HTML Template Structure

### Basic Template

```html
<!-- src/pages/example.html -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Example Page</title>
</head>
<body>
    <!-- Hero Section -->
    <section class="section-hero dot-grid">
        <div class="content">
            <h1 class="h1">Welcome to Our Site</h1>
            <p class="lead">Subheading goes here</p>
            <a href="/contact" class="btn btn-primary">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="content">
            <h2 class="h2">Our Features</h2>
            <div class="card-grid-3">
                <div class="v-card">
                    <h3 class="h3">Feature One</h3>
                    <p class="body-sm">Description of feature one.</p>
                </div>
                <div class="v-card">
                    <h3 class="h3">Feature Two</h3>
                    <p class="body-sm">Description of feature two.</p>
                </div>
                <div class="v-card">
                    <h3 class="h3">Feature Three</h3>
                    <p class="body-sm">Description of feature three.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
```

### With Dynamic Content (Twig)

```html
<!-- src/pages/home.html -->
<section class="section">
    <div class="content">
        <h2 class="h2">Recent Posts</h2>

        <!-- Loop through posts from context -->
        <div loopsource="recent_posts" loopvariable="post" class="card-grid">
            <article class="v-card">
                <h3 class="h3">{{ post.title }}</h3>
                <p class="body-sm">{{ post.excerpt }}</p>
                <a href="{{ post.link }}" class="btn">Read More</a>
            </article>
        </div>
    </div>
</section>
```

### With Conditional Content

```html
<!-- src/pages/dashboard.html -->
<section class="section">
    <div class="content">
        <!-- Show different content for logged-in users -->
        <div conditionalvisibility="true" conditionalexpression="user.ID > 0">
            <h1 class="h1">Welcome back, {{ user.display_name }}!</h1>
            <p class="lead">Your dashboard</p>
        </div>

        <div conditionalvisibility="true" conditionalexpression="user.ID == 0">
            <h1 class="h1">Please Log In</h1>
            <a href="/login" class="btn btn-primary">Log In</a>
        </div>
    </div>
</section>
```

---

## Universal Block Syntax

### Essential Attributes

**Loop through data:**
```html
<div loopsource="posts" loopvariable="post">
    <h3>{{ post.title }}</h3>
</div>
```

**Conditional visibility:**
```html
<div conditionalvisibility="true" conditionalexpression="user.ID > 0">
    <p>Logged in content</p>
</div>
```

**Set variables:**
```html
<div setvariable="featured" setexpression="post.meta('featured')">
    {% if featured %}
        <span class="badge">Featured</span>
    {% endif %}
</div>
```

**Custom block name (Gutenberg sidebar):**
```html
<section data-block-name="Hero Section">
    <!-- Content -->
</section>
```

**For complete syntax reference, see:** [src/docs/block-markup-guide.md](../docs/block-markup-guide.md)

---

## Workflow

### 1. Create HTML Template

```bash
# Create new page template
touch src/pages/landing-page.html
```

Edit the file with your HTML structure and content.

### 2. Convert to Pattern

```bash
# Convert all pages
npm run parse:pages

# Or convert specific file via CLI
html2pattern convert src/pages/landing-page.html -o patterns
```

### 3. Insert in WordPress

1. Edit any page in WordPress
2. Click "+" to open block inserter
3. Search for your pattern name (e.g., "Landing Page")
4. Click to insert
5. Customize the instance as needed

### 4. Iterate

```bash
# Edit HTML template
# Re-convert
npm run parse:pages

# Pattern updates automatically
# Existing page instances remain unchanged
```

**Important:** Updating the pattern doesn't change existing page instances—each insertion is independent.

---

## Structure by Project Type

### Marketing / Landing Page Site

**Focus:** Multiple landing page templates for campaigns

```
src/pages/
├── home.html
├── product-launch.html
├── webinar-registration.html
├── free-trial.html
├── pricing.html
└── thank-you.html
```

**Why:** Each campaign gets a custom page template, easily duplicated and modified.

### Corporate / Business Site

**Focus:** Standard business pages

```
src/pages/
├── home.html
├── about.html
├── services.html
├── team.html
├── contact.html
├── careers.html
└── case-studies.html
```

**Why:** Consistent page layouts across standard business sections.

### Portfolio / Agency Site

**Focus:** Project showcases and service pages

```
src/pages/
├── home.html
├── portfolio.html
├── services.html
├── process.html
├── about.html
└── contact.html
```

**Why:** Rich visual layouts for showcasing work and explaining services.

### E-commerce / Product Site

**Focus:** Product-focused pages

```
src/pages/
├── home.html
├── shop.html
├── product-category.html
├── about-us.html
├── shipping-returns.html
└── faq.html
```

**Why:** Category pages, informational pages, conversion-optimized layouts.

### Documentation / Knowledge Base

**Focus:** Content-heavy pages

```
src/pages/
├── docs-home.html
├── getting-started.html
├── api-reference.html
├── tutorials.html
└── faq.html
```

**Why:** Documentation layouts with sidebars, TOCs, and content structure.

---

## Best Practices

### 1. Use Semantic HTML

```html
<!-- Good -->
<section class="section">
    <div class="content">
        <article class="v-card">
            <h3>Title</h3>
            <p>Content</p>
        </article>
    </div>
</section>

<!-- Bad -->
<div class="section">
    <div class="content">
        <div class="v-card">
            <div class="h3">Title</div>
            <div>Content</div>
        </div>
    </div>
</div>
```

### 2. Follow CSS Architecture

Use component classes from `src/styles/`:

```html
<!-- Layout classes -->
<section class="section">          <!-- Vertical spacing -->
    <div class="content">            <!-- Max-width container -->

<!-- Typography classes -->
<h1 class="h1">Heading</h1>
<p class="lead">Lead text</p>
<p class="body">Body text</p>

<!-- Component classes -->
<div class="v-card">                <!-- Vertical card -->
<button class="btn btn-primary">    <!-- Primary button -->
<div class="card-grid-3">           <!-- 3-column grid -->
```

### 3. Name Blocks for Gutenberg

```html
<!-- Makes it easier to identify in block list view -->
<section data-block-name="Hero Section" class="section-hero">
    ...
</section>

<section data-block-name="Features Grid" class="section">
    ...
</section>

<section data-block-name="Call to Action" class="section">
    ...
</section>
```

### 4. Keep Templates Focused

**Good - Single purpose:**
```
home.html              # Homepage only
pricing.html           # Pricing page only
contact.html           # Contact page only
```

**Bad - Generic:**
```
template.html          # What kind of page?
page-1.html            # Not descriptive
generic.html           # Too vague
```

### 5. Document Complex Logic

```html
<!--
  Featured Posts Section

  Dependencies:
  - Context: featured_posts (from src/context/featured-posts.php)
  - Requires at least 3 featured posts
  - Falls back to recent posts if none featured
-->
<section class="section">
    <div loopsource="featured_posts" loopvariable="post">
        ...
    </div>
</section>
```

### 6. Test After Conversion

```bash
npm run parse:pages

# Check WordPress:
# 1. Pattern appears in inserter
# 2. Pattern inserts correctly
# 3. Dynamic content renders
# 4. No PHP/JS errors
```

---

## Advanced Patterns

### Reusable Sections

For sections used across multiple pages, create as patterns instead:

```
src/patterns/hero.html          # Reusable hero section
src/patterns/cta.html           # Reusable CTA section
src/patterns/testimonials.html  # Reusable testimonials

src/pages/home.html             # Combines multiple patterns
src/pages/about.html            # Combines multiple patterns
```

This creates both:
- **Full page patterns** (from `src/pages/`)
- **Section patterns** (from `src/patterns/`)

### Viewport Width Optimization

```bash
# Wide layouts (desktop-first)
npm run parse:pages  # Uses --viewport-width=1600

# Mobile layouts (mobile-first)
html2pattern convert src/pages/mobile-home.html \
  --viewport-width=375 \
  -o patterns
```

Viewport width affects how responsive classes are rendered.

### Pattern Categories

Pages are automatically categorized as `pages`:

```bash
npm run parse:pages
# Equivalent to:
# html2pattern convert src/pages -o patterns --categories=pages
```

This separates page patterns from section patterns in the inserter.

---

## Integration with Theme Workflow

### With Content Collections

```html
<!-- src/pages/blog.html -->
<section class="section">
    <div class="content">
        <h1 class="h1">{{ post.title }}</h1>

        <!-- Content from src/content/pages/blog/ -->
        <div class="prose">
            {{ post.content }}
        </div>
    </div>
</section>
```

**Workflow:**
1. Design page structure in `src/pages/blog.html`
2. Manage page content in `src/content/pages/blog/`
3. Structure (HTML) and content (MD/HTML) are separate

### With Context Filters

```html
<!-- src/pages/home.html -->
<section class="section">
    <!-- Data from src/context/recent-posts.php -->
    <div loopsource="recent_posts" loopvariable="post">
        <article class="v-card">
            <h3>{{ post.title }}</h3>
            <p>{{ post.excerpt }}</p>
        </article>
    </div>
</section>
```

Context filters provide data, page templates structure presentation.

### With Styles

```html
<!-- Use classes from src/styles/ -->
<section class="section dot-grid theme-night">
    <div class="content">
        <h1 class="h1 brand-gradient">Heading</h1>
        <p class="lead">Lead paragraph</p>
        <a href="/contact" class="btn btn-primary">CTA</a>
    </div>
</section>
```

CSS changes rebuild automatically with `npm run build:css`.

---

## Troubleshooting

### Pattern Not Appearing in Inserter

**Check:**
1. Conversion succeeded: `npm run parse:pages`
2. Pattern file exists in `patterns/` directory
3. Pattern file is valid PHP (no syntax errors)
4. WordPress cache cleared

### Dynamic Content Not Rendering

**Verify:**
1. Context filter exists in `src/context/`
2. Variable name matches in HTML (`loopsource="recent_posts"`)
3. Universal Block syntax is correct
4. Twig is enabled (`?twig=false` to debug)

### HTML Structure Changes Not Reflected

**Solution:**
1. Edit HTML in `src/pages/`, not `patterns/`
2. Re-run `npm run parse:pages`
3. Clear WordPress cache
4. Re-insert pattern (existing instances won't update)

### Styles Not Applying

**Check:**
1. CSS classes match `src/styles/` components
2. CSS build is current: `npm run build:css`
3. Assets enqueued correctly
4. Browser cache cleared

---

## Commands Reference

```bash
# Convert all page templates
npm run parse:pages

# Convert specific file
html2pattern convert src/pages/home.html -o patterns

# With custom options
html2pattern convert src/pages/home.html \
  --namespace=my-theme \
  --categories=pages,custom \
  --viewport-width=1600 \
  --description="Custom homepage template"

# Rebuild CSS after style changes
npm run build:css

# Rebuild JS after script changes
npm run build:js

# Pull page content from WordPress
npm run page:pull

# Push page content to WordPress
npm run page:push
```

---

## Related Documentation

- **[src/docs/block-markup-guide.md](../docs/block-markup-guide.md)** - Universal Block syntax reference (REQUIRED READING)
- **[src/patterns/README.md](../patterns/README.md)** - Reusable section patterns
- **[src/styles/README.md](../styles/README.md)** - CSS component classes
- **[src/context/README.md](../context/README.md)** - Data layer for dynamic content
- **[src/content/README.md](../content/README.md)** - Content collections (page content)
- **[CLAUDE.md](../../CLAUDE.md)** - Overall theme architecture

---

**Summary:**

This directory contains **full HTML page templates** that convert to WordPress block patterns via `npm run parse:pages`. Write HTML with Universal Block syntax for dynamic content, convert to patterns, insert in WordPress. Each page design version controlled, reusable, and customizable per instance.

The structure you choose depends on your project type and pages needed, but the principle remains the same: **design pages in HTML, convert to WordPress patterns, insert where needed.**

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
