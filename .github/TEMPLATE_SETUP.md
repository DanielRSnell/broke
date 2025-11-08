# Template Setup Guide

This document explains how to use Broke FSE as a starting point for your own WordPress theme.

## Quick Start (Recommended)

### Option 1: Use GitHub Template Feature

1. Click **"Use this template"** button on GitHub
2. Create your new repository
3. Clone your new repository:
   ```bash
   git clone https://github.com/yourusername/your-theme-name.git
   cd your-theme-name
   ```
4. Run the setup script:
   ```bash
   ./setup.sh
   ```
5. Follow the prompts to customize your theme

### Option 2: Use Degit (No Git History)

```bash
# Install degit globally (first time only)
npm install -g degit

# Clone without git history
npx degit DanielRSnell/broke-fse my-theme-name
cd my-theme-name

# Run setup script
./setup.sh
```

### Option 3: Manual Clone and Setup

```bash
# Clone the repository
git clone https://github.com/DanielRSnell/broke-fse.git my-theme-name
cd my-theme-name

# Run setup script
./setup.sh
```

---

## What the Setup Script Does

The `setup.sh` script automates the following:

1. ✅ Removes boilerplate git history
2. ✅ Updates theme metadata in:
   - `style.css` (theme name, author, description, text domain)
   - `package.json` (name, author, description)
   - `composer.json` (name, description)
   - `theme.json` (text domain)
   - `README.md` (all references to broke.dev and GitHub links)
3. ✅ Initializes new git repository
4. ✅ Creates initial commit
5. ✅ Optionally adds your remote repository

---

## Manual Setup (Without Script)

If you prefer manual setup:

### 1. Remove Git History

```bash
rm -rf .git
git init
```

### 2. Update Theme Files

**style.css:**
```css
Theme Name: Your Theme Name
Theme URI: https://yoursite.com
Author: Your Name
Author URI: https://yoursite.com
Description: Your theme description
Text Domain: your-theme-slug
```

**package.json:**
```json
{
  "name": "your-theme-slug",
  "author": "Your Name <you@example.com>",
  "description": "Your theme description"
}
```

**composer.json:**
```json
{
  "name": "yourname/your-theme-slug",
  "description": "Your theme description"
}
```

**README.md:**
- Update all references to `broke.dev`
- Update GitHub links to your repository
- Update author information

### 3. Initialize Git

```bash
git add .
git commit -m "Initial commit: Your Theme Name"
git remote add origin YOUR_REMOTE_URL
git push -u origin main
```

---

## Post-Setup Steps

After running setup (automatic or manual):

### 1. Install Dependencies

```bash
# PHP dependencies (Timber, ACF, etc.)
composer install

# Node dependencies (Tailwind, Vite, etc.)
pnpm install
```

### 2. Build Assets

```bash
# Build CSS (Tailwind)
pnpm run build:css

# Build JavaScript
pnpm run build:js

# Or build everything
pnpm run build
```

### 3. Activate Theme

1. Go to WordPress admin → **Appearance → Themes**
2. Find your theme name
3. Click **Activate**

### 4. Start Development

```bash
# Watch CSS changes
pnpm run watch:css

# Watch JS changes
pnpm run watch:js

# Or run both in separate terminals
```

---

## Customizing Your Theme

### Update Color Scheme

Edit `theme.json`:
```json
{
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "primary",
          "color": "#your-color",
          "name": "Primary"
        }
      ]
    }
  }
}
```

Then reference in `src/styles/theme/colors.css`:
```css
@theme {
  --color-primary: var(--wp--preset--color--primary);
}
```

### Add Custom Components

1. Create component file:
   ```bash
   touch src/styles/components/my-component.css
   ```

2. Define styles:
   ```css
   @layer components {
     .my-component {
       /* Your styles using CSS custom properties */
     }
   }
   ```

3. Import in `src/styles/tailwind.css`:
   ```css
   @import './components/my-component.css' layer(components);
   ```

4. Rebuild:
   ```bash
   pnpm run build:css
   ```

### Create Custom Post Types

See [acf-json/README.md](../acf-json/README.md) for detailed instructions on:
- Creating custom post types via ACF UI
- Adding custom field groups
- Creating taxonomies
- Setting up option pages

---

## Development Workflow

### Recommended Git Workflow

```bash
# Create feature branch
git checkout -b feature/my-feature

# Make changes, commit frequently
git add .
git commit -m "Add my feature"

# Push to remote
git push origin feature/my-feature

# Create pull request on GitHub
```

### Content Management

**Edit in WordPress → Pull to Files:**
```bash
pnpm run content:pull    # Pull posts/resources/projects
pnpm run page:pull       # Pull pages
```

**Edit in Files → Push to WordPress:**
```bash
pnpm run content:push    # Push markdown content
pnpm run page:push       # Push HTML sections
```

### Building for Production

```bash
# Build optimized assets
pnpm run build:css
pnpm run build:js

# Commit built files
git add _production/
git commit -m "Build production assets"

# Deploy to production
# (Upload theme directory to wp-content/themes/)
```

---

## Removing Boilerplate Examples

The theme includes example patterns and content. To clean up:

### Remove Example Patterns

```bash
# Remove example HTML files
rm src/pages/home.html
rm src/patterns/*.html

# Remove generated patterns
rm patterns/*.php

# Create your own
touch src/pages/my-page.html
pnpm run parse:all
```

### Remove Example Content

```bash
# Remove example markdown files
rm src/content/posts/*.md
rm src/content/resources/*.md
rm src/content/projects/*.md

# Remove example pages
rm -rf src/content/pages/*
```

### Clean ACF JSON

```bash
# Remove example field groups
rm acf-json/*.json

# Create your own in WordPress admin (ACF → Field Groups)
# They'll auto-export to acf-json/
```

---

## Troubleshooting

### Setup Script Not Working

**macOS/Linux:**
```bash
chmod +x setup.sh
./setup.sh
```

**Windows (Git Bash):**
```bash
bash setup.sh
```

### Build Errors

```bash
# Clean and rebuild
pnpm run clean:all
pnpm install
pnpm run build
```

### Theme Not Showing

1. Check `style.css` has proper header
2. Verify `index.php` exists (required by WordPress)
3. Check file permissions (755 for directories, 644 for files)

---

## Support

- **Documentation:** [README.md](../README.md) and subdirectory READMEs
- **Original Boilerplate:** [broke.dev](https://broke.dev)
- **Issues:** Report issues with the boilerplate at [GitHub Issues](https://github.com/DanielRSnell/broke-fse/issues)

---

## License

Your theme inherits the GPL-2.0-or-later license from the boilerplate. You can:

- ✅ Use commercially
- ✅ Modify and distribute
- ✅ Use privately
- ✅ Sell themes built with this

Requirements:
- Keep GPL license
- State changes made
- Include original copyright notice

---

**Happy theme building! 🚀**
