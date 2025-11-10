<?php
/**
 * Template for displaying the front page
 */

$context = Timber::context();


    $context['content'] = Timber::compile_string($context['post']->content, $context);


Timber::render('front-page.twig', $context);
