<?php
/**
 * Plugin Name:       Changelogger
 * Description:       A simple shortcode generation for remote text files in to changelogs
 * Tested up to:      6.8.2
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Version:           1.23.0
 * Author:            reallyusefulplugins.com
 * Author URI:        https://reallyusefulplugins.com
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rup-changelogger
 * Website:           https://reallyusefulplugins.com
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

define('RUP_Changelogger_VERSION', '1.23.0');

// Include functions
 require_once __DIR__ . '/includes/functions.php';

// ──────────────────────────────────────────────────────────────────────────
//  Updater bootstrap (plugins_loaded priority 1):
// ──────────────────────────────────────────────────────────────────────────
add_action( 'plugins_loaded', function() {
    // 1) Load our universal drop-in. Because that file begins with "namespace UUPD\V1;",
    //    both the class and the helper live under UUPD\V1.    
    require_once __DIR__ . '/includes/updater.php';

    // 2) Build a single $updater_config array:
    $updater_config = [
        'plugin_file' => plugin_basename( __FILE__ ),             // e.g. "simply-static-export-notify/simply-static-export-notify.php"
        'slug'        => 'rup-changelogger',           // must match your updater‐server slug
        'name'        => 'RUP ChangeLogger',         // human‐readable plugin name
        'version'     => RUP_Changelogger_VERSION, // same as the VERSION constant above
        'key'         => 'gdNFVtg8eLNWqJchJRsQh5SbjzTTVStvo',                 // your secret key for private updater
        'server'      => 'https://raw.githubusercontent.com/stingray82/RUPChangelog/main/uupd/index.json',
        // 'textdomain' is omitted, so the helper will automatically use 'slug'
        //'textdomain'  => 'rup-crm-tag-mapper',           // used to translate “Check for updates”
    ];

    // 3) Call the helper in the UUPD\V1 namespace:
    \RUP\Updater\Updater_V1::register( $updater_config );

}, 20 );


// Run on activation
function rup_changelogger_rup_changelogger_activate() {
    update_option('rup_changelogger_rup_changelogger_activated', time());
}
register_activation_hook(__FILE__, 'rup_changelogger_rup_changelogger_activate');

// Run on deactivation
function rup_changelogger_rup_changelogger_deactivate() {
    delete_option('rup_changelogger_rup_changelogger_activated');
}
register_deactivation_hook(__FILE__, 'rup_changelogger_rup_changelogger_deactivate');