# Content Collections Directory

## Overview

This directory implements an **Astro-inspired content collections workflow** for WordPress—allowing you to manage posts, pages, and custom post types as **markdown or HTML files** with **bidirectional sync** to the WordPress database.

Think of this as **git-friendly WordPress content**. Edit in your IDE with version control, or edit in WordPress and pull changes back to files.

---

## Philosophy

### The Problem This Solves

WordPress traditionally stores all content in the database, which creates challenges for modern development workflows:

- **No version control** - Content changes aren't tracked in git
- **Difficult collaboration** - Multiple writers can't work on content simultaneously via pull requests
- **Editor lock-in** - Must use WordPress editor to create/edit content
- **Portability issues** - Content tied to database, hard to migrate
- **No code review** - Content changes bypass review processes

### The Solution

Content collections provide a **file-based content layer** that syncs bidirectionally with WordPress:

1. **Version controlled** - All content tracked in git
2. **Editor flexibility** - Write in IDE, WordPress editor, or both
3. **Clean format** - Markdown for posts, HTML sections for pages
4. **Portable** - Content exists as files, not just database records
5. **Team workflow** - Content updates via pull requests
6. **Choose your flow** - Edit where you want, sync when you need

### Core Principle

**"Your content should live where your code lives—in version control, editable in your IDE, reviewable via pull requests."**

---

## Directory Structure

### Typical Organization

```
src/content/
├── README.md           # This file
├── posts/              # Blog posts (markdown)
│   ├── hello-world.md
│   └── my-article.md
├── resources/          # Custom post type: resources (markdown)
│   ├── guide-1.md
│   └── guide-2.md
├── projects/           # Custom post type: projects (markdown)
│   └── project-1.md
└── pages/              # Pages (HTML sections)
    ├── home/
    │   ├── section-1.html
    │   ├── section-2.html
    │   └── section-3.html
    └── about/
        └── section-1.html
```

**Post type auto-detection:**
- `posts/` → `post` type
- `resources/` → `resource` type
- `projects/` → `project` type
- `pages/` → `page` type

Add directories for any custom post type your project needs.

---

## How It Works

### Posts, Resources, Projects (Markdown)

**File format:**
```markdown
---
title: "Post Title"
slug: "post-slug"
status: "publish"
author: 1
date: "2025-10-20 12:00:00"
excerpt: "Brief description"
custom_fields:
  featured: true
  custom_field_name: "value"
---

## Your Content Here

Clean markdown content without WordPress block comments.
```

**Pull (WordPress → Markdown):**
```bash
npm run content:pull
```

What happens:
1. Fetches posts from WordPress database
2. Converts WordPress blocks → HTML → clean markdown
3. Separates ACF custom fields into `custom_fields:` frontmatter
4. Saves to `src/content/{post_type}s/{slug}.md`

**Push (Markdown → WordPress):**
```bash
npm run content:push
```

What happens:
1. Parses YAML frontmatter
2. Converts markdown → HTML → Universal Blocks
3. Auto-detects post type from directory path
4. Dynamically casts ACF field values based on schema
5. Creates or updates post in WordPress

### Pages (HTML Sections)

Pages work differently—each **top-level block** becomes a **separate HTML file**.

**Pull (WordPress → HTML Sections):**
```bash
npm run page:pull
```

What happens:
1. Fetches pages from WordPress
2. Each top-level block → `section-N.html` file
3. WordPress-generated IDs stripped for clean HTML
4. Saves to `src/content/pages/{slug}/section-*.html`

**Push (HTML Sections → WordPress):**
```bash
npm run page:push
```

What happens:
1. Reads all `section-*.html` files in page directory
2. Converts HTML → Universal Blocks
3. Combines sections into single page content
4. Updates or creates page in WordPress

---

## Workflows

### Workflow 1: Edit in WordPress, Pull to Files

```bash
# Make changes in WordPress editor
npm run content:pull    # Pull posts/resources/projects
npm run page:pull       # Pull pages
git add src/content/
git commit -m "Update content from WordPress"
git push
```

**Use when:**
- Content team prefers WordPress editor
- Using WordPress-specific features (media library, etc.)
- Non-technical writers creating content

### Workflow 2: Edit in Files, Push to WordPress

```bash
# Edit markdown/HTML files in your IDE
npm run content:push    # Push posts/resources/projects
npm run page:push       # Push pages
# Verify changes in WordPress
```

**Use when:**
- Developers creating content
- Content needs version control review
- Bulk content updates
- Migrating content from other sources

### Workflow 3: Hybrid (Best of Both)

```bash
# Pull latest before editing
npm run content:pull && npm run page:pull

# Edit wherever is most convenient
# - WordPress for media-rich posts
# - IDE for technical documentation
# - Both for collaborative editing

# Push local changes, pull remote changes
npm run content:push && npm run page:push
npm run content:pull && npm run page:pull
```

**Use when:**
- Team has mixed preferences
- Different content types need different tools
- Maximum flexibility required

---

## Structure by Project Type

### Blog / Publication Site

**Focus:** Editorial workflow, version control for articles

```
src/content/
├── posts/              # Blog posts
│   ├── 2025-01-article-1.md
│   └── 2025-01-article-2.md
├── authors/            # Author profiles (custom post type)
│   ├── john-doe.md
│   └── jane-smith.md
└── pages/
    ├── about/
    └── contact/
```

**Why:** Version control for articles, editorial review via pull requests, author management as files.

### Documentation Site

**Focus:** Technical content, code examples, versioning

```
src/content/
├── docs/               # Documentation pages (custom post type)
│   ├── getting-started.md
│   ├── api-reference.md
│   └── tutorials.md
├── guides/             # Step-by-step guides
│   └── quickstart.md
└── changelog/          # Version history
    └── v1-0-0.md
```

**Why:** Markdown for technical content, code blocks, version history tracked in git.

### Portfolio / Agency Site

**Focus:** Project showcases, case studies

```
src/content/
├── projects/           # Portfolio projects
│   ├── project-a.md
│   └── project-b.md
├── case-studies/       # In-depth case studies
│   └── client-success-story.md
└── pages/
    ├── home/
    ├── services/
    └── about/
```

**Why:** Projects as files for easy updates, case studies with custom fields for metrics.

### E-commerce / Product Site

**Focus:** Product content, category descriptions

```
src/content/
├── products/           # If using custom post type for products
│   ├── product-1.md
│   └── product-2.md
├── collections/        # Product collections/categories
│   ├── summer-2025.md
│   └── best-sellers.md
└── pages/
    ├── home/
    ├── shop/
    └── about/
```

**Why:** Product descriptions version controlled, seasonal content managed as files.

### Marketing / Landing Pages

**Focus:** Page content, A/B testing, rapid updates

```
src/content/
└── pages/
    ├── home/
    │   ├── section-hero.html
    │   ├── section-features.html
    │   └── section-cta.html
    ├── pricing/
    ├── features/
    └── case-studies/
```

**Why:** Page sections as files for A/B testing, version control for landing page iterations.

---

## Custom Fields (ACF)

### Frontmatter Format

```yaml
custom_fields:
  # Text fields
  subtitle: "Secondary heading"

  # Repeater fields
  table_of_contents:
    - order: 1
      section: "Introduction"
      anchor: "intro"
    - order: 2
      section: "Features"
      anchor: "features"

  # Boolean fields
  featured: true

  # Numeric fields
  reading_time: 5

  # Post object / relationship
  related_post: 123
  related_posts: [123, 456, 789]
```

### Dynamic Field Casting

The push command automatically casts field values based on ACF schema:

- `number` → Cast to integer
- `true_false` → Boolean
- `text` → String
- `repeater` → Array with sub-field casting
- `post_object` → Post ID
- `relationship` → Array of post IDs

**No manual type conversion needed**—the system reads your ACF schema and handles it.

---

## Commands Reference

### Content Commands (Posts/Resources/Projects)

```bash
# Pull all posts from WordPress to markdown
npm run content:pull

# Push all markdown files to WordPress
npm run content:push
```

**WP-CLI equivalents** (for more control):
```bash
# Pull specific post type
wp content pull --post_type=resource

# Pull single post by ID
wp content pull 123

# Push specific file
wp content push src/content/resources/example.md

# Push all of one type
wp content push --post_type=resource

# Push everything
wp content push --all
```

### Page Commands (HTML Sections)

```bash
# Pull all pages from WordPress to HTML sections
npm run page:pull

# Push all HTML sections to WordPress pages
npm run page:push
```

**WP-CLI equivalents:**
```bash
# Pull specific page
wp page pull <page-id>

# Pull all pages
wp page pull --all

# Push specific page directory
wp page push src/content/pages/home

# Push all pages
wp page push --all
```

---

## Best Practices

### 1. Pull Before Editing

Always pull latest content before making changes:
```bash
npm run content:pull && npm run page:pull
```

This prevents overwriting changes made in WordPress.

### 2. Use Descriptive Slugs

```yaml
# Good
slug: "complete-guide-to-wordpress-blocks"

# Bad
slug: "post-1"
```

Slugs become filenames—make them meaningful.

### 3. Keep Frontmatter Clean

Only include fields that have values:
```yaml
# Good
---
title: "My Post"
status: "publish"
custom_fields:
  featured: true
---

# Bad (unnecessary empty fields)
---
title: "My Post"
status: "publish"
author:
excerpt:
custom_fields:
  featured: true
  subtitle:
  category:
---
```

### 4. Version Control Everything

```bash
# After pulling content
git add src/content/
git commit -m "Update content from WordPress"

# Before pushing changes
git diff src/content/    # Review changes
npm run content:push     # Push to WordPress
```

### 5. Use Custom Fields Consistently

Define ACF field groups first, then reference in frontmatter:
- Matches field names exactly
- Uses correct data types
- Leverages automatic casting

### 6. Separate Concerns

**Posts/Resources/Projects** - Use markdown for:
- Text-heavy content
- Articles, blog posts
- Documentation
- Content that changes frequently

**Pages** - Use HTML sections for:
- Marketing landing pages
- Complex layouts
- Visual sections
- Content that needs precise HTML control

---

## Advanced Patterns

### Team Collaboration

**Content approval workflow:**
```bash
# Writer creates content
# In src/content/posts/new-article.md

git add src/content/posts/new-article.md
git commit -m "Draft: New article about X"
git push origin feature/new-article

# Create pull request
# Editor reviews markdown in GitHub/GitLab
# Approves and merges

# Deploy triggers content:push
# Article goes live in WordPress
```

### Content Migrations

**Migrate from another platform:**
```bash
# 1. Export content to markdown
# 2. Place files in src/content/posts/
# 3. Push to WordPress
npm run content:push

# All content now in WordPress with proper formatting
```

### A/B Testing Landing Pages

**Test different page variations:**
```bash
# Version A
src/content/pages/pricing-v1/
  ├── section-hero.html
  └── section-features.html

# Version B
src/content/pages/pricing-v2/
  ├── section-hero.html
  └── section-features.html

# Push version to test
npm run page:push src/content/pages/pricing-v1
# Measure results
# Push winning version
npm run page:push src/content/pages/pricing-v2
```

### Bulk Content Updates

**Update all posts programmatically:**
```bash
# Edit multiple markdown files
sed -i '' 's/old-term/new-term/g' src/content/posts/*.md

# Push all changes
npm run content:push

# All posts updated in WordPress
```

---

## Troubleshooting

### Content Not Syncing

**Check:**
1. Post type exists in WordPress
2. ACF field groups registered
3. File permissions (read/write)
4. WordPress permalink settings

### Custom Fields Not Saving

**Verify:**
1. Field names match ACF field group
2. Data types compatible (string vs. number)
3. ACF field group assigned to post type
4. Field group is active

### Page Sections Out of Order

**Solution:**
Rename section files to control order:
```
section-1-hero.html
section-2-features.html
section-3-cta.html
```

Files are combined in alphabetical order.

### Markdown Conversion Issues

**Common causes:**
- Complex WordPress blocks → Simplified markdown
- Custom block types → Generic HTML
- Nested blocks → Flattened structure

**Solution:** Use page sections (HTML) for complex layouts instead of markdown.

---

## Integration with Theme Workflow

### With Patterns & Pages

```bash
# Update page content
npm run page:pull

# Update page structure/design
# Edit src/pages/home.html
npm run parse:pages

# Content and structure independent
```

### With Context Filters

```php
// src/context/recent-posts.php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts([
        'posts_per_page' => 5
    ]);
    return $context;
});
```

Content collections manage **content**, context filters provide **data** to templates.

---

## Related Documentation

- **[CLAUDE.md](../../CLAUDE.md)** - Overall theme architecture
- **[src/docs/project/README.md](../docs/project/README.md)** - Project context for AI
- **[src/pages/README.md](../pages/README.md)** - Page template patterns
- **[src/patterns/README.md](../patterns/README.md)** - Reusable pattern sections

---

**Summary:**

This directory enables **file-based content management** for WordPress with bidirectional sync. Edit content as markdown (posts) or HTML sections (pages) in your IDE or WordPress editor—whichever fits your workflow. All content version controlled, all changes reviewable, all workflows supported.

The structure you choose depends on your project type and team preferences, but the principle remains the same: **your content should live where your code lives.**

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
