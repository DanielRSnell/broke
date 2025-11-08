# HTML to Pattern CLI

Convert HTML files to WordPress block patterns with a simple command-line tool.

## Features

- ✅ Convert HTML to WordPress PHP pattern files
- ✅ Support for single files or entire directories
- ✅ Automatic pattern metadata generation
- ✅ Preserve all HTML attributes and structure
- ✅ Support for Twig/dynamic attributes
- ✅ Customizable pattern headers

## Installation

### Local Development

```bash
# Install dependencies
npm install

# Link globally for local testing
npm link
```

### Global Installation (when published)

```bash
npm install -g @universal-blocks/html2pattern
```

## Usage

### Basic Usage

```bash
# Convert a single HTML file
html2pattern hero.html

# Convert a directory of HTML files
html2pattern ./html-files

# Specify output directory
html2pattern ./html-files -o ./patterns
```

### With Pattern Metadata

```bash
# Add namespace
html2pattern ./html-files --namespace=mytheme

# Add categories
html2pattern hero.html --categories="featured,hero,pages"

# Add keywords
html2pattern hero.html --keywords="hero,banner,header"

# Add description
html2pattern hero.html --description="Beautiful hero section with gradient background"

# Set viewport width
html2pattern hero.html --viewport-width=1600
```

### Convert Entire Directory

```bash
# Convert all HTML files in a directory
html2pattern ./src/pages -o ./patterns --namespace=mytheme --categories="pages"

# Use custom glob pattern
html2pattern ./templates -p "sections/*.html" -o ./patterns
```

## Command Options

| Option | Description | Default | Example |
|--------|-------------|---------|---------|
| `-o, --output <path>` | Output directory | `./patterns` | `-o ./my-patterns` |
| `-p, --pattern <pattern>` | Glob pattern for HTML files | `**/*.html` | `-p "sections/*.html"` |
| `--namespace <namespace>` | Pattern namespace/prefix | none | `--namespace=mytheme` |
| `--categories <categories>` | Comma-separated categories | none | `--categories="hero,featured"` |
| `--keywords <keywords>` | Comma-separated keywords | none | `--keywords="hero,banner"` |
| `--description <description>` | Pattern description | none | `--description="Hero section"` |
| `--viewport-width <width>` | Viewport width for preview | `1280` | `--viewport-width=1600` |

## Examples

### Example 1: Single File

**Input** (`hero.html`):
```html
<section class="hero bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
  <div class="container mx-auto">
    <h1 class="text-5xl font-bold">Welcome</h1>
    <p class="text-xl mt-4">Your amazing tagline here</p>
  </div>
</section>
```

**Command:**
```bash
html2pattern hero.html --namespace=mytheme --categories="hero,featured"
```

**Output** (`patterns/hero.php`):
```php
<?php
/**
 * Title: Hero
 * Slug: mytheme/hero
 * Description:
 * Categories: hero, featured
 * Keywords:
 * Viewport Width: 1280
 * Block Types:
 * Post Types:
 * Inserter: true
 */
?>
<!-- wp:universal/element {"tagName":"section",...} -->
  <!-- wp:universal/element {"tagName":"div",...} -->
    <!-- wp:universal/element {"tagName":"h1",...} /-->
    <!-- wp:universal/element {"tagName":"p",...} /-->
  <!-- /wp:universal/element -->
<!-- /wp:universal/element -->
```

### Example 2: Directory with Twig

**Input** (`team.html`):
```html
<div class="grid grid-cols-3 gap-6" loopSource="post.meta('team_members')" loopVariable="member">
  <div class="team-card">
    <h3>{{ member.name }}</h3>
    <p>{{ member.role }}</p>
  </div>
</div>
```

**Command:**
```bash
html2pattern ./templates --namespace=mytheme --categories="dynamic,team"
```

The Twig attributes (`loopSource`, `loopVariable`) are preserved in the pattern!

### Example 3: Batch Conversion

```bash
# Convert all page templates
html2pattern ./src/pages -o ./patterns \
  --namespace=mytheme \
  --categories="pages" \
  --viewport-width=1600
```

## Pattern Registration

Patterns are automatically registered by WordPress when placed in the theme's `patterns/` directory (WordPress 6.0+).

**Theme structure:**
```
mytheme/
├── patterns/
│   ├── hero.php          ← Registered as "mytheme/hero"
│   ├── team.php          ← Registered as "mytheme/team"
│   └── cta.php           ← Registered as "mytheme/cta"
├── style.css
└── functions.php
```

No additional code needed!

## Supported Features

- ✅ All HTML5 elements
- ✅ Custom elements
- ✅ All HTML attributes (id, class, data-*, aria-*, etc.)
- ✅ Twig control attributes (loopSource, conditionalExpression, etc.)
- ✅ Nested structures
- ✅ SVG elements
- ✅ Self-closing tags
- ✅ Alpine.js attributes (x-data, x-bind, etc.)

### Style Attribute Handling

The `style` attribute is automatically converted to `data-style` to prevent Gutenberg preview issues.

```html
<!-- Input HTML -->
<div style="background: red;">Content</div>

<!-- Converted to -->
<!-- wp:universal/element {"globalAttrs":{"data-style":"background: red;"},...} -->
```

## Development Workflow

1. **Design in HTML** - Create templates using HTML with Tailwind CSS
2. **Convert to patterns** - Use this CLI to generate PHP pattern files
3. **Copy to theme** - Move files to `theme/patterns/` directory
4. **Test in WordPress** - Insert patterns in the block editor
5. **Iterate** - Update HTML and reconvert as needed

```bash
# Development cycle
html2pattern ./src/templates -o ./build/patterns --namespace=mytheme
cp -r ./build/patterns/* ~/Sites/mytheme/patterns/
```

## NPM Scripts

Add to your theme's `package.json`:

```json
{
  "scripts": {
    "patterns:convert": "html2pattern ./src/templates -o ./patterns --namespace=mytheme",
    "patterns:pages": "html2pattern ./src/pages -o ./patterns --namespace=mytheme --categories=pages",
    "patterns:sections": "html2pattern ./src/sections -o ./patterns --namespace=mytheme --categories=sections"
  }
}
```

Then run:
```bash
npm run patterns:convert
```

## Troubleshooting

### "Command not found"

Make sure the package is linked:
```bash
npm link
```

### Patterns not appearing in WordPress

1. Check the `patterns/` directory is in your active theme
2. Ensure PHP files have valid pattern headers
3. Verify WordPress version (6.0+ required)

## API Usage

You can also use the library programmatically:

```javascript
const { convertHTMLToPattern } = require('@universal-blocks/html2pattern');

const html = '<div class="hero"><h1>Hello</h1></div>';
const pattern = convertHTMLToPattern(html, 'hero', {
  namespace: 'mytheme',
  categories: ['hero', 'featured'],
  description: 'Hero section'
});

console.log(pattern);
```

## License

GPL-2.0-or-later

## Contributing

This is part of the Universal Blocks project. Contributions welcome!
