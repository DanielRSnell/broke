# Gutenberg Block Extensions

A complete guide to extending WordPress core blocks with custom functionality, controls, and styling without creating new block types.

---

## Table of Contents

- [What Are Block Extensions?](#what-are-block-extensions)
- [When to Use Extensions](#when-to-use-extensions)
- [Extension Architecture](#extension-architecture)
- [Creating an Extension](#creating-an-extension)
- [File Structure](#file-structure)
- [Complete Example](#complete-example)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

---

## What Are Block Extensions?

Block extensions add custom functionality to **existing WordPress blocks** (like Group, Column, Heading) without creating entirely new block types. They use WordPress's filter system to:

1. **Add new attributes** to blocks
2. **Add inspector controls** (sidebar panels)
3. **Modify block output** (add classes, styles, HTML)
4. **Enhance editor preview** with visual feedback

### Example Use Cases

- Add background pattern selector to Group blocks
- Add position controls (sticky, fixed) to any block
- Add animation triggers to specific blocks
- Add custom spacing or sizing controls

---

## When to Use Extensions

✅ **Use Extensions When:**

- Enhancing existing core blocks
- Adding reusable design system controls
- Maintaining consistency across block types
- Avoiding block registration overhead

❌ **Don't Use Extensions When:**

- Creating completely new block types
- Functionality is specific to one custom block
- You need full control over markup structure

---

## Extension Architecture

Extensions consist of three components:

```
1. PHP Backend     → Register attributes, modify output
2. JavaScript      → Add editor controls and preview
3. CSS (optional)  → Style the extended functionality
```

### How It Works

```mermaid
Editor → JS adds controls → User sets attributes →
PHP processes attributes → Adds classes/styles to HTML →
CSS applies visual styling
```

---

## File Structure

All extensions live in `src/extensions/`:

```
src/extensions/
├── pattern-backgrounds/
│   ├── pattern-backgrounds.php        # Backend logic
│   ├── pattern-backgrounds.js         # Editor controls
│   └── pattern-backgrounds.css        # Frontend styles
│
├── position-controls/
│   ├── position-controls.php
│   ├── position-controls.js
│   └── position-controls.css
│
└── register-extensions.php            # Loads all extensions
```

**In `functions.php`:**

```php
require_once get_template_directory() . '/src/extensions/register-extensions.php';
```

**In `src/extensions/register-extensions.php`:**

```php
<?php
// Load all extensions
require_once __DIR__ . '/pattern-backgrounds/pattern-backgrounds.php';
require_once __DIR__ . '/position-controls/position-controls.php';
// Add more extensions as needed
```

---

## Creating an Extension

### Step 1: Create Directory Structure

```bash
mkdir -p src/extensions/my-extension
touch src/extensions/my-extension/my-extension.php
touch src/extensions/my-extension/my-extension.js
touch src/extensions/my-extension/my-extension.css
```

---

### Step 2: PHP Backend (Modify Block Output)

**`src/extensions/my-extension/my-extension.php`**

```php
<?php
/**
 * Block Extension: My Extension
 * Adds custom functionality to Group blocks
 */

// Safety: Check WordPress version compatibility
if (!class_exists('WP_HTML_Tag_Processor')) {
    return; // Requires WP 6.2+
}

/**
 * Modify block output on the frontend
 */
add_filter('render_block_core/group', 'my_extension_render_block', 10, 3);
function my_extension_render_block($block_content, $block, $instance) {
    // Safety: Skip if no content
    if (empty($block_content)) {
        return $block_content;
    }

    // Check if our custom attributes exist
    if (empty($block['attrs']['myCustomSettings'])) {
        return $block_content;
    }

    $settings = $block['attrs']['myCustomSettings'];

    // Validate settings
    if (empty($settings['enabled']) || $settings['enabled'] !== true) {
        return $block_content;
    }

    try {
        // Use WP_HTML_Tag_Processor to modify HTML safely
        $html = new WP_HTML_Tag_Processor($block_content);

        if ($html->next_tag()) {
            // Add custom class
            $html->add_class('has-my-extension');

            // Add data attribute
            if (!empty($settings['option'])) {
                $option = sanitize_text_field($settings['option']);
                $html->set_attribute('data-extension-option', $option);
            }

            // Add inline styles if needed
            if (!empty($settings['customValue'])) {
                $value = floatval($settings['customValue']);
                $existing_style = $html->get_attribute('style') ?? '';
                $new_style = "--custom-value: {$value}";
                $combined = $existing_style ? $existing_style . '; ' . $new_style : $new_style;
                $html->set_attribute('style', $combined);
            }

            return $html->get_updated_html();
        }
    } catch (Exception $e) {
        // Log error and return original content
        error_log('Extension Error: ' . $e->getMessage());
        return $block_content;
    }

    return $block_content;
}

/**
 * Enqueue editor JavaScript
 */
add_action('enqueue_block_editor_assets', 'my_extension_enqueue_editor_assets');
function my_extension_enqueue_editor_assets() {
    $script_path = get_template_directory() . '/src/extensions/my-extension/my-extension.js';

    if (!file_exists($script_path)) {
        return;
    }

    wp_enqueue_script(
        'my-extension-editor',
        get_template_directory_uri() . '/src/extensions/my-extension/my-extension.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n'),
        filemtime($script_path),
        true
    );

    // Pass PHP data to JavaScript
    wp_localize_script('my-extension-editor', 'myExtensionData', array(
        'options' => array(
            array('label' => __('Option 1', 'textdomain'), 'value' => 'option-1'),
            array('label' => __('Option 2', 'textdomain'), 'value' => 'option-2'),
        )
    ));
}

/**
 * Enqueue frontend CSS
 */
add_action('wp_enqueue_scripts', 'my_extension_enqueue_frontend_assets');
function my_extension_enqueue_frontend_assets() {
    $style_path = get_template_directory() . '/src/extensions/my-extension/my-extension.css';

    if (file_exists($style_path)) {
        wp_enqueue_style(
            'my-extension-frontend',
            get_template_directory_uri() . '/src/extensions/my-extension/my-extension.css',
            array(),
            filemtime($style_path)
        );
    }
}
```

---

### Step 3: JavaScript Editor Controls

**`src/extensions/my-extension/my-extension.js`**

```javascript
(function () {
  'use strict';

  // Safety: Check dependencies
  if (!window.wp || !wp.blocks || !wp.element || !wp.components) {
    console.warn('Extension: Required dependencies not found');
    return;
  }

  const { __ } = wp.i18n;
  const { addFilter } = wp.hooks;
  const { createHigherOrderComponent } = wp.compose;
  const { Fragment } = wp.element;
  const { InspectorControls } = wp.blockEditor;
  const { PanelBody, ToggleControl, SelectControl, RangeControl } = wp.components;

  // Get localized data
  const extensionData = window.myExtensionData || {};
  const options = extensionData.options || [];

  /**
   * Add custom attributes to target blocks
   */
  function addCustomAttributes(settings) {
    // Only extend specific block types
    if (settings.name !== 'core/group') {
      return settings;
    }

    return {
      ...settings,
      attributes: {
        ...settings.attributes,
        myCustomSettings: {
          type: 'object',
          default: {
            enabled: false,
            option: 'option-1',
            customValue: 50,
          },
        },
      },
    };
  }

  /**
   * Add inspector controls to block editor
   */
  const withCustomControls = createHigherOrderComponent(BlockEdit => {
    return props => {
      const { name, attributes, setAttributes } = props;

      // Only add controls to specific blocks
      if (name !== 'core/group') {
        return <BlockEdit {...props} />;
      }

      const { myCustomSettings = {} } = attributes;

      const updateSettings = (key, value) => {
        setAttributes({
          myCustomSettings: {
            ...myCustomSettings,
            [key]: value,
          },
        });
      };

      return (
        <Fragment>
          <BlockEdit {...props} />

          <InspectorControls>
            <PanelBody title={__('My Extension', 'textdomain')} initialOpen={false}>
              <ToggleControl
                label={__('Enable Extension', 'textdomain')}
                checked={myCustomSettings.enabled || false}
                onChange={value => updateSettings('enabled', value)}
                help={__('Turn on custom functionality', 'textdomain')}
              />

              {myCustomSettings.enabled && (
                <Fragment>
                  <SelectControl
                    label={__('Option', 'textdomain')}
                    value={myCustomSettings.option || 'option-1'}
                    options={options}
                    onChange={value => updateSettings('option', value)}
                  />

                  <RangeControl
                    label={__('Custom Value', 'textdomain')}
                    value={myCustomSettings.customValue || 50}
                    onChange={value => updateSettings('customValue', value)}
                    min={0}
                    max={100}
                    step={5}
                  />
                </Fragment>
              )}
            </PanelBody>
          </InspectorControls>
        </Fragment>
      );
    };
  }, 'withCustomControls');

  /**
   * Add classes to block wrapper for editor preview
   */
  const withCustomClasses = createHigherOrderComponent(BlockListBlock => {
    return props => {
      const { name, attributes } = props;

      if (name !== 'core/group' || !attributes.myCustomSettings?.enabled) {
        return <BlockListBlock {...props} />;
      }

      const { myCustomSettings } = attributes;

      // Build editor preview classes
      const className = [props.className || '', 'has-my-extension', 'editor-preview']
        .filter(Boolean)
        .join(' ');

      // Build editor preview styles
      const style = {
        ...props.style,
        '--custom-value': myCustomSettings.customValue || 50,
      };

      return <BlockListBlock {...props} className={className} style={style} />;
    };
  }, 'withCustomClasses');

  // Register filters
  addFilter('blocks.registerBlockType', 'my-theme/add-custom-attributes', addCustomAttributes);

  addFilter('editor.BlockEdit', 'my-theme/with-custom-controls', withCustomControls);

  addFilter('editor.BlockListBlock', 'my-theme/with-custom-classes', withCustomClasses);
})();
```

---

### Step 4: Frontend CSS (Optional)

**`src/extensions/my-extension/my-extension.css`**

```css
/* Extension Styles */
.has-my-extension {
  --custom-value: 50;
  position: relative;
}

.has-my-extension[data-extension-option='option-1'] {
  border: 2px solid rgba(0, 0, 0, calc(var(--custom-value) / 100));
}

.has-my-extension[data-extension-option='option-2'] {
  background: rgba(0, 0, 0, calc(var(--custom-value) / 100));
}

/* Editor-specific preview */
.wp-block-editor .has-my-extension.editor-preview {
  outline: 2px dashed var(--wp--preset--color--primary);
  outline-offset: 4px;
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  .has-my-extension {
    transition: none;
  }
}
```

---

## Complete Example

### Simple Universal Block Extension

Here's a practical example of adding a "highlighted" toggle to Universal/Element blocks.

#### Directory Structure

```
src/extensions/highlight-toggle/
├── highlight-toggle.php
├── highlight-toggle.js
└── highlight-toggle.css
```

---

#### PHP Backend

**`src/extensions/highlight-toggle/highlight-toggle.php`**

```php
<?php
/**
 * Highlight Toggle Extension
 * Adds a simple highlight toggle to universal/element blocks
 */

if (!class_exists('WP_HTML_Tag_Processor')) {
    return;
}

/**
 * Modify universal/element block output
 */
add_filter('render_block_universal/element', 'highlight_extension_render', 10, 3);
function highlight_extension_render($block_content, $block, $instance) {
    if (empty($block_content) || empty($block['attrs']['highlightSettings']['enabled'])) {
        return $block_content;
    }

    $settings = $block['attrs']['highlightSettings'];

    try {
        $html = new WP_HTML_Tag_Processor($block_content);

        if ($html->next_tag()) {
            // Add highlight class
            $html->add_class('is-highlighted');

            // Add color if specified
            if (!empty($settings['color'])) {
                $color = sanitize_hex_color($settings['color']);
                if ($color) {
                    $existing_style = $html->get_attribute('style') ?? '';
                    $new_style = "--highlight-color: {$color}";
                    $combined = $existing_style ? $existing_style . '; ' . $new_style : $new_style;
                    $html->set_attribute('style', $combined);
                }
            }

            return $html->get_updated_html();
        }
    } catch (Exception $e) {
        error_log('Highlight Extension: ' . $e->getMessage());
    }

    return $block_content;
}

/**
 * Enqueue editor assets
 */
add_action('enqueue_block_editor_assets', 'highlight_extension_enqueue_editor');
function highlight_extension_enqueue_editor() {
    $script = get_template_directory() . '/src/extensions/highlight-toggle/highlight-toggle.js';

    if (!file_exists($script)) {
        return;
    }

    wp_enqueue_script(
        'highlight-extension-editor',
        get_template_directory_uri() . '/src/extensions/highlight-toggle/highlight-toggle.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n'),
        filemtime($script)
    );
}

/**
 * Enqueue frontend styles
 */
add_action('wp_enqueue_scripts', 'highlight_extension_enqueue_frontend');
function highlight_extension_enqueue_frontend() {
    $style = get_template_directory() . '/src/extensions/highlight-toggle/highlight-toggle.css';

    if (file_exists($style)) {
        wp_enqueue_style(
            'highlight-extension',
            get_template_directory_uri() . '/src/extensions/highlight-toggle/highlight-toggle.css',
            array(),
            filemtime($style)
        );
    }
}
```

---

#### JavaScript Editor Controls

**`src/extensions/highlight-toggle/highlight-toggle.js`**

```javascript
(function () {
  'use strict';

  if (!window.wp) return;

  const { __ } = wp.i18n;
  const { addFilter } = wp.hooks;
  const { createHigherOrderComponent } = wp.compose;
  const { Fragment } = wp.element;
  const { InspectorControls } = wp.blockEditor;
  const { PanelBody, ToggleControl, ColorPicker } = wp.components;

  /**
   * Add highlight attributes to universal/element blocks
   */
  function addHighlightAttributes(settings) {
    if (settings.name !== 'universal/element') {
      return settings;
    }

    return {
      ...settings,
      attributes: {
        ...settings.attributes,
        highlightSettings: {
          type: 'object',
          default: {
            enabled: false,
            color: '#ffeb3b',
          },
        },
      },
    };
  }

  /**
   * Add highlight controls to inspector
   */
  const withHighlightControls = createHigherOrderComponent(BlockEdit => {
    return props => {
      const { name, attributes, setAttributes } = props;

      if (name !== 'universal/element') {
        return <BlockEdit {...props} />;
      }

      const { highlightSettings = {} } = attributes;

      return (
        <Fragment>
          <BlockEdit {...props} />

          <InspectorControls>
            <PanelBody title={__('Highlight', 'textdomain')} initialOpen={false}>
              <ToggleControl
                label={__('Enable Highlight', 'textdomain')}
                checked={highlightSettings.enabled || false}
                onChange={enabled => {
                  setAttributes({
                    highlightSettings: {
                      ...highlightSettings,
                      enabled,
                    },
                  });
                }}
              />

              {highlightSettings.enabled && (
                <div style={{ marginTop: '16px' }}>
                  <label style={{ display: 'block', marginBottom: '8px' }}>
                    {__('Highlight Color', 'textdomain')}
                  </label>
                  <ColorPicker
                    color={highlightSettings.color || '#ffeb3b'}
                    onChangeComplete={color => {
                      setAttributes({
                        highlightSettings: {
                          ...highlightSettings,
                          color: color.hex,
                        },
                      });
                    }}
                  />
                </div>
              )}
            </PanelBody>
          </InspectorControls>
        </Fragment>
      );
    };
  }, 'withHighlightControls');

  /**
   * Add preview classes in editor
   */
  const withHighlightPreview = createHigherOrderComponent(BlockListBlock => {
    return props => {
      const { name, attributes } = props;

      if (name !== 'universal/element' || !attributes.highlightSettings?.enabled) {
        return <BlockListBlock {...props} />;
      }

      const { highlightSettings } = attributes;

      const className = [props.className || '', 'is-highlighted', 'editor-preview']
        .filter(Boolean)
        .join(' ');

      const style = {
        ...props.style,
        '--highlight-color': highlightSettings.color || '#ffeb3b',
      };

      return <BlockListBlock {...props} className={className} style={style} />;
    };
  }, 'withHighlightPreview');

  // Register filters
  addFilter('blocks.registerBlockType', 'my-theme/highlight-attributes', addHighlightAttributes);

  addFilter('editor.BlockEdit', 'my-theme/highlight-controls', withHighlightControls);

  addFilter('editor.BlockListBlock', 'my-theme/highlight-preview', withHighlightPreview);
})();
```

---

#### Frontend Styles

**`src/extensions/highlight-toggle/highlight-toggle.css`**

```css
/* Highlight Extension Styles */
.is-highlighted {
  --highlight-color: #ffeb3b;
  position: relative;
  background: linear-gradient(
    90deg,
    transparent 0%,
    var(--highlight-color) 0%,
    var(--highlight-color) 100%,
    transparent 100%
  );
  background-size: 100% 30%;
  background-repeat: no-repeat;
  background-position: 0 75%;
}

/* Editor preview indicator */
.wp-block-editor .is-highlighted.editor-preview::before {
  content: '✨ Highlighted';
  position: absolute;
  top: -24px;
  left: 0;
  font-size: 11px;
  padding: 2px 8px;
  background: var(--highlight-color);
  color: #000;
  border-radius: 3px;
  font-weight: 600;
  z-index: 1;
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  .is-highlighted {
    transition: none;
  }
}
```

---

#### Usage

1. **Register the extension** in `src/extensions/register-extensions.php`:

   ```php
   require_once __DIR__ . '/highlight-toggle/highlight-toggle.php';
   ```

2. **In the WordPress Editor:**
   - Add a Universal/Element block (or any HTML element)
   - Open block settings sidebar
   - Find "Highlight" panel
   - Toggle "Enable Highlight"
   - Choose highlight color
   - Block now has a visual highlight effect

3. **Result:**
   - Adds `.is-highlighted` class to element
   - Applies CSS variable `--highlight-color`
   - Shows editor preview with label
   - Works on any Universal/Element block

This example demonstrates:

- ✅ Simple, focused functionality
- ✅ Clean attribute structure
- ✅ Editor preview feedback
- ✅ Accessible controls
- ✅ Minimal code footprint
- ✅ Works specifically with Universal blocks

---

## Best Practices

### 1. Safety & Validation

```php
// ✅ Always validate and sanitize
$value = sanitize_text_field($input);
$color = sanitize_hex_color($color_input);
$number = floatval($number_input);
$number = max(0, min(100, $number)); // Clamp range

// ✅ Use try/catch for HTML processing
try {
    $html = new WP_HTML_Tag_Processor($content);
    // Process...
} catch (Exception $e) {
    error_log($e->getMessage());
    return $original_content;
}

// ✅ Check dependencies
if (!class_exists('WP_HTML_Tag_Processor')) {
    return; // Graceful degradation
}
```

### 2. Performance

```php
// ✅ Check file existence before enqueuing
if (file_exists($script_path)) {
    wp_enqueue_script(...);
}

// ✅ Use filemtime() for cache busting
wp_enqueue_script(
    'handle',
    $url,
    $deps,
    filemtime($path) // Auto-updates on file change
);

// ❌ Don't process if not needed
if (empty($block['attrs']['mySettings'])) {
    return $block_content; // Early return
}
```

### 3. Code Organization

```php
// ✅ Group related functionality
class MyExtension {
    public function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_filter('render_block_core/group', [$this, 'render'], 10, 3);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue']);
    }

    public function render($content, $block, $instance) {
        // Implementation
    }

    public function enqueue() {
        // Implementation
    }
}

new MyExtension();
```

### 4. Naming Conventions

```php
// Files: kebab-case
pattern-backgrounds.php
position-controls.js

// Functions: snake_case with prefix
function mytheme_extension_render() {}

// CSS Classes: kebab-case with prefix
.mytheme-has-pattern
.mytheme-pattern-dots

// JS Variables: camelCase
const myExtensionSettings = {};
```

### 5. Accessibility

```javascript
// ✅ Provide descriptive labels
<ToggleControl
    label={__('Enable Pattern', 'textdomain')}
    help={__('Adds a decorative background pattern', 'textdomain')}
/>

// ✅ Support high contrast mode
@media (prefers-contrast: high) {
    .has-extension {
        border-color: currentColor;
    }
}

// ✅ Respect reduced motion
@media (prefers-reduced-motion: reduce) {
    .has-extension {
        animation: none;
    }
}
```

---

## Troubleshooting

### Extension Not Loading

**Check:**

1. File is required in `register-extensions.php`
2. File path is correct
3. No PHP syntax errors: `php -l file.php`
4. Check error logs: `tail -f wp-content/debug.log`

### Controls Not Appearing

**Check:**

1. JavaScript dependencies are correct
2. Browser console for errors
3. Block name matches exactly: `'core/group'`
4. Script is enqueued in editor: `enqueue_block_editor_assets`

### Styles Not Applying

**Check:**

1. CSS file exists and is enqueued
2. Classes are being added (inspect element)
3. CSS specificity is high enough
4. Cache cleared (Ctrl+Shift+R)

### Attributes Not Saving

**Check:**

1. Attribute schema is correct in JS
2. Default values are set
3. `setAttributes()` is called properly
4. No JavaScript errors in console

### Debugging

```php
// PHP Debug
add_action('wp_footer', function() {
    if (WP_DEBUG) {
        echo '<!-- Extension loaded -->';
    }
});

// Log block attributes
error_log(print_r($block['attrs'], true));
```

```javascript
// JS Debug
console.log('Extension settings:', myCustomSettings);
console.log('Block attributes:', attributes);
```

---

## Advanced Techniques

### Multiple Block Types

```javascript
// Extend multiple blocks
const SUPPORTED_BLOCKS = ['core/group', 'core/column', 'core/cover'];

function addCustomAttributes(settings) {
  if (!SUPPORTED_BLOCKS.includes(settings.name)) {
    return settings;
  }

  // Add attributes...
}
```

### Conditional Controls

```javascript
// Show controls based on other settings
{
  mySettings.type === 'advanced' && (
    <RangeControl
      label={__('Advanced Option', 'textdomain')}
      value={mySettings.advancedValue}
      onChange={value => updateSettings('advancedValue', value)}
    />
  );
}
```

### Dynamic PHP Data

```php
// Pass dynamic data to JavaScript
wp_localize_script('my-extension', 'myData', array(
    'categories' => get_categories(),
    'users' => get_users(),
    'options' => get_option('my_theme_options')
));
```

### Custom Hooks

```php
// Allow other plugins to modify
$settings = apply_filters('mytheme_extension_settings', $default_settings);

// Trigger actions
do_action('mytheme_before_extension_render', $block, $attributes);
```

---

## Summary

**Block Extensions let you:**

- ✅ Enhance existing WordPress blocks
- ✅ Add custom controls without complexity
- ✅ Maintain clean, organized code
- ✅ Create reusable design systems
- ✅ Extend functionality across block types

**Key Components:**

1. **PHP** - Process attributes, modify output
2. **JavaScript** - Add editor controls
3. **CSS** - Style the functionality

**Location:** `src/extensions/{extension-name}/`

**Best Practices:**

- Validate all inputs
- Handle errors gracefully
- Check dependencies
- Use semantic naming
- Document thoroughly

---

**Version:** 1.0.0
**Last Updated:** 2025-01-26
