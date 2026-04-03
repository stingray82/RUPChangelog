<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * -------------------------------------------------------
 * Helpers
 * -------------------------------------------------------
 */

function rup_changelogger_get_default_type_aliases() {
    return apply_filters('rup_changelogger_type_aliases', [
        'new'           => 'New',
        'add'           => 'Added',
        'added'         => 'Added',
        'change'        => 'Changed',
        'changed'       => 'Changed',
        'update'        => 'Updated',
        'updated'       => 'Updated',
        'fix'           => 'Fixed',
        'fixed'         => 'Fixed',
        'hotfix'        => 'Hotfix',
        'tweak'         => 'Tweaked',
        'tweaked'       => 'Tweaked',
        'improve'       => 'Improvement',
        'improved'      => 'Improvement',
        'improvement'   => 'Improvement',
        'performance'   => 'Performance',
        'security'      => 'Security',
        'deprecated'    => 'Deprecated',
        'remove'        => 'Removed',
        'removed'       => 'Removed',
        'breaking'      => 'Breaking',
        'compatibility' => 'Compatibility',
        'experimental'  => 'Experimental',
        'known'         => 'Known Issue',
        'warning'       => 'Warning',
        'warn'          => 'Warning',
    ]);
}

function rup_changelogger_get_default_label_colors() {
    return apply_filters('rup_changelogger_label_colors', [
        'New'           => '#28a745',
        'Added'         => '#20c997',
        'Changed'       => '#17a2b8',
        'Updated'       => '#343a40',
        'Fixed'         => '#dc3545',
        'Hotfix'        => '#b02a37',
        'Tweaked'       => '#007bff',
        'Improvement'   => '#6f42c1',
        'Performance'   => '#6610f2',
        'Security'      => '#e83e8c',
        'Deprecated'    => '#fd7e14',
        'Removed'       => '#6c757d',
        'Breaking'      => '#c82333',
        'Compatibility' => '#198754',
        'Experimental'  => '#0dcaf0',
        'Known Issue'   => '#ffc107',
        'Warning'       => '#ffc107',
        'Info'          => '#6c757d',
    ]);
}

function rup_changelogger_slugify_type($type) {
    $type = strtolower(trim($type));
    $type = str_replace([' ', '_'], '-', $type);
    return preg_replace('/[^a-z0-9\-]/', '', $type);
}

function rup_changelogger_build_cache_key($url, $atts = []) {
    $key_data = [
        'url'  => $url,
        'atts' => $atts,
    ];

    return 'cached_changelog_timeline_' . md5(wp_json_encode($key_data));
}

/**
 * Supports:
 * 1.0
 * 1.0.0
 * v1.0.0
 * 1.0.0-alpha
 * 1.0.0-beta.1
 * 1.0.0-rc1
 * 1.0.0-dev
 * 1.0.0-pre
 * 1.0.0-alpha+build
 */
function rup_changelogger_get_version_pattern() {
    return '(v?\d+(?:\.\d+)*(?:[-._]?(?:alpha|beta|rc|pre|preview|dev|canary|nightly)[-._]?[a-z0-9]*)?(?:\+[a-z0-9.\-_]+)?)';
}

/**
 * -------------------------------------------------------
 * Fetch
 * -------------------------------------------------------
 */

function rup_changelogger_fetch_changelog_data_timeline($url, $atts = []) {
    $transient_key = rup_changelogger_build_cache_key($url, $atts);
    $cached_data   = get_transient($transient_key);

    if (false !== $cached_data) {
        return $cached_data;
    }

    $fetch_args = apply_filters('rup_changelogger_fetch_args', [
        'timeout'     => 10,
        'redirection' => 5,
        'headers'     => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36'
        ],
    ], $url, $atts);

    $response = wp_remote_get($url, $fetch_args);

    if (is_wp_error($response)) {
        return '<p style="color:red;">Error fetching changelog: ' . esc_html($response->get_error_message()) . '</p>';
    }

    $changelog_text = wp_remote_retrieve_body($response);

    if (!$changelog_text) {
        return '<p style="color:red;">No changelog data available.</p>';
    }

    if (strpos(strtolower($changelog_text), '<html') !== false) {
        preg_match('/<pre.*?>(.*?)<\/pre>/is', $changelog_text, $matches);
        if (!empty($matches[1])) {
            $changelog_text = html_entity_decode(trim($matches[1]));
        } else {
            return '<p style="color:red;">Could not extract raw changelog text from the response.</p>';
        }
    }

    $changelog_text = mb_convert_encoding($changelog_text, 'UTF-8', 'auto');

    $format = !empty($atts['format']) ? $atts['format'] : 'auto';
    $parsed = rup_changelogger_parse_changelog($changelog_text, $format);
    $output = rup_changelogger_render_changelog_timeline($parsed, $atts);

    $cache_days     = isset($atts['cache_days']) ? absint($atts['cache_days']) : 7;
    $cache_duration = apply_filters('rup_changelogger_cache_duration', max(1, $cache_days) * DAY_IN_SECONDS, $url, $atts);

    set_transient($transient_key, $output, $cache_duration);

    return $output;
}

/**
 * -------------------------------------------------------
 * Parse router
 * -------------------------------------------------------
 */

function rup_changelogger_parse_changelog($text, $format = 'auto') {
    $format = strtolower(trim($format));

    if ($format === 'markdown') {
        return rup_changelogger_parse_markdown_changelog($text);
    }

    if ($format === 'plain') {
        return rup_changelogger_parse_plain_changelog($text);
    }

    if (rup_changelogger_looks_like_markdown($text)) {
        return rup_changelogger_parse_markdown_changelog($text);
    }

    return rup_changelogger_parse_plain_changelog($text);
}

function rup_changelogger_looks_like_markdown($text) {
    $text = str_replace(["\r\n", "\r"], "\n", $text);

    if (preg_match('/^##\s+.+$/m', $text) && preg_match('/^###\s+.+$/m', $text)) {
        return true;
    }

    if (preg_match('/^[-*]\s+.+$/m', $text) && preg_match('/^##\s+/m', $text)) {
        return true;
    }

    return false;
}

/**
 * -------------------------------------------------------
 * Plain text parser
 * -------------------------------------------------------
 */

function rup_changelogger_parse_plain_changelog($text) {
    $text = str_replace(["\r\n", "\r"], "\n", trim($text));
    $lines = explode("\n", $text);

    $type_aliases = rup_changelogger_get_default_type_aliases();
    $versions = [];

    $current_version = '';
    $current_date = '';
    $current_entries = [];

    $version_pattern = rup_changelogger_get_version_pattern();

    foreach ($lines as $line) {
        $line = preg_replace('/\s+/', ' ', trim($line));

        if ($line === '') {
            continue;
        }

        $version_regex = '/^(?:=|\s*)?\s*(?:Version\s*|version\s*|Release\s*|release\s*|v)?\s*' . $version_pattern . '(?:\s+|\s*[-–]\s*|\s*\(\s*)(.*?)\s*(?:\)|\s*)\s*(?:=|\s*)?$/i';

        if (preg_match($version_regex, $line, $matches)) {
            if (!empty($current_version) || !empty($current_entries)) {
                $versions[] = [
                    'version' => $current_version,
                    'date'    => $current_date,
                    'entries' => $current_entries,
                ];
            }

            $current_version = isset($matches[1]) ? trim($matches[1]) : '';
            $current_date    = isset($matches[2]) ? trim($matches[2]) : '';
            $current_entries = [];
            continue;
        }

        if (preg_match('/^([a-zA-Z\s]+):\s+(.+)$/', $line, $matches)) {
            $raw_type   = strtolower(trim($matches[1]));
            $entry_text = trim($matches[2]);

            $normalized_type = isset($type_aliases[$raw_type]) ? $type_aliases[$raw_type] : ucwords($raw_type);

            $current_entries[] = [
                'type' => $normalized_type,
                'text' => $entry_text,
            ];
        }
    }

    if (!empty($current_version) || !empty($current_entries)) {
        $versions[] = [
            'version' => $current_version,
            'date'    => $current_date,
            'entries' => $current_entries,
        ];
    }

    return apply_filters('rup_changelogger_parsed_entries', $versions, $text);
}

/**
 * -------------------------------------------------------
 * Markdown parser
 * -------------------------------------------------------
 */

function rup_changelogger_parse_markdown_changelog($text) {
    $text = str_replace(["\r\n", "\r"], "\n", trim($text));
    $lines = explode("\n", $text);

    $type_aliases = rup_changelogger_get_default_type_aliases();
    $versions = [];

    $current_version = '';
    $current_date = '';
    $current_entries = [];
    $current_type = '';

    $version_pattern = rup_changelogger_get_version_pattern();

    foreach ($lines as $line) {
        $raw_line = rtrim($line);
        $line = trim($raw_line);

        if ($line === '') {
            continue;
        }

        $markdown_version_regex = '/^##\s+(?:Version\s+|Release\s+)?' . $version_pattern . '\s*(?:[-–]\s*(.+)|\((.+)\))?$/i';

        if (preg_match($markdown_version_regex, $line, $matches)) {
            if (!empty($current_version) || !empty($current_entries)) {
                $versions[] = [
                    'version' => $current_version,
                    'date'    => $current_date,
                    'entries' => $current_entries,
                ];
            }

            $current_version = isset($matches[1]) ? trim($matches[1]) : '';
            $current_date = '';

            if (!empty($matches[2])) {
                $current_date = trim($matches[2]);
            } elseif (!empty($matches[3])) {
                $current_date = trim($matches[3]);
            }

            $current_entries = [];
            $current_type = '';
            continue;
        }

        if (preg_match('/^###\s+(.+)$/', $line, $matches)) {
            $raw_type = strtolower(trim($matches[1]));
            $current_type = isset($type_aliases[$raw_type]) ? $type_aliases[$raw_type] : ucwords($raw_type);
            continue;
        }

        if (preg_match('/^[-*]\s+(.+)$/', $line, $matches)) {
            $entry_text = trim($matches[1]);

            $current_entries[] = [
                'type' => $current_type ? $current_type : 'Info',
                'text' => $entry_text,
            ];
            continue;
        }
    }

    if (!empty($current_version) || !empty($current_entries)) {
        $versions[] = [
            'version' => $current_version,
            'date'    => $current_date,
            'entries' => $current_entries,
        ];
    }

    return apply_filters('rup_changelogger_parsed_entries', $versions, $text);
}

/**
 * -------------------------------------------------------
 * Render
 * -------------------------------------------------------
 */

function rup_changelogger_render_changelog_timeline($versions, $atts = []) {
    $defaults = [
        'layout'            => 'timeline',
        'format'            => 'auto',
        'show_date'         => 'yes',
        'show_version'      => 'yes',
        'show_labels'       => 'yes',
        'show_filters'      => 'no',
        'show_summary'      => 'no',
        'collapsible'       => 'no',
        'animate_warnings'  => 'yes',
        'filter'            => '',
        'limit'             => 0,
        'order'             => 'desc',
        'cache_days'        => 7,
        'title'             => '',
        'class'             => '',
    ];

    $atts = shortcode_atts($defaults, $atts, 'rup_changelogger_timeline');

    $filter_types = [];
    if (!empty($atts['filter'])) {
        $filter_types = array_map('trim', explode(',', $atts['filter']));
        $filter_types = array_filter($filter_types);
    }

    // desc = keep file/source order
    // asc  = reverse it
    $order = strtolower($atts['order']);
    if ($order === 'asc') {
        $versions = array_reverse($versions);
    }

    if (!empty($atts['limit'])) {
        $versions = array_slice($versions, 0, absint($atts['limit']));
    }

    $all_types = [];
    foreach ($versions as $version_data) {
        foreach ($version_data['entries'] as $entry) {
            $all_types[$entry['type']] = $entry['type'];
        }
    }

    $wrapper_classes = [
        'rup-changelogger',
        'layout-' . sanitize_html_class($atts['layout']),
        $atts['collapsible'] === 'yes' ? 'is-collapsible' : '',
        $atts['animate_warnings'] === 'yes' ? 'animate-warnings' : 'no-warning-animation',
        sanitize_html_class($atts['class']),
    ];

    $output  = '<div class="' . esc_attr(trim(implode(' ', array_filter($wrapper_classes)))) . '">';

    if (!empty($atts['title'])) {
        $output .= '<h3 class="rup-changelogger-title">' . esc_html($atts['title']) . '</h3>';
    }

    if ($atts['show_filters'] === 'yes' && !empty($all_types)) {
        $output .= '<div class="rup-changelogger-filters" data-rup-filters>';
        $output .= '<button type="button" class="rup-filter-btn active" data-filter="all">All</button>';

        foreach ($all_types as $type) {
            $output .= '<button type="button" class="rup-filter-btn" data-filter="' . esc_attr(rup_changelogger_slugify_type($type)) . '">' . esc_html($type) . '</button>';
        }

        $output .= '</div>';
    }

    $output .= '<div class="changelog-timeline">';

    foreach ($versions as $index => $version_data) {
        $version = isset($version_data['version']) ? $version_data['version'] : '';
        $date    = isset($version_data['date']) ? $version_data['date'] : '';
        $entries = isset($version_data['entries']) ? $version_data['entries'] : [];

        if (!empty($filter_types)) {
            $entries = array_filter($entries, function($entry) use ($filter_types) {
                return in_array($entry['type'], $filter_types, true);
            });
        }

        if (empty($entries)) {
            continue;
        }

        $summary = [];
        foreach ($entries as $entry) {
            if (!isset($summary[$entry['type']])) {
                $summary[$entry['type']] = 0;
            }
            $summary[$entry['type']]++;
        }

        $entry_id = 'rup-changelog-version-' . $index . '-' . wp_rand(1000, 9999);

        $output .= '<div class="changelog-entry">';
        $output .= '<div class="changelog-header">';

        if ($atts['show_version'] === 'yes') {
            $output .= '<div class="changelog-version-box">' . esc_html($version) . '</div>';
        }

        if ($atts['show_date'] === 'yes' && !empty($date)) {
            $output .= '<div class="changelog-date">' . esc_html($date) . '</div>';
        }

        if ($atts['collapsible'] === 'yes') {
            $output .= '<button type="button" class="rup-toggle-version" aria-expanded="' . ($index === 0 ? 'true' : 'false') . '" aria-controls="' . esc_attr($entry_id) . '">';
            $output .= $index === 0 ? 'Hide' : 'Show';
            $output .= '</button>';
        }

        $output .= '</div>';

        if ($atts['show_summary'] === 'yes') {
            $output .= '<div class="changelog-summary">';
            foreach ($summary as $type => $count) {
                $output .= '<span class="changelog-summary-pill type-' . esc_attr(rup_changelogger_slugify_type($type)) . '">' . esc_html($count . ' ' . $type) . '</span>';
            }
            $output .= '</div>';
        }

        $content_classes = 'changelog-meta';
        if ($atts['collapsible'] === 'yes' && $index !== 0) {
            $content_classes .= ' is-collapsed';
        }

        $output .= '<div id="' . esc_attr($entry_id) . '" class="' . esc_attr($content_classes) . '">';
        $output .= '<ul class="changelog-items">';

        foreach ($entries as $entry) {
            $type      = isset($entry['type']) ? $entry['type'] : 'Info';
            $text      = isset($entry['text']) ? $entry['text'] : '';
            $type_slug = rup_changelogger_slugify_type($type);

            $output .= '<li class="changelog-item" data-type="' . esc_attr($type_slug) . '">';

            if ($atts['show_labels'] === 'yes') {
                $output .= '<span class="changelog-label ' . esc_attr($type_slug) . '">' . esc_html($type) . '</span>';
            }

            $output .= '<span class="changelog-text">' . esc_html($text) . '</span>';
            $output .= '</li>';
        }

        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</div>';
    }

    $output .= '</div>';
    $output .= '</div>';

    return apply_filters('rup_changelogger_output_html', $output, $versions, $atts);
}

/**
 * -------------------------------------------------------
 * Shortcode
 * -------------------------------------------------------
 */

function rup_changelogger_timeline_shortcode($atts) {
    $atts = shortcode_atts([
        'url'               => '',
        'title'             => '',
        'layout'            => 'timeline',
        'format'            => 'auto',
        'show_date'         => 'yes',
        'show_version'      => 'yes',
        'show_labels'       => 'yes',
        'show_filters'      => 'no',
        'show_summary'      => 'no',
        'collapsible'       => 'no',
        'animate_warnings'  => 'yes',
        'filter'            => '',
        'limit'             => 0,
        'order'             => 'desc',
        'cache_days'        => 7,
        'class'             => '',
    ], $atts, 'rup_changelogger_timeline');

    if (empty($atts['url'])) {
        return '<p>No changelog URL provided.</p>';
    }

    return rup_changelogger_fetch_changelog_data_timeline($atts['url'], $atts);
}
add_shortcode('rup_changelogger_timeline', 'rup_changelogger_timeline_shortcode');

/**
 * -------------------------------------------------------
 * Admin toolbar cache clear
 * -------------------------------------------------------
 */

function rup_changelogger_admin_toolbar($wp_admin_bar) {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    $args = [
        'id'    => 'clear_changelog_cache',
        'title' => 'Clear Changelog Cache',
        'href'  => wp_nonce_url(admin_url('admin-post.php?action=clear_changelog_cache'), 'clear_changelog_cache'),
        'meta'  => ['class' => 'clear-changelog-cache']
    ];

    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'rup_changelogger_admin_toolbar', 100);

function rup_changelogger_clear_all_transients() {
    global $wpdb;

    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_cached_changelog_timeline_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_cached_changelog_timeline_%'");
}

function rup_changelogger_clear_cache() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    check_admin_referer('clear_changelog_cache');

    rup_changelogger_clear_all_transients();

    wp_safe_redirect(wp_get_referer() ?: admin_url());
    exit;
}
add_action('admin_post_clear_changelog_cache', 'rup_changelogger_clear_cache');

/**
 * -------------------------------------------------------
 * Optional URL-based cache clear
 * Example:
 * ?rup_clear_cache=1&key=YOUR_SECRET_KEY
 * -------------------------------------------------------
 */

function rup_changelogger_clear_cache_via_url() {
    $secret_key = apply_filters('rup_changelogger_secret_key', 'YOUR_SECRET_KEY');
    $enabled    = apply_filters('rup_changelogger_enable_public_cache_clear', false);

    if (!$enabled) {
        return;
    }

    if (
        isset($_GET['rup_clear_cache'], $_GET['key']) &&
        $_GET['rup_clear_cache'] === '1' &&
        hash_equals((string) $secret_key, (string) $_GET['key'])
    ) {
        rup_changelogger_clear_all_transients();
        wp_send_json_success(['message' => 'Changelog cache cleared!']);
        exit;
    }
}
add_action('template_redirect', 'rup_changelogger_clear_cache_via_url');

/**
 * -------------------------------------------------------
 * Styles
 * -------------------------------------------------------
 */

function rup_changelogger_enqueue_styles() {
    $colors = rup_changelogger_get_default_label_colors();
    $custom_css = apply_filters('rup_changelogger_custom_css', '');

    echo '<style>
        .rup-changelogger {
            max-width: 800px;
            margin: 0 auto;
        }

        .rup-changelogger-title {
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .rup-changelogger-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .rup-filter-btn,
        .rup-toggle-version {
            cursor: pointer;
            border: 1px solid #ccc;
            background: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
        }

        .rup-filter-btn.active {
            background: #333;
            color: #fff;
            border-color: #333;
        }

        .changelog-timeline {
            position: relative;
            border-left: 3px solid #ddd;
            padding-left: 55px;
        }

        .changelog-entry {
            position: relative;
            margin-bottom: 40px;
            padding-bottom: 12px;
        }

        .changelog-header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            position: relative;
            margin-bottom: 10px;
        }

        .changelog-version-box {
            background: #333;
            color: #fff;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 5px;
            min-width: 60px;
            text-align: center;
            position: absolute;
            left: -85px;
            top: 50%;
            transform: translateY(-50%);
        }

        .changelog-date {
            color: #777;
            font-size: 0.95em;
            font-weight: bold;
            margin-left: 15px;
        }

        .changelog-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 0 12px 25px;
        }

        .changelog-summary-pill {
            background: #f1f1f1;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .changelog-meta {
            margin-left: 25px;
        }

        .changelog-meta.is-collapsed {
            display: none;
        }

        .changelog-items {
            list-style: none;
            padding: 0;
            margin: 0 0 0 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .changelog-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            align-items: start;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
        }

        .changelog-label {
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-flex;
            min-width: 140px;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            line-height: 1.2;
            flex-shrink: 0;
        }

        .changelog-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
            min-width: 0;
        }

        .changelog-label.new { background: ' . esc_attr($colors['New']) . '; }
        .changelog-label.added { background: ' . esc_attr($colors['Added']) . '; }
        .changelog-label.changed { background: ' . esc_attr($colors['Changed']) . '; }
        .changelog-label.updated { background: ' . esc_attr($colors['Updated']) . '; }
        .changelog-label.fixed { background: ' . esc_attr($colors['Fixed']) . '; }
        .changelog-label.hotfix { background: ' . esc_attr($colors['Hotfix']) . '; }
        .changelog-label.tweaked { background: ' . esc_attr($colors['Tweaked']) . '; }
        .changelog-label.improvement { background: ' . esc_attr($colors['Improvement']) . '; }
        .changelog-label.performance { background: ' . esc_attr($colors['Performance']) . '; }
        .changelog-label.security { background: ' . esc_attr($colors['Security']) . '; }
        .changelog-label.deprecated { background: ' . esc_attr($colors['Deprecated']) . '; }
        .changelog-label.removed { background: ' . esc_attr($colors['Removed']) . '; }
        .changelog-label.breaking { background: ' . esc_attr($colors['Breaking']) . '; }
        .changelog-label.compatibility { background: ' . esc_attr($colors['Compatibility']) . '; }
        .changelog-label.experimental { background: ' . esc_attr($colors['Experimental']) . '; }
        .changelog-label.info { background: ' . esc_attr($colors['Info']) . '; }

        .changelog-label.known-issue,
        .changelog-label.warning {
            background: ' . esc_attr($colors['Warning']) . ';
            color: #000;
            border: 2px solid #ff8c00;
            box-shadow: 0 0 8px rgba(255, 140, 0, 0.6);
        }

        @keyframes warningPulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 8px rgba(255, 140, 0, 0.6);
            }
            100% {
                transform: scale(1.02);
                box-shadow: 0 0 12px rgba(255, 140, 0, 0.85);
            }
        }

        .animate-warnings .changelog-label.warning,
        .animate-warnings .changelog-label.known-issue {
            animation: warningPulse 2s infinite alternate ease-in-out;
        }

        /* Cards layout */
        .rup-changelogger.layout-cards .changelog-timeline {
            border-left: none;
            padding-left: 0;
        }

        .rup-changelogger.layout-cards .changelog-entry {
            background: #fff;
            border: 1px solid #e2e2e2;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .rup-changelogger.layout-cards .changelog-header {
            margin-bottom: 14px;
        }

        .rup-changelogger.layout-cards .changelog-version-box {
            position: static;
            transform: none;
            left: auto;
            top: auto;
        }

        .rup-changelogger.layout-cards .changelog-meta,
        .rup-changelogger.layout-cards .changelog-summary {
            margin-left: 0;
        }

        .rup-changelogger.layout-cards .changelog-items {
            margin-left: 0;
        }

        /* Compact layout */
        .rup-changelogger.layout-compact .changelog-timeline {
            border-left: none;
            padding-left: 0;
        }

        .rup-changelogger.layout-compact .changelog-entry {
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e5e5;
        }

        .rup-changelogger.layout-compact .changelog-header {
            gap: 10px;
            margin-bottom: 8px;
        }

        .rup-changelogger.layout-compact .changelog-version-box {
            position: static;
            transform: none;
            left: auto;
            top: auto;
            font-size: 14px;
            padding: 5px 10px;
            min-width: auto;
        }

        .rup-changelogger.layout-compact .changelog-date {
            font-size: 14px;
            margin-left: 0;
        }

        .rup-changelogger.layout-compact .changelog-meta,
        .rup-changelogger.layout-compact .changelog-summary {
            margin-left: 0;
        }

        .rup-changelogger.layout-compact .changelog-items {
            margin-left: 0;
            gap: 8px;
        }

        .rup-changelogger.layout-compact .changelog-item {
            grid-template-columns: 120px 1fr;
        }

        .rup-changelogger.layout-compact .changelog-label {
            min-width: 110px;
            font-size: 12px;
            padding: 6px 10px;
        }

        @media (max-width: 768px) {
            .changelog-timeline {
                border-left: none;
                padding-left: 20px;
            }

            .changelog-version-box {
                position: static;
                transform: none;
                left: auto;
                top: auto;
            }

            .changelog-date {
                margin-left: 0;
            }

            .changelog-items {
                margin-left: 0;
            }

            .changelog-item,
            .rup-changelogger.layout-compact .changelog-item {
                grid-template-columns: 1fr;
            }

            .changelog-label {
                min-width: auto;
                justify-content: flex-start;
            }

            .changelog-summary {
                margin-left: 0;
            }

            .changelog-meta {
                margin-left: 0;
            }
        }

        ' . $custom_css . '
    </style>';
}
add_action('wp_head', 'rup_changelogger_enqueue_styles');

/**
 * -------------------------------------------------------
 * Scripts
 * -------------------------------------------------------
 */

function rup_changelogger_enqueue_inline_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.rup-changelogger').forEach(function(wrapper) {
            const filterButtons = wrapper.querySelectorAll('.rup-filter-btn');

            filterButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');
                    const versionBlocks = wrapper.querySelectorAll('.changelog-entry');

                    versionBlocks.forEach(function(versionBlock) {
                        const items = versionBlock.querySelectorAll('.changelog-item');
                        let visibleCount = 0;

                        items.forEach(function(item) {
                            if (filter === 'all' || item.getAttribute('data-type') === filter) {
                                item.style.display = '';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        versionBlock.style.display = visibleCount > 0 ? '' : 'none';
                    });
                });
            });

            wrapper.querySelectorAll('.rup-toggle-version').forEach(function(toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const targetId = this.getAttribute('aria-controls');
                    const target = wrapper.querySelector('#' + CSS.escape(targetId));

                    if (!target) return;

                    const isCollapsed = target.classList.contains('is-collapsed');
                    target.classList.toggle('is-collapsed');

                    this.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');
                    this.textContent = isCollapsed ? 'Hide' : 'Show';
                });
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'rup_changelogger_enqueue_inline_script');
