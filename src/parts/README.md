# Template Parts Directory

## Overview

This directory contains **WordPress Full Site Editing (FSE) template parts**—reusable components like headers, footers, and sidebars that appear across multiple page templates.

Think of this as **global site components** that define your site's chrome and persistent UI elements.

---

## Philosophy

### The Problem This Solves

Traditional WordPress themes hard-code header and footer PHP files, making customization difficult:

- **Developer dependency** - Changes require PHP knowledge
- **No visual editing** - Must edit code to change layout
- **Limited flexibility** - Hard to create variations
- **Not version controlled** - Edits happen in WordPress only

FSE template parts solve this by making these components:
- Editable in block editor
- Reusable across templates
- Syncable as HTML files
- Version controllable

### The Solution

This directory enables **bidirectional sync** between HTML files and WordPress template parts:

1. **Edit in WordPress** - Use block editor for visual changes
2. **Pull to files** - Run `npm run parts:pull` to save to HTML
3. **Edit in IDE** - Edit HTML directly when needed
4. **Push to WordPress** - Run `npm run parts:push` to update

**Benefits:**
- Version control for site structure
- Edit visually (WordPress) or in code (HTML)
- Reusable across all templates
- Clean separation of concerns

### Core Principle

**"Global site components (header, footer) should be manageable as template parts—editable in WordPress or as HTML files."**

---

## What Are Template Parts?

### FSE Template Parts

Template parts are **reusable components** in WordPress Full Site Editing:

| **Template Part** | **Purpose** | **Used In** |
|-------------------|-------------|-------------|
| Header | Site-wide header/navigation | All pages |
| Footer | Site-wide footer | All pages |
| Sidebar | Sidebar for posts/archives | Archive templates |
| Post Meta | Author, date, categories | Single post template |

### Parts vs. Patterns

| **Template Parts** | **Patterns** |
|--------------------|--------------|
| Global site components | Reusable page sections |
| Header, footer, sidebar | Hero, CTA, features |
| Same across all pages | Inserted per page |
| Lives in `parts/` | Lives in `patterns/` |
| Synced instances | Independent instances |

**Key difference:** Template parts are **synchronized**—changing the header updates it everywhere. Patterns are **independent**—each insertion can be customized.

---

## How It Works

### Bidirectional Sync

**Pull (WordPress → HTML):**
```bash
npm run parts:pull
```

What happens:
1. Fetches template parts from WordPress
2. Converts blocks to clean HTML
3. Saves to `src/parts/*.html`

**Push (HTML → WordPress):**
```bash
npm run parts:push
```

What happens:
1. Reads HTML files from `src/parts/`
2. Converts HTML to Universal Blocks
3. Updates template parts in WordPress

### File Flow

```
WordPress Block Editor
    ↓ (npm run parts:pull)
src/parts/header.html
    ↓ (Edit in IDE)
src/parts/header.html
    ↓ (npm run parts:push)
WordPress Block Editor
```

---

## Directory Structure

### Typical Organization

```
src/parts/
├── README.md           # This file
├── header.html         # Site header/navigation
├── footer.html         # Site footer
├── sidebar.html        # Sidebar for posts/archives (optional)
└── post-meta.html      # Post metadata display (optional)
```

**Naming convention:** Must match WordPress template part slugs exactly.

### Common Template Parts

**Essential (most sites):**
- `header.html` - Site header and navigation
- `footer.html` - Site footer

**Optional (as needed):**
- `sidebar.html` - Sidebar for blog/archives
- `post-meta.html` - Post author/date/categories
- `comments.html` - Comments section
- `newsletter.html` - Newsletter signup
- `breadcrumbs.html` - Breadcrumb navigation

---

## Template Part Examples

### Header

```html
<!-- src/parts/header.html -->
<header class="header">
    <div class="content">
        <nav class="header-nav">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2">
                <img src="{{ site.theme.link }}/public/logo.svg" alt="Site Logo" class="h-8">
                <span class="font-bold">{{ site.name }}</span>
            </a>

            <!-- Primary Navigation -->
            <div class="flex items-center gap-6">
                {% for item in primary_menu.items %}
                    <a href="{{ item.link }}" class="nav-link">
                        {{ item.title }}
                    </a>
                {% endfor %}
            </div>

            <!-- CTA Button -->
            <a href="/contact" class="btn btn-primary">Get Started</a>
        </nav>
    </div>
</header>
```

**Uses:**
- `primary_menu` from `src/context/navigation.php`
- CSS classes from `src/styles/components/navigation.css`

### Footer

```html
<!-- src/parts/footer.html -->
<footer class="section theme-night">
    <div class="content">
        <div class="footer-content">
            <!-- Footer Columns -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="h5">{{ site.name }}</h3>
                    <p class="body-sm mt-4">{{ site.description }}</p>
                </div>

                <!-- Links -->
                <div>
                    <h3 class="h5">Company</h3>
                    <nav class="footer-nav mt-4">
                        {% for item in footer_menu.items %}
                            <a href="{{ item.link }}">{{ item.title }}</a>
                        {% endfor %}
                    </nav>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="h5">Contact</h3>
                    <div class="footer-nav mt-4">
                        <a href="mailto:{{ site_settings.contact_email }}">
                            {{ site_settings.contact_email }}
                        </a>
                        <a href="tel:{{ site_settings.phone }}">
                            {{ site_settings.phone }}
                        </a>
                    </div>
                </div>

                <!-- Social -->
                <div>
                    <h3 class="h5">Follow Us</h3>
                    <div class="flex gap-4 mt-4">
                        <a href="{{ site_settings.social_links.twitter }}" aria-label="Twitter">
                            <svg><!-- Twitter icon --></svg>
                        </a>
                        <!-- More social links -->
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-primary-800 mt-12 pt-8 text-center">
                <p class="meta">© {{ "now"|date("Y") }} {{ site.name }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
```

**Uses:**
- `footer_menu` from `src/context/navigation.php`
- `site_settings` from `src/context/site-settings.php`

### Sidebar

```html
<!-- src/parts/sidebar.html -->
<aside class="sidebar">
    <!-- Search -->
    <div class="v-card">
        <h3 class="h5">Search</h3>
        <form role="search" method="get" action="/" class="mt-4">
            <input type="search" name="s" placeholder="Search..." class="w-full">
            <button type="submit" class="btn btn-primary w-full mt-2">Search</button>
        </form>
    </div>

    <!-- Recent Posts -->
    <div class="v-card mt-6">
        <h3 class="h5">Recent Posts</h3>
        <div class="stack-sm mt-4">
            <div loopsource="recent_posts" loopvariable="post">
                <a href="{{ post.link }}" class="body-sm">{{ post.title }}</a>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="v-card mt-6">
        <h3 class="h5">Categories</h3>
        <nav class="stack-sm mt-4">
            {% for category in categories %}
                <a href="{{ category.link }}" class="body-sm">
                    {{ category.name }} ({{ category.count }})
                </a>
            {% endfor %}
        </nav>
    </div>
</aside>
```

**Uses:**
- `recent_posts` from `src/context/recent-posts.php`
- `categories` from `src/context/categories.php` (if created)

### Post Meta

```html
<!-- src/parts/post-meta.html -->
<div class="post-meta flex items-center gap-6 text-sm text-primary-600">
    <!-- Author -->
    <div class="flex items-center gap-2">
        <img src="{{ post.author.avatar }}" alt="{{ post.author.name }}" class="w-8 h-8 rounded-full">
        <a href="{{ post.author.link }}">{{ post.author.name }}</a>
    </div>

    <!-- Date -->
    <time datetime="{{ post.date }}">
        {{ post.date|date("M j, Y") }}
    </time>

    <!-- Categories -->
    <div>
        {% for category in post.categories %}
            <a href="{{ category.link }}" class="badge">
                {{ category.name }}
            </a>
        {% endfor %}
    </div>

    <!-- Reading Time -->
    <span>{{ post.meta('reading_time')|default(5) }} min read</span>
</div>
```

**Uses:**
- `post` from WordPress context (available on single posts)

---

## Workflow

### Starting Fresh (WordPress First)

```bash
# 1. Create template parts in WordPress
#    - Appearance → Editor → Template Parts
#    - Create "Header" and "Footer"
#    - Design using block editor

# 2. Pull to HTML files
npm run parts:pull

# 3. Now you have version-controlled template parts
git add src/parts/
git commit -m "Add header and footer template parts"
```

### Starting from HTML

```bash
# 1. Create HTML files
touch src/parts/header.html
touch src/parts/footer.html

# 2. Add HTML content
# (Edit files)

# 3. Push to WordPress
npm run parts:push

# 4. Template parts now available in WordPress
#    - Appearance → Editor → Templates
#    - Insert template parts into templates
```

### Iterative Development

```bash
# Edit in WordPress
# Pull to sync
npm run parts:pull
git diff src/parts/    # Review changes
git add src/parts/
git commit -m "Update header navigation"

# OR

# Edit HTML files
# Push to sync
npm run parts:push
# Verify in WordPress
```

---

## Using Template Parts in Templates

### In FSE Templates

**WordPress Template Editor:**
1. Appearance → Editor → Templates
2. Edit template (e.g., "Single Post")
3. Click "+" and search "Template Part"
4. Select "Header" or "Footer"
5. Template part is inserted (synced instance)

**In HTML Templates (`src/templates/`):**
```html
<!-- src/templates/single.html -->
<!DOCTYPE html>
<html>
<body>
    <!-- Insert header template part -->
    <template-part slug="header"></template-part>

    <!-- Page content -->
    <main class="section">
        <div class="content">
            <article>
                <h1>{{ post.title }}</h1>
                <div>{{ post.content }}</div>
            </article>
        </div>
    </main>

    <!-- Insert footer template part -->
    <template-part slug="footer"></template-part>
</body>
</html>
```

---

## Structure by Project Type

### Blog / Publication Site

```
src/parts/
├── header.html           # Site header with navigation
├── footer.html           # Footer with links and copyright
├── sidebar.html          # Blog sidebar (recent, categories)
├── post-meta.html        # Author, date, categories
└── newsletter.html       # Newsletter signup box
```

### Corporate / Business Site

```
src/parts/
├── header.html           # Corporate header
├── footer.html           # Multi-column footer
└── breadcrumbs.html      # Breadcrumb navigation
```

### E-commerce Site

```
src/parts/
├── header.html           # Header with cart icon
├── footer.html           # Footer with policies
├── product-sidebar.html  # Product filters
└── trust-badges.html     # Trust/security badges
```

### Marketing / Landing Page Site

```
src/parts/
├── header-minimal.html   # Minimal header for conversions
├── footer-minimal.html   # Minimal footer
└── sticky-cta.html       # Sticky call-to-action bar
```

---

## Best Practices

### 1. Keep Parts Focused

**Good:**
```
header.html       # Just header
footer.html       # Just footer
sidebar.html      # Just sidebar
```

**Bad:**
```
layout.html       # Header + footer + sidebar (too much)
```

Each part should have one clear responsibility.

### 2. Use Context for Data

**Good:**
```html
<!-- Data from src/context/navigation.php -->
<nav>
    {% for item in primary_menu.items %}
        <a href="{{ item.link }}">{{ item.title }}</a>
    {% endfor %}
</nav>
```

**Bad:**
```html
<!-- Inline data fetching -->
<nav setvariable="menu" setexpression="timber.get_menu('primary')">
    {% for item in menu.items %}
        <a href="{{ item.link }}">{{ item.title }}</a>
    {% endfor %}
</nav>
```

Use context filters for better MVC separation.

### 3. Design Mobile-First

```html
<!-- Responsive header -->
<header class="header">
    <nav class="header-nav">
        <!-- Logo (always visible) -->
        <a href="/" class="logo">
            <img src="..." alt="Logo">
        </a>

        <!-- Desktop nav (hidden on mobile) -->
        <div class="hidden md:flex gap-6">
            {% for item in primary_menu.items %}
                <a href="{{ item.link }}">{{ item.title }}</a>
            {% endfor %}
        </div>

        <!-- Mobile menu button -->
        <button class="md:hidden" x-data @click="$store.mobile_menu.toggle()">
            Menu
        </button>
    </nav>
</header>
```

### 4. Sync Regularly

```bash
# After WordPress edits
npm run parts:pull
git diff src/parts/
git add src/parts/
git commit -m "Update footer social links"

# Before making HTML edits
npm run parts:pull  # Get latest from WordPress
# Edit files
npm run parts:push  # Push changes back
```

### 5. Document Dependencies

```html
<!--
  Header Template Part

  Dependencies:
  - Context: primary_menu (src/context/navigation.php)
  - Styles: src/styles/components/navigation.css
  - Scripts: src/scripts/components/mobile-menu.js (Alpine.js)

  Used in:
  - All templates via FSE
-->
<header class="header">
    ...
</header>
```

---

## Advanced Patterns

### Conditional Headers/Footers

```html
<!-- src/parts/header.html -->
<header class="header">
    <!-- Default header -->
    <nav conditionalvisibility="true" conditionalexpression="not is_page('landing')" class="header-nav">
        <!-- Full navigation -->
    </nav>

    <!-- Minimal header for landing pages -->
    <nav conditionalvisibility="true" conditionalexpression="is_page('landing')" class="header-nav-minimal">
        <!-- Logo only -->
    </nav>
</header>
```

### Sticky Elements

```html
<!-- src/parts/header.html -->
<header class="fixed inset-x-0 top-0 z-50 backdrop-blur">
    <nav class="header-nav">
        <!-- Navigation -->
    </nav>
</header>
```

### Dark Mode Toggle

```html
<!-- src/parts/header.html -->
<header class="header">
    <nav class="header-nav">
        <!-- ... -->

        <!-- Dark mode toggle -->
        <button
            x-data
            @click="document.body.classList.toggle('theme-night')"
            class="btn btn-sm">
            Toggle Dark Mode
        </button>
    </nav>
</header>
```

---

## Integration with Theme Workflow

### With Context Filters

```php
// src/context/navigation.php
add_filter('timber/context', function($context) {
    $context['primary_menu'] = Timber::get_menu('primary');
    $context['footer_menu'] = Timber::get_menu('footer');
    return $context;
});
```

```html
<!-- src/parts/header.html uses the data -->
<nav>
    {% for item in primary_menu.items %}
        <a href="{{ item.link }}">{{ item.title }}</a>
    {% endfor %}
</nav>
```

### With Styles

```css
/* src/styles/components/navigation.css */
.header {
    position: fixed;
    inset-inline: 0;
    top: calc(var(--spacing) * 4);
    z-index: 50;
}

.header-nav {
    display: flex;
    justify-content: space-between;
    padding: calc(var(--spacing) * 3);
    background-color: var(--color-card);
    backdrop-filter: blur(12px);
}
```

### With Templates

```html
<!-- templates/index.html -->
<template-part slug="header"></template-part>
<main>...</main>
<template-part slug="footer"></template-part>
```

---

## Troubleshooting

### Template Part Not Appearing

1. Verify slug matches WordPress exactly
2. Run `npm run parts:pull` to see current parts
3. Check `parts/` directory for generated PHP
4. Clear WordPress cache

### Changes Not Syncing

**After WordPress edits:**
```bash
npm run parts:pull
git status  # See if files changed
```

**After HTML edits:**
```bash
npm run parts:push
# Check WordPress editor to verify
```

### Styles Not Applying

1. Rebuild CSS: `npm run build:css`
2. Clear browser cache
3. Verify class names match `src/styles/`

---

## Commands Reference

```bash
# Pull all template parts from WordPress
npm run parts:pull

# Push all template parts to WordPress
npm run parts:push

# WP-CLI commands (manual)
cd ../../..  # Navigate to WordPress root
wp blocks:html parts --all     # Pull to HTML
wp html:blocks src/parts --all # Push to WordPress
```

---

## Related Documentation

- **[src/templates/README.md](../templates/README.md)** - Full page templates
- **[src/patterns/README.md](../patterns/README.md)** - Reusable patterns
- **[src/context/README.md](../context/README.md)** - Data layer
- **[src/styles/README.md](../styles/README.md)** - CSS architecture
- **[CLAUDE.md](../../CLAUDE.md)** - Theme architecture

---

**Summary:**

This directory contains **WordPress FSE template parts**—global site components like headers and footers that appear across all templates. Bidirectionally synced between WordPress block editor and HTML files for version control and flexible editing.

The structure you choose depends on your site's needs, but the principle remains the same: **global components managed as template parts, synced between WordPress and code.**

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
