# Changelog Timeline for WordPress

## Overview

Changelog Timeline is a WordPress plugin that dynamically fetches and displays
changelog data from an external source, caches it for performance, and provides
shortcode-based output.

Version 2.0 expands the plugin with broader version parsing, month/year date
support, Markdown support, multiple layouts, and rich release-note text blocks.

---

## WordPress.org Tags

changelog, changelog timeline, release notes, version history, plugin updates,
developer tools, documentation, markdown, shortcode, github

---

## Features

- Fetches changelogs from external URLs and formats them into a visual changelog
- Transient caching per URL + shortcode configuration
- Builder-agnostic shortcode output
- Multiple layouts: timeline, cards, and compact
- Supports plain text and Markdown changelog formats
- Auto-detection for Markdown changelogs
- Expanded semantic version parsing
- Month/year version date support
- Label-less release note text blocks
- Automatic URL detection inside text blocks
- Global and per-release text block CSS classes
- Optional front-end filtering buttons
- Optional collapsible versions
- Optional summary pills per release
- Safer escaped output
- Admin toolbar cache clearing
- Optional secure URL-based cache clearing
- Customizable via filters and CSS

---

## Installation

1. Upload the plugin files to `/wp-content/plugins/changelog-timeline/`
2. Activate the plugin through WordPress Admin.
3. Add the shortcode to any page, post, template, or builder.

---

## Shortcode Usage

### Basic

[rup_changelogger_timeline url='https://yourdomain.com/changelog.txt']

### Advanced

[rup_changelogger_timeline url="https://yourdomain.com/changelog.txt" title="Plugin Updates" layout="cards" format="auto" show_date="yes" show_version="yes" show_labels="yes" show_filters="yes" show_summary="no" collapsible="no" filter="" limit="0" order="desc" cache_days="7"]

### Available Options

- url
- title
- layout
- format
- show_date
- show_version
- show_labels
- show_filters
- show_summary
- collapsible
- filter
- limit
- order
- cache_days
- class

---

## Supported Changelog Formats

### Plain Text

```txt
Version 1.3.0-alpha (24 August 2025)
New: Added cards layout
Fixed: Corrected ordering logic
```

### Markdown

```md
## 1.3.0-alpha - 24 August 2025

### New
- Added cards layout

### Fixed
- Corrected ordering logic
```

---

## Release Note Text Blocks (New in 2.0)

Text blocks allow rich release notes, migration instructions, upgrade notices,
documentation references and general release commentary without using labels.

### Plain Text Text Blocks

```txt
= 2.0.0 May 2026 =

Text: 2.0 Release Notes
This release introduces the new text block system.

Documentation:
https://example.com/docs

Migration guide:
www.example.com/migrate

No configuration changes are required.
EndText

New: Added text block support.
```

### Multiple Text Blocks

```txt
Text:
Introduction note.
EndText

New: Added feature.

Text:
Migration instructions.
EndText

Fixed: Corrected issue.

Text:
Additional documentation.
EndText
```

Within blocks if you want to render something that would usually be tagged you should wrap it in a code set

```
Code: Example Inclusion
Text:
Introduction note.
EndText
EndCode
```



### Features

- Multiple text blocks per release
- Automatic URL detection and linking
- Supports http, https and www URLs
- Compatible with timeline, cards and compact layouts
- Compatible with filters and collapsible releases
- Ideal for release notes and upgrade guidance

### Generated CSS Classes

```html
changelog-text-block
changelog-text-block-1
changelog-text-block-from-top-1
changelog-text-block-from-bottom-2
changelog-text-block-first
changelog-text-block-last
changelog-text-block-global-27
```

This allows targeting blocks globally or within individual releases.

---

## Supported Version Styles

- 1.0
- 1.0.0
- v1.0.0
- 1.0.0-alpha
- 1.0.0-beta.1
- 1.0.0-rc1
- 1.0.0-dev
- 1.0.0-pre
- 1.0.0-preview
- 1.0.0-canary
- 1.0.0-nightly
- 1.0.0-alpha+build
- 2.0.0 May 2026

---

## Supported Changelog Types

New, Added, Changed, Updated, Fixed, Hotfix, Tweaked, Improvement,
Performance, Security, Deprecated, Removed, Breaking, Compatibility,
Experimental, Known Issue, Warning, and Info.

---

## Filters

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

### Label Colours

```php
add_filter('rup_changelogger_label_colors', function($colors) {
    $colors['Fixed'] = '#ff0000';
    return $colors;
});
```

---

## Cache Clearing

### Admin Toolbar

Use the "Clear Changelog Cache" admin toolbar item.

### URL Method

`?rup_clear_cache=1&key=YOUR_SECRET_KEY`

---

## Layouts

### Timeline

[rup_changelogger_timeline layout="timeline"]

### Cards

[rup_changelogger_timeline layout="cards"]

### Compact

[rup_changelogger_timeline layout="compact"]

---

## Version 2.0 Highlights

- Month/year version header support
- Markdown support improvements
- Rich release note text blocks
- Automatic URL linking
- Per-release text block targeting
- Global text block targeting
- Enhanced styling capabilities
- Cards and compact layouts
- Expanded semantic version support

---

## Future Enhancements

- Admin settings page
- More layouts
- Search box
- Icons per change type
- Date formatting controls

---

## Contributing

Contributions and feedback are welcome.


---


# Styling Text Blocks

Text blocks are intentionally designed to be highly customizable. Every text
block receives a collection of helper classes that allow styling by position,
order, or globally across the entire changelog.

## Basic Release Notes Style

```css
.changelog-text-block {
    padding: 16px 20px;
    border-left: 4px solid #2271b1;
    background: #f6f7f7;
    border-radius: 4px;
}
```
## WordPress Admin Notice Style

```css
.changelog-text-block {
    padding: 12px 16px;
    background: #fff;
    border-left: 4px solid #2271b1;
    box-shadow: 0 1px 2px rgba(0,0,0,.05);
}

.changelog-text-block::before {
    content: "ℹ Release Notes";
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
}
```

## Enhanced Release Notes Card

```css
.changelog-text-block {
    position: relative;
    padding: 18px 22px;
    border-radius: 12px;
    background: linear-gradient(
        180deg,
        rgba(0,0,0,0.02),
        rgba(0,0,0,0.04)
    );
    border-left: 5px solid #4f46e5;
    box-shadow:
        0 2px 8px rgba(0,0,0,0.05),
        inset 0 1px 0 rgba(255,255,255,0.5);
}

.changelog-text-block::before {
    content: "Release Notes";
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    opacity: .65;
    margin-bottom: 10px;
}
```

## Position-Based Styling

Style the first or last text block in each release:

```css
.changelog-text-block-first {
    border-left-color: #16a34a;
}

.changelog-text-block-last {
    border-left-color: #f59e0b;
}
```

Style blocks based on position within a release:

```css
.changelog-text-block-2 {
    background: #fff8dc;
}

.changelog-text-block-from-bottom-2 {
    border-left-color: #dc3545;
}
```

## Global Text Block Styling

Every text block receives a unique global class:

```html
changelog-text-block-global-1
changelog-text-block-global-2
changelog-text-block-global-3
```

This allows styling specific release notes anywhere on the page:

```css
.changelog-text-block-global-27 {
    border-left-color: #9333ea;
}
```
