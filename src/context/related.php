<?php
/**
 * Related Posts Context Filter
 *
 * Adds related_posts to the global Timber context.
 * Gets 6 posts of the same post type as the current post, excluding the current post.
 *
 * Usage in patterns:
 * <div loopsource="related_posts" loopvariable="article">
 *   <h3>{{ article.title }}</h3>
 * </div>
 */

use Timber\Timber;

add_filter('timber/context', function($context) {
    global $post;

    // Get current post ID and type
    $current_id = $post->ID ?? 0;
    $current_type = $post->post_type ?? 'post';

    // If no valid post, return empty array
    if (!$current_id) {
        $context['related_posts'] = [];
        return $context;
    }

    // Query using WP_Query
    $query = new WP_Query([
        'post_type'      => $current_type,
        'posts_per_page' => 6,
        'post__not_in'   => [$current_id],
        'post_status'    => 'publish',
    ]);

    // Get the posts from WP_Query and convert to Timber Posts
    if ($query->have_posts()) {
        $context['related_posts'] = array_map(function($post) {
            return Timber::get_post($post);
        }, $query->posts);
    } else {
        $context['related_posts'] = [];
    }

    // Reset post data
    wp_reset_postdata();

    return $context;
});
