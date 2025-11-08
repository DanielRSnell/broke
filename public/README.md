# Public Assets Directory

## Overview

This directory stores **static theme assets**—images, icons, fonts, and other files served directly to the frontend.

Think of this as **your theme's static file server**—assets here are publicly accessible via URL.

---

## Philosophy

The public directory provides:

1. **Direct access** - Files served without PHP processing
2. **Organized structure** - Separate folders for different asset types
3. **Version control** - Track assets alongside code
4. **CDN-friendly** - Easy to move to CDN later
5. **Build output** - Some assets may be generated

### Core Principle

**"Static assets should be organized, optimized, and version-controlled with your theme."**

---

## Recommended Directory Structure

```
public/
├── README.md           # This file
├── images/             # General images (logos, backgrounds, etc.)
│   ├── logo.svg
│   ├── logo-dark.svg
│   ├── hero-bg.jpg
│   └── placeholder.png
├── icons/              # Icon files (favicons, app icons)
│   ├── favicon.ico
│   ├── icon-192.png
│   ├── icon-512.png
│   ├── apple-touch-icon.png
│   └── site.webmanifest
├── fonts/              # Custom web fonts (if not using CDN)
│   ├── custom-font.woff2
│   └── custom-font.woff
├── documents/          # Downloadable files (PDFs, etc.)
│   ├── brochure.pdf
│   └── press-kit.zip
└── generated/          # Build output (optimized images, etc.)
    └── sprites/
        └── icons.svg
```

---

## Asset Categories

### Images (`images/`)

**Purpose:** General theme images not managed through WordPress media library

**Common files:**
- `logo.svg` - Site logo
- `logo-dark.svg` - Dark mode variant
- `hero-bg.jpg` - Hero section backgrounds
- `placeholder.png` - Loading placeholders
- `avatar-default.png` - Default user avatar

**Best practices:**
- Use SVG for logos and icons (scalable, small file size)
- Optimize JPG/PNG images before committing
- Use WebP format for modern browsers
- Provide retina (@2x) versions for important images
- Name files descriptively: `hero-homepage-bg.jpg` not `img1.jpg`

**Example usage in templates:**
```php
<!-- Using theme URL -->
<img src="<?php echo get_template_directory_uri(); ?>/public/images/logo.svg" alt="Site Logo">

<!-- Using Timber -->
<img src="{{ theme.link }}/public/images/logo.svg" alt="Site Logo">
```

---

### Icons (`icons/`)

**Purpose:** Favicons, app icons, and PWA assets

**Required files for modern sites:**
```
icons/
├── favicon.ico          # 32x32 legacy favicon
├── icon-192.png         # Android icon
├── icon-512.png         # Android splash screen
├── apple-touch-icon.png # iOS home screen (180x180)
└── site.webmanifest     # PWA manifest
```

**Example `site.webmanifest`:**
```json
{
  "name": "Your Theme Name",
  "short_name": "Theme",
  "icons": [
    {
      "src": "/wp-content/themes/your-theme/public/icons/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/wp-content/themes/your-theme/public/icons/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ],
  "theme_color": "#ffffff",
  "background_color": "#ffffff",
  "display": "standalone"
}
```

**Add to `<head>` in your theme:**
```php
<!-- functions.php or header template -->
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/public/icons/favicon.ico">
<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/public/icons/apple-touch-icon.png">
<link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/public/icons/site.webmanifest">
```

---

### Fonts (`fonts/`)

**Purpose:** Custom web fonts not loaded from CDN

**When to use:**
- Self-hosting fonts for privacy (GDPR compliance)
- Custom brand fonts not available on CDN
- Offline-first PWA requirements
- Performance optimization (single origin)

**Font formats:**
- **WOFF2** - Modern, best compression (required)
- **WOFF** - Fallback for older browsers (optional)
- TTF/OTF - Not recommended (large file size)

**Example structure:**
```
fonts/
├── inter/
│   ├── inter-regular.woff2
│   ├── inter-bold.woff2
│   └── inter-italic.woff2
└── custom-font/
    ├── custom-font-regular.woff2
    └── custom-font-bold.woff2
```

**Loading fonts in CSS:**
```css
/* src/styles/theme/fonts.css */
@font-face {
  font-family: 'Inter';
  src: url('/wp-content/themes/your-theme/public/fonts/inter/inter-regular.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}

@font-face {
  font-family: 'Inter';
  src: url('/wp-content/themes/your-theme/public/fonts/inter/inter-bold.woff2') format('woff2');
  font-weight: 700;
  font-style: normal;
  font-display: swap;
}
```

**Using in Tailwind:**
```css
/* tailwind.css */
@theme {
  --font-sans: 'Inter', system-ui, sans-serif;
}
```

---

### Documents (`documents/`)

**Purpose:** Downloadable files (PDFs, press kits, brochures)

**Common files:**
- `brochure.pdf` - Company brochure
- `press-kit.zip` - Media assets
- `whitepaper.pdf` - Technical documents
- `terms.pdf` - Legal documents

**Best practices:**
- Keep file names lowercase with hyphens: `privacy-policy.pdf`
- Compress PDFs before uploading
- Provide multiple formats when possible (PDF + DOCX)
- Version files: `annual-report-2024.pdf`

**Example usage:**
```html
<a href="<?php echo get_template_directory_uri(); ?>/public/documents/brochure.pdf" download>
  Download Brochure
</a>
```

---

### Generated Assets (`generated/`)

**Purpose:** Build output from asset pipelines

**Common uses:**
- SVG sprite sheets
- Optimized/compressed images
- Generated favicons
- Minified assets (if not in `_production/`)

**Example: SVG sprite sheet**
```
generated/
└── sprites/
    └── icons.svg
```

**Usage:**
```html
<!-- Reference sprites -->
<svg><use href="<?php echo get_template_directory_uri(); ?>/public/generated/sprites/icons.svg#icon-arrow"></use></svg>
```

---

## Asset Optimization

### Image Optimization

**Before committing images:**

1. **Resize:** Don't commit 5000px images if max display is 1200px
2. **Compress:** Use tools like:
   - [TinyPNG](https://tinypng.com/) - PNG/JPG compression
   - [Squoosh](https://squoosh.app/) - Google's image optimizer
   - [ImageOptim](https://imageoptim.com/) - Mac app
3. **Format:** Choose right format:
   - **SVG** - Logos, icons, simple graphics
   - **WebP** - Photos with transparency
   - **JPG** - Photos without transparency
   - **PNG** - Screenshots, UI elements

### Automated Optimization

Add build script to `package.json`:

```json
{
  "scripts": {
    "optimize:images": "imageoptim public/images/**/*.{jpg,png}"
  }
}
```

**Using imagemin:**
```bash
pnpm add -D imagemin imagemin-webp imagemin-svgo

# Add script
"optimize:images": "imagemin public/images/*.{jpg,png} --out-dir=public/images --plugin=webp --plugin=svgo"
```

---

## Referencing Assets in Templates

### WordPress Functions

```php
<!-- Theme directory URL -->
<?php echo get_template_directory_uri(); ?>/public/images/logo.svg

<!-- Shorthand -->
<?php echo esc_url(get_template_directory_uri() . '/public/images/logo.svg'); ?>
```

### Timber/Twig

```twig
<!-- Theme link -->
<img src="{{ theme.link }}/public/images/logo.svg">

<!-- Set in context filter -->
{# src/context/theme-assets.php #}
<?php
add_filter('timber/context', function($context) {
    $context['logo'] = get_template_directory_uri() . '/public/images/logo.svg';
    return $context;
});
?>

<!-- In template -->
<img src="{{ logo }}">
```

### CSS (Tailwind/SCSS)

```css
/* Relative to theme root */
background-image: url('/wp-content/themes/your-theme/public/images/hero-bg.jpg');

/* Better: Use CSS variable */
:root {
  --theme-url: '/wp-content/themes/your-theme';
}

.hero {
  background-image: url(var(--theme-url)/public/images/hero-bg.jpg);
}
```

---

## CDN Integration

To move assets to CDN later:

### Option 1: Define constant

```php
// wp-config.php
define('THEME_ASSETS_URL', 'https://cdn.example.com/themes/your-theme');

// functions.php
function get_theme_asset($path) {
    if (defined('THEME_ASSETS_URL')) {
        return THEME_ASSETS_URL . '/' . ltrim($path, '/');
    }
    return get_template_directory_uri() . '/' . ltrim($path, '/');
}

// Usage
<img src="<?php echo get_theme_asset('public/images/logo.svg'); ?>">
```

### Option 2: Use plugin

Install CDN plugin that rewrites asset URLs automatically.

---

## WordPress Media Library vs Public Directory

### Use WordPress Media Library For:
- ✅ Content images (blog posts, pages)
- ✅ User-uploaded images
- ✅ Images managed by editors
- ✅ Images with metadata (alt text, captions)
- ✅ Images that change frequently

### Use Public Directory For:
- ✅ Theme assets (logos, icons)
- ✅ Structural images (backgrounds, patterns)
- ✅ Build artifacts (sprites, optimized assets)
- ✅ Fonts and documents
- ✅ Assets version-controlled with code

**Rule of thumb:** If it's **part of the design**, use `public/`. If it's **part of the content**, use Media Library.

---

## Security Considerations

**DO:**
- ✅ Only store publicly accessible files here
- ✅ Set proper permissions (644 for files, 755 for directories)
- ✅ Sanitize file uploads if allowing user uploads
- ✅ Use `.htaccess` to prevent directory listing

**DON'T:**
- ❌ Store sensitive files (config, keys, credentials)
- ❌ Store PHP files (could be executed)
- ❌ Store database backups
- ❌ Store user data

**Example `.htaccess`:**
```apache
# public/.htaccess
Options -Indexes
<FilesMatch "\.(jpg|jpeg|png|gif|svg|webp|woff|woff2|ico|pdf|zip)$">
  Allow from all
</FilesMatch>
```

---

## Performance Best Practices

1. **Lazy load images:** Use `loading="lazy"` attribute
2. **Responsive images:** Use `<picture>` or `srcset`
3. **Cache headers:** Set long cache times for static assets
4. **Compress files:** Gzip/Brotli compression
5. **Use CDN:** Offload assets to CDN for global delivery

**Example responsive image:**
```html
<picture>
  <source srcset="<?php echo get_template_directory_uri(); ?>/public/images/hero.webp" type="image/webp">
  <source srcset="<?php echo get_template_directory_uri(); ?>/public/images/hero.jpg" type="image/jpeg">
  <img src="<?php echo get_template_directory_uri(); ?>/public/images/hero.jpg" alt="Hero" loading="lazy">
</picture>
```

---

## Troubleshooting

### Assets Not Loading

**Check:**
1. File path is correct (absolute or relative to theme root)
2. File permissions (644 for files, 755 for directories)
3. `.htaccess` not blocking file types
4. Browser cache (hard refresh with Cmd+Shift+R / Ctrl+Shift+R)

### 404 Errors

**Common causes:**
- Incorrect theme directory path
- File moved/renamed but references not updated
- Server doesn't allow direct access to directory

**Debug:**
```php
// Check theme URL
<?php echo get_template_directory_uri(); ?>
// Should output: https://yoursite.com/wp-content/themes/your-theme

// Check file exists
<?php var_dump(file_exists(get_template_directory() . '/public/images/logo.svg')); ?>
```

---

## Workflow for Adding Assets

1. **Create/optimize asset** locally
2. **Place in appropriate directory:**
   - Images → `public/images/`
   - Icons → `public/icons/`
   - Fonts → `public/fonts/`
   - Documents → `public/documents/`
3. **Reference in template/CSS:**
   ```php
   <?php echo get_template_directory_uri(); ?>/public/images/filename.ext
   ```
4. **Commit to Git:**
   ```bash
   git add public/images/logo.svg
   git commit -m "Add site logo"
   ```
5. **Deploy** - assets deploy with code

---

**Summary:**

This directory stores static theme assets—images, icons, fonts, documents. Organize by type, optimize before committing, reference via `get_template_directory_uri()`. Use for theme assets (not content images). CDN-friendly structure for performance.

**Structure:**
```
public/
├── images/      # General images
├── icons/       # Favicons, app icons
├── fonts/       # Self-hosted fonts
├── documents/   # PDFs, downloads
└── generated/   # Build output
```

**For boilerplate:**
- Directory starts empty with this README
- Add assets as your project needs them
- Maintain organized structure

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
