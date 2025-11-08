<?php

namespace BlankTheme\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Convert HTML files to block markup
 */
class HtmlToBlocksCommand extends WP_CLI_Command {

    /**
     * Convert HTML to block markup
     *
     * ## OPTIONS
     *
     * <path>
     * : Path to file or directory containing HTML
     *
     * [--all]
     * : Convert all files in the directory
     *
     * [--output=<path>]
     * : Output directory (defaults to removing 'src/' prefix from input path)
     *
     * ## EXAMPLES
     *
     *     # Convert single file
     *     wp html:blocks src/parts/header.html
     *
     *     # Convert all files in directory
     *     wp html:blocks src/parts --all
     *
     *     # Convert with custom output directory
     *     wp html:blocks src/parts/header.html --output=parts
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

        WP_CLI::success("Converted {$success_count} file(s) from HTML to block markup");
    }

    /**
     * Convert a single file from HTML to block markup
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
            // Auto-generate output path by removing 'src/' prefix
            $relative_path = str_replace($theme_dir . '/', '', $source_file);

            // Remove 'src/' prefix if present
            if (str_starts_with($relative_path, 'src/')) {
                $relative_path = substr($relative_path, 4); // Remove 'src/'
            }

            $dir_name = dirname($relative_path);
            $output_dir = $theme_dir . '/' . $dir_name;
        }

        // Create output directory if needed
        if (!is_dir($output_dir)) {
            wp_mkdir_p($output_dir);
        }

        $filename = basename($source_file);
        $dest_file = $output_dir . '/' . $filename;

        // Read the HTML
        $html = file_get_contents($source_file);

        // Convert HTML to blocks using Node.js parser
        $node_script = <<<'JS'
const {html2blocks, generateBlockMarkup} = require('./includes/CLI/html2pattern-cli/src/parser.js');
const fs = require('fs');
const html = fs.readFileSync(process.argv[1], 'utf8');
const blocks = html2blocks(html);
const markup = generateBlockMarkup(blocks);
console.log(markup);
JS;

        // Execute Node.js script
        $escaped_source = escapeshellarg($source_file);
        $command = "cd " . escapeshellarg($theme_dir) . " && node -e " . escapeshellarg($node_script) . " " . $escaped_source;
        $block_markup = shell_exec($command);

        if (empty($block_markup)) {
            WP_CLI::error("Failed to convert HTML to blocks for: {$source_file}");
            return false;
        }

        // Write to destination
        file_put_contents($dest_file, trim($block_markup));

        // Calculate relative paths for display
        $source_relative = str_replace($theme_dir . '/', '', $source_file);
        $dest_relative = str_replace($theme_dir . '/', '', $dest_file);

        WP_CLI::log("✓ {$source_relative} → {$dest_relative}");

        return true;
    }
}
