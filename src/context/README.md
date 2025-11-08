# Context Filters Directory

## Overview

This directory implements the **data layer** of an MVC architecture for WordPress templates—providing a clean separation between data fetching (PHP) and presentation (Twig templates).

Think of this as **where your templates get their data**. Instead of mixing PHP queries into templates or using helper functions, you define data once here and access it cleanly in any template.

---

## Philosophy

### The Problem This Solves

Traditional WordPress and even Timber templates often mix data fetching with presentation:

**Bad (mixed concerns):**
```twig
{# Template file - mixing data and presentation #}
{% set posts = timber.get_posts({'posts_per_page': 5}) %}
<div loopsource="posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
</div>
```

**Problems:**
- Data logic scattered across templates
- Duplicate queries in multiple files
- Hard to test data independently
- Violates MVC separation of concerns
- Templates know too much about data structure

### The Solution

Context filters centralize data fetching in PHP files that automatically load and inject data into the global Timber context:

**Good (separated concerns):**
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

```twig
{# Template file - clean presentation #}
<div loopsource="recent_posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
</div>
```

**Benefits:**
1. **Single responsibility** - Each file provides specific data
2. **Reusable** - Data available in all templates
3. **Testable** - Data logic separate from presentation
4. **Maintainable** - Update data queries in one place
5. **MVC compliant** - Proper separation of concerns

### Core Principle

**"Templates should render data, not fetch it. Data fetching belongs in context filters."**

---

## How It Works

### Auto-Loading

All PHP files in `src/context/` are automatically loaded by the theme:

```php
// functions.php
foreach (glob(get_template_directory() . '/src/context/*.php') as $context_file) {
    require_once $context_file;
}
```

**This means:**
- Drop a new PHP file here → It automatically loads
- No manual registration needed
- No imports or requires in other files
- Files are self-contained

### Context Filter Pattern

Every file follows the same pattern:

```php
<?php
/**
 * [Descriptive Name] Context
 *
 * Brief description of what data this provides and why.
 *
 * Example usage in templates:
 * {{ variable_name.property }}
 */

add_filter('timber/context', function($context) {
    // Fetch or prepare data
    $data = get_some_data();

    // Add to context under a descriptive key
    $context['variable_name'] = $data;

    // Return modified context
    return $context;
});
```

### Accessing in Templates

Once defined, context variables are available in **all** Twig templates:

```twig
{# Any template file #}
{{ recent_posts }}           {# Available everywhere #}
{{ user_data }}             {# Available everywhere #}
{{ site_settings }}         {# Available everywhere #}
```

---

## Directory Structure

### Typical Organization

```
src/context/
├── README.md               # This file
├── recent-posts.php        # Recent posts data
├── related-posts.php       # Related content logic
├── user-data.php           # Current user information
├── site-settings.php       # Global site data
├── navigation.php          # Menu data
├── params.php              # URL query parameters
└── archive.php             # Archive page data
```

**Naming convention:** Use descriptive names that indicate what data the file provides.

---

## Common Use Cases

### 1. Recent Posts

**File:** `src/context/recent-posts.php`

```php
<?php
/**
 * Recent Posts Context
 *
 * Provides the 5 most recent published posts globally.
 * Available as {{ recent_posts }} in all templates.
 */

add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts([
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ]);

    return $context;
});
```

**Template usage:**
```twig
<div loopsource="recent_posts" loopvariable="post">
  <h3>{{ post.title }}</h3>
  <time>{{ post.date }}</time>
</div>
```

### 2. Related Posts

**File:** `src/context/related-posts.php`

```php
<?php
/**
 * Related Posts Context
 *
 * On single posts, provides related posts based on categories.
 * Empty array on non-post pages.
 */

add_filter('timber/context', function($context) {
    if (!is_single()) {
        $context['related_posts'] = [];
        return $context;
    }

    $post = $context['post'];
    $categories = wp_get_post_categories($post->ID);

    $context['related_posts'] = Timber::get_posts([
        'posts_per_page' => 3,
        'post__not_in' => [$post->ID],
        'category__in' => $categories,
        'post_status' => 'publish'
    ]);

    return $context;
});
```

**Template usage:**
```twig
{% if related_posts|length > 0 %}
  <section class="related">
    <h2>Related Articles</h2>
    <div loopsource="related_posts" loopvariable="post">
      <a href="{{ post.link }}">{{ post.title }}</a>
    </div>
  </section>
{% endif %}
```

### 3. URL Parameters

**File:** `src/context/params.php`

```php
<?php
/**
 * Params Context
 *
 * Makes URL query parameters available in templates.
 * Example: /page?email=user@example.com
 * Access as: {{ params.email }}
 */

add_filter('timber/context', function($context) {
    $params = [];

    if (!empty($_GET)) {
        foreach ($_GET as $key => $value) {
            // Sanitize key
            $sanitized_key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

            if (empty($sanitized_key)) {
                continue;
            }

            // Sanitize value
            if (is_array($value)) {
                $params[$sanitized_key] = array_map('sanitize_text_field', $value);
            } else {
                $params[$sanitized_key] = sanitize_text_field($value);
            }
        }
    }

    $context['params'] = $params;
    return $context;
});
```

**Template usage:**
```twig
{# URL: /page?email=user@example.com&name=John #}
<p>Email: {{ params.email }}</p>
<p>Name: {{ params.name }}</p>

{# Pre-fill form fields #}
<input type="email" value="{{ params.email }}">
```

### 4. Navigation Menus

**File:** `src/context/navigation.php`

```php
<?php
/**
 * Navigation Context
 *
 * Provides primary and footer menus to all templates.
 */

add_filter('timber/context', function($context) {
    $context['primary_menu'] = Timber::get_menu('primary');
    $context['footer_menu'] = Timber::get_menu('footer');

    return $context;
});
```

**Template usage:**
```twig
<nav>
  {% for item in primary_menu.items %}
    <a href="{{ item.link }}">{{ item.title }}</a>
  {% endfor %}
</nav>
```

### 5. Site Settings (ACF Options)

**File:** `src/context/site-settings.php`

```php
<?php
/**
 * Site Settings Context
 *
 * Provides global site settings from ACF Options page.
 */

add_filter('timber/context', function($context) {
    $context['site_settings'] = [
        'contact_email' => get_field('contact_email', 'option'),
        'phone' => get_field('phone_number', 'option'),
        'social_links' => get_field('social_links', 'option'),
        'footer_text' => get_field('footer_text', 'option')
    ];

    return $context;
});
```

**Template usage:**
```twig
<footer>
  <p>{{ site_settings.footer_text }}</p>
  <a href="mailto:{{ site_settings.contact_email }}">Contact Us</a>
</footer>
```

### 6. User Data

**File:** `src/context/user-data.php`

```php
<?php
/**
 * User Data Context
 *
 * Provides current logged-in user data, or null if not logged in.
 */

add_filter('timber/context', function($context) {
    if (is_user_logged_in()) {
        $context['current_user'] = Timber::get_user(get_current_user_id());
    } else {
        $context['current_user'] = null;
    }

    return $context;
});
```

**Template usage:**
```twig
{% if current_user %}
  <div>Welcome, {{ current_user.name }}!</div>
  <a href="{{ current_user.link }}">View Profile</a>
{% else %}
  <a href="/login">Log In</a>
{% endif %}
```

### 7. Archive Context

**File:** `src/context/archive.php`

```php
<?php
/**
 * Archive Context
 *
 * Provides archive-specific data (category, tag, author info).
 */

add_filter('timber/context', function($context) {
    if (is_category() || is_tag() || is_tax()) {
        $context['term'] = Timber::get_term();
    }

    if (is_author()) {
        $context['author'] = Timber::get_user();
    }

    if (is_post_type_archive()) {
        $context['post_type'] = get_post_type();
    }

    return $context;
});
```

**Template usage:**
```twig
{# category.twig #}
<h1>{{ term.name }}</h1>
<p>{{ term.description }}</p>

{# author.twig #}
<h1>Posts by {{ author.name }}</h1>
```

---

## Structure by Project Type

### Blog / Publication Site

**Focus:** Content relationships, author data, editorial metadata

```
src/context/
├── recent-posts.php
├── related-posts.php
├── popular-posts.php
├── author-data.php
├── category-data.php
└── featured-posts.php
```

### E-commerce Site

**Focus:** Product data, cart info, customer data

```
src/context/
├── featured-products.php
├── product-categories.php
├── cart-data.php
├── customer-data.php
└── recently-viewed.php
```

### SaaS / App Site

**Focus:** User data, account info, feature flags

```
src/context/
├── user-subscription.php
├── account-data.php
├── feature-flags.php
├── usage-stats.php
└── billing-info.php
```

### Agency / Portfolio Site

**Focus:** Project data, client testimonials, case studies

```
src/context/
├── featured-projects.php
├── services-data.php
├── testimonials.php
├── team-members.php
└── case-studies.php
```

### Marketing / Landing Page Site

**Focus:** Campaign data, A/B testing, conversion tracking

```
src/context/
├── campaign-params.php
├── ab-test-variant.php
├── conversion-data.php
└── tracking-info.php
```

---

## Best Practices

### 1. One Responsibility Per File

**Good:**
```php
// recent-posts.php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts([...]);
    return $context;
});
```

**Bad:**
```php
// everything.php
add_filter('timber/context', function($context) {
    $context['recent_posts'] = Timber::get_posts([...]);
    $context['user_data'] = Timber::get_user([...]);
    $context['menus'] = Timber::get_menu([...]);
    // Too many responsibilities
    return $context;
});
```

### 2. Use Descriptive Variable Names

**Good:**
```php
$context['recent_posts'] = ...;
$context['featured_products'] = ...;
$context['current_user'] = ...;
```

**Bad:**
```php
$context['posts'] = ...;   // Too generic
$context['data'] = ...;    // What data?
$context['items'] = ...;   // What items?
```

### 3. Handle Edge Cases

```php
add_filter('timber/context', function($context) {
    // Only run on single posts
    if (!is_single()) {
        $context['related_posts'] = [];
        return $context;
    }

    // Handle case where no related posts exist
    $related = Timber::get_posts([...]);
    $context['related_posts'] = $related ?: [];

    return $context;
});
```

### 4. Sanitize User Input

```php
add_filter('timber/context', function($context) {
    // Sanitize GET parameters
    $email = isset($_GET['email'])
        ? sanitize_email($_GET['email'])
        : '';

    $context['email'] = $email;
    return $context;
});
```

### 5. Document Your Context

```php
<?php
/**
 * [Name] Context
 *
 * WHAT: Brief description of what data this provides
 * WHY: Why this data is needed
 * WHERE: Which templates use this data
 * EXAMPLE: {{ variable_name.property }}
 */
```

### 6. Use Conditional Loading

```php
add_filter('timber/context', function($context) {
    // Only load on homepage
    if (is_front_page()) {
        $context['hero_data'] = get_field('hero_settings', 'option');
    }

    return $context;
});
```

This prevents unnecessary data fetching on pages that don't need it.

---

## Advanced Patterns

### Caching Expensive Queries

```php
<?php
/**
 * Popular Posts Context
 *
 * Expensive query - cached for 1 hour
 */

add_filter('timber/context', function($context) {
    $cache_key = 'popular_posts';
    $cached = wp_cache_get($cache_key);

    if ($cached !== false) {
        $context['popular_posts'] = $cached;
        return $context;
    }

    // Expensive meta query
    $popular = Timber::get_posts([
        'posts_per_page' => 10,
        'meta_key' => 'view_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    ]);

    wp_cache_set($cache_key, $popular, '', HOUR_IN_SECONDS);
    $context['popular_posts'] = $popular;

    return $context;
});
```

### Conditional Data by User Role

```php
<?php
/**
 * Admin Data Context
 *
 * Provides admin-only data if user has capability
 */

add_filter('timber/context', function($context) {
    if (current_user_can('manage_options')) {
        $context['admin_stats'] = [
            'pending_posts' => wp_count_posts()->pending,
            'total_users' => count_users()['total_users'],
            'recent_activity' => get_recent_activity()
        ];
    } else {
        $context['admin_stats'] = null;
    }

    return $context;
});
```

### API Data Integration

```php
<?php
/**
 * External API Context
 *
 * Fetches data from external API, cached for 30 minutes
 */

add_filter('timber/context', function($context) {
    $cache_key = 'api_data';
    $cached = get_transient($cache_key);

    if ($cached !== false) {
        $context['api_data'] = $cached;
        return $context;
    }

    $response = wp_remote_get('https://api.example.com/data');

    if (is_wp_error($response)) {
        $context['api_data'] = [];
        return $context;
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    set_transient($cache_key, $data, 30 * MINUTE_IN_SECONDS);

    $context['api_data'] = $data;
    return $context;
});
```

---

## Integration with Theme Workflow

### With Templates

```twig
{# templates/single.twig #}
<article>
  <h1>{{ post.title }}</h1>
  <div>{{ post.content }}</div>

  {# Data from src/context/related-posts.php #}
  {% if related_posts|length > 0 %}
    <aside>
      <h2>Related Articles</h2>
      <div loopsource="related_posts" loopvariable="post">
        <a href="{{ post.link }}">{{ post.title }}</a>
      </div>
    </aside>
  {% endif %}
</article>
```

### With Patterns

```html
<!-- src/patterns/related-posts.html -->
<section conditionalvisibility="true" conditionalexpression="related_posts|length > 0">
  <div class="content">
    <h2>Related Articles</h2>
    <div loopsource="related_posts" loopvariable="post" class="grid">
      <article class="v-card">
        <h3>{{ post.title }}</h3>
        <p>{{ post.excerpt }}</p>
        <a href="{{ post.link }}">Read More</a>
      </article>
    </div>
  </div>
</section>
```

Context filters provide the data (`related_posts`), patterns/templates handle presentation.

---

## Troubleshooting

### Context Variable Not Available

**Check:**
1. File exists in `src/context/`
2. File has `.php` extension
3. Contains valid `add_filter('timber/context', ...)` hook
4. Returns `$context` array
5. Variable name doesn't conflict with existing context

### Data Not Updating

**Solutions:**
1. Clear object cache: `wp cache flush`
2. Check if data is cached (transients, wp_cache)
3. Verify query parameters are correct
4. Check if conditional logic is preventing data load

### Performance Issues

**Optimize:**
1. Add caching for expensive queries
2. Use conditional loading (only load on specific pages)
3. Limit query results (`posts_per_page`)
4. Use `fields => 'ids'` if you only need IDs
5. Profile with Query Monitor plugin

---

## Related Documentation

- **[CLAUDE.md](../../CLAUDE.md)** - Theme architecture overview
- **[src/docs/block-markup-guide.md](../docs/block-markup-guide.md)** - Using context in templates
- **[src/content/README.md](../content/README.md)** - Content collections
- **[src/patterns/README.md](../patterns/README.md)** - Pattern templates

---

**Summary:**

This directory implements the **data layer** of MVC architecture for WordPress templates. Each PHP file adds specific data to the global Timber context, making it available in all templates. This creates clean separation between data fetching (here) and presentation (templates), following best practices for maintainable, testable code.

The structure you choose depends on your project's data needs, but the principle remains the same: **templates should render data, not fetch it.**

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
