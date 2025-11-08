#!/bin/bash

# Broke FSE Theme Setup Script
# This script initializes a new theme from the boilerplate

set -e

echo ""
echo "🚀 Broke FSE Theme Setup"
echo "========================"
echo ""
echo "This script will:"
echo "  1. Remove the boilerplate git history"
echo "  2. Update theme metadata (name, author, URIs)"
echo "  3. Initialize a new git repository"
echo "  4. Optionally add your remote repository"
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Setup cancelled."
    exit 1
fi

echo ""
echo "📝 Theme Information"
echo "===================="
echo ""

# Prompt for theme details
read -p "Theme Name (e.g., My Awesome Theme): " THEME_NAME
read -p "Theme Slug (e.g., my-awesome-theme): " THEME_SLUG
read -p "Theme URI (e.g., https://mysite.com): " THEME_URI
read -p "Author Name: " AUTHOR_NAME
read -p "Author Email: " AUTHOR_EMAIL
read -p "Author URI (e.g., https://mysite.com): " AUTHOR_URI
read -p "Theme Description: " THEME_DESC

# Validate required fields
if [ -z "$THEME_NAME" ] || [ -z "$THEME_SLUG" ] || [ -z "$AUTHOR_NAME" ]; then
    echo "❌ Error: Theme Name, Theme Slug, and Author Name are required."
    exit 1
fi

# Set defaults for optional fields
THEME_URI=${THEME_URI:-"https://github.com/yourusername/$THEME_SLUG"}
AUTHOR_EMAIL=${AUTHOR_EMAIL:-"you@example.com"}
AUTHOR_URI=${AUTHOR_URI:-$THEME_URI}
THEME_DESC=${THEME_DESC:-"A custom WordPress block theme built with Tailwind CSS v4, Universal Block, and Timber."}

echo ""
echo "🗑️  Removing boilerplate git history..."
rm -rf .git

echo "✓ Git history removed"

echo ""
echo "📝 Updating theme files..."

# Update style.css
if [ -f "style.css" ]; then
    sed -i '' "s/Theme Name: Broke FSE/Theme Name: $THEME_NAME/g" style.css
    sed -i '' "s|Theme URI: https://broke.dev|Theme URI: $THEME_URI|g" style.css
    sed -i '' "s/Author: Daniel Snell/Author: $AUTHOR_NAME/g" style.css
    sed -i '' "s|Author URI: https://broke.dev|Author URI: $AUTHOR_URI|g" style.css
    sed -i '' "s/daniel@broke.dev/$AUTHOR_EMAIL/g" style.css
    sed -i '' "s/Text Domain: broke-fse/Text Domain: $THEME_SLUG/g" style.css
    sed -i '' "s/Description: A modern WordPress block theme boilerplate built with Tailwind CSS v4, Universal Block, and Timber. Features bidirectional content sync, html2pattern CLI, clean MVC architecture, and zero JavaScript dependencies by default./Description: $THEME_DESC/g" style.css
    echo "✓ Updated style.css"
fi

# Update package.json
if [ -f "package.json" ]; then
    sed -i '' "s/\"name\": \"broke-theme\"/\"name\": \"$THEME_SLUG\"/g" package.json
    sed -i '' "s/\"author\": \"Daniel Snell <daniel@broke.dev>\"/\"author\": \"$AUTHOR_NAME <$AUTHOR_EMAIL>\"/g" package.json
    sed -i '' "s/\"description\": \"A modern WordPress block theme boilerplate built with Tailwind CSS v4, Universal Block, and Timber. Features bidirectional content sync, html2pattern CLI, clean MVC architecture, and zero JavaScript dependencies by default.\"/\"description\": \"$THEME_DESC\"/g" package.json
    echo "✓ Updated package.json"
fi

# Update composer.json
if [ -f "composer.json" ]; then
    sed -i '' "s/\"name\": \"broke\/fse-theme\"/\"name\": \"$(echo $AUTHOR_NAME | tr '[:upper:]' '[:lower:]' | tr ' ' '-')\/$THEME_SLUG\"/g" composer.json
    sed -i '' "s/\"description\": \"A modern WordPress block theme boilerplate\"/\"description\": \"$THEME_DESC\"/g" composer.json
    echo "✓ Updated composer.json"
fi

# Update theme.json (text domain)
if [ -f "theme.json" ]; then
    # Note: theme.json might not have a textDomain field by default, but we'll try
    if grep -q "broke-fse" theme.json; then
        sed -i '' "s/broke-fse/$THEME_SLUG/g" theme.json
        echo "✓ Updated theme.json"
    fi
fi

# Update README.md
if [ -f "README.md" ]; then
    sed -i '' "s/# Broke FSE/# $THEME_NAME/g" README.md
    sed -i '' "s|Website:\*\* \[broke.dev\](https://broke.dev)|Website:** [$THEME_URI]($THEME_URI)|g" README.md
    sed -i '' "s/Author:\*\* Daniel Snell/Author:** $AUTHOR_NAME/g" README.md
    sed -i '' "s|Contact:\*\* \[daniel@broke.dev\](mailto:daniel@broke.dev)|Contact:** [$AUTHOR_EMAIL](mailto:$AUTHOR_EMAIL)|g" README.md
    sed -i '' "s|git clone https://github.com/DanielRSnell/broke-fse.git|git clone $THEME_URI.git|g" README.md
    sed -i '' "s|Download ZIP from \[GitHub\](https://github.com/DanielRSnell/broke-fse)|Download ZIP from [GitHub]($THEME_URI)|g" README.md
    sed -i '' "s|Issues:\*\* \[GitHub Issues\](https://github.com/DanielRSnell/broke-fse/issues)|Issues:** [GitHub Issues]($THEME_URI/issues)|g" README.md
    sed -i '' "s|Documentation:\*\* \[GitHub Wiki\](https://github.com/DanielRSnell/broke-fse/wiki)|Documentation:** [GitHub Wiki]($THEME_URI/wiki)|g" README.md
    sed -i '' "s|Community:\*\* \[GitHub Discussions\](https://github.com/DanielRSnell/broke-fse/discussions)|Community:** [GitHub Discussions]($THEME_URI/discussions)|g" README.md
    echo "✓ Updated README.md"
fi

echo ""
echo "🔧 Initializing new git repository..."
git init
git add .
git commit -m "Initial commit: $THEME_NAME

Forked from Broke FSE boilerplate
Author: $AUTHOR_NAME <$AUTHOR_EMAIL>
"

echo "✓ Git repository initialized"

echo ""
read -p "Would you like to add a git remote? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "Git remote URL (e.g., git@github.com:username/repo.git): " REMOTE_URL
    if [ ! -z "$REMOTE_URL" ]; then
        git remote add origin "$REMOTE_URL"
        echo "✓ Added remote origin: $REMOTE_URL"
        echo ""
        echo "To push to remote, run:"
        echo "  git push -u origin main"
    fi
fi

echo ""
echo "✅ Setup Complete!"
echo "=================="
echo ""
echo "Theme Details:"
echo "  Name: $THEME_NAME"
echo "  Slug: $THEME_SLUG"
echo "  Author: $AUTHOR_NAME"
echo ""
echo "Next Steps:"
echo "  1. Install dependencies:"
echo "     composer install"
echo "     pnpm install"
echo ""
echo "  2. Build assets:"
echo "     pnpm run build:css"
echo "     pnpm run build:js"
echo ""
echo "  3. Activate theme in WordPress:"
echo "     Appearance → Themes → $THEME_NAME → Activate"
echo ""
echo "  4. (Optional) Push to remote:"
echo "     git push -u origin main"
echo ""
echo "Happy building! 🚀"
echo ""
