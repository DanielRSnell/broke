<?php

namespace BlankTheme\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Pull templates from block markup to HTML
 */
class TemplatePullCommand extends WP_CLI_Command {

    /**
     * Convert template from block markup to HTML
     *
     * ## OPTIONS
     *
     * <template>
     * : The template name (without .html extension)
     *
     * ## EXAMPLES
     *
     *     wp template pull index
     *     wp template pull single
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args) {
        $template_name = $args[0];
        $theme_dir = get_template_directory();

        // Source: ./templates/{name}.html
        $source_file = $theme_dir . '/templates/' . $template_name . '.html';

        // Destination: src/templates/{name}.html
        $dest_dir = $theme_dir . '/src/templates';
        $dest_file = $dest_dir . '/' . $template_name . '.html';

        // Check if source exists
        if (!file_exists($source_file)) {
            WP_CLI::error("Template not found: {$source_file}");
            return;
        }

        // Create destination directory if needed
        if (!is_dir($dest_dir)) {
            wp_mkdir_p($dest_dir);
        }

        // Read the block markup
        $block_markup = file_get_contents($source_file);

        // Parse blocks from template content
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

        $html = implode("\n\n", $html_parts);

        // Write to destination
        file_put_contents($dest_file, $html);

        WP_CLI::success("Pulled template: {$template_name}.html → src/templates/{$template_name}.html");
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
