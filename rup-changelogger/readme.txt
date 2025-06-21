=== RUP Changelogger ===
Contributors: reallyusefulplugins
Donate link: https://reallyusefulplugins.com/donate
Tags: Changelogger, Change log, plugins
Requires at least: 6.5
Tested up to: 6.8.1
Stable tag: 1.0.18
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