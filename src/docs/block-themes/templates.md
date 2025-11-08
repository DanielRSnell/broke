# Block Theme Template Naming Conventions

This document outlines the naming conventions and hierarchy for WordPress Full Site Editing (FSE) block theme templates. Follow these conventions when creating new template files.

## Template Directory Structure

All templates are located in the `/templates/` directory as `.html` files containing block markup.

## Core WordPress Template Hierarchy

### Essential Templates

- **index.html** - Fallback template for all views (REQUIRED)
- **home.html** - Blog posts index/homepage
- **front-page.html** - Static front page (takes precedence over home.html)
- **single.html** - Single post view
- **page.html** - Single page view
- **archive.html** - Archive pages (category, tag, date, author)
- **search.html** - Search results page
- **404.html** - Not found/error page

### Specialized Templates

- **singular.html** - Fallback for both single.html and page.html
- **blank.html** - Minimal template with no header/footer
- **canvas.html** - Completely bare template (often for full-width editing)

### Page-Specific Templates

Use the `page-{slug}.html` or `page-{ID}.html` naming pattern:

- **page-cart.html** - Page with slug "cart"
- **page-checkout.html** - Page with slug "checkout"
- **page-my-account.html** - Page with slug "my-account"

### Custom Post Type Templates

Use the `single-{post-type}.html` or `archive-{post-type}.html` pattern:

- **single-product.html** - Single product view (WooCommerce)
- **archive-product.html** - Product archive/shop page (WooCommerce)

### Taxonomy Templates

Use the `taxonomy-{taxonomy}.html` or `taxonomy-{taxonomy}-{term}.html` pattern:

- **taxonomy-product_cat.html** - Product category archives
- **taxonomy-product_tag.html** - Product tag archives
- **taxonomy-product_brand.html** - Product brand archives (custom taxonomy)
- **taxonomy-product_attribute.html** - Product attribute archives

### Author Templates

- **author.html** - Author archive pages
- **author-{nicename}.html** - Specific author by nicename
- **author-{id}.html** - Specific author by ID

### Date Archives

- **date.html** - Date-based archives
- **year.html** - Yearly archives
- **month.html** - Monthly archives
- **day.html** - Daily archives

## WooCommerce-Specific Templates

### Special WooCommerce Templates

- **product-search-results.html** - Product search results
- **order-confirmation.html** - Order confirmation/thank you page

### WooCommerce Template Hierarchy

WooCommerce follows WordPress template hierarchy with product-specific variants:

1. single-product.html → single.html → singular.html → index.html
2. archive-product.html → archive.html → index.html
3. taxonomy-product_cat.html → taxonomy.html → archive.html → index.html

## Template Naming Rules

### File Naming Convention

- Use lowercase letters only
- Use hyphens (-) to separate words, NOT underscores
- Use `.html` extension (NOT `.php`)
- Match exact slug/taxonomy/post-type names

### Valid Examples

```
page-about-us.html          // Page with slug "about-us"
single-portfolio.html       // Custom post type "portfolio"
taxonomy-genre.html         // Custom taxonomy "genre"
archive-book.html           // Custom post type "book" archive
```

### Invalid Examples

```
Page-About.html             // Wrong: uppercase letters
page_about_us.html          // Wrong: underscores instead of hyphens
page-about-us.php           // Wrong: .php extension in FSE themes
```

## Template Hierarchy Priority

WordPress searches for templates in this order (example for a product category):

1. taxonomy-product_cat-{term-slug}.html
2. taxonomy-product_cat.html
3. taxonomy.html
4. archive.html
5. index.html

## Special Template Types

### Coming Soon / Maintenance Mode

- **coming-soon.html** - Coming soon or maintenance mode page

### Custom Page Templates

Any template can be assigned to a page through the Page Editor. Add metadata to make it selectable:

```html
<!-- wp:template-part {"slug":"header"} /-->
<!-- This template can be assigned to any page -->
```

## Best Practices

1. **Always include index.html** - It's the ultimate fallback
2. **Use specific templates for customization** - Don't modify index.html for specific views
3. **Follow the hierarchy** - Let WordPress use its native template resolution
4. **Match existing naming patterns** - Stay consistent with core WordPress conventions
5. **Test template selection** - Verify WordPress selects the correct template for each view

## Template Parts vs Templates

- **Templates** (`/templates/`) - Full page layouts
- **Template Parts** (`/parts/`) - Reusable sections (header, footer, sidebar)

Template parts use the same naming conventions but are referenced differently:

```html
<!-- wp:template-part {"slug":"header","area":"header"} /-->
```

## Checking Active Template

Use the Query Monitor plugin or add this to a template to debug:

```html
<!-- wp:paragraph -->
<p><?php echo get_page_template_slug(); ?></p>
<!-- /wp:paragraph -->
```

## Resources

- [WordPress Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)
- [Block Theme Documentation](https://developer.wordpress.org/block-editor/how-to-guides/themes/block-theme-overview/)
- [WooCommerce Template Structure](https://woocommerce.com/document/template-structure/)
