<?php

namespace BlankTheme\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Convert block markup files to clean HTML
 */
class BlocksToHtmlCommand extends WP_CLI_Command {

    /**
     * Convert block markup to HTML
     *
     * ## OPTIONS
     *
     * <path>
     * : Path to file or directory containing block markup
     *
     * [--all]
     * : Convert all files in the directory
     *
     * [--output=<path>]
     * : Output directory (defaults to src/ prefixed version of input)
     *
     * ## EXAMPLES
     *
     *     # Convert single file
     *     wp blocks:html parts/header.html
     *
     *     # Convert all files in directory
     *     wp blocks:html parts --all
     *
     *     # Convert with custom output directory
     *     wp blocks:html parts/header.html --output=src/parts
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args) {
        $input_path = $args[0];
        $theme_dir = get_template_directory();
        $convert_all = isset($assoc_args['all']);
        $custom_output = $assoc_args['output'] ?? null;

        // Resolve full path
        if (!str_starts_with($input_path, '/')) {
            $input_path = $theme_dir . '/' . $input_path;
        }

        // Check if path exists
        if (!file_exists($input_path)) {
            WP_CLI::error("Path not found: {$input_path}");
            return;
        }

        $files_to_convert = [];

        // Determine files to convert
        if (is_dir($input_path)) {
            if (!$convert_all) {
                WP_CLI::error("Path is a directory. Use --all flag to convert all files.");
                return;
            }

            $files = glob($input_path . '/*.html');

            if (empty($files)) {
                WP_CLI::error("No HTML files found in directory: {$input_path}");
                return;
            }

            $files_to_convert = $files;
        } else {
            // Single file
            if (!str_ends_with($input_path, '.html')) {
                WP_CLI::error("File must be an HTML file: {$input_path}");
                return;
            }

            $files_to_convert = [$input_path];
        }

        // Convert each file
        $success_count = 0;
        foreach ($files_to_convert as $source_file) {
            $result = $this->convert_file($source_file, $custom_output, $theme_dir);
            if ($result) {
                $success_count++;
            }
        }

        WP_CLI::success("Converted {$success_count} file(s) from block markup to HTML");
    }

    /**
     * Convert a single file from block markup to HTML
     */
    private function convert_file($source_file, $custom_output, $theme_dir) {
        // Determine output path
        if ($custom_output) {
            // Use custom output directory
            $output_dir = $custom_output;
            if (!str_starts_with($output_dir, '/')) {
                $output_dir = $theme_dir . '/' . $output_dir;
            }
        } else {
            // Auto-generate output path by prepending 'src/' to the relative path
            $relative_path = str_replace($theme_dir . '/', '', $source_file);
            $dir_name = dirname($relative_path);
            $output_dir = $theme_dir . '/src/' . $dir_name;
        }

        // Create output directory if needed
        if (!is_dir($output_dir)) {
            wp_mkdir_p($output_dir);
        }

        $filename = basename($source_file);
        $dest_file = $output_dir . '/' . $filename;

        // Read the block markup
        $block_markup = file_get_contents($source_file);

        // Parse blocks from file content
        $blocks = parse_blocks($block_markup);

        // Convert blocks to HTML
        $html_parts = [];
        foreach ($blocks as $block) {
            if (empty($block['blockName'])) {
                continue; // Skip empty/whitespace blocks
            }

            // Convert WordPress core blocks to custom HTML elements
            $block_html = $this->convert_block_to_html($block);

            // Strip WordPress-generated IDs from elements
            $block_html = preg_replace('/\s+id=["\'][^"\']*["\']/i', '', $block_html);

            $html_parts[] = trim($block_html);
        }

        $html = implode("\n", $html_parts);

        // Write to destination
        file_put_contents($dest_file, $html);

        // Calculate relative paths for display
        $source_relative = str_replace($theme_dir . '/', '', $source_file);
        $dest_relative = str_replace($theme_dir . '/', '', $dest_file);

        WP_CLI::log("✓ {$source_relative} → {$dest_relative}");

        return true;
    }

    /**
     * Convert WordPress block to HTML (with custom elements for core blocks)
     */
    private function convert_block_to_html($block) {
        $block_name = $block['blockName'];
        $attrs = $block['attrs'] ?? [];

        // Convert core WordPress blocks to custom HTML elements
        if ($block_name === 'core/template-part') {
            return $this->generate_part_element($attrs);
        }

        if ($block_name === 'core/pattern') {
            return $this->generate_pattern_element($attrs);
        }

        if ($block_name === 'core/post-content') {
            return $this->generate_content_element($attrs);
        }

        // Handle blocks with innerBlocks recursively
        if (!empty($block['innerBlocks'])) {
            // Process innerBlocks first, replacing core blocks with custom elements
            $processed_inner_blocks = [];
            foreach ($block['innerBlocks'] as $inner_block) {
                $inner_block_name = $inner_block['blockName'] ?? '';

                // If it's a core block we handle, convert it
                if (in_array($inner_block_name, ['core/template-part', 'core/pattern', 'core/post-content'])) {
                    $processed_inner_blocks[] = [
                        'blockName' => '',
                        'attrs' => [],
                        'innerBlocks' => [],
                        'innerHTML' => $this->convert_block_to_html($inner_block),
                        'innerContent' => [$this->convert_block_to_html($inner_block)]
                    ];
                } else {
                    // Recursively process other blocks
                    $processed_inner_blocks[] = $this->process_inner_blocks($inner_block);
                }
            }

            // Replace innerBlocks in the block
            $block['innerBlocks'] = $processed_inner_blocks;
        }

        // For all other blocks, render normally
        return render_block($block);
    }

    /**
     * Recursively process inner blocks
     */
    private function process_inner_blocks($block) {
        if (!empty($block['innerBlocks'])) {
            $processed_inner_blocks = [];
            foreach ($block['innerBlocks'] as $inner_block) {
                $inner_block_name = $inner_block['blockName'] ?? '';

                if (in_array($inner_block_name, ['core/template-part', 'core/pattern', 'core/post-content'])) {
                    $processed_inner_blocks[] = [
                        'blockName' => '',
                        'attrs' => [],
                        'innerBlocks' => [],
                        'innerHTML' => $this->convert_block_to_html($inner_block),
                        'innerContent' => [$this->convert_block_to_html($inner_block)]
                    ];
                } else {
                    $processed_inner_blocks[] = $this->process_inner_blocks($inner_block);
                }
            }
            $block['innerBlocks'] = $processed_inner_blocks;
        }
        return $block;
    }

    /**
     * Generate <Part></Part> element
     */
    private function generate_part_element($attrs) {
        $attributes = [];

        if (!empty($attrs['slug'])) {
            $attributes[] = 'slug="' . esc_attr($attrs['slug']) . '"';
        }

        if (!empty($attrs['theme'])) {
            $attributes[] = 'theme="' . esc_attr($attrs['theme']) . '"';
        }

        if (!empty($attrs['className'])) {
            $attributes[] = 'class="' . esc_attr($attrs['className']) . '"';
        }

        $attr_string = !empty($attributes) ? ' ' . implode(' ', $attributes) : '';
        return "<Part{$attr_string}></Part>";
    }

    /**
     * Generate <Pattern></Pattern> element
     */
    private function generate_pattern_element($attrs) {
        $attributes = [];

        if (!empty($attrs['slug'])) {
            $attributes[] = 'slug="' . esc_attr($attrs['slug']) . '"';
        }

        if (!empty($attrs['category'])) {
            $attributes[] = 'category="' . esc_attr($attrs['category']) . '"';
        }

        if (!empty($attrs['className'])) {
            $attributes[] = 'class="' . esc_attr($attrs['className']) . '"';
        }

        $attr_string = !empty($attributes) ? ' ' . implode(' ', $attributes) : '';
        return "<Pattern{$attr_string}></Pattern>";
    }

    /**
     * Generate <Content></Content> element
     */
    private function generate_content_element($attrs) {
        $attributes = [];

        if (!empty($attrs['className'])) {
            $attributes[] = 'class="' . esc_attr($attrs['className']) . '"';
        }

        $attr_string = !empty($attributes) ? ' ' . implode(' ', $attributes) : '';
        return "<Content{$attr_string}></Content>";
    }
}
