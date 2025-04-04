<?php
/**
 * Plugin Name:         Changelogger
 * Description:         A simple shortcode generation for remote text files in to changelogs
 * Tested up to:        6.7.2
 * Requires at least:   6.5
 * Requires PHP:        8.0
 * Version:             1.07
 * Author:              reallyusefulplugins.com
 * Author URI:          https://reallyusefulplugins.com
 * License:             GPL2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         rup-changelogger
 * Website:             https://reallyusefulplugins.com
 */
if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

function rup_changelogger_initialize_plugin_update_checker() {
    // Ensure the required function is available.
    if ( ! function_exists( 'get_plugin_data' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    // Get the plugin data from the header.
    $plugin_data = get_plugin_data( __FILE__ );
    
    // Build the constant name prefix using the Text Domain.
    $prefix = 'rup_' . $plugin_data['TextDomain'];

    // Define the constants and their corresponding values.
    $constants = array(
        '_version'         => $plugin_data['Version'],
        '_slug'            => $plugin_data['TextDomain'],
        '_main_file'       => __FILE__,
        '_dir'             => plugin_dir_path( __FILE__ ),
        '_url'             => plugin_dir_url( __FILE__ ),
        '_access_key'      => 'gdNFVtg8eLNWqJchJRsQh5SbjzTTVStvo',
        '_server_location' => 'https://updater.reallyusefulplugins.com/u/'
    );

    // Loop through the array and define each constant dynamically.
    foreach ( $constants as $suffix => $value ) {
        if ( ! defined( $prefix . $suffix ) ) {
            define( $prefix . $suffix, $value );
        }
    }

    // Retrieve the dynamic constants for easier reference.
    $version         = constant($prefix . '_version');
    $slug            = constant($prefix . '_slug');
    $main_file       = constant($prefix . '_main_file');
    $dir             = constant($prefix . '_dir');
    $url             = constant($prefix . '_url');
    $access_key      = constant($prefix . '_access_key');
    $server_location = constant($prefix . '_server_location');

    // Build the update server URL dynamically.
    $updateserver = $server_location . '?key=' . $access_key . '&action=get_metadata&slug=' . $slug;

    // Include the update checker.
    require_once $dir . 'plugin-update-checker/plugin-update-checker.php';

    // Use the fully qualified class name to build the update checker.
    $my_plugin_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        $updateserver,
        $main_file,
        $slug
    );

    // Include functions
    require_once $dir . 'includes/functions.php';
}

//add_action( 'init', 'rup_changelogger_initialize_plugin_update_checker' );


function rup_changelogger_initialize_plugin_git_update_checker() {
require_once 'plugin-update-checker/plugin-update-checker.php';

$$myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/stingray82/RUPChangelog/',
    __FILE__,
    'rup-changelogger'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('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');

}

add_action( 'init', 'rup_changelogger_initialize_plugin_git_update_checker' );


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