<?php
/**
 * Content Push Command
 *
 * Pushes markdown files from src/content/ to WordPress posts
 * Converts MD -> HTML -> Universal Blocks using html2blocks parser
 */

namespace BlankTheme\CLI;

use WP_CLI;

class ContentPushCommand {

    public function __invoke($args, $assoc_args) {
        $file = $args[0] ?? null;
        $post_type = $assoc_args['post_type'] ?? null;
        $all = isset($assoc_args['all']);

        $theme_dir = get_template_directory();

        if ($file) {
            if (substr($file, 0, 1) !== '/') {
                $file = $theme_dir . '/' . $file;
            }
            $this->push_file($file);
            return;
        }

        if ($post_type) {
            $this->push_post_type($post_type, $theme_dir);
            return;
        }

        if ($all) {
            $this->push_all_files($theme_dir);
            return;
        }

        WP_CLI::error('Please specify a file path, --post_type, or --all');
    }

    private function push_file($file_path) {
        if (!file_exists($file_path)) {
            WP_CLI::error("File not found: {$file_path}");
        }

        $content = file_get_contents($file_path);
        $parsed = $this->parse_markdown($content, $file_path);

        $slug = $parsed['frontmatter']['slug'] ?? basename($file_path, '.md');
        $post = get_page_by_path($slug, OBJECT, $parsed['post_type']);

        if ($post) {
            $post_id = $this->update_post($post->ID, $parsed);
            WP_CLI::success("Updated post {$post_id}: {$parsed['frontmatter']['title']}");
        } else {
            $post_id = $this->create_post($parsed);
            WP_CLI::success("Created post {$post_id}: {$parsed['frontmatter']['title']}");
        }
    }

    private function push_post_type($post_type, $theme_dir) {
        $content_dir = $theme_dir . '/src/content/' . $post_type . 's';

        if (!is_dir($content_dir)) {
            WP_CLI::error("Directory not found: {$content_dir}");
        }

        $files = glob($content_dir . '/*.md');
        $count = 0;

        foreach ($files as $file) {
            $this->push_file($file);
            $count++;
        }

        WP_CLI::success("Pushed {$count} {$post_type} files");
    }

    private function push_all_files($theme_dir) {
        $content_base = $theme_dir . '/src/content';
        $total = 0;

        // Scan src/content/ for all post type directories
        if (!is_dir($content_base)) {
            WP_CLI::error("Content directory not found: {$content_base}");
        }

        $directories = glob($content_base . '/*', GLOB_ONLYDIR);

        foreach ($directories as $content_dir) {
            $dir_name = basename($content_dir);

            // Extract post type from directory name (remove trailing 's')
            // e.g., "posts" -> "post", "portfolios" -> "portfolio"
            $post_type = rtrim($dir_name, 's');

            $files = glob($content_dir . '/*.md');

            foreach ($files as $file) {
                $this->push_file($file);
                $total++;
            }
        }

        WP_CLI::success("Pushed {$total} files total");
    }

    private function parse_markdown($markdown, $file_path = '') {
        preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $markdown, $matches);

        if (empty($matches)) {
            WP_CLI::error('Invalid markdown format. Missing frontmatter.');
        }

        $frontmatter = $this->parse_yaml($matches[1]);
        $markdown_content = trim($matches[2]);

        // Detect post type from file path
        $post_type = $this->detect_post_type($file_path, $frontmatter);

        // Convert MD -> HTML -> Blocks
        $blocks_content = $this->markdown_to_blocks($markdown_content);

        return [
            'frontmatter' => $frontmatter,
            'content' => $blocks_content,
            'post_type' => $post_type,
        ];
    }

    private function detect_post_type($file_path, $frontmatter) {
        if (!empty($frontmatter['post_type'])) {
            return $frontmatter['post_type'];
        }

        // Extract from path: src/content/{post_type}s/file.md -> {post_type}
        // Allow hyphens in post type names (e.g., stack-tools)
        if (preg_match('/\/content\/([\w-]+)s?\//', $file_path, $matches)) {
            $dir_name = $matches[1];
            // Remove trailing 's' if it's part of plural (e.g., "stack-tools" -> "stack-tool")
            return rtrim($dir_name, 's');
        }

        return 'post';
    }

    private function parse_yaml($yaml) {
        $data = [];
        $lines = explode("\n", $yaml);
        $current_section = null;
        $current_array = null;
        $current_key = null;

        foreach ($lines as $line) {
            $line = rtrim($line);

            if (empty($line)) continue;

            // Top-level key
            if (preg_match('/^(\w+):\s*(.*)$/', $line, $matches)) {
                $key = $matches[1];
                $value = trim($matches[2], '"');

                if (empty($value)) {
                    $current_section = $key;
                    $data[$key] = [];
                    $current_array = null;
                } else {
                    // Store as int if numeric, otherwise string
                    $data[$key] = is_numeric($value) ? (int)$value : $value;
                    $current_section = null;
                }
                continue;
            }

            // Nested field (2 spaces)
            if (preg_match('/^  (\w+):\s*(.*)$/', $line, $matches)) {
                $key = $matches[1];
                $value = trim($matches[2], '"');

                if ($current_section) {
                    if (empty($value)) {
                        $current_array = $key;
                        $data[$current_section][$key] = [];
                    } else {
                        $data[$current_section][$key] = is_numeric($value) ? (int)$value : $value;
                        $current_array = null;
                    }
                }
                continue;
            }

            // Array item (4 spaces) - can have inline key:value or be empty
            if (preg_match('/^    -\s*(.*)$/', $line, $matches)) {
                if ($current_section && $current_array) {
                    $data[$current_section][$current_array][] = [];
                    $current_key = count($data[$current_section][$current_array]) - 1;

                    // Check if there's an inline key:value after the dash
                    $inline = trim($matches[1]);
                    if (!empty($inline) && preg_match('/^(\w+):\s*(.*)$/', $inline, $inline_matches)) {
                        $key = $inline_matches[1];
                        $value = trim($inline_matches[2], '"');
                        $data[$current_section][$current_array][$current_key][$key] = is_numeric($value) && $value !== '' ? (int)$value : $value;
                    }
                }
                continue;
            }

            // Nested array value (6 spaces)
            if (preg_match('/^      (\w+):\s*(.*)$/', $line, $matches)) {
                $key = $matches[1];
                $value = trim($matches[2], '"');

                if ($current_section && $current_array !== null && isset($current_key)) {
                    // Convert to int if numeric, keep as string otherwise
                    $data[$current_section][$current_array][$current_key][$key] = is_numeric($value) && $value !== '' ? (int)$value : $value;
                }
                continue;
            }
        }

        return $data;
    }

    private function markdown_to_blocks($markdown) {
        // If already has block markup at the START (not just in code examples), return as-is
        // Trim to ignore leading whitespace
        $trimmed = ltrim($markdown);
        if (strpos($trimmed, '<!-- wp:') === 0) {
            WP_CLI::debug("Content already has block markup, skipping conversion", 'content-push');
            return $markdown;
        }

        WP_CLI::debug("Converting markdown to HTML...", 'content-push');

        // Convert markdown to HTML
        $html = $this->markdown_to_html($markdown);

        WP_CLI::debug("HTML conversion complete. Length: " . strlen($html) . " bytes", 'content-push');

        // Use Node.js html2blocks parser
        $theme_dir = get_template_directory();
        $parser_path = $theme_dir . '/includes/CLI/html2pattern-cli/src/parser.js';

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
const markup = generateBlockMarkup(blocks, { doubleEscape: true });
console.log(markup);
JS;

        $temp_script = tempnam(sys_get_temp_dir(), 'script_') . '.js';
        file_put_contents($temp_script, $node_script);

        $output = shell_exec("node {$temp_script} 2>&1");

        WP_CLI::debug("Node.js parser output length: " . strlen($output) . " bytes", 'content-push');

        // Cleanup
        unlink($temp_html);
        unlink($temp_script);

        if (empty($output)) {
            WP_CLI::warning('html2blocks conversion failed. Using basic conversion.');
            return $this->basic_html_to_blocks($html);
        }

        WP_CLI::debug("Successfully converted HTML to blocks", 'content-push');

        return trim($output);
    }

    private function markdown_to_html($markdown) {
        // Use Parsedown to convert markdown to HTML
        // No need to protect HTML - Parsedown handles it correctly
        require_once get_template_directory() . '/vendor/autoload.php';
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(false); // Allow raw HTML
        $html = $parsedown->text($markdown);

        return $html;
    }

    private function basic_html_to_blocks($html) {
        $blocks = '';
        $lines = explode("\n", $html);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^<h([1-6])>(.+)<\/h[1-6]>$/', $line, $matches)) {
                $blocks .= "<!-- wp:heading -->\n{$line}\n<!-- /wp:heading -->\n\n";
            } elseif (preg_match('/^<p>(.+)<\/p>$/', $line)) {
                $blocks .= "<!-- wp:paragraph -->\n{$line}\n<!-- /wp:paragraph -->\n\n";
            } else {
                $blocks .= "<!-- wp:paragraph -->\n<p>{$line}</p>\n<!-- /wp:paragraph -->\n\n";
            }
        }

        return trim($blocks);
    }

    private function create_post($parsed) {
        $frontmatter = $parsed['frontmatter'];

        $post_data = [
            'post_title' => $frontmatter['title'] ?? 'Untitled',
            'post_name' => $frontmatter['slug'] ?? '',
            'post_content' => $parsed['content'],
            'post_status' => $frontmatter['status'] ?? 'draft',
            'post_type' => $parsed['post_type'],
            'post_author' => isset($frontmatter['author']) ? (int)$frontmatter['author'] : 1,
            'post_excerpt' => isset($frontmatter['excerpt']) && is_string($frontmatter['excerpt']) ? $frontmatter['excerpt'] : '',
        ];

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            WP_CLI::error($post_id->get_error_message());
        }

        $this->update_acf_fields($post_id, $frontmatter);

        return $post_id;
    }

    private function update_post($post_id, $parsed) {
        $frontmatter = $parsed['frontmatter'];

        $post_data = [
            'ID' => $post_id,
            'post_title' => $frontmatter['title'] ?? '',
            'post_name' => $frontmatter['slug'] ?? '',
            'post_content' => $parsed['content'],
            'post_status' => $frontmatter['status'] ?? 'draft',
            'post_excerpt' => isset($frontmatter['excerpt']) && is_string($frontmatter['excerpt']) ? $frontmatter['excerpt'] : '',
        ];

        wp_update_post($post_data);
        $this->update_acf_fields($post_id, $frontmatter);

        return $post_id;
    }

    private function update_acf_fields($post_id, $frontmatter) {
        if (!function_exists('update_field')) {
            return;
        }

        if (isset($frontmatter['custom_fields']) && is_array($frontmatter['custom_fields'])) {
            foreach ($frontmatter['custom_fields'] as $key => $value) {
                // Cast value based on ACF field type
                $casted_value = $this->cast_acf_value($key, $value);

                // Debug output
                WP_CLI::debug("Updating field '{$key}' with value: " . print_r($casted_value, true), 'content-push');

                update_field($key, $casted_value, $post_id);
            }
        }

        if (!empty($frontmatter['featured_image'])) {
            set_post_thumbnail($post_id, $frontmatter['featured_image']);
        }
    }

    /**
     * Cast value based on ACF field type schema
     */
    private function cast_acf_value($field_key, $value) {
        if (!function_exists('acf_get_field')) {
            return $value;
        }

        // Get field object to determine type
        $field = acf_get_field($field_key);

        if (!$field) {
            return $value;
        }

        $field_type = $field['type'] ?? 'text';

        // Cast based on field type
        switch ($field_type) {
            case 'number':
                return is_numeric($value) ? (int)$value : $value;

            case 'true_false':
                return (bool)$value;

            case 'repeater':
                // For repeaters, cast each sub-field
                if (is_array($value)) {
                    return array_map(function($row) use ($field) {
                        if (!is_array($row)) return $row;

                        $casted_row = [];
                        foreach ($row as $sub_key => $sub_value) {
                            // Find sub-field definition
                            $sub_field = null;
                            if (isset($field['sub_fields'])) {
                                foreach ($field['sub_fields'] as $sf) {
                                    if ($sf['name'] === $sub_key) {
                                        $sub_field = $sf;
                                        break;
                                    }
                                }
                            }

                            // Cast sub-field value
                            if ($sub_field) {
                                $sub_type = $sub_field['type'] ?? 'text';
                                if ($sub_type === 'number' && is_numeric($sub_value)) {
                                    $casted_row[$sub_key] = (int)$sub_value;
                                } elseif ($sub_type === 'true_false') {
                                    $casted_row[$sub_key] = (bool)$sub_value;
                                } else {
                                    $casted_row[$sub_key] = $sub_value;
                                }
                            } else {
                                $casted_row[$sub_key] = $sub_value;
                            }
                        }
                        return $casted_row;
                    }, $value);
                }
                return $value;

            case 'post_object':
            case 'relationship':
                // Should be array of post IDs
                if (is_array($value)) {
                    return array_map('intval', $value);
                }
                return (int)$value;

            case 'taxonomy':
                // Should be array of term IDs
                if (is_array($value)) {
                    return array_map('intval', $value);
                }
                return (int)$value;

            default:
                return $value;
        }
    }
}
