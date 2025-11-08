<?php
namespace BlankTheme\CLI;

use WP_CLI;
use WP_CLI_Command;
use DOMDocument;
use DOMXPath;

/**
 * Extract top-level children from an HTML file into individual pattern files.
 */
class PatternExtractCommand extends WP_CLI_Command {

    /**
     * Extract top-level children from an HTML file into separate pattern files.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to the HTML file to extract from (e.g., src/pages/portfolio.html)
     *
     * ## EXAMPLES
     *
     *     wp pattern extract src/pages/portfolio.html
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args) {
        $source_file = $args[0];
        $theme_dir = get_template_directory();
        $full_path = $theme_dir . '/' . $source_file;

        if (!file_exists($full_path)) {
            WP_CLI::error("File not found: {$full_path}");
            return;
        }

        // Get the base filename without extension
        $path_info = pathinfo($source_file);
        $base_name = $path_info['filename'];

        // Read the HTML content
        $html_content = file_get_contents($full_path);

        // Wrap content in a div to create a proper structure
        $wrapped_html = '<div>' . $html_content . '</div>';

        // Parse HTML
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $wrapped_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Get the wrapper div's children
        $wrapper = $doc->documentElement;
        $children = $wrapper->childNodes;

        $child_count = 0;
        $extracted_count = 0;

        foreach ($children as $child) {
            // Skip text nodes that are just whitespace
            if ($child->nodeType === XML_TEXT_NODE && trim($child->textContent) === '') {
                continue;
            }

            // Skip non-element nodes (comments, etc.)
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $child_count++;

            // Create output filename
            $output_file = $theme_dir . '/src/patterns/' . $base_name . '-child-' . $child_count . '.html';

            // Get the HTML content of this child
            $child_html = $doc->saveHTML($child);

            // Clean up the HTML
            $child_html = $this->clean_html($child_html);

            // Save to file
            file_put_contents($output_file, $child_html);

            $extracted_count++;
            WP_CLI::log("Extracted: {$base_name}-child-{$child_count}.html");
        }

        if ($extracted_count === 0) {
            WP_CLI::warning("No top-level children found in {$source_file}");
        } else {
            WP_CLI::success("Extracted {$extracted_count} pattern(s) from {$source_file} → src/patterns/");
        }
    }

    /**
     * Clean up HTML output
     */
    private function clean_html($html) {
        // Remove XML encoding declaration if present
        $html = preg_replace('/<\?xml[^?]+\?>/', '', $html);

        // Trim whitespace
        $html = trim($html);

        return $html;
    }
}
