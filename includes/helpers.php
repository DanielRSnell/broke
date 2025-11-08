<?php

/**
 * Helper Functions
 */

/**
 * Allow SVG uploads to WordPress media library
 */
function blank_theme_allow_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'blank_theme_allow_svg_upload');

/**
 * Fix SVG display in media library
 */
function blank_theme_fix_svg_display($response, $attachment, $meta) {
    if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml') {
        $response['image'] = [
            'src' => $response['url'],
        ];
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'blank_theme_fix_svg_display', 10, 3);

/**
 * Sanitize SVG uploads for security
 */
function blank_theme_sanitize_svg($file) {
    // Only process SVG files
    if ($file['type'] !== 'image/svg+xml') {
        return $file;
    }

    // Get the file contents
    $svg_content = file_get_contents($file['tmp_name']);

    // Remove potentially dangerous elements and attributes
    $svg_content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $svg_content);
    $svg_content = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $svg_content);
    $svg_content = preg_replace('/<\?.*?\?>/s', '', $svg_content);

    // Write sanitized content back
    file_put_contents($file['tmp_name'], $svg_content);

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'blank_theme_sanitize_svg');
