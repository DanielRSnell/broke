<?php
/**
 * Page Push Command
 *
 * Pushes HTML section files from src/content/pages/ to WordPress pages
 *
 * Converts HTML sections to Universal Blocks and updates pages:
 * src/content/pages/{slug}/section-1.html → WordPress blocks
 *
 * Usage:
 *   wp page push src/content/pages/home
 *   wp page push --all
 */

namespace BlankTheme\CLI;

use WP_CLI;

class PagePushCommand {

    /**
     * Push HTML section files to WordPress pages
     *
     * ## OPTIONS
     *
     * [<path>]
     * : Path to page directory (e.g., src/content/pages/home)
     *
     * [--all]
     * : Push all pages
     *
     * ## EXAMPLES
     *
     *     wp page push src/content/pages/home
     *     wp page push --all
     */
    public function __invoke($args, $assoc_args) {
        $path = $args[0] ?? null;
        $all = isset($assoc_args['all']);

        $theme_dir = get_template_directory();
        $pages_dir = $theme_dir . '/src/content/pages';

        if (!file_exists($pages_dir)) {
            WP_CLI::error("Pages directory not found: {$pages_dir}");
        }

        // Push all pages
        if ($all) {
            $this->push_all_pages($pages_dir);
            return;
        }

        // Push single page directory
        if ($path) {
            $this->push_page_directory($path);
            return;
        }

        WP_CLI::error('Please specify a path or --all');
    }

    /**
     * Push all page directories
     */
    private function push_all_pages($pages_dir) {
        $page_dirs = glob($pages_dir . '/*', GLOB_ONLYDIR);

        if (empty($page_dirs)) {
            WP_CLI::warning("No page directories found in {$pages_dir}");
            return;
        }

        $count = 0;
        foreach ($page_dirs as $page_dir) {
            $this->push_page_directory($page_dir);
            $count++;
        }

        WP_CLI::success("Pushed {$count} pages to WordPress");
    }

    /**
     * Push a single page directory
     */
    private function push_page_directory($page_dir) {
        if (!is_dir($page_dir)) {
            WP_CLI::error("Not a directory: {$page_dir}");
        }

        // Get slug from directory name
        $slug = basename($page_dir);

        // Find all section HTML files
        $section_files = glob($page_dir . '/section-*.html');

        if (empty($section_files)) {
            WP_CLI::warning("No section files found in {$page_dir}");
            return;
        }

        // Sort section files by number
        natsort($section_files);

        // Convert each HTML section to blocks
        $all_blocks = '';
        foreach ($section_files as $section_file) {
            $html = file_get_contents($section_file);
            $blocks = $this->html_to_blocks($html);
            $all_blocks .= $blocks . "\n\n";
        }

        // Find or create page by slug
        $page = get_page_by_path($slug, OBJECT, 'page');

        // Disable kses filtering to preserve SVGs and other HTML
        kses_remove_filters();

        if ($page) {
            // Update existing page - use wp_slash to preserve backslashes in JSON
            wp_update_post([
                'ID' => $page->ID,
                'post_content' => wp_slash(trim($all_blocks)),
            ]);
            WP_CLI::success("Updated page {$page->ID}: {$page->post_title}");
        } else {
            // Create new page - use wp_slash to preserve backslashes in JSON
            $post_id = wp_insert_post([
                'post_type' => 'page',
                'post_title' => ucwords(str_replace('-', ' ', $slug)),
                'post_name' => $slug,
                'post_content' => wp_slash(trim($all_blocks)),
                'post_status' => 'draft',
            ]);

            if (is_wp_error($post_id)) {
                WP_CLI::error("Failed to create page: " . $post_id->get_error_message());
            }

            WP_CLI::success("Created page {$post_id}: " . ucwords(str_replace('-', ' ', $slug)));
        }

        // Re-enable kses filtering
        kses_init_filters();
    }

    /**
     * Convert HTML to Universal Blocks using html2blocks parser
     */
    private function html_to_blocks($html) {
        $theme_dir = get_template_directory();
        $parser_path = $theme_dir . '/includes/cli/html2pattern-cli/src/parser.js';

        if (!file_exists($parser_path)) {
            WP_CLI::warning('html2blocks parser not found. Using basic conversion.');
            return $this->basic_html_to_blocks($html);
        }

        // Create temp file for HTML
        $temp_html = tempnam(sys_get_temp_dir(), 'html_');
        file_put_contents($temp_html, $html);

        // Run Node.js parser
        $node_script = <<<JS
const { html2blocks, generateBlockMarkup } = require('{$parser_path}');
const fs = require('fs');
const html = fs.readFileSync('{$temp_html}', 'utf8');
const blocks = html2blocks(html);
const markup = generateBlockMarkup(blocks, { doubleEscape: false });
console.log(markup);
JS;

        $temp_script = tempnam(sys_get_temp_dir(), 'script_') . '.js';
        file_put_contents($temp_script, $node_script);

        $output = shell_exec("node {$temp_script} 2>&1");

        // Cleanup
        unlink($temp_html);
        unlink($temp_script);

        if (empty($output)) {
            WP_CLI::warning('html2blocks conversion failed. Using basic conversion.');
            return $this->basic_html_to_blocks($html);
        }

        return trim($output);
    }

    /**
     * Basic fallback HTML to blocks conversion
     */
    private function basic_html_to_blocks($html) {
        // Wrap entire HTML in a universal/element block
        return '<!-- wp:universal/element {"tagName":"div","contentType":"html","globalAttrs":{},"metadata":{"name":"Div"},"content":"' .
               addslashes($html) .
               '"} /-->';
    }
}
