<?php

namespace BlankTheme\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Push templates from HTML to block markup
 */
class TemplatePushCommand extends WP_CLI_Command {

    /**
     * Convert template from HTML to block markup
     *
     * ## OPTIONS
     *
     * <template>
     * : The template name (without .html extension)
     *
     * ## EXAMPLES
     *
     *     wp template push index
     *     wp template push single
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args) {
        $template_name = $args[0];
        $theme_dir = get_template_directory();

        // Source: src/templates/{name}.html
        $source_file = $theme_dir . '/src/templates/' . $template_name . '.html';

        // Destination: ./templates/{name}.html
        $dest_dir = $theme_dir . '/templates';
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
            WP_CLI::error("Failed to convert HTML to blocks");
            return;
        }

        // Write to destination
        file_put_contents($dest_file, trim($block_markup));

        WP_CLI::success("Pushed template: src/templates/{$template_name}.html → templates/{$template_name}.html");
    }
}
