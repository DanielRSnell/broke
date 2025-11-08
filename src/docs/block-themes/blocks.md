# LLM Instructions: Universal Block Markup Guide

This guide is for AI assistants (LLMs) working with Universal Block markup. It documents all HTML attributes and syntaxes for creating dynamic Twig-powered content.

## Table of Contents

1. [Overview](#overview)
2. [Custom WordPress Elements](#custom-wordpress-elements) ⭐ NEW
3. [Inline Twig Syntax](#inline-twig-syntax)
4. [Twig Control Attributes](#twig-control-attributes)
5. [Complete Examples](#complete-examples)
6. [Context Filters](#context-filters-recommended-approach) (Recommended)
7. [Helper Functions](#helper-functions-legacyadvanced) (Legacy/Advanced)
8. [Best Practices](#best-practices)

---

## Overview

Universal Block supports multiple ways to create dynamic WordPress content:

1. **Custom WordPress Elements**: Use `<Part>`, `<Pattern>`, and `<Content>` elements ⭐ NEW
2. **Inline Twig**: Use `{{ }}` and `{% %}` directly in content or attributes
3. **Control Attributes**: Use special HTML attributes that generate Twig control structures

All approaches are compiled by Timber on the frontend and work in:

- Post/Page content
- FSE template parts (headers, footers, sidebars)
- Templates (index.html, single.html, etc.)

---

## Custom WordPress Elements

⭐ **NEW:** The theme now supports custom HTML elements that convert to WordPress core blocks. These provide a clean, HTML-compliant way to include template parts, patterns, and post content.

### Quick Reference

| Element     | Purpose                | Example                             |
| ----------- | ---------------------- | ----------------------------------- |
| `<Part>`    | Include template parts | `<Part slug="header"></Part>`       |
| `<Pattern>` | Include block patterns | `<Pattern slug="hero"></Pattern>`   |
| `<Content>` | Display post content   | `<Content class="prose"></Content>` |

⚠️ **CRITICAL:** Always use closing tags (`</Part>`), NEVER self-closing (`<Part />`).

### Available Elements

#### `<Part>` - Template Part

Include a template part from the `parts/` directory.

**Syntax:**

```html
<Part slug="header"></Part>
<Part slug="footer" theme="portfolio"></Part>
<Part slug="sidebar" class="col-span-4"></Part>
```

**Attributes:**

- `slug` (required) - Template part slug (e.g., "header", "footer")
- `theme` (optional) - Theme name (defaults to current theme)
- `class` (optional) - CSS classes to add to the wrapper

**Converts to WordPress Block:**

```html
<!-- wp:core/template-part {"slug":"header"} /-->
```

**Example - Template with Header/Footer:**

```html
<div class="site-wrapper">
  <Part slug="header" theme="portfolio"></Part>

  <main class="flex-grow">
    <!-- Your content here -->
  </main>

  <Part slug="footer" theme="portfolio"></Part>
</div>
```

---

#### `<Pattern>` - Block Pattern

Include a registered WordPress block pattern.

**Syntax:**

```html
<Pattern slug="hero-section"></Pattern>
<Pattern slug="cta-buttons" category="call-to-action"></Pattern>
```

**Attributes:**

- `slug` (required) - Pattern slug
- `category` (optional) - Pattern category
- `class` (optional) - CSS classes

**Converts to WordPress Block:**

```html
<!-- wp:core/pattern {"slug":"hero-section"} /-->
```

**Example - Homepage with Patterns:**

```html
<Part slug="header"></Part>

<Pattern slug="hero-home"></Pattern>
<Pattern slug="featured-services"></Pattern>
<Pattern slug="testimonials"></Pattern>

<Part slug="footer"></Part>
```

---

#### `<Content>` - Post Content

Display the current post's content (for single post/page templates).

**Syntax:**

```html
<content></content> <content class="prose prose-lg max-w-4xl mx-auto"></content>
```

**Attributes:**

- `class` (optional) - CSS classes to add to the content wrapper

**Converts to WordPress Block:**

```html
<!-- wp:core/post-content {"className":"prose prose-lg"} /-->
```

**Example - Single Post Template:**

```html
<Part slug="header"></Part>

<article class="post-single">
  <header>
    <h1>{{ post.title }}</h1>
    <time>{{ post.date|date('F j, Y') }}</time>
  </header>

  <content class="prose prose-lg"></content>

  <footer>
    <div loopsource="post.categories" loopvariable="cat">
      <a href="{{ cat.link }}">{{ cat.name }}</a>
    </div>
  </footer>
</article>

<Part slug="footer"></Part>
```

---

### Important: Closing Tags Required

⚠️ **HTML5 parsers do NOT support self-closing custom elements.**

```html
<!-- ✅ CORRECT - Use closing tags -->
<Part slug="header"></Part>
<Pattern slug="hero"></Pattern>
<content></content>

<!-- ❌ WRONG - Self-closing syntax will break -->
<Part slug="header" />
<Pattern slug="hero" />
<content />
```

**Why?** HTML5 parsers treat `<Part />` as an unclosed opening tag, causing the parser to nest subsequent elements incorrectly.

---

### Combining with Twig

Custom elements work seamlessly with Twig expressions:

```html
<Part slug="header"></Part>

<main>
  <!-- Conditional pattern based on post type -->
  <div conditionalvisibility="true" conditionalexpression="post.post_type == 'portfolio'">
    <Pattern slug="portfolio-hero"></Pattern>
  </div>

  <!-- Post content with custom classes -->
  <content class="prose {{ post.meta('content_width') }}"></content>
</main>

<Part slug="footer"></Part>
```

---

### Use Cases

**1. Templates** (`src/templates/*.html`)

```html
<!-- index.html -->
<Part slug="header"></Part>
<main class="flex-grow">
  <content></content>
</main>
<Part slug="footer"></Part>
```

**2. Page Sections** (`src/content/pages/about/section-1.html`)

```html
<section class="hero">
  <Part slug="navigation"></Part>
  <h1>{{ post.title }}</h1>
</section>
```

**3. Patterns** (`src/pages/*.html`)

```html
<!-- Can include template parts within patterns -->
<section class="contact-cta">
  <Part slug="contact-form"></Part>
</section>
```

---

## Inline Twig Syntax

### Variables in Content

```html
<h1>{{ post.title }}</h1>
<p>{{ post.excerpt }}</p>
<span>{{ user.display_name }}</span>
```

### Variables in Attributes

```html
<a href="{{ post.link }}">Read More</a>
<img src="{{ post.thumbnail.src }}" alt="{{ post.title }}" />
<div data-post-id="{{ post.ID }}">...</div>
```

### Twig Filters

```html
<p>{{ post.content|length }}</p>
<time>{{ post.date|date('F j, Y') }}</time>
<h2>{{ post.title|upper }}</h2>
```

### Twig Control Structures

```html
{% if user.ID > 0 %}
<p>Welcome back, {{ user.display_name }}!</p>
{% else %}
<p>Please log in</p>
{% endif %} {% for item in posts %}
<article>{{ item.title }}</article>
{% endfor %} {% set greeting = 'Hello ' ~ user.display_name %}
<p>{{ greeting }}</p>
```

---

## Twig Control Attributes

Universal Block provides HTML attributes that automatically generate Twig control structures. These are useful when working with the block editor UI.

### 1. Loop Attributes

**Wraps element in `{% for %}` loop**

#### Basic Loop

```html
<div loopsource="posts">
  <h3>{{ item.title }}</h3>
</div>
```

Renders as:

```twig
{% for item in posts %}
<div>
  <h3>{{ item.title }}</h3>
</div>
{% endfor %}
```

#### Custom Loop Variable

```html
<article loopsource="products" loopvariable="product">
  <h2>{{ product.title }}</h2>
  <p>{{ product.price }}</p>
</article>
```

Renders as:

```twig
{% for product in products %}
<article>
  <h2>{{ product.title }}</h2>
  <p>{{ product.price }}</p>
</article>
{% endfor %}
```

#### Available Attributes:

- `loopsource` (required): The collection to iterate (e.g., "posts", "item in posts")
- `loopvariable` (optional): Variable name for each item (defaults to "item")

---

### 2. Conditional Visibility Attributes

**Wraps element in `{% if %}` condition**

```html
<div conditionalvisibility="true" conditionalexpression="user.ID > 0">
  <p>Logged in content</p>
</div>
```

Renders as:

```twig
{% if user.ID > 0 %}
<div>
  <p>Logged in content</p>
</div>
{% endif %}
```

#### Available Attributes:

- `conditionalvisibility` (required): Must be "true" to enable
- `conditionalexpression` (required): The Twig condition (e.g., "user.ID > 0", "post.thumbnail")

---

### 3. Set Variable Attributes

**Outputs `{% set %}` before element**

```html
<div setvariable="greeting" setexpression="'Hello ' ~ user.display_name">
  <p>{{ greeting }}</p>
</div>
```

Renders as:

```twig
{% set greeting = 'Hello ' ~ user.display_name %}
<div>
  <p>{{ greeting }}</p>
</div>
```

#### Available Attributes:

- `setvariable` (required): Variable name to set
- `setexpression` (required): Twig expression for the value

---

### 4. Practical Example: CSS Carousel with ACF Gallery

**Use Case:** Creating a horizontal scrolling carousel from an ACF Gallery field using CSS scroll-snap.

This example demonstrates the **correct** way to combine `loopsource` and `setvariable` attributes, and why attribute placement matters.

#### The Problem

ACF Gallery fields return an array of image IDs (integers), not Timber Image objects. To display images properly and access methods like `.src()` and `.alt`, you need to:

1. Loop over the gallery IDs
2. Transform each ID to a Timber Image object
3. Display the image with proper attributes

#### The Solution

**✅ CORRECT - Loop and set on different elements:**

```html
<!-- Project Gallery Carousel -->
<section conditionalvisibility="true" conditionalexpression="post.meta('show_gallery')">
  <div class="rounded-3xl border border-white/10 bg-zinc-950/70 backdrop-blur p-8 sm:p-12">
    <h2 class="text-3xl font-light text-white tracking-tight mb-8">Project Gallery</h2>

    <!-- Carousel Container with Loop -->
    <div
      class="flex overflow-x-scroll snap-x snap-mandatory gap-4 scrollbar-hide"
      loopsource="post.meta('project_gallery')"
      loopvariable="image"
    >
      <!-- Each carousel item with Image transformation -->
      <div
        class="snap-center shrink-0 w-[85%] sm:w-[75%] md:w-[65%] lg:w-[55%]"
        setvariable="gallery_image"
        setexpression="timber.get_image(image)"
      >
        <div class="relative overflow-hidden rounded-xl border border-white/10 group">
          <div class="aspect-video relative overflow-hidden">
            <img
              src="{{ gallery_image.src('large') }}"
              alt="{{ gallery_image.alt }}"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div
              class="absolute inset-0 bg-gradient-to-b from-black/0 to-black/40  group-hover:opacity-100 transition-opacity duration-300"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
```

**What's happening:**

1. **Outer div** (line 9): Has `loopsource` and `loopvariable` - creates the `{% for image in post.meta('project_gallery') %}` loop
2. **Inner div** (line 13): Has `setvariable` and `setexpression` - transforms each `image` ID to a Timber Image object
3. **Image element** (line 18): Uses the transformed `gallery_image` object to access `.src('large')` and `.alt`

**Renders as:**

```twig
{% for image in post.meta('project_gallery') %}
  {% set gallery_image = timber.get_image(image) %}
  <div class="snap-center shrink-0...">
    <div class="relative overflow-hidden...">
      <div class="aspect-video...">
        <img src="{{ gallery_image.src('large') }}" alt="{{ gallery_image.alt }}">
      </div>
    </div>
  </div>
{% endfor %}
```

#### Why Attribute Placement Matters

**❌ WRONG - Loop and set on same element:**

```html
<!-- This won't work! -->
<div
  loopsource="post.meta('project_gallery')"
  loopvariable="image"
  setvariable="gallery_image"
  setexpression="timber.get_image(image)"
>
  <img src="{{ gallery_image.src('large') }}" />
</div>
```

**Problem:** When both attributes are on the same element, the transformation happens at the wrong scope and `gallery_image` is undefined inside the loop.

**✅ CORRECT - Separate elements:**

```html
<!-- Loop on parent -->
<div loopsource="post.meta('project_gallery')" loopvariable="image">
  <!-- Transform on child -->
  <div setvariable="gallery_image" setexpression="timber.get_image(image)">
    <img src="{{ gallery_image.src('large') }}" />
  </div>
</div>
```

**Why this works:** The `setvariable` runs **inside** the loop scope where `image` exists, creating `gallery_image` for each iteration.

#### CSS Carousel Classes Explained

The carousel uses pure CSS scroll-snap (no JavaScript required):

| Class                   | Purpose                                     |
| ----------------------- | ------------------------------------------- |
| `flex`                  | Creates horizontal flex container           |
| `overflow-x-scroll`     | Enables horizontal scrolling                |
| `snap-x snap-mandatory` | Forces scroll snapping on X-axis            |
| `snap-center`           | Items snap to center of container           |
| `shrink-0`              | Prevents items from shrinking               |
| `scrollbar-hide`        | Hides the scrollbar (Tailwind plugin)       |
| `w-[85%]` etc           | Responsive widths showing partial next item |
| `gap-4`                 | Spacing between carousel items              |

#### Complete Working Example

**ACF Setup:**

```php
// In ACF field group
[
  'key' => 'field_project_gallery',
  'label' => 'Project Gallery',
  'name' => 'project_gallery',
  'type' => 'gallery',
  'return_format' => 'id', // Important: Return IDs, not arrays
]
```

**Template Usage:**

```html
<section conditionalvisibility="true" conditionalexpression="post.meta('show_gallery')">
  <div class="container">
    <h2>Project Gallery</h2>

    <div
      class="flex overflow-x-scroll snap-x snap-mandatory gap-4 scrollbar-hide"
      loopsource="post.meta('project_gallery')"
      loopvariable="image"
    >
      <div
        class="snap-center shrink-0 w-[300px]"
        setvariable="gallery_image"
        setexpression="timber.get_image(image)"
      >
        <img src="{{ gallery_image.src('large') }}" alt="{{ gallery_image.alt }}" />
      </div>
    </div>
  </div>
</section>
```

**Result:** A horizontal scrolling carousel where:

- Users can swipe/scroll through images
- Images snap to center position
- Each image is 300px wide
- Images are loaded at 'large' WordPress size
- Proper alt text for accessibility

#### Key Takeaways

1. **Attribute Separation**: `loopsource` and `setvariable` must be on different elements (parent/child relationship)
2. **Data Transformation**: Use `timber.get_image()` to transform ACF image IDs to Timber Image objects
3. **Scope Management**: `setvariable` must be inside the loop scope to access loop variables
4. **ACF Configuration**: Set gallery return format to 'id' for this pattern to work
5. **No JavaScript**: Pure CSS scroll-snap provides smooth native scrolling behavior

---

## Complete Examples

### Example 1: Blog Post Loop with Conditionals

```html
<section loopsource="posts" loopvariable="post">
  <article>
    <h2>{{ post.title }}</h2>

    <div conditionalvisibility="true" conditionalexpression="post.thumbnail">
      <img src="{{ post.thumbnail.src }}" alt="{{ post.title }}" />
    </div>

    <div class="meta">
      <time>{{ post.date|date('F j, Y') }}</time>
      <span>by {{ post.author.name }}</span>
    </div>

    <div class="excerpt">{{ post.excerpt }}</div>

    <a href="{{ post.link }}">Read More</a>
  </article>
</section>
```

### Example 2: User Greeting with Set Variables

```html
<header
  setvariable="greeting"
  setexpression="user.ID > 0 ? 'Welcome back, ' ~ user.display_name : 'Welcome, Guest'"
>
  <h1>{{ greeting }}</h1>

  <nav conditionalvisibility="true" conditionalexpression="user.ID > 0">
    <a href="/dashboard">Dashboard</a>
    <a href="/profile">Profile</a>
    <a href="/logout">Logout</a>
  </nav>

  <nav conditionalvisibility="true" conditionalexpression="user.ID == 0">
    <a href="/login">Login</a>
    <a href="/register">Register</a>
  </nav>
</header>
```

### Example 3: Product Grid with Nested Loops

```html
<div loopsource="product_categories" loopvariable="category">
  <h2>{{ category.name }}</h2>

  <div class="product-grid" loopsource="category.products" loopvariable="product">
    <div class="product-card">
      <img src="{{ product.image.src }}" alt="{{ product.title }}" />
      <h3>{{ product.title }}</h3>
      <p class="price">{{ product.price }}</p>
      <a href="{{ product.link }}">View Product</a>
    </div>
  </div>
</div>
```

### Example 4: FSE Header with Dynamic Menu

```html
<header class="site-header">
  <div class="container">
    <a href="{{ site.url }}" class="logo">
      <img src="{{ site.theme.link }}/assets/logo.svg" alt="{{ site.name }}" />
    </a>

    <nav loopsource="menu.get_items('primary')" loopvariable="item">
      <a href="{{ item.url }}" class="{{ item.current ? 'active' : '' }}"> {{ item.title }} </a>
    </nav>

    <div class="user-menu" conditionalvisibility="true" conditionalexpression="user.ID > 0">
      <span>{{ user.display_name }}</span>
      <img src="{{ user.avatar }}" alt="Avatar" />
    </div>
  </div>
</header>
```

---

## Best Practices

### 1. Data Fetching - Use Context Filters First! 🌟

**ALWAYS prefer `timber/context` filter over template helpers:**

```php
// ✅ GOOD - Use context filter in functions.php
add_filter('timber/context', function ($context) {
    if (is_singular('post')) {
        $context['related_posts'] = Timber::get_posts([/* ... */]);
    }
    return $context;
});
```

```html
<!-- ✅ GOOD - Clean template -->
<div loopsource="related_posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
</div>
```

```html
<!-- ⚠️ TEMPLATE FETCHING - Valid for self-contained components -->
<!-- ✅ GOOD - Reusable component with built-in data -->
<div setvariable="recent_posts" setexpression="timber.get_posts({'posts_per_page': 3})">
  <h2>Latest Updates</h2>
  <div loopsource="recent_posts" loopvariable="post">
    <h3>{{ post.title }}</h3>
  </div>
</div>

<!-- ❌ BAD - Complex page-specific queries (use context filter instead) -->
{% set related_posts = timber.get_posts({'post_type': 'post', 'category__in': [1,2,3], 'meta_key': 'featured'}) %}
```

**When template data fetching is valid:**

- **Self-contained reusable components/patterns** - Components that need to work independently anywhere on the site
- **Simple, generic queries** - e.g., "latest 3 posts", "recent comments"
- **Component encapsulation** - When the component owns its data requirements

**When to use context filters instead:**

- **Page-specific data** - Data that depends on the current page/post context
- **Complex queries** - Queries with multiple conditions, joins, or custom SQL
- **Performance-critical** - Data needed across multiple templates or sections
- **Shared data** - Data used by multiple components on the same page

**When to use template helpers:**

- Dynamic conditions: `{% if fun.is_user_logged_in() %}`
- User-specific data that changes per request
- Conditional ACF fields based on template logic

### 2. Attribute Naming

- Control attributes use **lowercase** in HTML markup
- They are case-insensitive: `loopSource`, `loopsource`, `LOOPSOURCE` all work
- Prefer lowercase for consistency

### 3. Quote Escaping

When using Twig expressions in attributes, use single quotes inside double quotes:

```html
<!-- Good -->
<div setexpression="'Hello ' ~ user.name">
  <!-- Avoid -->
  <div setexpression='"Hello " ~ user.name'></div>
</div>
```

### 4. Combining Approaches

You can mix control attributes with inline Twig:

```html
<div loopsource="posts" conditionalvisibility="true" conditionalexpression="posts|length > 0">
  <h3>{{ item.title }}</h3>
  <p>{{ item.excerpt|truncate(100) }}</p>
</div>
```

### 5. Attribute Order Doesn't Matter

Control attributes are processed in this order regardless of how you write them:

1. `setvariable` / `setexpression` (runs first)
2. `conditionalvisibility` / `conditionalexpression` (wraps opening)
3. `loopsource` / `loopvariable` (wraps innermost)
4. Element renders
5. Closing tags in reverse order

### 6. Debugging

Add `?twig=false` to URL to see raw Twig syntax before compilation:

```
https://yoursite.com/page/?twig=false
```

Enable debug mode in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', true);
```

### 7. Available Context

The following variables are available in all Twig expressions:

**Post Context:**

- `post` - Current post object
- `post.ID`, `post.title`, `post.content`, `post.excerpt`
- `post.link`, `post.thumbnail`, `post.author`, `post.date`
- `post.meta('field_name')` - Custom fields
- `post.categories` - Array of category terms (NOT `post.category`)

**User Context:**

- `user` - Current user object
- `user.ID`, `user.display_name`, `user.email`
- `user.roles`, `user.avatar`

**Site Context:**

- `site` - Site object
- `site.name`, `site.url`, `site.theme`

**Custom Context:**
Use the `timber/context` filter to add custom data (see [Context Filters](#context-filters-recommended-approach) section).

---

## Context Filters (Recommended Approach)

**IMPORTANT:** Instead of fetching data directly in templates using `timber.get_posts()` or `fun.get_field()`, it's **strongly recommended** to use the `timber/context` filter to prepare data in PHP. This approach is cleaner, more performant, and follows Timber best practices.

### Why Use Context Filters?

1. **Separation of Concerns**: Keep data logic in PHP, presentation in templates
2. **Better Performance**: Data fetched once and cached by Timber
3. **Cleaner Templates**: Templates focus on display, not data fetching
4. **Easier Testing**: Test data logic separately from templates
5. **Reusability**: Context data available across all templates automatically

### The `timber/context` Filter

Add this filter to your theme's `functions.php` to extend the global Timber context:

```php
add_filter('timber/context', function ($context) {
    // Add custom data to context
    $context['custom_variable'] = 'value';

    return $context;
});
```

### Common Use Cases

#### 1. Related Posts on Single Posts

**Problem:** You want to show related posts based on categories.

**Bad Approach (in template):**

```html
<!-- DON'T DO THIS -->
<section>
  {% set related_posts = timber.get_posts({'post_type': 'post', 'posts_per_page': 6, 'post__not_in':
  [post.ID]}) %}
  <div loopsource="related_posts" loopvariable="article">
    <h3>{{ article.title }}</h3>
  </div>
</section>
```

**Good Approach (using filter):**

**In functions.php:**

```php
add_filter('timber/context', function ($context) {
    // Only on single post pages
    if (is_singular('post')) {
        $current_post = $context['post'];

        // Get categories of current post
        $categories = wp_get_post_categories($current_post->ID);

        // Query related posts
        $context['related_posts'] = Timber::get_posts([
            'post_type' => 'post',
            'posts_per_page' => 6,
            'post__not_in' => [$current_post->ID],
            'category__in' => $categories,
            'orderby' => 'rand'
        ]);
    }

    return $context;
});
```

**In template:**

```html
<!-- Clean and simple! -->
<section conditionalvisibility="true" conditionalexpression="related_posts|length > 0">
  <h2>Related Articles</h2>
  <div loopsource="related_posts" loopvariable="article">
    <article>
      <h3>{{ article.title }}</h3>
      <p>{{ article.excerpt }}</p>
      <a href="{{ article.link }}">Read More</a>
    </article>
  </div>
</section>
```

#### 2. WooCommerce Product Data

**In functions.php:**

```php
add_filter('timber/context', function ($context) {
    // On single product pages
    if (is_singular('product')) {
        $product_id = $context['post']->ID;

        // Get related products
        $context['related_products'] = Timber::get_posts([
            'post_type' => 'product',
            'posts_per_page' => 4,
            'post__not_in' => [$product_id],
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']),
                ]
            ]
        ]);

        // Get product categories
        $context['product_categories'] = Timber::get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true
        ]);
    }

    // On shop archive
    if (is_post_type_archive('product')) {
        $context['featured_products'] = Timber::get_posts([
            'post_type' => 'product',
            'posts_per_page' => 3,
            'meta_key' => '_featured',
            'meta_value' => 'yes'
        ]);
    }

    return $context;
});
```

**In template:**

```html
<div loopsource="related_products" loopvariable="product">
  <div class="product-card">
    <img src="{{ product.thumbnail.src }}" alt="{{ product.title }}" />
    <h3>{{ product.title }}</h3>
    <p class="price">{{ product.meta('_price') }}</p>
  </div>
</div>
```

#### 3. ACF Options and Global Data

**In functions.php:**

```php
add_filter('timber/context', function ($context) {
    // Add ACF options (available on all pages)
    $context['theme_options'] = get_field('theme_options', 'option');
    $context['social_links'] = get_field('social_links', 'option');
    $context['contact_info'] = get_field('contact_info', 'option');

    // Add navigation menus
    $context['primary_menu'] = Timber::get_menu('primary');
    $context['footer_menu'] = Timber::get_menu('footer');

    return $context;
});
```

**In template (FSE header):**

```html
<header>
  <nav loopsource="primary_menu.items" loopvariable="item">
    <a href="{{ item.url }}" class="{{ item.current ? 'active' : '' }}"> {{ item.title }} </a>
  </nav>

  <div class="social">
    <a href="{{ social_links.facebook }}">Facebook</a>
    <a href="{{ social_links.twitter }}">Twitter</a>
  </div>
</header>
```

#### 4. User-Specific Data

**In functions.php:**

```php
add_filter('timber/context', function ($context) {
    // Current user is already in context as 'user'
    // But you can add more user-related data

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

        // User's posts
        $context['user_posts'] = Timber::get_posts([
            'author' => $user_id,
            'posts_per_page' => 5
        ]);

        // User's favorites (using custom meta)
        $favorite_ids = get_user_meta($user_id, 'favorite_posts', true);
        if ($favorite_ids) {
            $context['user_favorites'] = Timber::get_posts([
                'post__in' => $favorite_ids,
                'orderby' => 'post__in'
            ]);
        }
    }

    return $context;
});
```

**In template:**

```html
<div conditionalvisibility="true" conditionalexpression="user.ID > 0">
  <h2>Your Recent Posts</h2>
  <div loopsource="user_posts" loopvariable="post">
    <article>
      <h3>{{ post.title }}</h3>
      <a href="{{ post.link }}">Edit</a>
    </article>
  </div>
</div>
```

#### 5. Archive-Specific Context

**In functions.php:**

```php
add_filter('timber/context', function ($context) {
    // On category archives
    if (is_category()) {
        $category = get_queried_object();
        $context['category_description'] = $category->description;
        $context['category_image'] = get_field('category_image', 'category_' . $category->term_id);

        // Related categories
        $context['related_categories'] = Timber::get_terms([
            'taxonomy' => 'category',
            'exclude' => [$category->term_id],
            'number' => 5
        ]);
    }

    // On date archives
    if (is_date()) {
        $year = get_query_var('year');
        $month = get_query_var('monthnum');

        $context['archive_title'] = $month
            ? date('F Y', mktime(0, 0, 0, $month, 1, $year))
            : $year;
    }

    return $context;
});
```

**In template:**

```html
<div class="archive-header">
  <h1>{{ archive_title }}</h1>
  <p>{{ category_description }}</p>

  <img
    conditionalvisibility="true"
    conditionalexpression="category_image"
    src="{{ category_image.url }}"
    alt="{{ category_image.alt }}"
  />
</div>
```

### Best Practices for Context Filters

1. **Check Context Before Adding:**

   ```php
   add_filter('timber/context', function ($context) {
       // Only add data where it's needed
       if (is_singular('post')) {
           $context['related_posts'] = /* ... */;
       }

       if (is_page('about')) {
           $context['team_members'] = /* ... */;
       }

       return $context;
   });
   ```

2. **Avoid Heavy Queries on Every Page:**

   ```php
   // Good - only on specific pages
   if (is_singular('product')) {
       $context['related_products'] = Timber::get_posts(/* ... */);
   }

   // Bad - runs on EVERY page
   $context['all_products'] = Timber::get_posts(['post_type' => 'product', 'posts_per_page' => -1]);
   ```

3. **Use Timber Objects:**

   ```php
   // Good - returns Timber\Post objects
   $context['posts'] = Timber::get_posts([/* ... */]);

   // Bad - returns WP_Post objects
   $context['posts'] = get_posts([/* ... */]);
   ```

4. **Provide Fallbacks:**

   ```php
   add_filter('timber/context', function ($context) {
       $context['related_posts'] = Timber::get_posts([/* ... */]) ?: [];
       // Now templates can safely check: {% if related_posts|length > 0 %}

       return $context;
   });
   ```

5. **Group Related Data:**

   ```php
   add_filter('timber/context', function ($context) {
       // Group WooCommerce data
       $context['shop'] = [
           'featured' => Timber::get_posts([/* featured products */]),
           'categories' => Timber::get_terms([/* categories */]),
           'cart_count' => WC()->cart->get_cart_contents_count()
       ];

       return $context;
   });
   ```

---

## Helper Functions (Use with Discretion)

**⚠️ IMPORTANT:** These helpers provide flexibility but should be used thoughtfully. Understand when each approach is appropriate.

**Recommended Approaches (in order of preference):**

1. **Context Filters** - For page-specific data, complex queries, and shared data across templates
2. **Timber Objects** - Access data via Timber Post/Term methods like `{{ post.meta('field_name') }}`
3. **Template Helpers** - For self-contained reusable components, simple generic queries, and dynamic conditions
4. **Emergency Fixes** - Quick editor changes when proper approach isn't immediately feasible

Universal Block provides two helper objects for accessing PHP and Timber functions from Twig:

### 1. Magic Function Helper (`fun`)

The `fun` object allows you to call any PHP function directly from Twig templates.

**Syntax:** `{{ fun.function_name(args) }}`

**Examples (for emergency use only):**

```twig
{# ❌ DON'T DO THIS - Use post.meta('custom_field') instead #}
{{ fun.get_post_meta(post.ID, 'custom_field', true) }}
{{ fun.get_field('custom_field', post.ID) }}

{# ❌ DON'T DO THIS - Add to context filter instead #}
{% set options = fun.get_field('theme_options', 'option') %}

{# ✅ ONLY acceptable uses #}
{{ fun.is_user_logged_in() }}  {# Conditional checks #}
{{ fun.current_user_can('edit_posts') }}  {# Permission checks #}
{{ fun.my_emergency_custom_function() }}  {# Emergency fixes #}
```

**Proper Alternatives:**

❌ **WRONG - Using fun for custom fields:**

```twig
{{ fun.get_field('subtitle', post.ID) }}
```

✅ **RIGHT - Using Timber Post object:**

```twig
{{ post.meta('subtitle') }}
```

❌ **WRONG - Using fun for options:**

```twig
{% set logo = fun.get_option('site_logo') %}
```

✅ **RIGHT - Using context filter:**

```php
// In functions.php
add_filter('timber/context', function($context) {
    $context['site_logo'] = get_option('site_logo');
    return $context;
});
```

```twig
<!-- In template -->
{{ site_logo }}
```

**Legitimate Use Cases (Rare):**

- Conditional function calls: `{% if fun.is_user_logged_in() %}`
- Permission checks: `{{ fun.current_user_can('edit_posts') }}`
- Emergency hotfixes directly in block editor
- Utility functions that don't fetch data

### 2. Timber Wrapper (`timber`)

The `timber` object provides direct access to Timber static methods.

**⚠️ CONTEXT MATTERS:** This helper can be used for self-contained components, but context filters are preferred for page-specific data.

**Syntax:** `{{ timber.method_name(args) }}`

**Examples - Valid Use Cases:**

```twig
{# ✅ VALID - Self-contained reusable component #}
<div setvariable="latest_posts" setexpression="timber.get_posts({'posts_per_page': 3})">
  <h3>Latest Updates</h3>
  <div loopsource="latest_posts" loopvariable="post">
    <span>{{ post.title }}</span>
  </div>
</div>

{# ❌ AVOID - Complex page-specific queries (use context filter) #}
{% set posts = timber.get_posts({'category__in': [1,2,3], 'meta_key': 'featured'}) %}
{% set menu = timber.get_menu('primary') %}  {# Better in context filter #}
{% set categories = timber.get_terms('category') %}  {# Better in context filter #}
```

**Comparison:**

❌ **AVOID - Complex queries or page-specific data:**

```twig
{% set posts = timber.get_posts({'post_type': 'post', 'posts_per_page': 5, 'category__in': [1,2,3]}) %}
<div loopsource="posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
</div>
```

✅ **PREFERRED - Context filter for complex/page-specific data:**

```php
// In functions.php or src/context/recent-posts.php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts([
        'post_type' => 'post',
        'posts_per_page' => 5
    ]);
    return $context;
});
```

```html
<!-- In template -->
<div loopsource="recent_posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
</div>
```

**When to use each approach:**

**Context Filters (Preferred for):**
- ✅ Page-specific data requirements
- ✅ Complex queries with multiple conditions
- ✅ Data needed across multiple templates/sections
- ✅ Performance-critical queries (cached by Timber)
- ✅ Shared data between components

**Template Helpers (Valid for):**
- ✅ Self-contained reusable components/patterns
- ✅ Simple, generic queries ("latest 3 posts")
- ✅ Components that work independently anywhere
- ✅ Data encapsulated within a single component
- ✅ Quick prototyping or emergency fixes

### When to Use `timber.get_image()` - The Exception to the Rule

**✅ VALID USE CASE:** Transforming image IDs to Image objects within loops using `setvariable`

While you should generally avoid using `timber.*` functions for data fetching, there's one important exception: **transforming data types within loops**, especially converting image IDs to Timber Image objects.

**The Problem:** ACF image fields often return image IDs (integers), not Image objects. To access image properties like `.src()` and `.alt`, you need to convert the ID to a Timber Image object.

**✅ CORRECT - Using `timber.get_image()` with `setvariable`:**

```html
<!-- Gallery with image IDs from ACF -->
<div loopsource="post.meta('project_gallery')" loopvariable="image">
  <div setvariable="gallery_image" setexpression="timber.get_image(image)">
    <img
      src="{{ gallery_image.src('large') }}"
      alt="{{ gallery_image.alt }}"
      class="w-full h-full object-cover"
    />
  </div>
</div>
```

**What's happening:**

1. `post.meta('project_gallery')` returns an array of image IDs (e.g., `[123, 456, 789]`)
2. Loop iterates, setting `image` to each ID (e.g., `123`)
3. `setvariable` transforms the ID: `timber.get_image(123)` returns a Timber Image object
4. Now `gallery_image.src('large')` and `gallery_image.alt` work properly

**Why this is valid:**

- ✅ Not fetching new data - just transforming existing data
- ✅ Necessary because ACF returns IDs, not Image objects
- ✅ Happens within loop scope - no better alternative
- ✅ Uses `setvariable` pattern from Twig Control Attributes

**Other valid transformation use cases:**

```html
<!-- Convert term ID to Term object -->
<div setvariable="category" setexpression="timber.get_term(term_id)">{{ category.name }}</div>

<!-- Convert post ID to Post object -->
<div setvariable="related" setexpression="timber.get_post(post_id)">{{ related.title }}</div>
```

**Key Rule:** Use `timber.*` functions for **transforming data types**, not for **fetching new data**. If you're looping over IDs that came from ACF/context, transforming them to Timber objects is the correct approach.

---

### Accessing Custom Fields - The Right Way

**Use Timber Post object methods, NOT helper functions!**

❌ **WRONG - Using fun helper:**

```twig
{{ fun.get_field('subtitle', post.ID) }}
{{ fun.get_post_meta(post.ID, 'custom_field', true) }}
```

✅ **RIGHT - Using Timber Post object:**

```twig
{{ post.meta('subtitle') }}
{{ post.meta('custom_field') }}
```

**Complete Example - Proper MVC Approach:**

```php
// In src/context/page-data.php
add_filter('timber/context', function($context) {
    // Add global options to context
    $context['site_logo'] = get_option('site_logo');
    $context['contact_email'] = get_option('contact_email');

    // Add recent posts on homepage
    if (is_front_page()) {
        $context['recent_posts'] = Timber::get_posts([
            'posts_per_page' => 3
        ]);
    }

    return $context;
});
```

```html
<!-- In template - Clean and simple! -->
<header>
  <img src="{{ site_logo }}" alt="{{ site.name }}" />
</header>

<section loopsource="recent_posts" loopvariable="post">
  <article>
    <h2>{{ post.title }}</h2>

    <!-- Access custom fields via post.meta() -->
    <p class="subtitle">{{ post.meta('subtitle') }}</p>

    <!-- Access categories via post.categories -->
    <div loopsource="post.categories" loopvariable="cat">
      <span>{{ cat.name }}</span>
    </div>

    <a href="{{ post.link }}">Read More</a>
  </article>
</section>

<footer>
  <p>Contact: {{ contact_email }}</p>
</footer>
```

### When Helpers ARE Acceptable

**Only use helpers for:**

1. **Conditional checks (no data fetching):**

   ```html
   <nav conditionalvisibility="true" conditionalexpression="fun.is_user_logged_in()">
     <a href="/dashboard">Dashboard</a>
   </nav>
   ```

2. **Permission checks:**

   ```html
   <button conditionalvisibility="true" conditionalexpression="fun.current_user_can('edit_posts')">
     Edit Post
   </button>
   ```

3. **Emergency hotfixes in block editor:**
   ```twig
   {# Temporary fix until you can add to context filter #}
   {% if fun.get_option('maintenance_mode') %}
     <div>Site under maintenance</div>
   {% endif %}
   ```

---

## Notes for LLMs

When generating Universal Block markup:

1. **Custom WordPress Elements (NEW - USE THESE!):**

   ⭐ **ALWAYS use custom elements for template parts, patterns, and post content!**

   ✅ **DO THIS:**

   ```html
   <!-- Include header/footer -->
   <Part slug="header"></Part>
   <Part slug="footer"></Part>

   <!-- Include patterns -->
   <Pattern slug="hero-section"></Pattern>

   <!-- Display post content -->
   <content class="prose"></content>
   ```

   ❌ **NEVER DO THIS:**

   ```html
   <!-- Don't write WordPress block comments directly -->
   <!-- wp:template-part {"slug":"header"} /-->
   <!-- wp:pattern {"slug":"hero"} /-->
   ```

   **CRITICAL: Always use closing tags, NEVER self-closing:**

   ```html
   <!-- ✅ CORRECT -->
   <Part slug="header"></Part>

   <!-- ❌ WRONG - Will break parser -->
   <Part slug="header" />
   ```

2. **Data Fetching Strategy (CRITICAL - READ THIS FIRST!):**

   **Context filters are PREFERRED, but template helpers have valid use cases.**

   ✅ **PREFERRED - Use context filters for:**
   - Page-specific data requirements
   - Complex queries with multiple conditions
   - Data needed across multiple templates
   - Performance-critical queries
   - Use Timber Post methods: `{{ post.meta('field_name') }}` for custom fields
   - Use Timber object properties: `{{ post.categories }}`, `{{ post.thumbnail }}`

   ✅ **VALID - Use template helpers for:**
   - Self-contained reusable components/patterns
   - Simple generic queries: `timber.get_posts({'posts_per_page': 3})`
   - Components that work independently anywhere
   - Data encapsulated within a single component

   ❌ **NEVER DO THIS:**
   - `{{ fun.get_field('field_name') }}` - Use `{{ post.meta('field_name') }}` instead!
   - `{{ fun.get_post_meta(post.ID, 'key') }}` - Use `{{ post.meta('key') }}` instead!
   - Complex queries in templates when they're page-specific

   **Why context filters are preferred:**
   - Better for MVC pattern (logic in PHP, presentation in templates)
   - Better performance (cached by Timber, no redundant queries)
   - Easier to test and maintain
   - Cleaner templates

   **Why template helpers can be valid:**
   - Component encapsulation (component owns its data)
   - Reusability (works anywhere without setup)
   - Simplicity (no context filter needed for simple cases)

3. **Choose the right approach:**
   - Use **custom elements** for WordPress parts/patterns/content
   - Use **inline Twig** for simple variable output
   - Use **control attributes** when working with block editor UI
   - All approaches compile to the same output

4. **Template Structure:**

   ```html
   <!-- Always start templates with Part elements -->
   <Part slug="header"></Part>

   <main>
     <!-- Use Content for post content -->
     <content class="prose"></content>

     <!-- Use Pattern for reusable sections -->
     <Pattern slug="call-to-action"></Pattern>
   </main>

   <Part slug="footer"></Part>
   ```

5. **FSE Support:**
   - All markup works in FSE template parts
   - Headers, footers, and sidebars compile on `shutdown` hook
   - Custom elements work in all contexts (templates, pages, patterns, parts)
   - No special handling needed

6. **Validation:**
   - Custom elements MUST use closing tags (not self-closing)
   - Control structures must be properly nested
   - Opening tags need closing tags
   - Twig syntax must be valid

7. **Performance:**
   - Twig compiles once per page (not per block)
   - Safe to use multiple dynamic elements
   - Timber caches compiled templates
   - Use context filters to avoid redundant queries

---

**Version:** 1.0.0
**Last Updated:** 2025-01-26
