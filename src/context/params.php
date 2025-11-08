<?php
/**
 * Params Context
 *
 * Automatically adds GET request parameters to Timber context.
 * Makes URL query parameters available in templates as key-value pairs.
 *
 * Example:
 * URL: /page?email=user@example.com&name=John
 * Template: {{ params.email }} -> user@example.com
 *           {{ params.name }} -> John
 */

add_filter('timber/context', function($context) {
    // Initialize params array
    $params = [];

    // Parse GET parameters and sanitize them
    if (!empty($_GET)) {
        foreach ($_GET as $key => $value) {
            // Sanitize key (alphanumeric, underscore, hyphen only)
            $sanitized_key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);

            if (empty($sanitized_key)) {
                continue;
            }

            // Sanitize value based on type
            if (is_array($value)) {
                // Recursively sanitize arrays
                $params[$sanitized_key] = array_map('sanitize_text_field', $value);
            } else {
                // Sanitize scalar values
                $params[$sanitized_key] = sanitize_text_field($value);
            }
        }
    }

    // Add params to context
    $context['params'] = $params;

    return $context;
});
