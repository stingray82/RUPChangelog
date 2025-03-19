Changelog Timeline for WordPress
================================

📌 Overview
----------

Changelog Timeline is a WordPress plugin that dynamically fetches and displays
changelog data from an external source, caches it for performance, and provides
shortcode-based output. The plugin supports multiple changelogs with independent
caching and allows cache clearing via an admin toolbar button or a direct URL
parameter.

🚀 Features
----------

-   Fetches changelogs from external URLs and formats them into a visually
    structured timeline.

-   Uses transient caching for performance, with a unique cache per URL.

-   Supports different changelog entry types such as `New`, `Updated`, `Fixed`,
    `Tweaked`, `Improvement`, `Security`, `Deprecated`, and `Warning`.

-   Provides a shortcode `[rup_changelogger_timeline url='your-url-here']` for
    easy embedding.

-   Includes a URL-based cache-clearing mechanism for automated resets.

-   Customizable through filters and styles.

🛠️ Installation
--------------

1.  Upload the plugin files to the `/wp-content/plugins/changelog-timeline/`
    directory.

2.  Activate the plugin through the 'Plugins' screen in WordPress.

3.  Add the shortcode `[rup_changelogger_timeline url='your-url-here']` to any
    post or page.

4.  Clear the cache manually via the WordPress admin toolbar or by calling
    `https://yourwebsite.com/clear-changelog-cache/?key=YOUR_SECRET_KEY`.

🔧 Customization & Filters
-------------------------

This plugin includes the following filters for further customization:

### **Change Custom CSS via Filter**

Use this filter to modify the default styles programmatically:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
add_filter('rup_changelogger_custom_css', function() {
    return '.changelog-label { font-size: 16px; }';
});
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

### **Modify the Cache Duration**

The default cache duration is **7 days**. To change it, use this filter:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
add_filter('rup_changelogger_cache_duration', function() {
    return 2 * DAY_IN_SECONDS; // Change cache to 2 days
});
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

### **Adjust Label Colors Dynamically**

You can modify the colors of different label types using this filter:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
add_filter('rup_changelogger_label_colors', function($colors) {
    $colors['Fixed'] = '#ff0000'; // Change 'Fixed' label to red
    return $colors;
});
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

📢 Clearing Cache
----------------

You can clear the cached changelog data in two ways:

### **1. Admin Toolbar Button**

Navigate to the WordPress admin toolbar and click **"Clear Changelog Cache"**.

### **2. URL-Based Cache Clearing**

To clear the cache using a direct URL (useful for cURL automation), visit:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
https://yourwebsite.com/clear-changelog-cache/?key=YOUR_SECRET_KEY
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

 

You can change the Secret key using a filter

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
add_filter('rup_changelogger_secret_key', function($key) {
    return 'Batman';
});
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

🔹 Replace `YOUR_SECRET_KEY` with your actual key to prevent unauthorized cache
clearing.

To clear the cache via **Windows Command Prompt or PowerShell**, run:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
curl "https://yourwebsite.com/clear-changelog-cache/?key=YOUR_SECRET_KEY"
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

🎨 Styling
---------

### **Default Label Colors**

The following labels have preset colors:

-   **New** → Green (`#28a745`)

-   **Updated** → Dark Gray (`#343a40`)

-   **Fixed** → Red (`#dc3545`)

-   **Tweaked** → Blue (`#007bff`)

-   **Improvement** → Purple (`#6f42c1`)

-   **Deprecated** → Orange-Red (`#ff5733`)

-   **Security** → Pink (`#e83e8c`)

-   **Warning** → Yellow (`#ffc107`)

### **Adjusting Styles via CSS**

If you want to customize the styles, you can add custom CSS to your theme:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
.changelog-label.warning {
    background: #ffcc00 !important;
    color: black !important;
    font-size: 14px !important;
    padding: 8px 12px !important;
    border-radius: 4px !important;
}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

📚 Example Usage
---------------

### **Basic Usage**

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
[rup_changelogger_timeline url='https://yourdomain.com/changelog.txt']
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

### **Customizing Output via Filters**

Modify the cache duration and label colors dynamically:

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
add_filter('rup_changelogger_cache_duration', function() { return 3 * DAY_IN_SECONDS; });
add_filter('rup_changelogger_label_colors', function($colors) {
    $colors['Tweaked'] = '#ff6600'; // Change 'Tweaked' label to orange
    return $colors;
});
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

🎯 Future Enhancements
---------------------

-   Option to fetch changelogs via API endpoints.

-   Expand shortcode parameters for more control.

-   More visual customization options.

🤝 Contributing
--------------

If you find any issues or have suggestions, feel free to contribute to the
project!
