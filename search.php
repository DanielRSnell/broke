<?php
/**
 * Template for displaying search results
 */

$context = Timber::context();

// Get search results
$context['posts'] = Timber::get_posts();
$context['search_query'] = get_search_query();

Timber::render('search.twig', $context);
