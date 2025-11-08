<?php

/**
 * Enqueue block assets (frontend and editor) based on settings.json
 */
function blank_theme_enqueue_block_assets() {
    $settings_file = get_template_directory() . '/settings.json';

    // Check if settings.json exists
    if (!file_exists($settings_file)) {
        return;
    }

    $settings = json_decode(file_get_contents($settings_file), true);
    $production_dir = get_template_directory() . '/_production';
    $production_uri = get_template_directory_uri() . '/_production';

    // Enqueue JavaScript files if enabled
    if (!empty($settings['src']['scripts']) && is_dir($production_dir)) {
        $js_files = glob($production_dir . '/*.js');

        foreach ($js_files as $js_file) {
            $filename = basename($js_file);
            $handle = 'blank-theme-' . pathinfo($filename, PATHINFO_FILENAME);
            $file_url = $production_uri . '/' . $filename;
            $version = filemtime($js_file);

            wp_enqueue_script($handle, $file_url, array(), $version, true);
        }
    }

    // Enqueue CSS files if enabled
    if (!empty($settings['src']['styles']) && is_dir($production_dir)) {
        $css_files = glob($production_dir . '/*.css');

        foreach ($css_files as $css_file) {
            $filename = basename($css_file);
            $handle = 'blank-theme-' . pathinfo($filename, PATHINFO_FILENAME);
            $file_url = $production_uri . '/' . $filename;
            $version = filemtime($css_file);

            wp_enqueue_style($handle, $file_url, array(), $version);
        }
    }
}
add_action('enqueue_block_assets', 'blank_theme_enqueue_block_assets');

/**
 * Enqueue editor-only assets from _editor directory
 */
function blank_theme_enqueue_editor_assets() {
    $editor_dir = get_template_directory() . '/_editor';
    $editor_uri = get_template_directory_uri() . '/_editor';

    if (!is_dir($editor_dir)) {
        return;
    }

    // Enqueue editor-specific JavaScript
    $editor_js_files = glob($editor_dir . '/*.js');

    foreach ($editor_js_files as $editor_js_file) {
        $filename = basename($editor_js_file);
        $handle = 'blank-theme-editor-' . pathinfo($filename, PATHINFO_FILENAME);
        $file_url = $editor_uri . '/' . $filename;
        $version = filemtime($editor_js_file);

        wp_enqueue_script($handle, $file_url, array(), $version, true);
    }

    // Enqueue editor-specific CSS
    $editor_css_files = glob($editor_dir . '/*.css');

    foreach ($editor_css_files as $editor_css_file) {
        $filename = basename($editor_css_file);
        $handle = 'blank-theme-editor-' . pathinfo($filename, PATHINFO_FILENAME);
        $file_url = $editor_uri . '/' . $filename;
        $version = filemtime($editor_css_file);

        wp_enqueue_style($handle, $file_url, array(), $version);
    }
}
add_action('enqueue_block_editor_assets', 'blank_theme_enqueue_editor_assets');
