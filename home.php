<?php
/**
 * Template for displaying the blog posts index
 */

$context = Timber::context();

// Get posts for blog index
$context['posts'] = Timber::get_posts();

Timber::render('home.twig', $context);
