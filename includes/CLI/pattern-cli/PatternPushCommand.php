<?php
namespace BlankTheme\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Convert HTML patterns from patterns/ directory to PHP pattern files with metadata.
 */
class PatternPushCommand extends WP_CLI_Command {

    /**
     * Convert HTML pattern files to PHP pattern files with proper metadata.
     *
     * ## OPTIONS
     *
     * [--all]
     * : Convert all pattern files in the patterns/ directory
     *
     * [--title=<title>]
     * : Pattern title (default: derived from filename)
     *
     * [--description=<description>]
     * : Pattern description (default: "Reusable pattern section")
     *
     * [--categories=<categories>]
     * : Comma-separated list of categories (default: "patterns")
     *
     * [--keywords=<keywords>]
     * : Comma-separated list of keywords (default: empty)
     *
     * [--viewport-width=<width>]
     * : Viewport width for pattern preview (default: 1280)
     *
     * [--inserter=<inserter>]
     * : Whether to show in inserter (default: true)
     *
     * [<file>]
     * : Specific pattern file to convert (e.g., patterns/hello-cta.html)
     *
     * ## EXAMPLES
     *
     *     # Convert all patterns
     *     wp pattern push --all
     *
     *     # Convert specific pattern
     *     wp pattern push patterns/hello-cta.html
     *
     *     # Convert with custom metadata
     *     wp pattern push patterns/hero.html --title="Hero Section" --categories="hero,featured"
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args) {
        $theme_dir = get_template_directory();
        $theme_slug = basename($theme_dir);
        $patterns_dir = $theme_dir . '/patterns'; // Changed from '/src/patterns' to '/patterns'

        // Check if converting all patterns
        $convert_all = isset($assoc_args['all']);
        $specific_file = isset($args[0]) ? $args[0] : null;

        if (!$convert_all && !$specific_file) {
            WP_CLI::error('Please specify either --all or a specific file path.');
            return;
        }

        // Get list of files to convert
        $files = [];
        if ($convert_all) {
            if (!is_dir($patterns_dir)) {
                WP_CLI::error("Patterns directory not found: {$patterns_dir}. Run 'wp html:blocks src/patterns --all' first to convert HTML to block markup.");
                return;
            }
            $files = glob($patterns_dir . '/*.html');
        } else {
            $file_path = $theme_dir . '/' . $specific_file;
            if (!file_exists($file_path)) {
                WP_CLI::error("File not found: {$file_path}");
                return;
            }
            $files = [$file_path];
        }

        if (empty($files)) {
            WP_CLI::warning("No HTML pattern files found in {$patterns_dir}. Run 'wp html:blocks src/patterns --all' first to convert HTML to block markup.");
            return;
        }

        $converted_count = 0;

        foreach ($files as $file_path) {
            $filename = basename($file_path, '.html');

            // Read the HTML content
            $html_content = file_get_contents($file_path);

            // Build metadata
            $title = isset($assoc_args['title']) ? $assoc_args['title'] : $this->generate_title($filename);
            $slug = $theme_slug . '/' . $filename;
            $description = isset($assoc_args['description']) ? $assoc_args['description'] : 'Reusable pattern section';
            $categories = isset($assoc_args['categories']) ? $assoc_args['categories'] : 'patterns';
            $keywords = isset($assoc_args['keywords']) ? $assoc_args['keywords'] : '';
            $viewport_width = isset($assoc_args['viewport-width']) ? intval($assoc_args['viewport-width']) : 1280;
            $inserter = isset($assoc_args['inserter']) ? $assoc_args['inserter'] : 'true';

            // Generate PHP file content
            $php_content = $this->generate_php_pattern(
                $title,
                $slug,
                $description,
                $categories,
                $keywords,
                $viewport_width,
                $inserter,
                $html_content
            );

            // Write PHP file to patterns/ directory
            $output_dir = $theme_dir . '/patterns';
            if (!is_dir($output_dir)) {
                mkdir($output_dir, 0755, true);
            }
            $php_file_path = $output_dir . '/' . $filename . '.php';
            file_put_contents($php_file_path, $php_content);

            $converted_count++;
            WP_CLI::log("✓ Converted {$filename}.html → {$filename}.php");
        }

        WP_CLI::success("Converted {$converted_count} pattern(s) to PHP files");
    }

    /**
     * Generate a human-readable title from filename
     */
    private function generate_title($filename) {
        // Convert kebab-case to Title Case
        $title = str_replace(['-', '_'], ' ', $filename);
        $title = ucwords($title);
        return $title;
    }

    /**
     * Generate PHP pattern file content with metadata header
     */
    private function generate_php_pattern($title, $slug, $description, $categories, $keywords, $viewport_width, $inserter, $html_content) {
        $header = "<?php\n";
        $header .= "/**\n";
        $header .= " * Title: {$title}\n";
        $header .= " * Slug: {$slug}\n";
        $header .= " * Description: {$description}\n";
        $header .= " * Categories: {$categories}\n";

        if (!empty($keywords)) {
            $header .= " * Keywords: {$keywords}\n";
        }

        $header .= " * Viewport Width: {$viewport_width}\n";
        $header .= " * Inserter: {$inserter}\n";
        $header .= " */\n";
        $header .= "?>\n";

        return $header . $html_content;
    }
}
