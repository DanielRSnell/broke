# ACF JSON Directory

## Overview

This directory stores **Advanced Custom Fields (ACF) field group definitions** as JSON files for version control.

Think of this as **your field configuration as code**—track custom fields, post types, taxonomies, and option pages in Git alongside your theme.

---

## Philosophy

ACF JSON provides:

1. **Version control for fields** - Track field changes in Git
2. **Automatic sync** - Changes detected and synced automatically
3. **Team collaboration** - Share field configurations across environments
4. **Faster performance** - JSON files load faster than database queries
5. **Migration-friendly** - Deploy field configs with code deploys

### Core Principle

**"Field configurations should be version-controlled code, not database-only content."**

---

## How ACF JSON Works

When you save a field group, ACF automatically:
1. **Saves to database** (as usual)
2. **Exports JSON file** to this directory
3. **Names file** using field group key (e.g., `group_abc123def.json`)

When ACF loads:
1. **Checks for JSON files** in this directory
2. **Compares with database** versions
3. **Shows sync prompt** if JSON differs from database
4. **Loads from JSON** if database version doesn't exist

**Result:** Field groups are always in sync between code and database.

---

## Creating Custom Post Types

### JSON File Structure

Create `acf-json/post-type_[key].json` following this exact format:

```json
{
    "key": "post_type_portfolio",
    "title": "Portfolio",
    "menu_order": 0,
    "active": true,
    "post_type": "portfolio",
    "advanced_configuration": true,
    "import_source": "",
    "import_date": "",
    "labels": {
        "name": "Portfolio",
        "singular_name": "Work",
        "menu_name": "Portfolio",
        "all_items": "All Works",
        "edit_item": "Edit Work",
        "view_item": "View Work",
        "add_new_item": "Add New Work",
        // ... additional labels
    },
    "description": "Portfolio post type description",
    "public": true,
    "hierarchical": false,
    "show_ui": true,
    "show_in_menu": true,
    "show_in_rest": true,
    "menu_position": "5",
    "menu_icon": {
        "type": "dashicons",
        "value": "dashicons-portfolio"
    },
    "supports": [
        "title",
        "editor",
        "thumbnail",
        "excerpt",
        "custom-fields"
    ],
    "taxonomies": ["category"],
    "has_archive": true,
    "has_archive_slug": "portfolio",
    "rewrite": {
        "permalink_rewrite": "post_type_key",
        "with_front": "1",
        "feeds": "0",
        "pages": "1"
    },
    "modified": 1731093600
}
```

**Key Fields:**
- `key`: Unique identifier (format: `post_type_[slug]`)
- `title`: Display name in admin
- `post_type`: The post type slug (lowercase, no spaces)
- `advanced_configuration`: Must be `true` for ACF
- `menu_icon`: Object with `type` and `value` properties
- `supports`: Array of features (must include "custom-fields" for ACF)
- `taxonomies`: Array of associated taxonomies

**Example:** See `includes/acf-examples/post_type_portfolio.json`

---

## Creating Field Groups

### JSON File Structure

Create `acf-json/group_[name].json` following this exact format:

```json
{
    "key": "group_portfolio_fields",
    "title": "Portfolio Fields",
    "fields": [
        {
            "key": "field_portfolio_tab_details",
            "label": "Project Details",
            "name": "",
            "type": "tab",
            "placement": "top",
            "endpoint": 0
        },
        {
            "key": "field_portfolio_client_name",
            "label": "Client Name",
            "name": "client_name",
            "type": "text",
            "instructions": "Name of the client",
            "wrapper": {
                "width": "50"
            },
            "placeholder": "Client Name"
        }
    ],
    "location": [
        [
            {
                "param": "post_type",
                "operator": "==",
                "value": "portfolio"
            }
        ]
    ],
    "menu_order": 0,
    "position": "normal",
    "style": "default",
    "label_placement": "left",
    "instruction_placement": "label",
    "active": true,
    "show_in_rest": 1,
    "acfe_autosync": ["json"],
    "modified": 1731093600
}
```

**Key Fields:**
- `key`: Unique identifier (format: `group_[name]`)
- `title`: Field group display name
- `fields`: Array of field objects
- `location`: Array of location rule arrays (when to show this group)
- `menu_order`: Display order (lower = higher priority)
- `position`: Where to show (normal, acf_after_title, side)
- `label_placement`: left, top
- `acfe_autosync`: Must include ["json"] for auto-sync
- `show_in_rest`: Set to 1 for Gutenberg compatibility

**Tab Fields:**
Tabs have no `name` field and `type: "tab"`. Use `endpoint: 0` for regular tabs.

**Example:** See `includes/acf-examples/group_portfolio_fields.json`

### Common Field Types

| Field Type | Use Case | Example |
|------------|----------|---------|
| **Text** | Short text input | Client Name, Subtitle |
| **Textarea** | Long text | Description, Notes |
| **WYSIWYG** | Rich text editor | Bio, Long Content |
| **Image** | Single image upload | Featured Image, Logo |
| **Gallery** | Multiple images | Photo Gallery |
| **File** | File upload | PDF, Document |
| **Select** | Dropdown | Status, Category |
| **True/False** | Checkbox | Featured, Published |
| **Number** | Numeric input | Price, Quantity |
| **Date Picker** | Date selection | Event Date, Birthday |
| **URL** | Website link | Project URL, Social Link |
| **Repeater** | Repeating rows | Team Members, FAQ Items |
| **Group** | Nested fields | Address (street, city, zip) |
| **Post Object** | Link to post | Related Posts |
| **Relationship** | Multiple posts | Select Multiple Projects |
| **Taxonomy** | Term selection | Categories, Tags |

---

## Creating Repeater Fields

Repeaters allow multiple rows of sub-fields:

**Example: Team Members**

1. Create field group: `Team Section`
2. Add field:
   - Label: `Team Members`
   - Type: `Repeater`
3. Add sub-fields:
   - Name (Text)
   - Title (Text)
   - Photo (Image)
   - Bio (Textarea)
4. Save - creates `group_team.json`

**In template (Universal Block markup):**
```html
<div
  conditionalvisibility="true"
  conditionalexpression="post.meta('team_members')|length > 0"
  loopsource="post.meta('team_members')"
  loopvariable="member">
  <div class="team-member">
    <img src="{{ member.photo.url }}" alt="{{ member.name }}">
    <h3>{{ member.name }}</h3>
    <p class="title">{{ member.title }}</p>
    <p>{{ member.bio }}</p>
  </div>
</div>
```

---

## Creating Option Pages

Option pages store site-wide settings (not tied to specific posts).

### Step 1: Create Option Page via ACF UI

1. **In WordPress Admin:**
   - Go to `ACF → Options`
   - Click "Add New Options Page"
   - Configure:
     - Page Title: `Theme Settings`
     - Menu Title: `Theme Settings`
     - Menu Slug: `theme-settings`
     - Parent: `themes.php` (Appearance menu)

2. **Save** - creates option page

### Step 2: Create Field Group for Option Page

1. Go to `ACF → Field Groups`
2. Add field group: `Theme Settings`
3. Add fields:
   - Site Tagline (Text)
   - Contact Email (Email)
   - Social Links (Repeater with URL fields)
   - Footer Text (Textarea)

4. **Location Rules:**
   - Show if: Options Page is equal to `Theme Settings`

5. **Save** - creates JSON file

### Accessing Option Page Fields

**✅ Recommended Approach - Use Context Filters:**

```php
// src/context/theme-settings.php
add_filter('timber/context', function($context) {
    $context['site_tagline'] = get_field('site_tagline', 'option');
    $context['contact_email'] = get_field('contact_email', 'option');
    $context['social_links'] = get_field('social_links', 'option');
    return $context;
});
```

```html
<!-- In template - Clean and simple! -->
<p>{{ site_tagline }}</p>
<a href="mailto:{{ contact_email }}">{{ contact_email }}</a>
```

**⚠️ Emergency Use Only - Magic Helper (NOT recommended):**

```html
<!-- Only use for quick editor fixes -->
<p>{{ fun.get_field('site_tagline', 'option') }}</p>
```

This violates MVC principles. Always prefer context filters for maintainability.

---

## Creating Taxonomies

### JSON File Structure

Create `acf-json/taxonomy_[key].json` following this exact format:

```json
{
    "key": "taxonomy_project_type",
    "title": "Project Types",
    "menu_order": 0,
    "active": true,
    "taxonomy": "project_type",
    "advanced_configuration": true,
    "import_source": "",
    "import_date": "",
    "labels": {
        "name": "Project Types",
        "singular_name": "Project Type",
        "menu_name": "Project Types",
        "all_items": "All Project Types",
        "edit_item": "Edit Project Type",
        "add_new_item": "Add New Project Type",
        // ... additional labels
    },
    "description": "Categorize projects by type",
    "public": true,
    "publicly_queryable": true,
    "hierarchical": true,
    "show_ui": true,
    "show_in_menu": true,
    "show_in_rest": true,
    "rest_base": "project_type",
    "rest_controller_class": "WP_REST_Terms_Controller",
    "rest_namespace": "wp\/v2",
    "show_tagcloud": true,
    "show_in_quick_edit": true,
    "show_admin_column": true,
    "object_types": [
        "project"
    ],
    "rewrite": {
        "permalink_rewrite": "taxonomy_key",
        "with_front": "1",
        "hierarchical": "0"
    },
    "query_var": "taxonomy_key",
    "modified": 1731093600
}
```

**Key Fields:**
- `key`: Unique identifier (format: `taxonomy_[slug]`)
- `title`: Display name in admin
- `taxonomy`: The taxonomy slug (lowercase, no spaces)
- `advanced_configuration`: Must be `true` for ACF
- `hierarchical`: `true` for category-like, `false` for tag-like
- `object_types`: Array of post types this taxonomy applies to
- `show_admin_column`: Show taxonomy column in post list
- `rest_namespace`: Must escape forward slash as `wp\/v2`

### Using Taxonomies in Templates

**✅ For current post terms (Universal Block markup):**

```html
<!-- Get terms for current post -->
<div loopsource="post.terms('project_type')" loopvariable="term">
    <a href="{{ term.link }}">{{ term.name }}</a>
</div>
```

**✅ For all terms - Use Context Filter:**

```php
// src/context/taxonomy-data.php
add_filter('timber/context', function($context) {
    // Only load on archive or when needed
    if (is_post_type_archive('portfolio') || is_singular('portfolio')) {
        $context['project_types'] = Timber::get_terms('project_type');
    }
    return $context;
});
```

```html
<!-- In template - Clean and simple! -->
<div loopsource="project_types" loopvariable="type">
    <a href="{{ type.link }}">{{ type.name }}</a>
</div>
```

---

## ACF Workflow

### Development Workflow

1. **Local Development:**
   - Create/modify fields via ACF UI
   - ACF saves JSON to `acf-json/`
   - Commit JSON files to Git

2. **Staging/Production:**
   - Pull latest code (includes JSON files)
   - ACF detects JSON files
   - Go to `ACF → Tools → Sync Available`
   - Click "Sync" to import to database

### Sync States

| Icon | State | Action |
|------|-------|--------|
| 🟢 Green | In sync | No action needed |
| 🟡 Yellow | JSON newer | Click "Sync" to import |
| 🔴 Red | Database newer | Re-save in ACF UI to export |

### Best Practices

**DO:**
- ✅ Always commit ACF JSON files after field changes
- ✅ Sync fields on staging/production after deploy
- ✅ Use field groups to organize related fields
- ✅ Use meaningful field names (lowercase, underscores)
- ✅ Document complex field structures in comments

**DON'T:**
- ❌ Manually edit JSON files (use ACF UI instead)
- ❌ Delete JSON files without deleting field group
- ❌ Mix JSON and non-JSON field groups (confusing)
- ❌ Skip syncing on production (fields won't work)

---

## Common Field Patterns

### Pattern: Hero Section
```
Field Group: Page Hero
Location: Post Type = Page

Fields:
- Hero Heading (Text)
- Hero Subheading (Textarea)
- Hero Background (Image)
- Hero CTA Text (Text)
- Hero CTA URL (URL)
```

### Pattern: Content Sections (Flexible Content)
```
Field Group: Page Sections
Location: Post Type = Page

Fields:
- Sections (Flexible Content)
  - Layout: Text Block
    - Heading (Text)
    - Content (WYSIWYG)
  - Layout: Image Gallery
    - Gallery (Gallery)
  - Layout: Call to Action
    - CTA Text (Text)
    - CTA Button Text (Text)
    - CTA Button URL (URL)
```

### Pattern: SEO Meta
```
Field Group: SEO
Location: Post Type = Any

Fields:
- Meta Title (Text)
- Meta Description (Textarea, max 160 chars)
- Open Graph Image (Image)
- No Index (True/False)
```

---

## Troubleshooting

### Fields Not Showing

**Check:**
1. Location rules are correct
2. JSON file synced (`ACF → Tools → Sync Available`)
3. Field group is active (not trashed)
4. ACF plugin is active

### JSON Not Generating

**Fix:**
1. Check directory permissions (should be writable)
2. Verify ACF Pro is activated (free version doesn't support all features)
3. Re-save field group to trigger export

### Sync Conflicts

**Solution:**
1. If local is correct: Re-save in ACF UI, commit JSON
2. If remote is correct: Delete local JSON, pull from Git, sync in ACF
3. Never manually edit JSON to resolve—use ACF UI

### Fields Not Accessible in Templates

**✅ Recommended Debugging Approach:**

**For post custom fields:**
```html
<!-- Use Timber Post object method -->
{{ post.meta('field_name') }}
```

**For option page fields - Use Context Filter:**
```php
// src/context/theme-settings.php
add_filter('timber/context', function($context) {
    // Debug by adding to context
    $context['debug_option'] = get_field('field_name', 'option');
    return $context;
});
```

```html
<!-- Then access in template -->
{{ debug_option }}
```

**For PHP debugging:**
```php
// In src/context/ file or functions.php
add_filter('timber/context', function($context) {
    // Debug the value
    error_log(print_r(get_field('field_name'), true));
    return $context;
});
```

Check WordPress debug.log for output.

---

## Performance Considerations

ACF JSON provides:
- **Faster load times** - JSON files load faster than database queries
- **Reduced queries** - Field definitions cached from JSON
- **Better caching** - Static files can be cached by CDN

For heavy field groups:
- Use **Local JSON** (already enabled in this directory)
- **Limit repeater rows** - Set max rows for repeaters
- **Lazy load images** - Use ACF's lazy load option
- **Cache field values** - Cache expensive field queries

---

## Alternative: Carbon Fields

If you prefer PHP-based field registration, consider **Carbon Fields**:

```php
// Example with Carbon Fields
use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('post_meta', 'Portfolio Details')
    ->where('post_type', '=', 'portfolio')
    ->add_fields([
        Field::make('text', 'client_name', 'Client Name'),
        Field::make('date', 'completion_date', 'Completion Date'),
        Field::make('checkbox', 'featured', 'Featured'),
    ]);
```

**ACF vs Carbon Fields:**
- **ACF:** Visual UI, non-developers can use, JSON export
- **Carbon:** Pure PHP, better for developers, more control

---

**Summary:**

This directory stores ACF field group definitions as JSON for version control. Use ACF UI to create custom post types, field groups, option pages, and taxonomies. ACF automatically exports to JSON. Sync JSON files on staging/production after deploy.

**Workflow:**
1. Create fields via ACF UI
2. ACF saves to `acf-json/*.json`
3. Commit JSON to Git
4. Deploy and sync on production

**For boilerplate:**
- Directory starts empty with this README
- Add field groups as your project needs them
- JSON files auto-generate when you save field groups

---

**Version:** 1.0.0
**Last Updated:** 2025-01-21
