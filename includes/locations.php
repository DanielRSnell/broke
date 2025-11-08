<?php
/**
 * Timber Template Locations
 *
 * Configure where Timber looks for Twig template files.
 * This tells Timber to use src/views/ as the template directory.
 */

add_filter('timber/locations', function ($paths) {
    // Add src/views as the primary template location
    $paths[] = get_template_directory() . '/src/views';

    return $paths;
});
