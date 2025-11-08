<?php
/**
 * Archive Options Pages
 *
 * Automatically creates ACF options pages for post types with archives enabled.
 * Only runs if archive_options is enabled in settings.json and ACF PRO is not installed.
 */

// Check if archive_options is enabled in settings
$settings = json_decode(file_get_contents(get_template_directory() . '/settings.json'), true);
if (empty($settings['src']['archive_options'])) {
    return;
}

/**
 * Register archive options pages for post types with archives
 */
add_action('acf/init', function() {
    // Get all registered post types
    $post_types = get_post_types(['public' => true], 'objects');

    foreach ($post_types as $post_type) {
        // Skip built-in post types (post, page, attachment, etc.)
        if ($post_type->_builtin) {
            continue;
        }

        // Only create options page if post type has archive enabled
        // has_archive can be true, a string (slug), or false
        if (empty($post_type->has_archive)) {
            continue;
        }

        $slug = $post_type->name;
        $name = $post_type->labels->name;
        $singular = $post_type->labels->singular_name;

        // Generate dynamic placeholder description
        $placeholder_description = sprintf(
            'Explore our collection of %s. Browse through carefully curated %s showcasing creativity, innovation, and excellence in design and development.',
            strtolower($name),
            strtolower($name)
        );

        // Register options page for this archive
        acf_add_options_page([
            'page_title'  => sprintf('%s Archive Options', $singular),
            'menu_title'  => 'Settings',
            'menu_slug'   => $slug . '-archive-options',
            'parent_slug' => 'edit.php?post_type=' . $slug,
            'capability'  => 'edit_posts',
            'redirect'    => false,
        ]);

        // Register field group for this archive
        acf_add_local_field_group([
            'key' => 'group_' . $slug . '_archive_settings',
            'title' => sprintf('%s Archive Settings', $singular),
            'fields' => [
                [
                    'key' => 'field_' . $slug . '_title',
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => 'The main heading displayed on the archive page',
                    'required' => 0,
                    'default_value' => $name,
                ],
                [
                    'key' => 'field_' . $slug . '_description',
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => 'textarea',
                    'instructions' => 'A brief description shown below the title',
                    'required' => 0,
                    'rows' => 3,
                    'default_value' => $placeholder_description,
                    'maxlength' => 180,
                    'placeholder' => 'Enter a brief description for the archive page...',
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => $slug . '-archive-options',
                    ],
                ],
            ],
        ]);
    }
});
