# Broke Theme

> A modern WordPress theme built with Timber/Twig, Tailwind CSS v4, and optional block patterns.

![Broke FSE Theme](screenshot.png)

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](LICENSE)

**Website:** [broke.dev](https://broke.dev)
**Author:** Daniel Snell
**Contact:** [daniel@broke.dev](mailto:daniel@broke.dev)

## Features

- 🌲 **Timber/Twig Templates** - Clean MVC architecture with traditional WordPress template hierarchy
- 🎨 **Tailwind CSS v4** - Utility-first CSS with automatic tree-shaking
- 📐 **Traditional Theme Structure** - Standard WordPress template files (index.php, single.php, etc.)
- 🔄 **Bidirectional Content Sync** - Edit content as markdown or in WordPress
- 🎯 **Context Filters** - Clean data layer for dynamic content
- 🧩 **Reusable Partials** - Component-based Twig templates
- 📦 **ACF JSON Support** - Version control for custom fields
- ⚙️ **Optional Block Patterns** - Keep block editor functionality alongside traditional templates
- 🎯 **PNPM Recommended** - Fast, efficient package management
- 📝 **SCSS Optional** - Use Bootstrap-style SCSS alongside or instead of Tailwind

---

## Table of Contents

- [Quick Start](#quick-start)
- [Requirements](#requirements)
- [Installation](#installation)
- [Development Workflow](#development-workflow)
- [CLI Commands Reference](#cli-commands-reference)
- [Architecture](#architecture)
- [Directory Structure](#directory-structure)
- [Content Management](#content-management)
- [Customization](#customization)
- [Deployment](#deployment)
- [Contributing](#contributing)

---

## Quick Start

### For New Projects (Recommended)

Use the automated setup script to create your own theme:

```bash
# Option 1: Use GitHub Template (easiest)
# Click "Use this template" on GitHub, then:
git clone https://github.com/yourusername/your-theme.git wp-content/themes/your-theme
cd wp-content/themes/your-theme
./setup.sh

# Option 2: Use Degit (no git history)
npx degit DanielRSnell/broke-fse wp-content/themes/your-theme
cd wp-content/themes/your-theme
./setup.sh

# Option 3: Manual clone
git clone https://github.com/DanielRSnell/broke-fse.git wp-content/themes/your-theme
cd wp-content/themes/your-theme
./setup.sh
```

The setup script will:
- Remove boilerplate git history
- Update theme name, author, and metadata
- Initialize new git repository
- Guide you through customization

Then install and build:
```bash
composer install
pnpm install
pnpm run build:css
pnpm run build:js
```

**Full Template Setup Guide:** [.github/TEMPLATE_SETUP.md](.github/TEMPLATE_SETUP.md)

### For Development/Testing Only

To test the boilerplate without customization:

```bash
cd wp-content/themes/
git clone https://github.com/DanielRSnell/broke-fse.git broke
cd broke
composer install
pnpm install
pnpm run build:css
pnpm run build:js

# Activate in WordPress admin
# Appearance → Themes → Broke FSE → Activate
```

⚠️ **Warning:** This keeps the boilerplate branding and git history. Use the setup script for actual projects.

---

## Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 8.0 or higher
- **Node.js:** 18.0 or higher
- **PNPM:** 8.0 or higher (recommended) or NPM
- **Composer:** For PHP dependencies (Timber, Parsedown)
- **WP-CLI:** For content sync commands (optional but recommended)

---

## Installation

### New Project Setup (Recommended)

**Step 1: Create Your Theme**

Choose one of these methods:

**Method 1: GitHub Template** (Easiest)
1. Click "Use this template" button on GitHub
2. Create your new repository
3. Clone your new repo:
   ```bash
   git clone https://github.com/yourusername/your-theme.git wp-content/themes/your-theme
   cd wp-content/themes/your-theme
   ```

**Method 2: Degit** (No Git History)
```bash
npx degit DanielRSnell/broke-fse wp-content/themes/your-theme
cd wp-content/themes/your-theme
```

**Method 3: Manual Clone**
```bash
git clone https://github.com/DanielRSnell/broke-fse.git wp-content/themes/your-theme
cd wp-content/themes/your-theme
```

**Step 2: Run Setup Script**

```bash
./setup.sh
```

Follow the prompts to enter:
- Theme name
- Theme slug (text domain)
- Author information
- URIs and description

The script automatically updates all theme files with your information.

### Development/Testing Setup

For testing the boilerplate as-is (without customization):

```bash
cd wp-content/themes/
git clone https://github.com/DanielRSnell/broke-fse.git broke
cd broke
```

### 2. Install PHP Dependencies

```bash
composer install
```

This installs:
- **Timber** - Twig templating for WordPress
- **Parsedown** - Markdown parsing for content collections

### 3. Install Node Dependencies

```bash
pnpm install
```

Or with NPM:
```bash
npm install
```

### 4. Build Assets

```bash
# Build CSS (Tailwind)
pnpm run build:css

# Build JavaScript
pnpm run build:js
```

### 5. Activate Theme

1. Go to WordPress admin → **Appearance → Themes**
2. Find **Broke**
3. Click **Activate**

---

## Development Workflow

### Local Development

```bash
# Watch CSS changes (rebuilds on save)
pnpm run watch:css

# Watch JavaScript changes
pnpm run watch:js

# Run both in parallel (separate terminals)
pnpm run watch:css & pnpm run watch:js
```

### Editing Workflow

1. **Edit Twig templates** in `src/views/`
2. **Edit context filters** in `src/context/` (for data)
3. **Rebuild CSS/JS:**
   ```bash
   pnpm run build:css
   pnpm run build:js
   ```
4. **Refresh browser** to see changes

---

## CLI Commands Reference

### Build Commands

| Command | Description |
|---------|-------------|
| `pnpm run build` | Build JavaScript with Vite |
| `pnpm run build:js` | Build JavaScript (alias) |
| `pnpm run build:css` | Build Tailwind CSS |
| `pnpm run build:scss` | Build SCSS (optional) |

### Watch Commands

| Command | Description |
|---------|-------------|
| `pnpm run dev` | Start Vite dev server |
| `pnpm run watch:css` | Watch CSS changes |
| `pnpm run watch:js` | Watch JavaScript changes |

### Clean Commands

| Command | Description |
|---------|-------------|
| `pnpm run clean:all` | Clean all generated files |
| `pnpm run clean:production` | Clean `_production/` directory |
| `pnpm run clean:editor` | Clean `_editor/` directory |
| `pnpm run clean:patterns` | Clean generated pattern PHP files |
| `pnpm run clean:parts` | Clean template parts HTML |
| `pnpm run clean:templates` | Clean template HTML files |
| `pnpm run clean:css` | Clean CSS files only |
| `pnpm run clean:js` | Clean JavaScript files only |

### Content Sync (WordPress ↔ Files)

#### Posts/Resources/Projects (Markdown)

| Command | Description |
|---------|-------------|
| `pnpm run content:pull` | Pull posts from WordPress to markdown |
| `pnpm run content:push` | Push markdown files to WordPress |

#### Pages (HTML Sections)

| Command | Description |
|---------|-------------|
| `pnpm run page:pull` | Pull pages from WordPress to HTML sections |
| `pnpm run page:push` | Push HTML sections to WordPress pages |

#### Template Parts

| Command | Description |
|---------|-------------|
| `pnpm run parts:pull` | Pull template parts from WordPress |
| `pnpm run parts:push` | Push template parts to WordPress |
| `pnpm run parts:download` | Download all template parts |

#### Templates

| Command | Description |
|---------|-------------|
| `pnpm run template:pull` | Pull templates from WordPress |
| `pnpm run template:push` | Push templates to WordPress |
| `pnpm run template:download` | Download all templates |

### Code Formatting

| Command | Description |
|---------|-------------|
| `pnpm run format` | Format all code (Prettier) |
| `pnpm run format:check` | Check code formatting |

---

## Architecture

### Tech Stack

- **WordPress 6.0+** - Traditional theme with template hierarchy
- **Timber/Twig** - MVC templating engine for clean separation of concerns
- **Tailwind CSS v4** - Utility-first CSS framework
- **Vite** - JavaScript bundler
- **PNPM** - Package manager
- **WP-CLI** - Command-line WordPress management
- **ACF PRO** - Advanced Custom Fields (optional)

### Design Patterns

**MVC Architecture:**
- **Model:** WordPress data (posts, terms, options)
- **View:** Twig templates (`src/views/*.twig`)
- **Controller:** PHP templates + Context filters (`src/context/`)

**Example:**
```php
// src/context/recent-posts.php (Controller)
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts([
        'posts_per_page' => 5
    ]);
    return $context;
});
```

```twig
{# src/views/home.twig (View) #}
{% for post in recent_posts %}
    <h3>{{ post.title }}</h3>
    <p>{{ post.excerpt }}</p>
{% endfor %}
```

**Template Hierarchy:**
- WordPress routes request (e.g., single post)
- PHP template loads (`single.php`)
- PHP prepares context with Timber
- Twig renders view (`src/views/single.twig`)
- Clean HTML output

---

## Directory Structure

```
broke-theme/
├── _production/          # Built CSS/JS (frontend + editor)
├── _editor/              # Editor-only assets
├── acf-json/             # ACF field group definitions (JSON)
│   └── README.md
├── includes/             # PHP functionality
│   ├── CLI/              # WP-CLI commands
│   │   ├── content-cli/  # Content sync
│   │   ├── page-cli/     # Page sync
│   │   └── ...
│   ├── context/          # Additional context filters
│   ├── enqueue.php       # Asset loading
│   └── helpers.php       # Helper functions
├── patterns/             # Optional WordPress patterns (PHP)
├── public/               # Static assets (images, icons, fonts)
│   └── README.md
├── src/                  # Source files
│   ├── content/          # Content collections (markdown)
│   │   ├── posts/
│   │   ├── pages/
│   │   ├── resources/
│   │   └── projects/
│   ├── context/          # Timber context filters (MVC data layer)
│   │   ├── archive.php
│   │   ├── related.php
│   │   └── params.php
│   ├── docs/             # Documentation
│   ├── scripts/          # JavaScript source
│   │   ├── components/
│   │   │   ├── form-handler.js
│   │   │   └── index.js
│   │   └── main.js
│   ├── scss/             # SCSS source (optional)
│   ├── styles/           # Tailwind CSS source
│   │   ├── components/   # Component styles
│   │   ├── core/         # Core styles
│   │   ├── themes/       # Theme variants (dark mode)
│   │   └── tailwind.css  # Main entry point
│   └── views/            # Twig templates ⭐
│       ├── index.twig
│       ├── single.twig
│       ├── page.twig
│       ├── archive.twig
│       ├── partials/     # Reusable components
│       │   ├── header.twig
│       │   ├── footer.twig
│       │   └── post-card.twig
│       └── woocommerce/  # WooCommerce templates
│           ├── single-product.twig
│           └── archive-product.twig
├── index.php             # PHP template files ⭐
├── single.php
├── page.php
├── archive.php
├── 404.php
├── composer.json         # PHP dependencies
├── functions.php         # Theme initialization
├── package.json          # Node dependencies & scripts
├── pnpm-lock.yaml        # PNPM lock file
├── style.css             # WordPress theme header
├── theme.json            # Theme configuration
└── vite.config.js        # Vite configuration
```

---

## Content Management

### Content Collections (Markdown Sync)

Edit content as markdown files with YAML frontmatter:

**Example:** `src/content/resources/example.md`
```markdown
---
title: "My Resource"
slug: "my-resource"
status: "publish"
author: 1
custom_fields:
  featured: true
  table_of_contents:
    - order: 1
      section: "Introduction"
      anchor: "intro"
---

## Introduction

Your content here in clean markdown...
```

**Workflow:**
```bash
# Pull from WordPress to markdown
pnpm run content:pull

# Edit markdown files in src/content/
# ...

# Push to WordPress
pnpm run content:push
```

### Pages (HTML Section Sync)

Each page is split into separate HTML section files:

**Example:** `src/content/pages/home/`
```
home/
├── section-1.html
├── section-2.html
└── section-3.html
```

**Workflow:**
```bash
# Pull pages from WordPress
pnpm run page:pull

# Edit section HTML files
# ...

# Push back to WordPress
pnpm run page:push
```

---

## Customization

### Colors

Edit colors in `theme.json`:

```json
{
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "primary",
          "color": "oklch(0.45 0.20 264)",
          "name": "Primary"
        }
      ]
    }
  }
}
```

Colors automatically sync to `src/styles/core/colors.css`.

### Typography

Edit fonts in `theme.json`:

```json
{
  "settings": {
    "typography": {
      "fontFamilies": [
        {
          "slug": "system-sans",
          "fontFamily": "-apple-system, BlinkMacSystemFont, 'Segoe UI', ...",
          "name": "System Sans"
        }
      ]
    }
  }
}
```

### Custom Components

1. Create component file:
   ```bash
   touch src/styles/components/my-component.css
   ```

2. Define styles using CSS custom properties:
   ```css
   /* src/styles/components/my-component.css */
   @layer components {
     .my-component {
       display: flex;
       align-items: center;
       gap: var(--spacing-4);
       padding: var(--spacing-6);
     }
   }
   ```

3. Import in `tailwind.css`:
   ```css
   @import './components/my-component.css' layer(components);
   ```

4. Rebuild:
   ```bash
   pnpm run build:css
   ```

### Custom Post Types (ACF)

1. Create in WordPress: **ACF → Post Types → Add New**
2. Configure and save (auto-generates JSON in `acf-json/`)
3. Commit JSON to Git:
   ```bash
   git add acf-json/post_type_*.json
   git commit -m "Add custom post type"
   ```

See [acf-json/README.md](acf-json/README.md) for detailed instructions.

---

## Deployment

### Production Build

```bash
# Clean generated files
pnpm run clean:all

# Build production assets
pnpm run build:css
pnpm run build:js
```

### Deploy Checklist

- [ ] Run production build commands
- [ ] Commit all generated files (`_production/`, `patterns/`, etc.)
- [ ] Update version in `style.css` and `package.json`
- [ ] Test on staging environment
- [ ] Sync ACF fields on production (`ACF → Tools → Sync`)
- [ ] Clear WordPress cache
- [ ] Test FSE editor
- [ ] Verify frontend rendering

### Git Workflow

```bash
# Stage changes
git add .

# Commit
git commit -m "Your commit message"

# Push to remote
git push origin main
```

### Server Deployment

**Option 1: Git Pull**
```bash
# On server
cd wp-content/themes/broke-theme/
git pull origin main
composer install --no-dev
pnpm install --prod
```

**Option 2: SFTP/FTP**
- Upload entire theme directory
- Ensure `_production/` directory is uploaded
- Run `composer install` on server

---

## Advanced Features

### Timber/Twig Templating

This theme uses **Timber** to bring Twig templating to WordPress, providing a clean MVC architecture.

**Key Benefits:**
- ✅ Clean separation of PHP logic and HTML presentation
- ✅ Reusable template partials
- ✅ Context filters for data management
- ✅ Readable, maintainable code
- ✅ IDE support with autocomplete

#### Template Structure

**PHP Template (Controller):**
```php
// single.php
<?php
$context = Timber::context();
$context['post'] = Timber::get_post();
Timber::render('single.twig', $context);
```

**Twig Template (View):**
```twig
{# src/views/single.twig #}
{% include 'partials/header.twig' %}

<article class="single-post">
    <h1>{{ post.title }}</h1>
    <time>{{ post.date|date('F j, Y') }}</time>
    <div>{{ post.content }}</div>
</article>

{% include 'partials/footer.twig' %}
```

#### Context Filters (MVC Data Layer)

**✅ The proper way to fetch data:**

```php
// src/context/recent-posts.php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts([
        'posts_per_page' => 5
    ]);
    return $context;
});
```

```twig
{# Template - Clean and simple! #}
{% for post in recent_posts %}
    <h3>{{ post.title }}</h3>
    <p>{{ post.excerpt }}</p>
{% endfor %}
```

#### Twig Syntax Examples

**Variables and Filters:**
```twig
{{ post.title }}
{{ post.date|date('F j, Y') }}
{{ post.excerpt|length }}
{{ post.content|striptags|truncate(200) }}
```

**Conditionals:**
```twig
{% if post.thumbnail %}
    <img src="{{ post.thumbnail.src }}" alt="{{ post.title }}">
{% endif %}

{% if user.ID > 0 %}
    <p>Welcome, {{ user.display_name }}!</p>
{% endif %}
```

**Loops:**
```twig
{% for post in posts %}
    <article>
        <h2>{{ post.title }}</h2>
        <p>{{ post.excerpt }}</p>
    </article>
{% endfor %}
```

**Custom Fields (ACF):**
```twig
{{ post.meta('subtitle') }}
{{ post.meta('featured') }}

{% for item in post.meta('table_of_contents') %}
    <li><a href="#{{ item.anchor }}">{{ item.section }}</a></li>
{% endfor %}
```

**Full Documentation:** [src/views/README.md](src/views/README.md)

### SCSS Support

Optional Bootstrap-style SCSS alongside Tailwind:

```bash
# Build SCSS
pnpm run build:scss
```

Source files in `src/scss/`. See [src/scss/README.md](src/scss/README.md).

---

## Troubleshooting

### Assets Not Loading

1. Rebuild assets:
   ```bash
   pnpm run build:css
   pnpm run build:js
   ```
2. Clear WordPress cache
3. Hard refresh browser (Cmd+Shift+R / Ctrl+Shift+R)

### Patterns Not Showing

1. Check `patterns/` directory for generated PHP files
2. Clear WordPress cache
3. Verify pattern registration in WordPress admin

### Styles Not Applying

1. Check Tailwind sources (`@source` paths in `tailwind.css`)
2. Verify classes exist in HTML files
3. Rebuild:
   ```bash
   pnpm run clean:css
   pnpm run build:css
   ```

### Content Sync Issues

1. Ensure WP-CLI is installed:
   ```bash
   wp --info
   ```
2. Check file permissions on `src/content/`
3. Verify WordPress is running

---

## Documentation

### Core Documentation
- **[CLAUDE.md](CLAUDE.md)** - AI assistant instructions and theme architecture
- **[README.md](README.md)** - This file (getting started, CLI reference)

### Source Directory Guides
- **[src/views/README.md](src/views/README.md)** - Twig templates (traditional theme structure) ⭐
- **[src/context/README.md](src/context/README.md)** - Timber context filters (MVC data layer) ⭐
- **[src/content/README.md](src/content/README.md)** - Content collections (markdown & HTML sync)
- **[src/scripts/README.md](src/scripts/README.md)** - JavaScript source files

### Styles Documentation
- **[src/styles/README.md](src/styles/README.md)** - CSS architecture overview
- **[src/styles/core/README.md](src/styles/core/README.md)** - Theme layer (colors, core styles)
- **[src/styles/components/README.md](src/styles/components/README.md)** - Component layer
- **[src/styles/themes/README.md](src/styles/themes/README.md)** - Theme variants (dark mode)
- **[src/styles/vendor/README.md](src/styles/vendor/README.md)** - Third-party overrides
- **[src/scss/README.md](src/scss/README.md)** - Optional SCSS usage

### Other Directories
- **[acf-json/README.md](acf-json/README.md)** - ACF custom fields guide
- **[public/README.md](public/README.md)** - Static assets guide

---

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit changes: `git commit -m "Add my feature"`
4. Push to branch: `git push origin feature/my-feature`
5. Submit a pull request

---

## License

This theme is licensed under the [GPL-2.0-or-later](LICENSE).

---

## Credits

- **Tailwind CSS** - [https://tailwindcss.com/](https://tailwindcss.com/)
- **Timber** - [https://timber.github.io/timber/](https://timber.github.io/timber/)
- **WordPress** - [https://wordpress.org/](https://wordpress.org/)
- **Vite** - [https://vitejs.dev/](https://vitejs.dev/)

---

## Support

- **Website:** [broke.dev](https://broke.dev)
- **Email:** [daniel@broke.dev](mailto:daniel@broke.dev)
- **Issues:** [GitHub Issues](https://github.com/DanielRSnell/broke-fse/issues)
- **Documentation:** [GitHub Wiki](https://github.com/DanielRSnell/broke-fse/wiki)
- **Community:** [GitHub Discussions](https://github.com/DanielRSnell/broke-fse/discussions)

---

**Made with ❤️ for the WordPress community**
