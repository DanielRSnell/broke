<?php
/**
 * Prototype Routes Handler
 *
 * Automatically registers routes for prototypes in src/routes/prototypes/*
 * Each prototype directory should contain:
 * - index.html (required) - Main HTML template
 * - compiled.css (optional) - Compiled styles
 * - script.js or scripts.js (optional) - JavaScript
 *
 * Routes created:
 * - /prototypes/{name} - Viewer with preview options
 * - /prototypes/{name}/raw - Direct HTML output (no iframe)
 *
 * Context Filter:
 * Use 'timber/prototypes/context' to modify context passed to prototype viewer
 */

use Timber\Timber;

add_action('parse_request', function($wp_query_obj) {
    if (!isset($wp_query_obj->request)) {
        return;
    }

    $request = $wp_query_obj->request;

    // Check if this is a prototype route
    if (strpos($request, 'prototypes/') !== 0) {
        return;
    }

    // Parse the route: prototypes/{name} or prototypes/{name}/raw
    $parts = explode('/', trim($request, '/'));

    if (count($parts) < 2) {
        return;
    }

    $prototype_name = $parts[1];
    $is_raw = isset($parts[2]) && $parts[2] === 'raw';

    // Get theme directory
    $theme_dir = get_template_directory();
    $prototype_dir = $theme_dir . '/src/routes/prototypes/' . $prototype_name;

    // Check if prototype exists
    if (!is_dir($prototype_dir)) {
        status_header(404);
        echo '404 - Prototype not found';
        exit;
    }

    // Clear any existing output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // Start new output buffer
    ob_start();

    if ($is_raw) {
        // Serve raw prototype HTML with inline CSS/JS
        echo render_prototype_raw($prototype_dir, $prototype_name);
    } else {
        // Serve prototype viewer with preview options
        echo render_prototype_viewer($prototype_dir, $prototype_name);
    }

    // Get the content from the buffer
    $output = ob_get_clean();

    // Set headers
    status_header(200);
    header('Content-Type: text/html; charset=' . get_bloginfo('charset'));

    // Output the content
    echo $output;
    exit;
});

/**
 * Render prototype in a viewer with preview options using Timber
 */
function render_prototype_viewer($prototype_dir, $prototype_name) {
    $html_file = $prototype_dir . '/index.html';
    $css_file = $prototype_dir . '/compiled.css';

    // Check for both script.js and scripts.js
    $js_file = $prototype_dir . '/script.js';
    if (!file_exists($js_file)) {
        $js_file = $prototype_dir . '/scripts.js';
    }

    // Read files
    $html = file_exists($html_file) ? file_get_contents($html_file) : '<p>No HTML found</p>';
    $css = file_exists($css_file) ? file_get_contents($css_file) : '';
    $js = file_exists($js_file) ? file_get_contents($js_file) : '';

    // Build the srcdoc content
    $srcdoc = build_prototype_srcdoc($html, $css, $js);

    // Build context for the prototype
    $context = [
        'prototype' => [
            'name' => $prototype_name,
            'title' => ucwords(str_replace('-', ' ', $prototype_name)),
            'html' => $html,
            'css' => $css,
            'js' => $js,
            'srcdoc' => $srcdoc,
            'has_code' => !empty($html) && !empty($css),
            'dir' => $prototype_dir,
        ],
    ];

    /**
     * Filter prototype viewer context
     *
     * @param array  $context        The context array passed to Timber
     * @param string $prototype_name The prototype name
     * @param string $prototype_dir  The full path to the prototype directory
     */
    $context = apply_filters('timber/prototypes/context', $context, $prototype_name, $prototype_dir);

    // Render with Timber
    return Timber::compile('prototypes/viewer.twig', $context);
}

/**
 * Render raw prototype HTML with inline CSS/JS
 */
function render_prototype_raw($prototype_dir, $prototype_name) {
    $html_file = $prototype_dir . '/index.html';
    $css_file = $prototype_dir . '/compiled.css';

    // Check for both script.js and scripts.js
    $js_file = $prototype_dir . '/script.js';
    if (!file_exists($js_file)) {
        $js_file = $prototype_dir . '/scripts.js';
    }

    // Read files
    $html = file_exists($html_file) ? file_get_contents($html_file) : '<p>No HTML found</p>';
    $css = file_exists($css_file) ? file_get_contents($css_file) : '';
    $js = file_exists($js_file) ? file_get_contents($js_file) : '';

    // Build context
    $context = [
        'prototype' => [
            'name' => $prototype_name,
            'html' => $html,
            'css' => $css,
            'js' => $js,
            'dir' => $prototype_dir,
        ],
    ];

    /**
     * Filter prototype raw context
     *
     * @param array  $context        The context array
     * @param string $prototype_name The prototype name
     * @param string $prototype_dir  The full path to the prototype directory
     */
    $context = apply_filters('timber/prototypes/context', $context, $prototype_name, $prototype_dir);

    return build_prototype_srcdoc($context['prototype']['html'], $context['prototype']['css'], $context['prototype']['js']);
}

/**
 * Build complete HTML document with inline CSS and JS
 */
function build_prototype_srcdoc($html, $css, $js) {
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prototype</title>
    <?php if ($css): ?>
    <style><?php echo $css; ?></style>
    <?php endif; ?>
</head>
<body>
    <?php echo $html; ?>
    <?php if ($js): ?>
    <script><?php echo $js; ?></script>
    <?php endif; ?>
</body>
</html>
    <?php
    return ob_get_clean();
}
