# Twig Layouts

This directory contains base layout templates that use Twig's `{% extends %}` and `{% block %}` system for consistent structure across templates.

## Available Layouts

### base.twig

The root layout template with basic HTML structure.

**Blocks:**
- `head` - Additional head content
- `header` - Site header (defaults to `partials/header.twig`)
- `content` - Main content area (must be overridden)
- `footer` - Site footer (defaults to `partials/footer.twig`)

**Usage:**
```twig
{% extends "layouts/base.twig" %}

{% block content %}
    <main>Your content here</main>
{% endblock %}
```

### page.twig

Extends `base.twig` with page-specific structure.

**Blocks:**
- `page_header` - Optional page header section
- `main_content` - Main content area with container
- `page_footer` - Optional page footer section
- All blocks from `base.twig`

**Usage:**
```twig
{% extends "layouts/page.twig" %}

{% block main_content %}
    <div class="container mx-auto px-4 py-12">
        <h1>{{ post.title }}</h1>
        {{ post.content }}
    </div>
{% endblock %}
```

### single.twig

Extends `base.twig` for single posts/portfolio items with centered header.

**Blocks:**
- `hero` - Hero/header section (defaults to `page-header-centered.twig`)
- `article_content` - Article wrapper
- `article_body` - Article content area
- `related` - Optional related content section
- All blocks from `base.twig`

**Usage:**
```twig
{% extends "layouts/single.twig" %}

{% block article_body %}
    <h1>{{ post.title }}</h1>
    <div class="prose">
        {{ post.content }}
    </div>
{% endblock %}

{% block related %}
    {% include 'partials/related-posts.twig' %}
{% endblock %}
```

## Template Hierarchy

```
base.twig (HTML structure)
├── page.twig (Page layout)
└── single.twig (Single post/portfolio layout)
```

## Example Templates

### Front Page (using page layout)

**File:** `front-page.twig`
```twig
{% extends "layouts/page.twig" %}

{% block main_content %}
    <div class="hero">
        <h1>Welcome to Broke.dev</h1>
    </div>
{% endblock %}
```

### Single Portfolio (using single layout)

**File:** `single-portfolio.twig`
```twig
{% extends "layouts/single.twig" %}

{% block article_body %}
    <div class="portfolio-content">
        <h1>{{ post.title }}</h1>
        {{ post.content }}
    </div>
{% endblock %}

{% block related %}
    <div class="related-work">
        {# Related portfolio items #}
    </div>
{% endblock %}
```

### Custom Layout

You can override any block or create entirely custom layouts:

```twig
{% extends "layouts/base.twig" %}

{% block header %}
    {# Custom header instead of default #}
    <header class="custom-header">...</header>
{% endblock %}

{% block content %}
    <main class="custom-layout">
        {# Your custom content structure #}
    </main>
{% endblock %}

{% block footer %}
    {# Custom footer #}
    <footer class="custom-footer">...</footer>
{% endblock %}
```

## Block Override Patterns

### Extend with Addition

Add content before/after default content:

```twig
{% block head %}
    {{ parent() }}
    <link rel="stylesheet" href="/custom.css">
{% endblock %}
```

### Complete Override

Replace entire block:

```twig
{% block main_content %}
    <div class="my-custom-content">
        {# Completely custom structure #}
    </div>
{% endblock %}
```

### Conditional Override

Override block conditionally:

```twig
{% block page_header %}
    {% if post.meta('show_header') %}
        <header>{{ post.meta('custom_header') }}</header>
    {% endif %}
{% endblock %}
```

## Best Practices

1. **Choose the right base layout** - Use `page.twig` for pages, `single.twig` for posts/portfolio
2. **Override only what you need** - Keep templates focused and minimal
3. **Use {{ parent() }}** when you want to add to (not replace) default content
4. **Keep layouts in layouts/** - Don't create layout files in root views directory
5. **Document custom blocks** - If creating new layouts, document available blocks

## Benefits

- ✅ **Consistent structure** - All pages use same base HTML
- ✅ **DRY principle** - Don't repeat header/footer in every template
- ✅ **Flexibility** - Override only what changes
- ✅ **Maintainability** - Update structure in one place
- ✅ **Clean templates** - Templates focus on content, not boilerplate
