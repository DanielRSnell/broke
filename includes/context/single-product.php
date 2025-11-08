<?php

add_filter('timber/context', function ($context) {
    // Only run if WooCommerce is active and on single product pages
    if (!function_exists('is_product') || !is_product()) {
        return $context;
    }

    global $product;

    if (!$product) {
        return $context;
    }

    // Initialize product context
    $context['product'] = [];

    // Featured Image
    $featured_image_id = $product->get_image_id();
    if ($featured_image_id) {
        $context['product']['featured'] = [
            'url' => wp_get_attachment_image_url($featured_image_id, 'full'),
            'alt' => get_post_meta($featured_image_id, '_wp_attachment_image_alt', true),
        ];
    }

    // Gallery Images
    $gallery_ids = $product->get_gallery_image_ids();
    $gallery = [];

    foreach ($gallery_ids as $image_id) {
        $gallery[] = [
            'url' => wp_get_attachment_image_url($image_id, 'full'),
            'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
        ];
    }

    $context['product']['gallery'] = $gallery;

    return $context;
});