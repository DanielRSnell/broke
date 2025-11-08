# Extensions Directory

## Overview

This directory contains **project-specific PHP functionality**—custom features, integrations, and extensions beyond core WordPress.

Think of this as **your custom plugin layer**—functionality that extends WordPress for this specific project.

---

## Philosophy

Extensions provide a way to organize custom functionality:

1. **Project-specific features** - Code unique to this project
2. **Modular organization** - Each feature in its own file
3. **Auto-loading** - Files automatically loaded by `functions.php`
4. **Separation from core** - Keeps theme framework clean

### Core Principle

**"Extensions are project-specific features organized as separate modules, auto-loaded by the theme."**

---

## When to Use Extensions

### Use Extensions For:
- **Custom post types** - Beyond what ACF provides
- **Custom taxonomies** - Project-specific categorization
- **API integrations** - Third-party services (Stripe, Mailchimp, etc.)
- **Custom endpoints** - WordPress REST API extensions
- **Custom widgets** - Sidebar/footer widgets
- **Admin customizations** - Custom admin columns, meta boxes
- **Shortcodes** - Custom shortcodes for content
- **Cron jobs** - Scheduled tasks
- **Custom user roles** - Project-specific capabilities
- **WooCommerce extensions** - E-commerce customizations

### Don't Use Extensions For:
- **Core theme functionality** - Use `includes/` instead
- **Context filters** - Use `src/context/` for Timber data
- **Helper functions** - Use `includes/helpers.php`
- **Enqueue scripts** - Use `includes/enqueue.php`

---

## File Structure

Each extension should be a self-contained PHP file:

```
src/extensions/
├── custom-post-types.php     # Register custom post types
├── custom-taxonomies.php     # Register custom taxonomies
├── rest-api.php              # Custom REST API endpoints
├── stripe-integration.php    # Stripe payment integration
├── mailchimp-sync.php        # Mailchimp newsletter sync
├── custom-widgets.php        # Custom sidebar widgets
├── admin-customizations.php  # Admin UI tweaks
├── custom-shortcodes.php     # Shortcodes
├── cron-jobs.php             # Scheduled tasks
└── woocommerce-mods.php      # WooCommerce customizations
```

---

## Auto-Loading

Extensions are automatically loaded by `functions.php`:

```php
// functions.php

// Auto-load extension files
$extension_files = glob(get_template_directory() . '/src/extensions/*.php');
foreach ($extension_files as $file) {
    require_once $file;
}
```

**How it works:**
1. `functions.php` scans `src/extensions/` directory
2. Loads all `.php` files automatically
3. No need to manually require each extension

---

## Example Extensions

### Custom Post Types
```php
// src/extensions/custom-post-types.php

/**
 * Register custom post types
 */
add_action('init', function() {
    // Register Portfolio post type
    register_post_type('portfolio', [
        'labels' => [
            'name' => 'Portfolio',
            'singular_name' => 'Portfolio Item'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,  // Enable Gutenberg
        'menu_icon' => 'dashicons-portfolio'
    ]);

    // Register Testimonial post type
    register_post_type('testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial'
        ],
        'public' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-testimonial'
    ]);
});
```

### Custom Taxonomies
```php
// src/extensions/custom-taxonomies.php

/**
 * Register custom taxonomies
 */
add_action('init', function() {
    // Register Project Type taxonomy for Portfolio
    register_taxonomy('project_type', 'portfolio', [
        'labels' => [
            'name' => 'Project Types',
            'singular_name' => 'Project Type'
        ],
        'hierarchical' => true,  // Like categories
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'project-type']
    ]);

    // Register Industry taxonomy
    register_taxonomy('industry', ['portfolio', 'post'], [
        'labels' => [
            'name' => 'Industries',
            'singular_name' => 'Industry'
        ],
        'hierarchical' => false,  // Like tags
        'show_in_rest' => true
    ]);
});
```

### REST API Extensions
```php
// src/extensions/rest-api.php

/**
 * Custom REST API endpoints
 */
add_action('rest_api_init', function() {
    // GET /wp-json/theme/v1/featured-posts
    register_rest_route('theme/v1', '/featured-posts', [
        'methods' => 'GET',
        'callback' => function() {
            $posts = get_posts([
                'posts_per_page' => 5,
                'meta_key' => 'featured',
                'meta_value' => '1'
            ]);

            return array_map(function($post) {
                return [
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'link' => get_permalink($post->ID),
                    'excerpt' => get_the_excerpt($post->ID)
                ];
            }, $posts);
        },
        'permission_callback' => '__return_true'
    ]);
});
```

### Third-Party Integration (Stripe)
```php
// src/extensions/stripe-integration.php

/**
 * Stripe payment integration
 */
class Stripe_Integration {
    private $api_key;

    public function __construct() {
        $this->api_key = get_option('stripe_api_key');
        add_action('wp_ajax_create_payment_intent', [$this, 'create_payment_intent']);
        add_action('wp_ajax_nopriv_create_payment_intent', [$this, 'create_payment_intent']);
    }

    public function create_payment_intent() {
        // Stripe API logic here
        wp_send_json_success([
            'client_secret' => 'secret_here'
        ]);
    }
}

new Stripe_Integration();
```

### Admin Customizations
```php
// src/extensions/admin-customizations.php

/**
 * Custom admin columns
 */
add_filter('manage_portfolio_posts_columns', function($columns) {
    $columns['project_type'] = 'Project Type';
    $columns['featured'] = 'Featured';
    return $columns;
});

add_action('manage_portfolio_posts_custom_column', function($column, $post_id) {
    if ($column === 'project_type') {
        $terms = get_the_terms($post_id, 'project_type');
        echo $terms ? implode(', ', wp_list_pluck($terms, 'name')) : '—';
    }

    if ($column === 'featured') {
        $featured = get_field('featured', $post_id);
        echo $featured ? '⭐ Yes' : '—';
    }
}, 10, 2);
```

### Custom Shortcodes
```php
// src/extensions/custom-shortcodes.php

/**
 * Custom shortcodes
 */

// [button url="..." text="..."]
add_shortcode('button', function($atts) {
    $atts = shortcode_atts([
        'url' => '#',
        'text' => 'Click Here',
        'style' => 'primary'
    ], $atts);

    return sprintf(
        '<a href="%s" class="btn btn-%s">%s</a>',
        esc_url($atts['url']),
        esc_attr($atts['style']),
        esc_html($atts['text'])
    );
});

// [recent_posts count="5"]
add_shortcode('recent_posts', function($atts) {
    $atts = shortcode_atts(['count' => 5], $atts);

    $posts = get_posts([
        'posts_per_page' => $atts['count']
    ]);

    ob_start();
    foreach ($posts as $post) {
        printf(
            '<div class="recent-post"><a href="%s">%s</a></div>',
            get_permalink($post->ID),
            get_the_title($post->ID)
        );
    }
    return ob_get_clean();
});
```

### Cron Jobs
```php
// src/extensions/cron-jobs.php

/**
 * Scheduled tasks
 */

// Register custom cron schedule
add_filter('cron_schedules', function($schedules) {
    $schedules['weekly'] = [
        'interval' => 604800,  // 7 days in seconds
        'display' => 'Once Weekly'
    ];
    return $schedules;
});

// Schedule event on activation
add_action('after_switch_theme', function() {
    if (!wp_next_scheduled('cleanup_old_posts')) {
        wp_schedule_event(time(), 'weekly', 'cleanup_old_posts');
    }
});

// Cron task
add_action('cleanup_old_posts', function() {
    // Delete posts older than 2 years
    $old_posts = get_posts([
        'post_type' => 'post',
        'posts_per_page' => -1,
        'date_query' => [
            'before' => '2 years ago'
        ]
    ]);

    foreach ($old_posts as $post) {
        wp_delete_post($post->ID, true);
    }
});
```

---

## Best Practices

### 1. One Feature Per File

**Good:**
```
custom-post-types.php    # All CPTs in one file
custom-taxonomies.php    # All taxonomies in one file
stripe-integration.php   # Stripe-specific code
```

**Bad:**
```
portfolio.php            # Mix of CPT, taxonomy, admin columns
misc.php                 # Random unrelated functions
```

### 2. Use Descriptive Names

**Good:**
```php
// src/extensions/mailchimp-newsletter-sync.php
add_action('user_register', 'sync_user_to_mailchimp');
```

**Bad:**
```php
// src/extensions/integration.php
add_action('user_register', 'sync_user');
```

### 3. Namespace Classes

```php
// Avoid conflicts with plugins
namespace Theme\Extensions;

class Custom_Widget extends \WP_Widget {
    // Widget code
}
```

### 4. Check Dependencies

```php
// Only load if WooCommerce is active
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/src/extensions/woocommerce-mods.php';
}
```

### 5. Document Each Extension

```php
/**
 * Stripe Payment Integration
 *
 * Purpose: Process payments via Stripe API
 * Dependencies: Stripe PHP SDK
 * API Key: Stored in wp_options (stripe_api_key)
 * Endpoints:
 *   - /wp-json/theme/v1/create-payment
 *   - /wp-json/theme/v1/confirm-payment
 *
 * @package Theme\Extensions
 */
```

---

## WooCommerce Extensions Example

```php
// src/extensions/woocommerce-mods.php

/**
 * WooCommerce customizations
 */

// Add custom product field
add_action('woocommerce_product_options_general_product_data', function() {
    woocommerce_wp_text_input([
        'id' => '_custom_delivery_time',
        'label' => 'Delivery Time',
        'placeholder' => 'e.g., 2-3 business days',
        'desc_tip' => true,
        'description' => 'Estimated delivery time for this product'
    ]);
});

// Save custom field
add_action('woocommerce_process_product_meta', function($post_id) {
    $value = $_POST['_custom_delivery_time'] ?? '';
    update_post_meta($post_id, '_custom_delivery_time', sanitize_text_field($value));
});

// Display on product page
add_action('woocommerce_single_product_summary', function() {
    global $product;
    $delivery = get_post_meta($product->get_id(), '_custom_delivery_time', true);

    if ($delivery) {
        echo '<div class="delivery-time">';
        echo '<strong>Delivery:</strong> ' . esc_html($delivery);
        echo '</div>';
    }
}, 25);
```

---

## Removing for Clean Boilerplate

For a clean boilerplate, this directory should be **empty** with only a README:

**Remove:**
- All project-specific extension files
- API integrations
- Custom post types/taxonomies specific to one project

**Keep:**
- `README.md` (this file)
- Empty directory structure

**Why?** Extensions are highly project-specific. Each new project will have different requirements.

---

**Summary:**

This directory contains **project-specific PHP functionality**—custom features beyond core WordPress. Each extension is a self-contained file auto-loaded by `functions.php`. Use for custom post types, taxonomies, API integrations, widgets, and more. For boilerplate, keep directory empty with README only.

**Common extensions:**
- Custom post types and taxonomies
- REST API endpoints
- Third-party integrations (Stripe, Mailchimp)
- Admin customizations
- Shortcodes and widgets
- Cron jobs
- WooCommerce modifications

**For boilerplate:**
- Keep directory empty (project-specific code)
- Keep README.md for documentation

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
