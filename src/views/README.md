# Views Directory (Timber/Twig Templates)

## Overview

This directory contains **Twig template files** for traditional WordPress theme development using [Timber](https://timber.github.io/docs/).

Think of this as **your theme's presentation layer**—defining how content is displayed using clean, maintainable Twig syntax instead of PHP template tags.

---

## What is Timber/Twig?

**Timber** is a WordPress plugin that lets you write your templates using **Twig**, a modern template engine that separates your HTML from PHP logic.

### Benefits

- **Clean separation of concerns** - HTML/presentation separate from PHP/logic
- **Easier to read** - `{{ post.title }}` instead of `<?php echo get_the_title(); ?>`
- **Reusable components** - Include partials, extend layouts
- **Better for designers** - Less PHP knowledge required
- **Maintainable** - Organized structure with clear data flow

### Traditional PHP vs. Timber/Twig

**Traditional WordPress:**
```php
<!-- single.php -->
<?php get_header(); ?>
<h1><?php the_title(); ?></h1>
<div><?php the_content(); ?></div>
<?php get_footer(); ?>
```

**Timber/Twig:**
```twig
{# single.twig #}
{% include 'partials/header.twig' %}
<h1>{{ post.title }}</h1>
<div>{{ post.content }}</div>
{% include 'partials/footer.twig' %}
```

---

## How It Works

### The Flow

1. **WordPress routes request** (e.g., single post)
2. **PHP template loads** (`single.php` in theme root)
3. **PHP prepares data** (via Timber context)
4. **Twig renders template** (`src/views/single.twig`)
5. **HTML output** sent to browser

### Example

**Root-level PHP template (`single.php`):**
```php
<?php
// Prepare context data
$context = Timber::context();
$context['post'] = Timber::get_post();

// Render Twig template
Timber::render('single.twig', $context);
```

**Twig template (`src/views/single.twig`):**
```twig
{% include 'partials/header.twig' %}

<article class="single-post">
    <h1>{{ post.title }}</h1>
    <time>{{ post.date }}</time>
    <div>{{ post.content }}</div>
</article>

{% include 'partials/footer.twig' %}
```

---

## Directory Structure

```
src/views/
├── index.twig              # Fallback template (all content types)
├── single.twig             # Single blog post
├── page.twig               # WordPress page
├── archive.twig            # Category/tag/date archives
├── singular.twig           # Single post/page fallback
├── 404.twig                # Not found page
├── search.twig             # Search results
├── home.twig               # Blog posts index
├── front-page.twig         # Static homepage
│
├── woocommerce/            # WooCommerce templates
│   ├── single-product.twig
│   └── archive-product.twig
│
└── partials/               # Reusable components
    ├── header.twig         # Site header
    ├── footer.twig         # Site footer
    ├── post-card.twig      # Post card component
    ├── related-posts.twig  # Related posts section
    └── archive-header.twig # Archive page header
```

---

## WordPress Template Hierarchy

WordPress uses a **template hierarchy** to determine which template displays content. Your root-level PHP files follow this hierarchy and load corresponding Twig templates:

| **PHP Template** | **Twig Template** | **Used For** |
|------------------|-------------------|--------------|
| `index.php` | `index.twig` | Fallback for all pages |
| `front-page.php` | `front-page.twig` | Static homepage |
| `home.php` | `home.twig` | Blog posts page |
| `single.php` | `single.twig` | Single blog post |
| `page.php` | `page.twig` | Static page |
| `singular.php` | `singular.twig` | Single post/page fallback |
| `archive.php` | `archive.twig` | Category/tag archives |
| `search.php` | `search.twig` | Search results |
| `404.php` | `404.twig` | Not found page |

**Custom post types:**
- `single-{posttype}.php` → `single-{posttype}.twig`
- `archive-{posttype}.php` → `archive-{posttype}.twig`

**Learn more:** [WordPress Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)

---

## Twig Syntax Basics

### Output Variables

```twig
{{ post.title }}           {# Output post title #}
{{ post.content }}         {# Output post content #}
{{ post.date }}            {# Output post date #}
{{ post.author.name }}     {# Output author name #}
```

### Conditionals

```twig
{% if post.thumbnail %}
    <img src="{{ post.thumbnail.src }}" alt="{{ post.title }}">
{% endif %}

{% if user.ID > 0 %}
    <p>Welcome, {{ user.display_name }}!</p>
{% else %}
    <p>Please log in.</p>
{% endif %}
```

### Loops

```twig
{% for post in posts %}
    <article>
        <h2>{{ post.title }}</h2>
        <p>{{ post.excerpt }}</p>
    </article>
{% endfor %}
```

### Include Partials

```twig
{% include 'partials/header.twig' %}
{% include 'partials/post-card.twig' with { post: post } %}
```

### Filters

```twig
{{ post.title|upper }}                    {# UPPERCASE #}
{{ post.excerpt|length }}                 {# Character count #}
{{ post.date|date("F j, Y") }}           {# Format date #}
{{ post.content|striptags|truncate(200) }} {# Truncate text #}
```

**Learn more:** [Twig Documentation](https://twig.symfony.com/doc/)

---

## Available Context

Timber automatically provides context variables to your templates. Context is enhanced via filters in `src/context/` and `includes/context/`.

### Global Context (All Templates)

```twig
{{ site.name }}              {# Site title #}
{{ site.description }}       {# Site tagline #}
{{ site.url }}               {# Site URL #}
{{ user }}                   {# Current user (if logged in) #}
```

### Post Context (Single/Archive Templates)

```twig
{{ post.title }}             {# Post title #}
{{ post.content }}           {# Post content #}
{{ post.excerpt }}           {# Post excerpt #}
{{ post.date }}              {# Post date #}
{{ post.author }}            {# Author object #}
{{ post.thumbnail }}         {# Featured image #}
{{ post.link }}              {# Post permalink #}
{{ post.meta('field_name') }} {# ACF custom field #}
```

### Archive Context

```twig
{{ title }}                  {# Archive title (e.g., "Category: News") #}
{{ posts }}                  {# Array of posts in archive #}
```

### Custom Context (via Context Filters)

**Example context filter (`src/context/related.php`):**
```php
add_filter('timber/context', function($context) {
    $context['related_posts'] = Timber::get_posts([
        'posts_per_page' => 3,
        'post__not_in' => [get_the_ID()]
    ]);
    return $context;
});
```

**Use in template:**
```twig
{% if related_posts %}
    <h2>Related Posts</h2>
    {% for post in related_posts %}
        <article>{{ post.title }}</article>
    {% endfor %}
{% endif %}
```

---

## Working with Partials

Partials are reusable Twig components stored in `src/views/partials/`.

### Header Partial

**File: `partials/header.twig`**
```twig
<header class="site-header">
    <div class="container">
        <a href="{{ site.url }}" class="logo">
            {{ site.name }}
        </a>

        <nav class="main-nav">
            {% for item in menu.items %}
                <a href="{{ item.link }}">{{ item.title }}</a>
            {% endfor %}
        </nav>
    </div>
</header>
```

**Usage in templates:**
```twig
{% include 'partials/header.twig' %}
```

### Post Card Partial

**File: `partials/post-card.twig`**
```twig
<article class="post-card">
    {% if post.thumbnail %}
        <img src="{{ post.thumbnail.src }}" alt="{{ post.title }}">
    {% endif %}

    <h3>{{ post.title }}</h3>
    <time>{{ post.date|date("M j, Y") }}</time>
    <p>{{ post.excerpt }}</p>
    <a href="{{ post.link }}" class="btn">Read More</a>
</article>
```

**Usage with data:**
```twig
{% for post in posts %}
    {% include 'partials/post-card.twig' with { post: post } %}
{% endfor %}
```

---

## Template Examples

### Single Post Template

**File: `single.twig`**
```twig
{% include 'partials/header.twig' %}

<main class="site-main">
    <article class="single-post">
        <header class="entry-header">
            <h1>{{ post.title }}</h1>

            <div class="entry-meta">
                <time datetime="{{ post.date|date('c') }}">
                    {{ post.date|date("F j, Y") }}
                </time>
                <span class="author">By {{ post.author.name }}</span>
            </div>
        </header>

        {% if post.thumbnail %}
            <div class="featured-image">
                <img src="{{ post.thumbnail.src('large') }}"
                     alt="{{ post.title }}">
            </div>
        {% endif %}

        <div class="entry-content">
            {{ post.content }}
        </div>

        {# Related posts from context filter #}
        {% include 'partials/related-posts.twig' %}
    </article>
</main>

{% include 'partials/footer.twig' %}
```

### Archive Template

**File: `archive.twig`**
```twig
{% include 'partials/header.twig' %}

<main class="site-main">
    <div class="archive-container">
        {# Archive header partial uses context from src/context/archive.php #}
        {% include 'partials/archive-header.twig' %}

        <div class="posts-grid">
            {% for post in posts %}
                {% include 'partials/post-card.twig' with { post: post } %}
            {% endfor %}
        </div>

        {# Pagination #}
        {% if posts.pagination %}
            <nav class="pagination">
                {{ posts.pagination }}
            </nav>
        {% endif %}
    </div>
</main>

{% include 'partials/footer.twig' %}
```

### Page Template

**File: `page.twig`**
```twig
{% include 'partials/header.twig' %}

<main class="site-main">
    <article class="page-content">
        <header class="page-header">
            <h1>{{ post.title }}</h1>
        </header>

        <div class="page-body">
            {{ post.content }}
        </div>
    </article>
</main>

{% include 'partials/footer.twig' %}
```

### 404 Template

**File: `404.twig`**
```twig
{% include 'partials/header.twig' %}

<main class="site-main">
    <div class="error-404 text-center">
        <h1>404 - Page Not Found</h1>
        <p>Sorry, the page you're looking for doesn't exist.</p>

        {# Search form #}
        <form role="search" method="get" action="{{ site.url }}">
            <input type="search" name="s" placeholder="Search...">
            <button type="submit">Search</button>
        </form>

        <a href="{{ site.url }}" class="btn">Go Home</a>
    </div>
</main>

{% include 'partials/footer.twig' %}
```

---

## Custom Fields (ACF)

Access ACF custom fields using `post.meta()`:

```twig
{# Single field #}
{{ post.meta('subtitle') }}
{{ post.meta('custom_field_name') }}

{# True/false field #}
{% if post.meta('featured') %}
    <span class="badge">Featured</span>
{% endif %}

{# Image field #}
{% set image = post.meta('custom_image') %}
<img src="{{ image.url }}" alt="{{ image.alt }}">

{# Repeater field #}
{% for item in post.meta('table_of_contents') %}
    <li>
        <a href="#{{ item.anchor }}">
            {{ item.section }}
        </a>
    </li>
{% endfor %}
```

---

## Context Filters (MVC Pattern)

**This theme uses context filters for data fetching** - the proper MVC approach.

### How Context Filters Work

Context filters in `src/context/` and `includes/context/` automatically add data to the global `$context` array available in all Twig templates.

**Example: `src/context/related.php`**
```php
<?php
add_filter('timber/context', function($context) {
    if (is_single()) {
        $context['related_posts'] = Timber::get_posts([
            'posts_per_page' => 3,
            'post__not_in' => [get_the_ID()],
            'post_type' => 'post'
        ]);
    }
    return $context;
});
```

**Use in any template:**
```twig
{% if related_posts %}
    <section class="related-posts">
        <h2>Related Posts</h2>
        {% for post in related_posts %}
            {% include 'partials/post-card.twig' with { post: post } %}
        {% endfor %}
    </section>
{% endif %}
```

### Existing Context Filters

**`src/context/archive.php`** - Adds archive data:
```twig
{{ archive.title }}         {# Archive title from ACF options #}
{{ archive.description }}   {# Archive description from ACF options #}
```

**`src/context/related.php`** - Adds related posts:
```twig
{{ related_posts }}         {# Array of related posts #}
```

**`includes/context/single-product.php`** - WooCommerce context:
```twig
{{ product }}               {# WC_Product object #}
```

---

## WooCommerce Integration

WooCommerce templates go in `src/views/woocommerce/`.

### Single Product Template

**File: `woocommerce/single-product.twig`**
```twig
{% include 'partials/header.twig' %}

<main class="site-main woocommerce-product">
    <div class="product-container">
        <div class="product-gallery">
            {# Product images #}
        </div>

        <div class="product-info">
            <h1>{{ post.title }}</h1>
            <div class="price">{{ product.price }}</div>

            {# Add to cart button, etc. #}
        </div>
    </div>
</main>

{% include 'partials/footer.twig' %}
```

**Context filter provides `product` object** via `includes/context/single-product.php`.

---

## Best Practices

### 1. Use Context Filters for Data

**✅ Correct (MVC pattern):**
```php
// src/context/recent-posts.php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts(['posts_per_page' => 5]);
    return $context;
});
```

```twig
{# Template - clean and simple #}
{% for post in recent_posts %}
    <h3>{{ post.title }}</h3>
{% endfor %}
```

**❌ Wrong (logic in template):**
```twig
{# Don't fetch data in templates #}
{% set recent_posts = timber.get_posts({'posts_per_page': 5}) %}
```

### 2. Keep Templates Simple

Templates should focus on **presentation**, not logic.

**✅ Good:**
```twig
{% include 'partials/header.twig' %}

<main>
    <h1>{{ post.title }}</h1>
    {{ post.content }}
</main>

{% include 'partials/footer.twig' %}
```

**❌ Bad:**
```twig
{# Complex logic in template #}
{% set related = timber.get_posts(...complex query...) %}
{% for post in related %}
    {% if post.meta('featured') and post.date > now %}
        {# ...lots of nested logic... #}
    {% endif %}
{% endfor %}
```

### 3. Use Partials for Reusability

Break templates into reusable components:

```twig
{# Instead of duplicating post markup #}
{% include 'partials/post-card.twig' with { post: post } %}

{# Instead of repeating header/footer #}
{% include 'partials/header.twig' %}
{% include 'partials/footer.twig' %}
```

### 4. Use ACF `post.meta()` Method

**✅ Correct:**
```twig
{{ post.meta('subtitle') }}
{{ post.meta('custom_field') }}
```

**❌ Wrong:**
```twig
{{ fun.get_field('subtitle', post.ID) }}
```

---

## Debugging

### View Twig Context

Add to any template to see available variables:

```twig
{{ dump() }}              {# Dump all context #}
{{ dump(post) }}          {# Dump specific variable #}
```

### Enable Twig Debug Mode

**Add to `wp-config.php`:**
```php
define('WP_DEBUG', true);
define('TIMBER_DEBUG', true);
```

---

## Development Workflow

### Creating a New Template

1. **Create root PHP file** (e.g., `single-resource.php`):
```php
<?php
$context = Timber::context();
$context['post'] = Timber::get_post();
Timber::render('single-resource.twig', $context);
```

2. **Create Twig template** (`src/views/single-resource.twig`):
```twig
{% include 'partials/header.twig' %}
<main>
    {# Your template markup #}
</main>
{% include 'partials/footer.twig' %}
```

3. **Add context filter** if needed (`src/context/resource.php`):
```php
<?php
add_filter('timber/context', function($context) {
    // Add custom data
    return $context;
});
```

### Adding Custom Data

**Use context filters, not inline logic:**

1. Create filter in `src/context/my-data.php`
2. Add data to `$context` array
3. Use in templates via `{{ my_data }}`

---

## Commands Reference

```bash
# No special commands needed - Twig templates render automatically

# Clear Timber cache (if using cache)
wp timber clear_cache

# Clear WordPress cache
wp cache flush
```

---

## Related Documentation

- **[Timber Documentation](https://timber.github.io/docs/)** - Official Timber docs
- **[Twig Documentation](https://twig.symfony.com/doc/)** - Twig template syntax
- **[WordPress Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)** - Template selection
- **[CLAUDE.md](../../CLAUDE.md)** - Theme architecture overview
- **[src/context/](../context/)** - Context filter examples

---

## Summary

This directory contains **Twig templates** for traditional WordPress theme development using Timber. Templates follow the WordPress template hierarchy and are rendered by root-level PHP files. Data is provided via Timber context, enhanced by context filters in `src/context/`. Use partials for reusable components and keep templates focused on presentation, not logic.

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
**Theme:** Traditional WordPress Theme with Timber/Twig
