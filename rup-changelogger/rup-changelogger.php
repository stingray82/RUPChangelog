<?php
/**
 * Plugin Name:       Changelogger
 * Tested up to:      6.7.2
 * Description:       A simple shortcode generation for remote text files in to changelogs
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Version:           1.0
 * Author:            reallyusefulplugins.com
 * Author URI:        https://reallyusefulplugins.com
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rup-changelogger
 * Website:           https://reallyusefulplugins.com
 * */


if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Define plugin constants
define('RUP_CHANGELOGGER_RUP_CHANGELOGGER_VERSION', '1.0');
define('RUP_CHANGELOGGER_RUP_CHANGELOGGER_DIR', plugin_dir_path(__FILE__));
define('RUP_CHANGELOGGER_RUP_CHANGELOGGER_URL', plugin_dir_url(__FILE__));

// Include functions
require_once RUP_CHANGELOGGER_RUP_CHANGELOGGER_DIR . 'includes/functions.php';

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
