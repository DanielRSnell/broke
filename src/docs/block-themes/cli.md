# Broke CLI Documentation

Complete reference for all CLI commands available in the Portfolio Theme.

---

## Table of Contents

1. [Overview](#overview)
2. [WP-CLI Commands](#wp-cli-commands)
   - [Content Commands](#content-commands)
   - [Page Commands](#page-commands)
   - [Template Commands](#template-commands)
3. [NPM Scripts](#npm-scripts)
   - [Build Commands](#build-commands)
   - [Parse Commands](#parse-commands)
   - [Development Commands](#development-commands)
4. [Custom HTML Elements](#custom-html-elements)
5. [Workflow Examples](#workflow-examples)

---

## Overview

The Portfolio Theme includes a comprehensive CLI toolkit for managing content, templates, and build processes. Commands are split between:

- **WP-CLI Commands** - WordPress integration (content, pages, templates)
- **NPM Scripts** - Build tools and HTML-to-pattern conversion

All commands maintain bidirectional conversion between developer-friendly formats (HTML, Markdown) and WordPress blocks.

---

## WP-CLI Commands

### Content Commands

Pull and push WordPress posts as Markdown files.

#### `wp content pull`

Convert WordPress posts to Markdown files in `src/content/`.

**Usage:**

```bash
# Pull a specific post by ID
wp content pull 123

# Pull all posts of a specific type
wp content pull --post_type=post
wp content pull --post_type=portfolio
wp content pull --post_type=resource

# Pull all posts from all post types
wp content pull --all
```

**Output:**

- Creates markdown files in `src/content/{post-type}/{slug}.md`
- Includes frontmatter with post metadata (title, status, date, etc.)
- Converts post content to clean Markdown

**Example Output File:** `src/content/portfolio/my-project.md`

```markdown
---
title: My Project
slug: my-project
status: publish
date: 2025-01-26
---

# Project Overview

This is my project content...
```

---

#### `wp content push`

Convert Markdown files to WordPress posts with universal blocks.

**Usage:**

```bash
# Push a specific markdown file
wp content push src/content/portfolio/my-project.md

# Push all files of a specific post type
wp content push --post_type=portfolio

# Push all markdown files
wp content push --all
```

**Behavior:**

- Converts Markdown → HTML → Universal Blocks
- Updates existing posts (matches by slug)
- Creates new posts if not found
- Preserves frontmatter metadata

---

### Page Commands

Pull and push WordPress pages as HTML section files.

#### `wp page pull`

Convert WordPress pages to HTML section files in `src/content/pages/`.

**Usage:**

```bash
# Pull a specific page by ID
wp page pull 42

# Pull all pages
wp page pull --all

# Pull all pages of a specific post type
wp page pull --post_type=page
```

**Output:**

- Creates directory: `src/content/pages/{page-slug}/`
- Splits page into sections: `section-1.html`, `section-2.html`, etc.
- Each top-level block becomes a separate section file
- Strips WordPress-generated IDs for clean HTML

**Example:**

```
src/content/pages/
└── about/
    ├── section-1.html  (Hero section)
    ├── section-2.html  (Skills section)
    └── section-3.html  (Experience section)
```

---

#### `wp page push`

Convert HTML section files to WordPress pages with universal blocks.

**Usage:**

```bash
# Push a specific page by slug
wp page push about

# Push all pages
wp page push --all
```

**Behavior:**

- Reads all `section-*.html` files from `src/content/pages/{slug}/`
- Converts HTML → Universal Blocks using html2blocks parser
- Combines sections into single page content
- Updates page in WordPress database

---

### Template Commands

Pull and push WordPress templates with custom HTML element support.

#### `wp template pull`

Convert WordPress template blocks to clean HTML in `src/templates/`.

**Usage:**

```bash
# Pull index template
wp template pull index

# Pull single post template
wp template pull single

# Pull any template by name
wp template pull archive
```

**Output:**

- Creates HTML file: `src/templates/{template-name}.html`
- Converts WordPress blocks to HTML
- **Special:** Converts core blocks to custom elements:
  - `core/template-part` → `<Part slug="header"></Part>`
  - `core/pattern` → `<Pattern slug="hero"></Pattern>`
  - `core/post-content` → `<Content></Content>`

**Example Output:** `src/templates/index.html`

```html
<div>
  <Part slug="header" theme="portfolio"></Part>

  <main class="flex-grow">
    <content></content>
  </main>

  <Part slug="footer" theme="portfolio"></Part>
</div>
```

---

#### `wp template push`

Convert HTML templates to WordPress block markup.

**Usage:**

```bash
# Push index template
wp template push index

# Push single template
wp template push single
```

**Behavior:**

- Reads HTML from `src/templates/{template-name}.html`
- Converts custom elements back to WordPress blocks
- Converts all HTML → Universal Blocks
- Writes to `templates/{template-name}.html`

**Custom Element Conversion:**

```html
<!-- Input HTML -->
<Part slug="header"></Part>

<!-- Output Block -->
<!-- wp:core/template-part {"slug":"header"} /-->
```

---

## NPM Scripts

### Build Commands

#### `npm run build:css`

Compile Tailwind CSS with minification.

```bash
npm run build:css
```

- **Input:** `src/styles/tailwind.css`
- **Output:** `_production/styles.css`
- **Features:** Minified, purged unused CSS

---

#### `npm run build:js`

Bundle JavaScript with Vite.

```bash
npm run build:js
```

- **Input:** `src/scripts/main.js`
- **Output:** `_production/main.js`
- **Features:** Minified, tree-shaken

---

#### `npm run build`

Build both CSS and JS.

```bash
npm run build
```

Equivalent to running `build:js` (CSS is handled separately).

---

### Parse Commands

Convert HTML files to WordPress pattern PHP files.

#### `npm run parse:pages`

Convert page HTML to PHP patterns.

```bash
npm run parse:pages
```

- **Input:** `src/pages/*.html`
- **Output:** `patterns/*.php`
- **Viewport:** 1600px
- **Category:** `pages`

---

#### `npm run parse:patterns`

Convert pattern HTML to PHP patterns.

```bash
npm run parse:patterns
```

- **Input:** `src/patterns/*.html`
- **Output:** `patterns/*.php`
- **Viewport:** 1280px
- **Category:** `patterns`, `sections`

---

#### `npm run parse:parts`

Convert template parts to block markup.

```bash
npm run parse:parts
```

- **Input:** `src/parts/*.html`
- **Output:** `parts/*.html` (block markup)
- **Uses:** html2blocks parser directly

---

#### `npm run parse:all`

Parse both pages and patterns.

```bash
npm run parse:all
```

Equivalent to `parse:pages && parse:patterns`.

---

### Development Commands

#### `npm run format`

Format code with Prettier.

```bash
npm run format
```

Formats all `.js`, `.css`, and `.html` files.

---

#### `npm run format:check`

Check code formatting without modifying files.

```bash
npm run format:check
```

Useful for CI/CD pipelines.

---

## Custom HTML Elements

The theme supports custom HTML elements that convert to WordPress blocks.

### Available Elements

#### `<Part>` - Template Part

Include a template part from `parts/` directory.

```html
<Part slug="header"></Part> <Part slug="footer" theme="portfolio"></Part>
```

**Attributes:**

- `slug` (required) - Template part slug
- `theme` (optional) - Theme name
- `class` (optional) - CSS classes

**Converts to:**

```html
<!-- wp:core/template-part {"slug":"header"} /-->
```

---

#### `<Pattern>` - Block Pattern

Include a registered pattern.

```html
<Pattern slug="hero-section"></Pattern>
<Pattern slug="cta-buttons" category="call-to-action"></Pattern>
```

**Attributes:**

- `slug` (required) - Pattern slug
- `category` (optional) - Pattern category
- `class` (optional) - CSS classes

**Converts to:**

```html
<!-- wp:core/pattern {"slug":"hero-section"} /-->
```

---

#### `<Content>` - Post Content

Display post content (for single post templates).

```html
<content></content> <content class="prose prose-lg"></content>
```

**Attributes:**

- `class` (optional) - CSS classes

**Converts to:**

```html
<!-- wp:core/post-content {"className":"prose prose-lg"} /-->
```

---

### Important Notes

⚠️ **Must use closing tags** - HTML5 parsers don't support self-closing custom elements.

```html
<!-- ✅ Correct -->
<Part slug="header"></Part>

<!-- ❌ Wrong - will break parser -->
<Part slug="header" />
```

---

## Workflow Examples

### Complete Page Development Workflow

```bash
# 1. Pull existing page from WordPress
wp page pull about

# 2. Edit HTML sections
vim src/content/pages/about/section-1.html

# 3. Push changes back to WordPress
wp page push about

# 4. Rebuild assets if needed
npm run build:css
npm run build:js
```

---

### Template Development Workflow

```bash
# 1. Pull template from WordPress
wp template pull index

# 2. Edit as clean HTML with custom elements
vim src/templates/index.html

# Example content:
# <Part slug="header"></Part>
# <main>
#   <Content class="prose"></Content>
# </main>
# <Part slug="footer"></Part>

# 3. Push back to WordPress
wp template push index
```

---

### Content Development Workflow

```bash
# 1. Pull posts to markdown
wp content pull --post_type=portfolio

# 2. Edit markdown files
vim src/content/portfolio/my-project.md

# 3. Push back to WordPress
wp content push --post_type=portfolio
```

---

### Pattern Development Workflow

```bash
# 1. Create HTML in src/pages/
vim src/pages/new-pattern.html

# 2. Convert to PHP pattern
npm run parse:pages

# 3. Pattern is now available in WordPress
# Located at: patterns/new-pattern.php
```

---

### Template Part Workflow

```bash
# 1. Create or edit HTML
vim src/parts/header.html

# 2. Convert to blocks
npm run parse:parts

# 3. Use in templates
echo '<Part slug="header"></Part>' >> src/templates/index.html
wp template push index
```

---

## File Structure Reference

```
portfolio/
├── src/
│   ├── content/              # Markdown content files
│   │   ├── portfolio/        # Portfolio posts
│   │   ├── post/             # Blog posts
│   │   └── pages/            # Page HTML sections
│   │       └── about/
│   │           ├── section-1.html
│   │           └── section-2.html
│   ├── pages/                # Full page HTML (for patterns)
│   │   ├── home.html
│   │   └── about.html
│   ├── parts/                # Template part HTML
│   │   ├── header.html
│   │   └── footer.html
│   ├── templates/            # Template HTML
│   │   ├── index.html
│   │   └── single.html
│   ├── styles/               # CSS source
│   │   └── tailwind.css
│   └── scripts/              # JS source
│       └── main.js
├── patterns/                 # ⛔ Generated - do not edit
│   ├── home.php
│   └── about.php
├── parts/                    # ⛔ Generated - do not edit
│   ├── header.html           (block markup)
│   └── footer.html           (block markup)
├── templates/                # WordPress templates (block markup)
│   ├── index.html
│   └── single.html
└── _production/              # ⛔ Generated - do not edit
    ├── styles.css
    └── main.js
```

---

## Best Practices

### 1. Never Edit Generated Files

These directories contain auto-generated files:

- `patterns/` - Generated by `parse:pages` / `parse:patterns`
- `parts/` (block markup) - Generated by `parse:parts`
- `_production/` - Generated by build commands

Always edit source files in `src/` directory.

---

### 2. Use Custom Elements in Templates

```html
<!-- ✅ Good - Clean and readable -->
<Part slug="header"></Part>
<main>
  <content></content>
</main>

<!-- ❌ Avoid - Hard to read and edit -->
<!-- wp:template-part {"slug":"header"} /-->
<!-- wp:post-content /-->
```

---

### 3. Organize Page Sections Logically

```
src/content/pages/about/
├── section-1.html   # Hero
├── section-2.html   # Skills
├── section-3.html   # Experience
└── section-4.html   # CTA
```

Each section is a self-contained block of HTML.

---

### 4. Use Consistent Naming

- **Templates:** `index.html`, `single.html`, `archive.html`
- **Parts:** `header.html`, `footer.html`, `sidebar.html`
- **Pages:** Match WordPress slug (e.g., `about/`, `contact/`)

---

### 5. Test Round-Trip Conversions

Always verify that pull → edit → push maintains data integrity:

```bash
# Pull
wp template pull index

# Push without changes (should be identical)
wp template push index

# Check diff (should be minimal/none)
diff templates/index.html templates/index.html.backup
```

---

## Troubleshooting

### Parser Issues

**Problem:** HTML not converting to blocks correctly

**Solution:** Ensure valid HTML structure

```bash
# Test parser directly
node -e "
const {html2blocks, generateBlockMarkup} = require('./includes/CLI/html2pattern-cli/src/parser.js');
const fs = require('fs');
const html = fs.readFileSync('src/templates/index.html', 'utf8');
const blocks = html2blocks(html);
console.log(JSON.stringify(blocks, null, 2));
"
```

---

### Custom Elements Not Working

**Problem:** `<Part />` not converting

**Solution:** Use closing tags, not self-closing

```html
<!-- ❌ Wrong -->
<Part slug="header" />

<!-- ✅ Correct -->
<Part slug="header"></Part>
```

---

### Build Failures

**Problem:** `npm run build:css` fails

**Solution:** Check Tailwind config and input file exists

```bash
# Verify source file
ls -la src/styles/tailwind.css

# Check for syntax errors
npx tailwindcss --input src/styles/tailwind.css --output test.css
```

---

## Version

**Documentation Version:** 1.0.0
**Theme Version:** 1.0.0
**Last Updated:** 2025-01-26

---

## Support

For issues or questions:

- Check the [block markup guide](./blocks.md) for syntax examples
- Review the [template guide](./templates.md) for structure
- Consult [project-specific docs](../project/) for content writing guidelines
- File an issue on GitHub
