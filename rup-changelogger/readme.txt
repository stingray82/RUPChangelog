=== RUP Changelogger ===
Contributors: reallyusefulplugins
Donate link: https://reallyusefulplugins.com/donate
Tags: Changelogger, Change log, plugins
Requires at least: 6.5
Tested up to: 7.0
Stable tag: 1.30.1
Requires PHP: 8.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A quick and easy way to add changelog entries from a remote URL in WordPress

== Description ==
A quick and easy way to add changelog entries from a remote URL in WordPress

== Installation ==
1. Upload the `rup_changelogger` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Its now ready to use the shortcode

== Frequently Asked Questions ==
= How do I add a changelog shortcode? =
Add a shortcode like this, this one dsplays this plugins changelog[rup_changelogger_timeline url='https://raw.githubusercontent.com/stingray82/rup-changelogs/refs/heads/main/rupchangelogger.txt']

== Changelog ==
= 1.30.1 May 2026 = 
Added: Support for month and year version headers in plain changelogs.
Added: Support for month and year version headers in Markdown changelogs.
Info: Now 24 May 2026 and May 2026 is supported. 

= 1.30  April 2026 =
Final Release of 1.3.0

= 1.30.0-Beta 5 April 2026 =
Moved to Beta Final Change Log will be Updated on Release

= 1.30.0-alpha.2 3 April 2026 =
New: Added deep linking capabilities 
New: Aliases such as Hotfix, Security, Depreciated, known issue, breaking 
Updated: Markdown and Plain Parsing ability
Improved: CSS Styling
Improved: Parsing Ability for edge cases
Tweaked: Timeline View and Card View
Tweaked: Short code Defaults to better support backward compatibility
Experimental: Tweak to Filters
Breaking: Some old shortcodes may not render properly and look the same



= 1.3.0-alpha 3 April 2026 =
New: Added Markdown changelog support with auto, plain, and markdown format handling
New: Added cards and compact display layouts alongside the existing timeline layout
New: Added show_summary shortcode option for optional per-release summary pills
New: Expanded version parsing to support semantic versioning, alpha, beta, rc, pre, preview, dev, canary, nightly, and build metadata
Updated: Changed shortcode defaults to timeline layout with show_filters disabled, collapsible disabled, show_summary disabled, and show_date, show_version, and show_labels enabled
Updated: Improved builder-agnostic shortcode workflow and default output behaviour
Fixed: Corrected order handling so desc preserves source order and asc reverses it
Fixed: Improved client-side filtering so empty version groups are hidden when filters are applied
Tweaked: Refined README documentation to cover new shortcode options, defaults, layouts, format support, and version patterns

= 1.24 23 March 2026 =
Update: Updater to 2.0-Alpha
Update: Compatibility

= 1.23.2 12 August 2025 =
Fixed: Minor Improvements and Icon Filer Added back

= 1.23.1 5 August 2025 =
Fixed: Comment out WP Icon Filter which is causing issues in latest MainWP

= 1.23.0 27 July 2025 =
Fixed: Versioning hiccup
New: MainWP Dashboard Icon Filter

= 1.2.3 27 July 2025 =
New: Prepare Future Support for Preleases
New: Updated UUPD to 1.3.0

= 1.22 14 July 2025 =
Update: UUPD 1.2.5

= 1.21 06 July 2025 =
New: Rescoped RUP_UUPD
Update: UUPD 1.2.4

= 1.20 ( 05 July 2025) =
Fixed: Issue with Changelogs not loading correctly

= 1.19 ( 21 June 2025) =
New: Updates Now served directly from GitHub using UUPD

= 1.18 ( 21 June 2025) =
New: Updates Now served directly from GitHub using UUPD

= 1.17 ( 21 June 2025) =
New: Updates Now served directly from GitHub using UUPD

= 1.16 (14 June 2025) =
Fixed: New Updater Test

= 1.15 (14 June 2025) =
New: Deploy Test

= 1.14 (14 June 2025) =
New: Deploy Test

= 1.13 (14 June 2025) =
New: Changes for New Deploy Script
Improved: Updater

= 1.09 (17 April 2025) =
Improved: Fuzziness Again
New: Tested to 6.8

= 1.07 (27 March 2025) =
Improved: Fuzziness in naming conventions of changelog headers

= 1.06 (25 March 2025) =
New: Added Some Fuzziness to the interpretation of headers and words including common used like improved and improvement, Warn and Warning, Update and Updated

= 1.05 (24 March 2025) =
Tweaked: Stylesheet Filter for better override
Tweaked: Updater Location

= 1.04 (21 March 2025) =
Tweaked: Updater Settings

= 1.03 (21 March 2025) =
Tweaked: URL Cache Clear to only run if present not every page load

= 1.02 (20 March 2025) =
Fixed: CSS Tweak - Timeline boarder removed on smaller screen for cleaner look
Fixed: Toolbar Cache Clear not looking for new URL based transient

= 1.01 (20 March 2025) =
New: Added Automatic Updates

= 1.0 (19 March 2025) =
New: Initial Release
New: Filter CSS
New: Filter Tag Colour
New: Filter Secret Key