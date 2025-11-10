<?php
/**
 * Template for displaying single posts
 */

$context = Timber::context();

// Double compile to handle blocks in post content
if (!empty($context['post']->post_content)) {
    $context['content'] = Timber::compile_string($context['post']->post_content, $context);
}

Timber::render('single.twig', $context);
