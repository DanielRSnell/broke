# Theme Helpers & Features

Documentation for built-in theme features and helper functions.

---

## Table of Contents

1. [Archive Options](#archive-options)
2. [Extending Archive Fields](#extending-archive-fields)

---

## Archive Options

### Overview

Automatically generates ACF options pages for custom post types with archives enabled. This provides a UI to manage archive page titles and descriptions without editing code.

### Configuration

Enable in `settings.json`:

```json
{
  "src": {
    "archive_options": true
  }
}
```

### What It Does

When enabled, the theme will:

1. **Create Settings Page** - Adds a "Settings" submenu under each custom post type with archives
2. **Generate Fields** - Automatically creates two ACF fields:
   - `archive_title` - Text field for the archive page heading
   - `archive_description` - Textarea for the archive page description
3. **Add Timber Context** - Makes fields available as `{{ archive.title }}` and `{{ archive.description }}` in templates

### Usage in Templates

Access the archive data in your Twig templates:

```html
<h1>{{ archive.title }}</h1>
<p>{{ archive.description }}</p>
```

### Example

For a custom post type called "Portfolio":

**Admin Location:**

```
Portfolio → Settings
```

**Fields Created:**

- Archive Title (default: "Portfolio")
- Archive Description (empty by default)

**Template Usage:**

```twig
<section class="page-header">
  <h1>{{ archive.title }}</h1>
  <p>{{ archive.description }}</p>
</section>
```

### Custom Context Files

You can also manually set archive data using context files in `src/context/is_archive/`:

**File:** `src/context/is_archive/portfolio.php`

```php
<?php
add_filter('timber/context', function($context) {
    if (!is_post_type_archive('portfolio')) {
        return $context;
    }

    $context['archive'] = (object) [
        'title' => 'Selected Work',
        'description' => 'A collection of digital experiences...',
    ];

    return $context;
});
```

**Note:** ACF field values take precedence over hardcoded context files.

---

## Extending Archive Fields

### Overview

You can extend the auto-generated archive settings with additional custom fields.

### Method 1: Using ACF UI

1. Go to **Custom Fields** in WordPress admin
2. Find the field group named "[Post Type] Archive Settings"
3. Click **Edit**
4. Add your additional fields
5. Save

The fields will automatically appear on the Settings page and be available in the Timber context.

### Method 2: Programmatically with Filters

Create a file in `includes/context/` or `src/context/` to hook into the field generation:

**Example:** `includes/context/extend-archive-fields.php`

```php
<?php
/**
 * Extend archive settings with additional fields
 */

add_filter('acf/load_field_group', function($field_group) {
    // Only modify portfolio archive settings
    if ($field_group['key'] !== 'group_portfolio_archive_settings') {
        return $field_group;
    }

    // Add a featured image field
    $field_group['fields'][] = [
        'key' => 'field_portfolio_archive_featured_image',
        'label' => 'Featured Image',
        'name' => 'archive_featured_image',
        'type' => 'image',
        'instructions' => 'Optional hero image for the archive page',
        'return_format' => 'array',
        'preview_size' => 'medium',
    ];

    // Add a toggle for display options
    $field_group['fields'][] = [
        'key' => 'field_portfolio_archive_show_filters',
        'label' => 'Show Filters',
        'name' => 'archive_show_filters',
        'type' => 'true_false',
        'instructions' => 'Display category filters on archive page',
        'default_value' => 1,
    ];

    return $field_group;
});
```

### Method 3: Manual Field Group Registration

For complete control, register your own field group that targets the options page:

```php
<?php
add_action('acf/init', function() {
    acf_add_local_field_group([
        'key' => 'group_portfolio_custom_settings',
        'title' => 'Portfolio Display Options',
        'fields' => [
            [
                'key' => 'field_portfolio_layout',
                'label' => 'Layout Style',
                'name' => 'portfolio_layout',
                'type' => 'select',
                'choices' => [
                    'grid' => 'Grid',
                    'masonry' => 'Masonry',
                    'list' => 'List',
                ],
                'default_value' => 'grid',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'portfolio-archive-options',
                ],
            ],
        ],
    ]);
});
```

### Accessing Extended Fields in Templates

All fields saved to the archive options page are automatically available via ACF's options functions:

**In PHP/Context Files:**

```php
$layout = get_field('portfolio_layout', 'portfolio_archive_options');
$show_filters = get_field('archive_show_filters', 'portfolio_archive_options');
```

**In Twig Templates:**

```twig
{% set layout = function('get_field', 'portfolio_layout', 'portfolio_archive_options') %}
{% set show_filters = function('get_field', 'archive_show_filters', 'portfolio_archive_options') %}
```

**Or add them to the Timber context:**

```php
add_filter('timber/context', function($context) {
    if (!is_post_type_archive('portfolio')) {
        return $context;
    }

    // Extend the archive object with custom fields
    $context['archive']->layout = get_field('portfolio_layout', 'portfolio_archive_options');
    $context['archive']->show_filters = get_field('archive_show_filters', 'portfolio_archive_options');
    $context['archive']->featured_image = get_field('archive_featured_image', 'portfolio_archive_options');

    return $context;
});
```

Then use in templates:

```twig
<section class="portfolio-archive" data-layout="{{ archive.layout }}">
  {% if archive.featured_image %}
    <img src="{{ archive.featured_image.url }}" alt="{{ archive.featured_image.alt }}">
  {% endif %}

  <h1>{{ archive.title }}</h1>
  <p>{{ archive.description }}</p>

  {% if archive.show_filters %}
    <!-- Display filters -->
  {% endif %}
</section>
```

---

## Notes

### Requirements

- ACF (free or PRO) must be installed and active
- Post type must have `has_archive` set to `true`
- `archive_options` must be enabled in `settings.json`

### File Locations

- **Settings toggle**: `settings.json`
- **Main implementation**: `includes/context/archives.php`
- **Manual context files**: `src/context/is_archive/{post-type}.php`
- **Extensions**: `includes/context/` or `src/context/`

### Tips

1. **Default Values** - The `archive_title` field defaults to the post type name, making it optional to fill in
2. **Fallbacks** - If ACF fields are empty, you can provide fallbacks in your context files
3. **Multiple Post Types** - Settings pages are created for ALL custom post types with archives enabled
4. **Reusability** - The same archive pattern works across different post types without code changes

---

**Last Updated:** 2025-01-26
