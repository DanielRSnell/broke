<?php
/**
 * Archive Context
 *
 * Automatically adds archive options to Timber context for post types with archive settings pages.
 * Pulls all ACF fields from the archive options page and makes them available in templates.
 */

add_filter('timber/context', function($context) {
    // Get queried object to determine post type on archives
    $queried_object = get_queried_object();

    // Get current post type
    $type = null;
    if (is_post_type_archive()) {
        $type = $queried_object->name ?? null;
    }

    // Check if we're on an archive
    $is_archive = is_post_type_archive();

    // Create archive array with base properties
    $context['archive'] = [
        'is_archive' => $is_archive,
        'type' => $type,
    ];

    // If not an archive or no type, return early
    if (!$is_archive || !$type) {
        return $context;
    }

    // Get post type object for fallback data
    $post_type = get_post_type_object($type);
    if (!$post_type) {
        return $context;
    }

    // Get fields from ACF options page
    // Note: ACF options pages use the special 'option' parameter
    if (function_exists('get_field')) {
        $title = get_field('title', 'option');
        $description = get_field('description', 'option');

        // Add fields to archive array
        $context['archive']['title'] = $title ?: $post_type->labels->name;
        $context['archive']['description'] = $description ?: '';
    } else {
        // Fallback if ACF not available
        $context['archive']['title'] = $post_type->labels->name;
        $context['archive']['description'] = '';
    }

    return $context;
});
