<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options
delete_option('rup_changelogger_rup_changelogger_activated');
