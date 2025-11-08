<?php

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Timber.
Timber\Timber::init();

// Configure Timber template locations
require_once get_template_directory() . '/includes/locations.php';

/* Woocommerce Theme Suppport*/
function theme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}

add_action('after_setup_theme', 'theme_add_woocommerce_support');

// Disable core block patterns
function blank_theme_disable_core_patterns() {
    remove_theme_support('core-block-patterns');
}
add_action('after_setup_theme', 'blank_theme_disable_core_patterns');

// Register pattern categories
function blank_theme_register_pattern_categories() {
    $categories = [
        'blank-theme/featured' => [
            'label' => __('Featured', 'blank-theme'),
        ],
        'blank-theme/call-to-action' => [
            'label' => __('Call to Action', 'blank-theme'),
        ],
        'blank-theme/hero' => [
            'label' => __('Hero', 'blank-theme'),
        ],
        'blank-theme/card' => [
            'label' => __('Cards', 'blank-theme'),
        ],
        'blank-theme/gallery' => [
            'label' => __('Gallery', 'blank-theme'),
        ],
    ];

    foreach ($categories as $name => $properties) {
        register_block_pattern_category($name, $properties);
    }
}
add_action('init', 'blank_theme_register_pattern_categories');

// Unregister all default WordPress patterns
function blank_theme_unregister_default_patterns() {
    // Remove all core patterns
    $core_patterns = [
        'core/query-standard-posts',
        'core/query-medium-posts',
        'core/query-small-posts',
        'core/query-grid-posts',
        'core/query-large-title-posts',
        'core/query-offset-posts',
        'core/social-links-shared-background-color',
    ];

    foreach ($core_patterns as $pattern) {
        unregister_block_pattern($pattern);
    }

    // Unregister all patterns from pattern categories
    $pattern_categories = [
        'featured',
        'posts',
        'query',
        'text',
        'buttons',
        'columns',
        'gallery',
        'header',
        'footer',
        'call-to-action',
    ];

    foreach ($pattern_categories as $category) {
        unregister_block_pattern_category($category);
    }
}
add_action('init', 'blank_theme_unregister_default_patterns', 99);

// Include enqueue logic
require_once get_template_directory() . '/includes/enqueue.php';

// Include helper functions
require_once get_template_directory() . '/includes/helpers.php';

// Disable wptexturize to prevent it from breaking JavaScript syntax in attributes
add_filter('run_wptexturize', '__return_false');

// Recursively include all context hooks from includes/context
foreach (glob(get_template_directory() . '/includes/context/*.php') as $file) {
    require_once $file;
}

// Recursively include all context hooks from src/context
foreach (glob(get_template_directory() . '/src/context/*.php') as $file) {
    require_once $file;
}

// Recursively include all routes from includes/routes
foreach (glob(get_template_directory() . '/includes/routes/*.php') as $file) {
    require_once $file;
}

// Register WP-CLI commands
if (defined('WP_CLI') && WP_CLI) {
    require_once get_template_directory() . '/includes/cli/blocks-cli/register.php';
    require_once get_template_directory() . '/includes/cli/content-cli/register.php';
    require_once get_template_directory() . '/includes/cli/page-cli/register.php';
    require_once get_template_directory() . '/includes/cli/pattern-cli/register.php';
    require_once get_template_directory() . '/includes/cli/template-cli/register.php';
}

// Enable classic menus in FSE
function blank_theme_enable_classic_menus() {
    add_theme_support('menus');
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'blank-theme'),
        'footer' => __('Footer Menu', 'blank-theme')
    ));
}
add_action('after_setup_theme', 'blank_theme_enable_classic_menus'); 