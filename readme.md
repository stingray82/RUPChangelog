Changelog Timeline for WordPress
================================

📌 Overview
----------

Changelog Timeline is a WordPress plugin that dynamically fetches and displays
changelog data from an external source, caches it for performance, and provides
shortcode-based output.

This version keeps the plugin builder-agnostic while adding broader version
parsing, improved defaults, Markdown support, and multiple display layouts.

🚀 Features
----------

- Fetches changelogs from external URLs and formats them into a visual changelog
- Transient caching per URL + shortcode configuration
- Builder-agnostic shortcode output
- Multiple layouts: `timeline`, `cards`, and `compact`
- Supports plain text and Markdown changelog formats
- Auto-detection for Markdown changelogs
- Expanded version parsing for semantic versioning, including:
  - `1.0.0`
  - `v1.0.0`
  - `1.0.0-alpha`
  - `1.0.0-beta.1`
  - `1.0.0-rc1`
  - `1.0.0-dev`
  - `1.0.0-pre`
- Expanded changelog type support
- Optional front-end filtering buttons
- Optional collapsible versions
- Optional summary pills per release
- Safer escaped output
- Admin toolbar cache clearing
- Optional secure URL-based cache clearing
- Customizable via filters and CSS

🛠️ Installation
--------------

1. Upload the plugin files to the `/wp-content/plugins/changelog-timeline/`
   directory.

2. Activate the plugin through WordPress admin.

3. Use the shortcode in any post or page.

📚 Shortcode Usage
-----------------

### Basic

[rup_changelogger_timeline url='https://yourdomain.com/changelog.txt']

### Advanced Example

[rup_changelogger_timeline url="https://yourdomain.com/changelog.txt" title="Plugin Updates" layout="cards" format="auto" show_date="yes" show_version="yes" show_labels="yes" show_filters="yes" show_summary="no" collapsible="no" filter="" limit="0" order="desc" cache_days="7"]

### Current Default Behaviour

Using only:

[rup_changelogger_timeline url='https://yourdomain.com/changelog.txt']

will output:

- `layout="timeline"`
- `format="auto"`
- `show_date="yes"`
- `show_version="yes"`
- `show_labels="yes"`
- `show_filters="no"`
- `show_summary="no"`
- `collapsible="no"`
- `limit="0"`
- `order="desc"`
- `cache_days="7"`

### Available Shortcode Options

- `url` → required changelog source
- `title` → optional heading above the changelog
- `layout` → `timeline`, `cards`, or `compact`
- `format` → `auto`, `plain`, or `markdown`
- `show_date` → `yes` / `no`
- `show_version` → `yes` / `no`
- `show_labels` → `yes` / `no`
- `show_filters` → `yes` / `no`
- `show_summary` → `yes` / `no`
- `collapsible` → `yes` / `no`
- `filter` → comma-separated entry types, for example `Fixed,Security`
- `limit` → number of versions to display, `0` = no limit
- `order` → `desc` keeps source order, `asc` reverses it
- `cache_days` → cache duration in days
- `class` → add your own custom wrapper class

🧾 Supported Changelog Formats
-----------------------------

### Plain Text Format

Example:

Version 1.3.0-alpha (24 August 2025)
New: Added cards layout
Fixed: Corrected ordering logic

### Markdown Format

Example:

## 1.3.0-alpha - 24 August 2025
### New
- Added cards layout
- Added compact layout

### Fixed
- Corrected ordering logic

🔢 Supported Version Styles
--------------------------

The parser now supports a much wider set of version formats, including:

- `1.0`
- `1.0.0`
- `v1.0.0`
- `1.0.0-alpha`
- `1.0.0-beta`
- `1.0.0-beta.1`
- `1.0.0-rc1`
- `1.0.0-dev`
- `1.0.0-pre`
- `1.0.0-preview`
- `1.0.0-canary`
- `1.0.0-nightly`
- `1.0.0-alpha+build`

🔧 Supported Changelog Types
---------------------------

New, Added, Changed, Updated, Fixed, Hotfix, Tweaked, Improvement,
Performance, Security, Deprecated, Removed, Breaking, Compatibility,
Experimental, Known Issue, Warning, and Info fallback support.

🔧 Filters
----------

### Custom CSS

```php
add_filter('rup_changelogger_custom_css', function() {
    return '.changelog-label { font-size: 16px; }';
});
```

### Cache Duration

```php
add_filter('rup_changelogger_cache_duration', function() {
    return 2 * DAY_IN_SECONDS;
});
```

### Label Colors

```php
add_filter('rup_changelogger_label_colors', function($colors) {
    $colors['Fixed'] = '#ff0000';
    return $colors;
});
```

### Type Aliases

```php
add_filter('rup_changelogger_type_aliases', function($types) {
    $types['bug'] = 'Fixed';
    return $types;
});
```

### Secret Key

```php
add_filter('rup_changelogger_secret_key', function() {
    return 'Batman';
});
```

### Enable Public Cache Clearing

```php
add_filter('rup_changelogger_enable_public_cache_clear', '__return_true');
```

📢 Cache Clearing
----------------

### Admin Toolbar
Click **Clear Changelog Cache** in the WordPress admin toolbar.

### URL Method

Use:

`?rup_clear_cache=1&key=YOUR_SECRET_KEY`

Example:

`https://yourwebsite.com/?rup_clear_cache=1&key=YOUR_SECRET_KEY`

🎨 Styling
---------

All label colours are customizable via filter or CSS.

Example:

```css
.changelog-label.fixed {
    background: #ff0000;
}
```

📦 Layout Examples
-----------------

### Timeline

```php
[rup_changelogger_timeline url="https://yourdomain.com/changelog.txt" layout="timeline"]
```

### Cards

```php
[rup_changelogger_timeline url="https://yourdomain.com/changelog.txt" layout="cards"]
```

### Compact

```php
[rup_changelogger_timeline url="https://yourdomain.com/changelog.txt" layout="compact"]
```

### Markdown Cards Example

```php
[rup_changelogger_timeline url="https://yourdomain.com/CHANGELOG.md" format="markdown" layout="cards"]
```

✨ Highlights of This Version
---------------------------

- Fixed version ordering behaviour
- Added Markdown changelog support
- Added `cards` and `compact` layouts
- Added broader semantic version parsing
- Added `show_summary` option
- Improved default shortcode behaviour
- Kept shortcode-first, builder-agnostic workflow

🎯 Future Enhancements
---------------------

- Admin settings page
- More layouts
- Search box
- Icons per change type
- Date formatting controls

🤝 Contributing
--------------

Contributions and feedback are welcome!
