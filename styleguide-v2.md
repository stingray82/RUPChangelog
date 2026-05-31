# Changelog Timeline 2.0 Style Guide

## Introduction

Version 2.0 introduces Text Blocks, Code Blocks, automatic URL linking,
advanced CSS targeting hooks, and a richer release-note system.

This guide explains the rendered HTML structure and how to customize it
using CSS without modifying the plugin.

---

# Understanding the Output Structure

A typical release is rendered as:

```html
.changelog-entry
├── .changelog-header
├── .changelog-meta
│   └── .changelog-items
│       ├── .changelog-item
│       ├── .changelog-text-block
│       └── .changelog-code-block
```

The plugin intentionally exposes classes and data attributes so styling
can be handled entirely through CSS.

---

# Text Blocks

## Source Format

```txt
Text: Migration Guide

This release changes the configuration format.

EndText
```

## Rendered Structure

```html
<div
    class="changelog-text-block"
    data-text-block-heading="Migration Guide">

    <div class="changelog-text-block-title">
        Migration Guide
    </div>

    <p>This release changes the configuration format.</p>

</div>
```

---

# Text Block Classes

Every text block receives targeting helpers.

## Position Classes

```html
changelog-text-block-first
changelog-text-block-last
```

## Order Classes

```html
changelog-text-block-1
changelog-text-block-2
```

## Top / Bottom Classes

```html
changelog-text-block-from-top-1
changelog-text-block-from-top-2

changelog-text-block-from-bottom-1
changelog-text-block-from-bottom-2
```

## Global Classes

```html
changelog-text-block-global-1
changelog-text-block-global-2
changelog-text-block-global-3
```

---

# Code Blocks

## Source Format

```txt
Code: Example

Text:
Your release note content goes here.
EndText

EndCode
```

## Rendered Structure

```html
<div
    class="changelog-code-block-wrap"
    data-code-block-heading="Example">

    <div class="changelog-code-block-title">
        Example
    </div>

    <pre class="changelog-code-block">
        ...
    </pre>

</div>
```

---

# Code Block Classes

## Position Classes

```html
changelog-code-block-first
changelog-code-block-last
```

## Order Classes

```html
changelog-code-block-1
changelog-code-block-2
```

## Top / Bottom Classes

```html
changelog-code-block-from-top-1
changelog-code-block-from-bottom-1
```

## Global Classes

```html
changelog-code-block-global-1
changelog-code-block-global-2
```

---

# Targeting by Heading

One of the most powerful customization methods.

## Migration Guide

```css
[data-text-block-heading="Migration Guide"] {
    border-left: 4px solid #f59e0b;
}
```

## Known Issues

```css
[data-text-block-heading="Known Issues"] {
    border-left: 4px solid #dc3545;
}
```

## Documentation

```css
[data-text-block-heading="Documentation"] {
    border-left: 4px solid #2271b1;
}
```

The plugin intentionally does not apply special styling to headings.
This allows site owners to decide how headings should appear.

---

# Styling Text Block Titles

```css
.changelog-text-block-title {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 999px;
    background: #2271b1;
    color: white;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}
```

---

# Styling Code Block Titles

```css
.changelog-code-block-title {
    display: inline-block;
    padding: 8px 12px;
    background: #1d2327;
    color: white;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
```

---

# WordPress Admin Notice Style

```css
.changelog-text-block {
    background: #fff;
    border-left: 4px solid #2271b1;
    padding: 20px;
    border-radius: 6px;
}
```

---

# Release Notes Card Style

```css
.changelog-text-block {
    border-radius: 12px;
    padding: 24px;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
```

---

# Example Release Documentation Style

```css
.changelog-text-block-title {
    background: #2563eb;
    color: white;
}

.changelog-code-block-title {
    background: #111827;
    color: white;
}

.changelog-code-block {
    background: #111827;
    color: #f9fafb;
}
```

---

# Global Targeting

Useful for showcase pages or featured releases.

```css
.changelog-text-block-global-1 {
    border-left-color: #f59e0b;
}

.changelog-code-block-global-1 {
    border-color: #f59e0b;
}
```

---

# Position-Based Targeting

Style the first or last block inside each release.

```css
.changelog-text-block-first {
    border-left-color: #16a34a;
}

.changelog-text-block-last {
    border-left-color: #dc2626;
}
```

---

# Automatic URL Linking

URLs inside Text Blocks are automatically converted into links.

Supported:

```txt
https://example.com
http://example.com
www.example.com
```

Unsupported:

```txt
javascript:
file:
ftp:
```

These remain plain text for safety.

---

# Complete Example

## Source

```txt
Text: Version 2.0

Version 2.0 introduces label-less text blocks.

Code: Example

Text:
Your release note content goes here.
EndText

EndCode

Text blocks support automatic URL linking.

EndText
```

## Result

- Release note heading
- Rich text content
- Embedded code example
- Automatic URL linking
- CSS-targetable structure

---

# Design Philosophy

The parser understands structure.

The markup exposes metadata.

CSS controls presentation.

This keeps the plugin lightweight while allowing extensive customization.
