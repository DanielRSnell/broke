<?php
/**
 * Template for displaying archive pages
 */

$context = Timber::context();

// Get posts for archive
$context['posts'] = Timber::get_posts();
$context['title'] = get_the_archive_title();

Timber::render('archive.twig', $context);
