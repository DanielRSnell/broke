<?php
/**
 * Content Pull Command
 *
 * Pulls WordPress posts to markdown files in src/content/
 *
 * Usage:
 *   wp content pull <post-id>
 *   wp content pull --post_type=resource
 *   wp content pull --all
 */

namespace BlankTheme\CLI;

use WP_CLI;

class ContentPullCommand {

    /**
     * Pull WordPress posts to markdown files
     *
     * ## OPTIONS
     *
     * [<post-id>]
     * : The ID of the post to pull
     *
     * [--post_type=<type>]
     * : Pull all posts of a specific type (resource, project, post)
     *
     * [--all]
     * : Pull all posts from all post types
     *
     * ## EXAMPLES
     *
     *     wp content pull 100
     *     wp content pull --post_type=resource
     *     wp content pull --all
     */
    public function __invoke($args, $assoc_args) {
        $post_id = $args[0] ?? null;
        $post_type = $assoc_args['post_type'] ?? null;
        $all = isset($assoc_args['all']);

        // Get theme directory
        $theme_dir = get_template_directory();
        $content_dir = $theme_dir . '/src/content';

        // Ensure content directory exists
        if (!file_exists($content_dir)) {
            mkdir($content_dir, 0755, true);
        }

        // Pull single post
        if ($post_id) {
            $this->pull_post($post_id, $content_dir);
            return;
        }

        // Pull all posts of a type
        if ($post_type) {
            $this->pull_post_type($post_type, $content_dir);
            return;
        }

        // Pull all posts
        if ($all) {
            $this->pull_all_posts($content_dir);
            return;
        }

        WP_CLI::error('Please specify a post ID, --post_type, or --all');
    }

    /**
     * Pull a single post
     */
    private function pull_post($post_id, $content_dir) {
        $post = get_post($post_id);

        if (!$post) {
            WP_CLI::error("Post {$post_id} not found");
        }

        $file_path = $this->save_post_to_markdown($post, $content_dir);
        WP_CLI::success("Pulled post {$post_id} to {$file_path}");
    }

    /**
     * Pull all posts of a specific type
     */
    private function pull_post_type($post_type, $content_dir) {
        $posts = get_posts([
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        if (empty($posts)) {
            WP_CLI::warning("No posts found for post type: {$post_type}");
            return;
        }

        $count = 0;
        foreach ($posts as $post) {
            $this->save_post_to_markdown($post, $content_dir);
            $count++;
        }

        WP_CLI::success("Pulled {$count} {$post_type} posts");
    }

    /**
     * Pull all posts from all post types
     */
    private function pull_all_posts($content_dir) {
        // Get all public, non-hierarchical post types
        $post_types = get_post_types([
            'public' => true,
            'hierarchical' => false,
            '_builtin' => false
        ], 'names');

        // Include built-in 'post' type
        $post_types = array_merge(['post'], array_values($post_types));

        // Filter out unwanted types
        $excluded_types = [
            'attachment',
            'revision',
            'nav_menu_item',
            'wp_block',
            'wp_template',
            'wp_template_part',
            'wp_global_styles',
            'wp_navigation',
            'wp_font_family',
            'wp_font_face',
            'acf-field-group',
            'acf-field',
            'acf-taxonomy',
            'acf-post-type',
            'acf-ui-options-page',
            'oembed_cache',
            'user_request',
            'custom_css',
            'customize_changeset'
        ];
        $post_types = array_diff($post_types, $excluded_types);

        $total = 0;

        foreach ($post_types as $post_type) {
            $posts = get_posts([
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'post_status' => 'any',
            ]);

            foreach ($posts as $post) {
                $this->save_post_to_markdown($post, $content_dir);
                $total++;
            }
        }

        WP_CLI::success("Pulled {$total} posts total");
    }

    /**
     * Convert post to markdown and save to file
     */
    private function save_post_to_markdown($post, $content_dir) {
        // Create post type directory
        $post_type_dir = $content_dir . '/' . $post->post_type . 's';
        if (!file_exists($post_type_dir)) {
            mkdir($post_type_dir, 0755, true);
        }

        // Generate filename from slug
        $filename = $post->post_name . '.md';
        $file_path = $post_type_dir . '/' . $filename;

        // Get ACF fields
        $acf_fields = $this->get_acf_fields($post->ID);

        // Build frontmatter
        $frontmatter = $this->build_frontmatter($post, $acf_fields);

        // Convert blocks to markdown
        $content_markdown = $this->blocks_to_markdown($post->post_content);

        // Build markdown content
        $markdown = "---\n";
        $markdown .= $frontmatter;
        $markdown .= "---\n\n";
        $markdown .= $content_markdown;

        // Save to file
        file_put_contents($file_path, $markdown);

        return $file_path;
    }

    /**
     * Get ACF fields for a post
     * Returns ALL fields defined for the post type, including empty ones
     */
    private function get_acf_fields($post_id) {
        if (!function_exists('acf_get_field_groups') || !function_exists('acf_get_fields')) {
            return [];
        }

        $post_type = get_post_type($post_id);
        $all_fields = [];

        // Get field groups for this post type
        $field_groups = acf_get_field_groups(['post_type' => $post_type]);

        foreach ($field_groups as $group) {
            $fields = acf_get_fields($group['key']);

            if ($fields) {
                foreach ($fields as $field) {
                    $field_name = $field['name'];
                    $field_type = $field['type'];

                    // Skip non-data fields (tabs, messages, accordions)
                    if (in_array($field_type, ['tab', 'message', 'accordion'])) {
                        continue;
                    }

                    // Skip fields with empty names
                    if (empty($field_name)) {
                        continue;
                    }

                    // Get field value (will be empty string/null if not set)
                    $value = get_field($field_name, $post_id, false);

                    // Include the field even if empty
                    $all_fields[$field_name] = $value ?? '';
                }
            }
        }

        return $all_fields;
    }

    /**
     * Build YAML frontmatter from post data
     */
    private function build_frontmatter($post, $acf_fields) {
        $yaml = '';

        // Standard WordPress fields
        $yaml .= 'title: "' . addslashes($post->post_title) . '"' . "\n";
        $yaml .= 'slug: "' . $post->post_name . '"' . "\n";
        $yaml .= 'status: "' . $post->post_status . '"' . "\n";
        $yaml .= 'author: ' . $post->post_author . "\n";
        $yaml .= 'date: "' . $post->post_date . '"' . "\n";
        $yaml .= 'excerpt: "' . addslashes($post->post_excerpt) . '"' . "\n";

        // Featured image
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        if ($thumbnail_id) {
            $yaml .= 'featured_image: ' . $thumbnail_id . "\n";
        }

        // ACF fields under custom_fields
        if (!empty($acf_fields)) {
            $yaml .= "custom_fields:\n";
            foreach ($acf_fields as $field_name => $field_value) {
                // Handle empty values
                if ($field_value === '' || $field_value === null) {
                    $yaml .= '  ' . $field_name . ": \"\"\n";
                } else {
                    $yaml .= $this->format_yaml_field($field_name, $field_value, 1);
                }
            }
        }

        return $yaml;
    }

    /**
     * Format a field as YAML
     */
    private function format_yaml_field($name, $value, $indent = 0) {
        $spaces = str_repeat('  ', $indent);
        $yaml = '';

        if (is_array($value)) {
            $yaml .= $spaces . $name . ":\n";
            foreach ($value as $item) {
                if (is_array($item)) {
                    $yaml .= $spaces . "  -\n";
                    foreach ($item as $key => $val) {
                        $yaml .= $spaces . '    ' . $key . ': "' . addslashes($val) . '"' . "\n";
                    }
                } else {
                    $yaml .= $spaces . '  - "' . addslashes($item) . '"' . "\n";
                }
            }
        } else {
            $yaml .= $spaces . $name . ': "' . addslashes($value) . '"' . "\n";
        }

        return $yaml;
    }

    /**
     * Convert WordPress blocks to markdown
     */
    private function blocks_to_markdown($content) {
        // Handle empty content
        if (empty($content)) {
            return '';
        }

        // Parse blocks from content
        $blocks = parse_blocks($content);

        // Convert blocks to Gutenberg block format (same structure as client-side)
        $formatted_blocks = $this->format_blocks_for_converter($blocks);

        // Handle empty blocks
        if (empty($formatted_blocks)) {
            return '';
        }

        // Use Node.js script to convert blocks to HTML (same logic as client-blocks2html.js)
        $html = $this->blocks_to_html_via_node($formatted_blocks);

        // Convert HTML to markdown
        return $this->html_to_markdown($html);
    }

    /**
     * Format parsed blocks into the structure expected by blocks2html.js
     */
    private function format_blocks_for_converter($blocks) {
        $formatted = [];

        foreach ($blocks as $block) {
            if (empty($block['blockName'])) {
                continue; // Skip empty blocks
            }

            $formatted_block = [
                'name' => $block['blockName'],
                'attributes' => $block['attrs'] ?? [],
                'innerBlocks' => []
            ];

            // Recursively format inner blocks
            if (isset($block['innerBlocks']) && !empty($block['innerBlocks'])) {
                $formatted_block['innerBlocks'] = $this->format_blocks_for_converter($block['innerBlocks']);
            }

            $formatted[] = $formatted_block;
        }

        return $formatted;
    }

    /**
     * Convert blocks to HTML using Node.js server-blocks2html.js parser
     * This uses JSDOM to run client-blocks2html.js in Node.js environment
     * Avoids render_block() which executes/interprets content
     */
    private function blocks_to_html_via_node($blocks) {
        $theme_dir = get_template_directory();
        $script_path = $theme_dir . '/includes/CLI/parsers/server-blocks2html.js';

        // Write blocks to temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'blocks_');
        $json_content = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($temp_file, $json_content);

        // Debug: Save JSON for inspection
        file_put_contents('/tmp/broke-cli-blocks.json', $json_content);
        WP_CLI::debug("Saved blocks JSON to /tmp/broke-cli-blocks.json (" . count($blocks) . " blocks)", 'content-pull');

        // Run Node.js parser (uses JSDOM to execute client-blocks2html.js)
        $descriptorspec = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open(
            "node " . escapeshellarg($script_path) . " " . escapeshellarg($temp_file),
            $descriptorspec,
            $pipes
        );

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $return_code = proc_close($process);

            // Clean up temp file
            unlink($temp_file);

            // Check for actual parser errors (non-zero exit code or stderr output)
            if ($return_code !== 0 || !empty($error)) {
                WP_CLI::error("Parser error: " . $error);
            }

            // Allow empty output - it's valid when posts have no convertible content
        } else {
            unlink($temp_file);
            WP_CLI::error("Failed to start Node.js parser");
        }

        // Debug: Save HTML for inspection
        file_put_contents('/tmp/broke-cli-parsed.html', $output);
        WP_CLI::debug("Saved parsed HTML to /tmp/broke-cli-parsed.html", 'content-pull');

        return $output;
    }

    /**
     * Convert HTML to markdown
     * Preserves ONLY <section> and <div> elements with their full structure
     * Everything else is converted to markdown
     */
    private function html_to_markdown($html) {
        // Remove WordPress comments
        $html = preg_replace('/<!--\s*wp:.*?-->/s', '', $html);
        $html = preg_replace('/<!--\s*\/wp:.*?-->/s', '', $html);

        // Save rendered HTML for debugging
        file_put_contents('/tmp/broke-cli-custom-render.html', $html);
        WP_CLI::debug("Saved rendered HTML to /tmp/broke-cli-custom-render.html", 'content-pull');

        // Count code blocks before processing
        preg_match_all('/<pre[^>]*><code[^>]*class=["\'][^"\']*language-/is', $html, $before);
        WP_CLI::debug("html_to_markdown input has " . count($before[0]) . " code blocks with language", 'content-pull');

        // Step 1: Convert code blocks FIRST (before protection phase)
        // This prevents them from being wrapped inside protected sections/divs

        // Convert code blocks WITH language class
        // Use placeholders to protect PHP tags from being interpreted
        $code_blocks = [];
        $code_count = 0;
        $html = preg_replace_callback('/<pre[^>]*><code[^>]*class=["\'][^"\']*language-([a-z0-9\-]+)[^"\']*["\'][^>]*>(.*?)<\/code><\/pre>/is', function($matches) use (&$code_count, &$code_blocks) {
            $language = $matches[1];
            $code_before = $matches[2];
            // Decode HTML entities for human-readable markdown
            $code = html_entity_decode($code_before, ENT_QUOTES | ENT_HTML5);
            $code_count++;

            // Store in array to protect from PHP interpretation
            $placeholder = "___CODE_BLOCK_{$code_count}___";
            $code_blocks[$placeholder] = "```{$language}\n{$code}\n```\n\n";

            WP_CLI::debug("Converting code block #{$code_count} with language {$language}: " . substr($code, 0, 50), 'content-pull');

            return $placeholder;
        }, $html);
        WP_CLI::debug("Converted {$code_count} code blocks with language", 'content-pull');

        // Convert code blocks WITHOUT language class
        $code_no_lang_count = 0;
        $html = preg_replace_callback('/<pre[^>]*><code[^>]*>(.*?)<\/code><\/pre>/is', function($matches) use (&$code_no_lang_count) {
            // Decode HTML entities for human-readable markdown
            $code = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
            $code_no_lang_count++;
            return "```\n{$code}\n```\n\n";
        }, $html);
        if ($code_no_lang_count > 0) {
            WP_CLI::debug("Converted {$code_no_lang_count} code blocks WITHOUT language", 'content-pull');
        }

        // Step 2: Protect section and div elements by replacing them with placeholders
        $protected_elements = [];
        $placeholder_index = 0;

        // Protect sections (with all content and nested elements)
        $html = preg_replace_callback('/<section[^>]*>.*?<\/section>/is', function($matches) use (&$protected_elements, &$placeholder_index) {
            $placeholder = "___PROTECTED_ELEMENT_{$placeholder_index}___";
            $protected_elements[$placeholder] = $matches[0];
            $placeholder_index++;
            return $placeholder;
        }, $html);

        // Protect divs (with all content and nested elements)
        $html = preg_replace_callback('/<div[^>]*>.*?<\/div>/is', function($matches) use (&$protected_elements, &$placeholder_index) {
            $placeholder = "___PROTECTED_ELEMENT_{$placeholder_index}___";
            $protected_elements[$placeholder] = $matches[0];
            $placeholder_index++;
            return $placeholder;
        }, $html);

        // Step 3: Convert everything else to markdown

        // Convert headings
        $html = preg_replace('/<h1[^>]*>(.*?)<\/h1>/is', "# $1\n\n", $html);
        $html = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "## $1\n\n", $html);
        $html = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "### $1\n\n", $html);
        $html = preg_replace('/<h4[^>]*>(.*?)<\/h4>/is', "#### $1\n\n", $html);
        $html = preg_replace('/<h5[^>]*>(.*?)<\/h5>/is', "##### $1\n\n", $html);
        $html = preg_replace('/<h6[^>]*>(.*?)<\/h6>/is', "###### $1\n\n", $html);

        // Convert paragraphs
        $html = preg_replace('/<p[^>]*>(.*?)<\/p>/is', "$1\n\n", $html);

        // Convert strong/bold
        $html = preg_replace('/<strong[^>]*>(.*?)<\/strong>/is', "**$1**", $html);
        $html = preg_replace('/<b[^>]*>(.*?)<\/b>/is', "**$1**", $html);

        // Convert em/italic
        $html = preg_replace('/<em[^>]*>(.*?)<\/em>/is', "*$1*", $html);
        $html = preg_replace('/<i[^>]*>(.*?)<\/i>/is', "*$1*", $html);

        // Convert links
        $html = preg_replace('/<a[^>]*href=["\'](.*?)["\'][^>]*>(.*?)<\/a>/is', "[$2]($1)", $html);

        // Convert unordered lists
        $html = preg_replace_callback('/<ul[^>]*>(.*?)<\/ul>/is', function($matches) {
            $items = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "- $1\n", $matches[1]);
            return $items . "\n";
        }, $html);

        // Convert ordered lists
        $html = preg_replace_callback('/<ol[^>]*>(.*?)<\/ol>/is', function($matches) {
            $items = $matches[1];
            $counter = 1;
            $items = preg_replace_callback('/<li[^>]*>(.*?)<\/li>/is', function($m) use (&$counter) {
                return $counter++ . ". " . $m[1] . "\n";
            }, $items);
            return $items . "\n";
        }, $html);

        // Convert blockquotes
        $html = preg_replace('/<blockquote[^>]*>(.*?)<\/blockquote>/is', "> $1\n\n", $html);

        // NOTE: Code blocks (<pre><code>) are already converted above (before protection phase)
        // This ensures they're not wrapped inside protected sections/divs

        // Convert inline code - DON'T decode entities yet to preserve HTML examples
        $html = preg_replace_callback('/<code[^>]*>(.*?)<\/code>/is', function($matches) {
            $code_content = $matches[1];
            WP_CLI::debug("Converting INLINE code: " . substr($code_content, 0, 30), 'content-pull');

            // Use placeholder to protect from strip_tags()
            // We'll decode entities AFTER strip_tags() runs
            return "___INLINE_CODE_START___{$code_content}___INLINE_CODE_END___";
        }, $html);

        // Convert images
        $html = preg_replace('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*\/?>/is', "![$2]($1)\n\n", $html);
        $html = preg_replace('/<img[^>]*src=["\'](.*?)["\'][^>]*\/?>/is', "![]($1)\n\n", $html);

        // Convert line breaks
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);

        // Remove remaining HTML tags (except protected section/div)
        $html = strip_tags($html);

        // Step 3: Restore protected section and div elements and strip IDs
        foreach ($protected_elements as $placeholder => $original) {
            // Strip id attributes from all elements
            $cleaned = preg_replace('/\s+id=["\'][^"\']*["\']/i', '', $original);
            $html = str_replace($placeholder, $cleaned, $html);
        }

        // Step 4: Convert inline code placeholders to backticks (AFTER strip_tags)
        $html = preg_replace_callback('/___INLINE_CODE_START___(.*?)___INLINE_CODE_END___/s', function($matches) {
            // NOW decode HTML entities since we're past strip_tags()
            $code = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
            return "`{$code}`";
        }, $html);

        // Step 5: Restore code block placeholders (AFTER all other processing)
        foreach ($code_blocks as $placeholder => $code_block) {
            $html = str_replace($placeholder, $code_block, $html);
        }

        // Clean up extra whitespace
        $html = preg_replace('/\n{3,}/', "\n\n", $html);
        $html = trim($html);

        return $html;
    }
}
