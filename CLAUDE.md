# Claude AI Instructions: Broke FSE Theme Development

This document provides instructions for AI assistants working on this WordPress theme.

**⚠️ IMPORTANT:** This is a **boilerplate/starter theme**. Users should run `./setup.sh` to customize it for their project before development. See [.github/TEMPLATE_SETUP.md](.github/TEMPLATE_SETUP.md) for setup instructions.

## Table of Contents
1. [Theme Setup (New Projects)](#theme-setup-new-projects)
2. [Theme Architecture](#theme-architecture)
3. [HTML to Pattern Workflow](#html-to-pattern-workflow)
4. [Universal Block Markup](#universal-block-markup)
5. [Content Collections (Markdown Sync)](#content-collections-markdown-sync)
6. [Development Commands](#development-commands)
7. [File Organization](#file-organization)
8. [Best Practices](#best-practices)

---

## Theme Setup (New Projects)

### For Users Starting a New Theme

This is a **boilerplate template**, not a production theme. Before developing:

**Step 1: Run Setup Script**
```bash
./setup.sh
```

This script:
- Removes boilerplate git history
- Updates theme name, author, URIs
- Updates all metadata files (style.css, package.json, composer.json, README.md)
- Initializes new git repository
- Optionally adds remote repository

**Step 2: Install Dependencies**
```bash
composer install
pnpm install
```

**Step 3: Build Assets**
```bash
pnpm run build:css
pnpm run build:js
```

**Alternative Methods:**
- **GitHub Template:** Click "Use this template" button, clone, run setup
- **Degit:** `npx degit DanielRSnell/broke-fse your-theme && cd your-theme && ./setup.sh`
- **Manual:** Clone, run `./setup.sh`

**Full Guide:** [.github/TEMPLATE_SETUP.md](.github/TEMPLATE_SETUP.md)

### When User Hasn't Run Setup

If working with a user who cloned directly without setup:

1. **Ask:** "Have you run the setup script (`./setup.sh`) yet? This customizes the theme for your project."
2. **Explain:** The setup script updates theme name, author, and removes boilerplate branding
3. **Guide:** Point them to `.github/TEMPLATE_SETUP.md` for instructions
4. **Proceed carefully:** If they skip setup, they'll be pushing to the boilerplate's git remote (not their own)

---

## Theme Architecture

This is a **block theme** built with:
- **Universal Block** - Custom Gutenberg block for HTML elements with Twig/Timber support
- **Tailwind CSS v4** - Utility-first CSS framework (compiled via `npm run build:css`)
- **SCSS** - Bootstrap-style SCSS support available alongside Tailwind (compiled via same build process)
- **Alpine.js** - Lightweight JavaScript framework
- **GSAP** - Animation library
- **Timber/Twig** - Dynamic content templating

### Key Directories

```
blank-theme/
├── src/
│   ├── pages/           # HTML page templates to convert to patterns
│   ├── patterns/        # HTML pattern sections to convert to patterns
│   ├── content/         # Content collections (markdown files for posts/resources/projects/pages)
│   ├── context/         # Timber context filters (MVC data layer)
│   ├── docs/            # Documentation (IMPORTANT: Read these!)
│   ├── scripts/         # JavaScript source files
│   └── styles/          # Tailwind CSS v4 + SCSS source
├── patterns/            # Generated WordPress block patterns (PHP)
├── parts/               # FSE template parts (header, footer, etc.)
├── includes/
│   ├── cli/             # WP-CLI commands (html2pattern, content collections, page sync)
│   ├── enqueue.php      # Asset loading
│   └── helpers.php      # Helper functions (SVG upload, etc.)
├── assets/
│   ├── _production/     # Built CSS/JS (frontend + editor)
│   └── _editor/         # Editor-only CSS/JS
└── acf-json/            # ACF field group definitions
```

---

## HTML to Pattern Workflow

### Converting HTML Files to WordPress Patterns

**IMPORTANT:** Before making changes to HTML files, understand the Universal Block markup syntax:

📖 **Read: [src/docs/block-markup-guide.md](src/docs/block-markup-guide.md)**

This guide documents:
- Inline Twig syntax (`{{ }}`, `{% %}`)
- Twig control attributes (`loopsource`, `conditionalvisibility`, etc.)
- Magic function helpers (`fun` and `timber` objects) - **Emergency use only**
- MVC pattern with context filters (proper approach for data fetching)
- Dynamic content integration
- Available context (post, user, site)

### CLI Commands

The theme includes a custom CLI tool for converting HTML to WordPress block patterns:

```bash
# Convert a single file
html2pattern convert src/pages/home.html -o patterns

# Convert a directory
html2pattern convert src/pages -o patterns

# With options
html2pattern convert src/pages/services.html \
  --namespace=blank-theme \
  --categories=pages,services \
  --keywords=services,custom \
  --description="Services page template"
```

### NPM Scripts (Preferred)

```bash
# Convert all HTML files to patterns
npm run parse:all

# Convert only pages
npm run parse:pages

# Convert only pattern sections
npm run parse:patterns
```

These scripts are defined in `package.json`:
```json
{
  "scripts": {
    "parse:pages": "html2pattern convert src/pages -o patterns --namespace=blank-theme --categories=pages",
    "parse:patterns": "html2pattern convert src/patterns -o patterns --namespace=blank-theme --categories=patterns,sections",
    "parse:all": "npm run parse:pages && npm run parse:patterns"
  }
}
```

### Workflow for Editing HTML Templates

1. **Edit HTML files** in `src/pages/` or `src/patterns/`
2. **Use Universal Block syntax** (see [block-markup-guide.md](src/docs/block-markup-guide.md))
3. **Run conversion**: `npm run parse:all`
4. **Rebuild assets**: `npm run build:css` and `npm run build:js`
5. **Patterns update** automatically in WordPress

---

## Universal Block Markup

### When Editing HTML Files

**ALWAYS** use the proper attribute syntax for dynamic content:

#### ✅ Correct: Alpine.js Binding
```html
<input type="email" :disabled="loading" placeholder="your@email.com">
<button :disabled="loading">Submit</button>
```

#### ❌ Incorrect: Boolean Attribute with Value
```html
<!-- WRONG: This makes the input ALWAYS disabled -->
<input type="email" disabled="loading" placeholder="your@email.com">
```

#### ✅ Correct: Twig Loop Attributes
```html
<div loopsource="posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
  <p>{{ post.excerpt }}</p>
</div>
```

#### ✅ Correct: Conditional Visibility
```html
<div conditionalvisibility="true" conditionalexpression="user.ID > 0">
  <p>Welcome, {{ user.display_name }}!</p>
</div>
```

#### ✅ Correct: Using Context Filters (MVC Pattern - Preferred)
```php
// In src/context/recent-posts.php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts(['posts_per_page' => 5]);
    return $context;
});
```

```html
<!-- In template - Clean and simple! -->
<div loopsource="recent_posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
  <p>{{ post.meta('subtitle') }}</p>
</div>
```

#### ⚠️ Emergency Only: Using Magic Function Helpers
**Use context filters instead whenever possible!** Only use helpers for quick editor edits.

```html
<!-- ⚠️ Not recommended - violates MVC pattern -->
<div setvariable="recent_posts" setexpression="timber.get_posts({'posts_per_page': 5})">
  <div loopsource="recent_posts" loopvariable="post">
    <h3>{{ post.title }}</h3>
  </div>
</div>

<!-- ⚠️ Not recommended - use post.meta() instead -->
<header>
  <h1>{{ fun.get_bloginfo('name') }}</h1>
  <p>{{ fun.get_field('tagline', 'option') }}</p>
</header>
```

### Supported Attributes

The parser automatically handles these special attributes:

1. **`loopsource`** - Twig for loop collection
2. **`loopvariable`** - Loop variable name (default: "item")
3. **`conditionalvisibility`** - Enable conditional rendering (boolean)
4. **`conditionalexpression`** - Twig condition expression
5. **`setvariable`** - Variable name to set
6. **`setexpression`** - Twig expression for variable value
7. **`data-block-name`** - Custom block name in Gutenberg sidebar

### Parser Behavior

- **Case-insensitive**: `loopSource`, `loopsource`, `LOOPSOURCE` all work
- **HTML entities decoded**: Automatically handles `&quot;`, `&amp;`, etc.
- **Style attribute ignored**: `style` attributes are stripped during conversion
- **Metadata added**: Every block gets `metadata: { name: ElementTag }` for Gutenberg navigation

### Magic Function Helpers (Emergency Use Only)

**⚠️ IMPORTANT:** These helpers should be considered **a last resort** for quick editor changes only. They violate MVC principles and make templates harder to maintain.

**The Proper Approach:**
1. **Context Filters** - Add data via `timber/context` filter in PHP (`src/context/`)
2. **Timber Objects** - Access data via Timber Post/Term methods like `{{ post.meta('field_name') }}`
3. **Only use helpers** - When making emergency edits directly in the block editor

Universal Block provides two helper objects for accessing PHP and Timber functions:

#### 1. `fun` - PHP Function Helper
Access any WordPress or PHP function directly from Twig:

```twig
{{ fun.get_option('blogname') }}
{{ fun.get_field('custom_field') }}  {# ⚠️ Use post.meta() instead! #}
{{ fun.wp_get_attachment_image(123, 'thumbnail') }}
```

**❌ WRONG:**
```twig
{{ fun.get_field('subtitle', post.ID) }}
{{ fun.get_post_meta(post.ID, 'custom_field', true) }}
```

**✅ RIGHT:**
```twig
{{ post.meta('subtitle') }}
{{ post.meta('custom_field') }}
```

#### 2. `timber` - Timber Static Methods
Direct access to Timber's static methods:

```twig
{% set posts = timber.get_posts({'post_type': 'post', 'posts_per_page': 5}) %}  {# ⚠️ Use context filter instead! #}
{% set categories = timber.get_terms('category') %}
{% set menu = timber.get_menu('primary') %}
```

**❌ WRONG (in templates):**
```twig
{% set posts = timber.get_posts({'posts_per_page': 5}) %}
```

**✅ RIGHT (in src/context/ filter):**
```php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts(['posts_per_page' => 5]);
    return $context;
});
```

---

## Content Collections (Markdown & HTML Sync)

This theme includes an Astro-inspired content collections system for managing WordPress content as files with bidirectional sync.

### Overview

Content collections provide a git-friendly workflow for managing different content types:
- **Posts, Resources, Projects** - Markdown files with YAML frontmatter
- **Pages** - HTML section files (each top-level block = separate file)

Content syncs bidirectionally with WordPress database, allowing you to:
- **Edit in WordPress**, then pull to files
- **Edit in files**, then push to WordPress
- Version control your content
- Clean, portable content format

**Key Benefits:**
- Version control for all content
- Edit content in your IDE or WordPress editor
- Automatic WordPress blocks ↔ markdown/HTML conversion
- ACF custom fields in frontmatter
- Clean, portable content format
- Choose your preferred workflow

### File Structure

```
src/content/
├── posts/
│   └── hello-world.md
├── resources/
│   ├── database-performance.md
│   └── store-rebuilds-refactoring.md
├── projects/
│   └── example-project.md
└── pages/
    ├── home/
    │   ├── section-1.html
    │   ├── section-2.html
    │   └── section-3.html
    └── about/
        ├── section-1.html
        └── section-2.html
```

**Post type is auto-detected from directory:**
- `src/content/posts/` → `post`
- `src/content/resources/` → `resource`
- `src/content/projects/` → `project`
- `src/content/pages/` → `page`

### Markdown File Format

```markdown
---
title: "Post Title"
slug: "post-slug"
status: "publish"
author: 1
date: "2025-10-20 12:00:00"
excerpt: "Brief description"
custom_fields:
  table_of_contents:
    - order: 1
      section: "Introduction"
      anchor: "intro"
  featured: true
---

## Introduction

Your content here in clean markdown...
```

**Frontmatter sections:**
- **Core WordPress fields**: `title`, `slug`, `status`, `author`, `date`, `excerpt`
- **custom_fields**: All ACF fields go here
  - Field types are auto-detected from ACF schema
  - Numeric fields automatically cast to integers
  - Repeater fields support nested structures

### Pull: WordPress → Markdown

Pull posts from WordPress to markdown files:

```bash
# NPM (recommended)
npm run content:pull

# WP-CLI
wp content pull --all
wp content pull --post_type=resource
wp content pull 123
```

**What happens during pull:**
1. Fetches posts from WordPress database
2. Converts WordPress blocks → HTML using `the_content` filter
3. Converts HTML → clean markdown (headers, lists, links, etc.)
4. Separates ACF fields under `custom_fields:` in frontmatter
5. Saves to `src/content/{post_type}s/{slug}.md`

### Push: Markdown → WordPress

Push markdown files to WordPress:

```bash
# NPM (recommended)
npm run content:push

# WP-CLI
wp content push --all
wp content push --post_type=resource
wp content push src/content/resources/example.md
```

**What happens during push:**
1. Parses YAML frontmatter
2. Converts markdown → HTML
3. Converts HTML → Universal Blocks using `html2blocks` parser
4. Auto-detects post type from file path
5. Dynamically casts ACF field values based on schema
6. Creates or updates post in WordPress

### Dynamic ACF Field Casting

ACF field types are automatically detected and values are properly cast:

**Supported field types:**
- `number` → Cast to integer
- `text` → String
- `true_false` → Boolean
- `repeater` → Array with sub-field casting
- `post_object` → Post ID
- `relationship` → Array of post IDs
- `taxonomy` → Term ID or array

**Example:**
```yaml
custom_fields:
  table_of_contents:  # Repeater field
    - order: 1        # Auto-cast to integer
      section: "Intro"
      anchor: "intro"
  featured: true      # Auto-cast to boolean
```

### Page Content Collections (HTML Sections)

Pages work differently - they sync as HTML section files instead of markdown.

**Pull: WordPress → HTML Sections**
```bash
# NPM (recommended)
npm run page:pull

# WP-CLI
wp page pull --all
wp page pull <page-id>
```

**What happens during page pull:**
1. Fetches pages from WordPress
2. Each top-level block becomes a separate `section-N.html` file
3. Blocks are rendered to clean HTML
4. WordPress-generated IDs are stripped
5. Saves to `src/content/pages/{slug}/section-*.html`

**Push: HTML Sections → WordPress**
```bash
# NPM (recommended)
npm run page:push

# WP-CLI
wp page push --all
wp page push src/content/pages/home
```

**What happens during page push:**
1. Reads all `section-*.html` files in page directory
2. Converts HTML → Universal Blocks using `html2blocks` parser
3. Combines sections back into single page content
4. Updates or creates page in WordPress

**Example workflow:**
```bash
# Edit page in WordPress
wp page pull --all                # Pull latest to HTML sections
# Edit src/content/pages/home/section-2.html in your IDE
npm run page:push                 # Push changes back to WordPress
```

### Workflow Examples

**Edit existing post/resource content:**
```bash
npm run content:pull              # Pull latest from WordPress
# Edit src/content/resources/example.md in your IDE
npm run content:push              # Push changes back
```

**Edit existing page:**
```bash
npm run page:pull                 # Pull latest pages to HTML sections
# Edit src/content/pages/home/section-1.html
npm run page:push                 # Push changes back
```

**Create new post:**
```bash
# Create src/content/resources/new-guide.md
# Add frontmatter and content
wp content push src/content/resources/new-guide.md
```

**Sync before committing:**
```bash
npm run content:pull              # Get latest posts/resources/projects
npm run page:pull                 # Get latest pages
git add src/content/              # Commit all content files
git commit -m "Update content"    # Version controlled content
```

---

## Development Commands

### Build Commands

**CSS (Tailwind v4 + SCSS)**
```bash
# Build Tailwind CSS + SCSS
npm run build:css

# Watch CSS changes during development
npm run watch:css
```

The CSS build process:
- Compiles **Tailwind CSS v4** utility classes
- Supports **SCSS** syntax (Bootstrap-style) alongside Tailwind
- Outputs to `assets/_production/main.css` (loaded on frontend + editor)
- Source files in `src/styles/`
- Both Tailwind utilities and custom SCSS work together

**JavaScript (esbuild)**
```bash
# Build JavaScript with esbuild
npm run build:js

# Watch JS changes during development
npm run watch:js
```

**Build Everything**
```bash
# Build both CSS and JavaScript
npm run build
```

### Pattern Conversion
```bash
# Convert all HTML to patterns
npm run parse:all

# Convert only pages
npm run parse:pages

# Convert only patterns
npm run parse:patterns
```

### Content Collections

**Posts/Resources/Projects (Markdown Sync)**
```bash
# Pull all posts from WordPress to markdown
npm run content:pull

# Push all markdown files to WordPress
npm run content:push
```

**Pages (HTML Section Sync)**
```bash
# Pull all pages from WordPress to HTML sections
npm run page:pull

# Push all HTML sections to WordPress pages
npm run page:push
```

**WP-CLI Commands:**
```bash
# Content (posts/resources/projects)
wp content pull --post_type=resource
wp content pull --post_type=project
wp content pull <post-id>
wp content push src/content/resources/example.md
wp content push --post_type=resource
wp content push --all

# Pages
wp page pull --all
wp page pull <page-id>
wp page push --all
wp page push src/content/pages/home
```

---

## File Organization

### Source Files (`src/`)

**Pages** (`src/pages/*.html`):
- Full page templates
- Convert to patterns with `--categories=pages`
- Examples: home.html, services.html, how-it-works.html

**Patterns** (`src/patterns/*.html`):
- Reusable sections
- Convert to patterns with `--categories=patterns,sections`
- Examples: post-title.html, related.html

**Documentation** (`src/docs/*.md`):
- **MUST READ** before making markup changes
- `block-markup-guide.md` - Universal Block syntax reference

**Content Collections** (`src/content/`):
- Markdown-based content management (Astro-style)
- Bidirectional sync with WordPress database
- `posts/` - Blog posts
- `resources/` - Resource custom post type
- `projects/` - Project custom post type
- YAML frontmatter with WordPress fields + ACF custom fields
- Clean markdown content (converted from WordPress blocks)

**Context Filters** (`src/context/`):
- Individual context filter files
- Auto-loaded by `functions.php`
- Add variables to global Timber context
- Example: `related.php` provides `related_posts` variable

### Generated Files

**Patterns** (`patterns/*.php`):
- Auto-generated from `src/pages/` and `src/patterns/`
- **DO NOT EDIT DIRECTLY** - Edit source HTML instead
- Registered automatically by WordPress

**Assets** (`assets/`):
- `_production/` - CSS/JS loaded on frontend + editor
- `_editor/` - CSS/JS loaded ONLY in block editor

### WordPress Integration

**Enqueue Strategy** (`includes/enqueue.php`):
- `enqueue_block_assets` - Loads `_production/*` files (both frontend + editor)
- `enqueue_block_editor_assets` - Loads `_editor/*` files (editor only)

**Custom Post Types** (`acf-json/`):
- ACF field groups for `project` and `resource` post types
- Includes Table of Contents field group

---

## Best Practices

### 1. Always Read the Block Markup Guide

Before editing ANY HTML file:
```bash
# Reference this file
src/docs/block-markup-guide.md
```

### 2. Edit Source, Not Generated Files

**DO:**
- Edit `src/pages/home.html`
- Run `npm run parse:all`
- Rebuild CSS/JS

**DON'T:**
- Edit `patterns/home.php` directly
- Manually create pattern files

### 3. Use Proper Attribute Syntax

**Alpine.js bindings:**
```html
:disabled="loading"    <!-- ✅ Correct -->
disabled="loading"     <!-- ❌ Wrong -->
```

**Twig attributes:**
```html
loopsource="posts"     <!-- ✅ Correct (case-insensitive) -->
loop-source="posts"    <!-- ❌ Wrong (hyphenated) -->
```

### 4. Test After Conversion

1. Convert: `npm run parse:all`
2. Rebuild: `npm run build:css && npm run build:js`
3. Check WordPress admin for pattern in inserter
4. Test dynamic content rendering

### 5. Database Changes

**Creating posts via WP-CLI:**
```bash
cd /Users/broke/Herd/wtf-blocks

# Create a project
wp post create \
  --post_type=project \
  --post_title="Project Title" \
  --post_status=publish \
  --post_content="Project description"

# Create a resource
wp post create \
  --post_type=resource \
  --post_title="Resource Title" \
  --post_status=publish \
  --post_content="Resource content"
```

**Searching/replacing in database:**
```bash
# Search for a string in post content
wp db query "SELECT ID, post_title FROM wp_posts WHERE post_content LIKE '%search-term%';"

# Use wp search-replace for safe replacements
wp search-replace 'old-string' 'new-string' --dry-run
```

### 6. Debugging Twig

Add `?twig=false` to URL to see raw Twig before compilation:
```
https://yoursite.local/page/?twig=false
```

---

## Common Tasks

### Task: Update a Page Template

1. **Edit** the HTML file:
   ```bash
   # Open in editor
   src/pages/services.html
   ```

2. **Convert** to pattern:
   ```bash
   npm run parse:pages
   ```

3. **Rebuild** assets:
   ```bash
   npm run build:css
   npm run build:js
   ```

### Task: Add Dynamic Content (MVC Pattern)

1. **Review** block markup guide:
   ```bash
   src/docs/block-markup-guide.md
   ```

2. **Create context filter** in `src/context/`:
   ```php
   // src/context/recent-posts.php
   add_filter('timber/context', function($context) {
       $context['recent_posts'] = Timber::get_posts([
           'posts_per_page' => 5,
           'post_status' => 'publish'
       ]);
       return $context;
   });
   ```

3. **Add clean Twig markup** to template:
   ```html
   <!-- Using data from context filter - Clean and simple! -->
   <div loopsource="recent_posts" loopvariable="post">
     <h3>{{ post.title }}</h3>
     <p>{{ post.meta('subtitle') }}</p>
     <a href="{{ post.link }}">Read More</a>
   </div>
   ```

4. **Convert and test**:
   ```bash
   npm run parse:all
   npm run build:css && npm run build:js
   ```

### Task: Fix Alpine.js Bindings

1. **Search** for incorrect syntax:
   ```bash
   grep -r 'disabled="' src/
   ```

2. **Replace** with binding syntax:
   ```bash
   # Change disabled="loading" to :disabled="loading"
   sed -i '' 's/ disabled="loading"/ :disabled="loading"/g' src/pages/*.html
   ```

3. **Reconvert**:
   ```bash
   npm run parse:all
   ```

### Task: Edit Content in Markdown

1. **Pull** latest content from WordPress:
   ```bash
   npm run content:pull
   ```

2. **Edit** markdown files in `src/content/`:
   ```markdown
   ---
   title: "My Post Title"
   slug: "my-post"
   status: "publish"
   custom_fields:
     table_of_contents:
       - order: 1
         section: "Introduction"
         anchor: "intro"
   ---

   ## Introduction

   Your content here...
   ```

3. **Push** changes back to WordPress:
   ```bash
   npm run content:push
   ```

### Task: Create New Post via Markdown

1. **Create** new markdown file:
   ```bash
   # File: src/content/resources/new-guide.md
   ```

2. **Add** frontmatter and content:
   ```markdown
   ---
   title: "New Guide"
   slug: "new-guide"
   status: "draft"
   author: 1
   custom_fields:
     featured: true
   ---

   # Your content here
   ```

3. **Push** to create in WordPress:
   ```bash
   wp content push src/content/resources/new-guide.md
   ```

---

## Related Documentation

- **[src/docs/block-markup-guide.md](src/docs/block-markup-guide.md)** - Universal Block syntax (REQUIRED READING)
- **[includes/cli/html2pattern-cli/README.md](includes/cli/html2pattern-cli/README.md)** - CLI tool documentation
- **[package.json](package.json)** - NPM scripts and dependencies

---

## Notes for AI Assistants

### Before Making Changes

1. **Read the block markup guide** if working with HTML files
2. **Check existing patterns** to understand the structure
3. **Use the CLI tools** for conversions (don't manually create patterns)
4. **Test conversions** after making changes

### Important Reminders

**File Editing:**
- ✅ Edit `src/` files, not `patterns/` files
- ✅ Run `parse:all` after editing HTML
- ✅ Use `:disabled` not `disabled="..."` for Alpine.js
- ✅ Use lowercase Twig attributes (`loopsource` not `loopSource` in HTML)
- ✅ Rebuild CSS/JS after changes
- ❌ Never edit generated pattern PHP files directly
- ❌ Never use boolean attributes with values (e.g., `disabled="loading"`)
- ❌ Never commit without reconverting patterns

**MVC Pattern (CRITICAL):**
- ✅ Use `timber/context` filters in `src/context/` for ALL data fetching
- ✅ Use `{{ post.meta('field_name') }}` for custom fields
- ✅ Use Timber object methods and properties
- ❌ NEVER use `fun.get_field()` for data fetching - use `post.meta()` instead
- ❌ NEVER use `timber.get_posts()` in templates - use context filters instead
- ⚠️ Only use `fun`/`timber` helpers for emergency editor edits

**Content Collections Workflow:**
- ✅ You CAN edit in WordPress, then pull to sync files
- ✅ You CAN edit in files, then push to sync WordPress
- ✅ Use `npm run content:pull` to sync posts/resources/projects to markdown
- ✅ Use `npm run page:pull` to sync pages to HTML sections
- ✅ Use `npm run content:push` after editing markdown content
- ✅ Use `npm run page:push` after editing page HTML sections
- ✅ Choose whichever editing workflow you prefer (WordPress or files)

**CSS/JS:**
- ✅ Tailwind CSS v4 and SCSS both supported in same build
- ✅ Run `npm run build:css` to compile Tailwind + SCSS
- ✅ Run `npm run watch:css` during development
- ✅ Both utility classes and custom SCSS work together

### When User Asks to Edit HTML

1. Ask: "Have you reviewed the block markup guide?"
2. Reference: [src/docs/block-markup-guide.md](src/docs/block-markup-guide.md)
3. Use proper syntax for all dynamic attributes
4. Convert and rebuild after changes

### When User Asks to Edit Content

**For Posts/Resources/Projects:**
1. Pull latest: `npm run content:pull`
2. Edit markdown files in `src/content/{post_type}s/`
3. Push changes: `npm run content:push`
4. ACF fields go under `custom_fields:` in frontmatter
5. Content is clean markdown (no WordPress block comments)

**For Pages:**
1. Pull latest: `npm run page:pull`
2. Edit HTML sections in `src/content/pages/{slug}/section-*.html`
3. Push changes: `npm run page:push`
4. Each top-level block = separate section file

**Bidirectional Workflow:**
- You CAN edit in WordPress, then pull to get changes in files
- You CAN edit in files, then push to update WordPress
- Choose whichever workflow suits the task best

### Content Collections Features

**Posts/Resources/Projects (Markdown):**
- **Bidirectional sync**: WordPress ↔ Markdown
- **Pull**: Converts WordPress blocks → HTML → clean markdown
- **Push**: Converts markdown → HTML → Universal Blocks
- **Dynamic ACF casting**: Field types auto-detected from ACF schema
- **Post type detection**: Auto-detected from file path pattern
- **YAML frontmatter**: WordPress core fields + ACF custom fields

**Pages (HTML Sections):**
- **Bidirectional sync**: WordPress ↔ HTML sections
- **Pull**: Each block → separate `section-N.html` file
- **Push**: Combines sections → Universal Blocks
- **Clean HTML**: WordPress IDs stripped automatically
- **Section-based editing**: Edit individual page sections independently

---

**Version:** 1.3.0
**Last Updated:** 2025-01-21
**Theme:** Blank Theme (Universal Block)

**Changelog:**
- v1.3.0:
  - Added page content collections (HTML section sync)
  - Updated MVC pattern documentation (context filters preferred over helpers)
  - Clarified bidirectional workflow (edit in WordPress OR files)
  - Added Tailwind v4 + SCSS build process documentation
  - Added `src/context/` filters documentation
- v1.2.0: Added content collections (markdown sync) with bidirectional WordPress ↔ MD sync
- v1.1.0: Added magic function helpers (`fun` and `timber` objects) documentation
