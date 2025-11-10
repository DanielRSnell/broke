<?php
/**
 * Index template (fallback)
 */

$context = Timber::context();

// Double compile to handle blocks in post content
if (!empty($context['post']->post_content)) {
    $context['content'] = Timber::compile_string($context['post']->post_content, $context);
}

Timber::render('index.twig', $context);
