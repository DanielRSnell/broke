<?php
/**
 * Page Pull Command
 *
 * Pulls WordPress pages to HTML section files in src/content/pages/
 *
 * Pages are pulled as separate HTML section files:
 * src/content/pages/{slug}/section-1.html
 * src/content/pages/{slug}/section-2.html
 * etc.
 *
 * Usage:
 *   wp page pull <post-id>
 *   wp page pull --post_type=page
 *   wp page pull --all
 */

namespace BlankTheme\CLI;

use WP_CLI;

class PagePullCommand {

    /**
     * Pull WordPress pages to HTML section files
     *
     * ## OPTIONS
     *
     * [<post-id>]
     * : The ID of the page to pull
     *
     * [--post_type=<type>]
     * : Pull all pages (default: page)
     *
     * [--all]
     * : Pull all pages
     *
     * ## EXAMPLES
     *
     *     wp page pull 100
     *     wp page pull --all
     */
    public function __invoke($args, $assoc_args) {
        $post_id = $args[0] ?? null;
        $post_type = $assoc_args['post_type'] ?? 'page';
        $all = isset($assoc_args['all']);

        // Get theme directory
        $theme_dir = get_template_directory();
        $pages_dir = $theme_dir . '/src/content/pages';

        // Ensure pages directory exists
        if (!file_exists($pages_dir)) {
            mkdir($pages_dir, 0755, true);
        }

        // Pull single page
        if ($post_id) {
            $this->pull_page($post_id, $pages_dir);
            return;
        }

        // Pull all pages
        if ($all || $post_type) {
            $this->pull_all_pages($pages_dir, $post_type);
            return;
        }

        WP_CLI::error('Please specify a post ID, --post_type, or --all');
    }

    /**
     * Pull a single page
     */
    private function pull_page($post_id, $pages_dir) {
        $post = get_post($post_id);

        if (!$post) {
            WP_CLI::error("Page {$post_id} not found");
        }

        $this->save_page_to_html_sections($post, $pages_dir);
        WP_CLI::success("Pulled page {$post_id} ({$post->post_title}) to HTML sections");
    }

    /**
     * Pull all pages
     */
    private function pull_all_pages($pages_dir, $post_type = 'page') {
        $pages = get_posts([
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        if (empty($pages)) {
            WP_CLI::warning("No pages found for post type: {$post_type}");
            return;
        }

        $count = 0;
        foreach ($pages as $page) {
            $this->save_page_to_html_sections($page, $pages_dir);
            $count++;
        }

        WP_CLI::success("Pulled {$count} pages to HTML sections");
    }

    /**
     * Convert page to HTML sections and save to files
     */
    private function save_page_to_html_sections($post, $pages_dir) {
        // Create page directory
        $page_dir = $pages_dir . '/' . $post->post_name;
        if (!file_exists($page_dir)) {
            mkdir($page_dir, 0755, true);
        }

        // Parse blocks from content
        $blocks = parse_blocks($post->post_content);

        // Convert each top-level block to HTML section
        $section_count = 1;
        foreach ($blocks as $block) {
            if (empty($block['blockName'])) {
                continue; // Skip empty/whitespace blocks
            }

            // Convert block to structured format
            $formatted_block = $this->format_block_for_converter($block);

            // Convert to HTML using Node.js parser (preserves Twig attributes)
            $html = $this->block_to_html_via_node([$formatted_block]);

            // Strip WordPress-generated IDs from elements
            $html = preg_replace('/\s+id=["\'][^"\']*["\']/i', '', $html);

            // Decode HTML entities back to normal characters
            // This converts &quot; to ", &#x27; to ', &amp; to &, etc.
            $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5);

            // Save as section file
            $filename = 'section-' . $section_count . '.html';
            $file_path = $page_dir . '/' . $filename;
            file_put_contents($file_path, trim($html));

            $section_count++;
        }
    }

    /**
     * Format a single block into the structure expected by blocks2html.js
     */
    private function format_block_for_converter($block) {
        $formatted = [
            'name' => $block['blockName'],
            'attributes' => $block['attrs'] ?? [],
            'innerBlocks' => []
        ];

        // Recursively format inner blocks
        if (isset($block['innerBlocks']) && !empty($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $inner_block) {
                $formatted['innerBlocks'][] = $this->format_block_for_converter($inner_block);
            }
        }

        return $formatted;
    }

    /**
     * Convert block to HTML using Node.js server-blocks2html.js parser
     * This preserves Twig attributes instead of rendering them
     */
    private function block_to_html_via_node($blocks) {
        $theme_dir = get_template_directory();
        $script_path = $theme_dir . '/includes/CLI/parsers/server-blocks2html.js';

        // Write blocks to temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'blocks_');
        $json_content = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($temp_file, $json_content);

        // Run Node.js parser
        $command = "node " . escapeshellarg($script_path) . " " . escapeshellarg($temp_file) . " 2>&1";
        $output = shell_exec($command);

        // Clean up temp file
        unlink($temp_file);

        if ($output === null || empty($output)) {
            WP_CLI::error("Failed to convert blocks to HTML using server-blocks2html.js parser");
        }

        // Check for errors in output
        if (strpos($output, 'Error processing blocks:') !== false) {
            WP_CLI::error("Parser error: " . $output);
        }

        return $output;
    }
}
