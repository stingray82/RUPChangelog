<?php
if (!defined('ABSPATH')) {
    exit;
}

function rup_changelogger_fetch_changelog_data_timeline($url) {
    $transient_key = 'cached_changelog_timeline_' . md5($url);

    $cached_data = get_transient($transient_key);
    if ($cached_data) {
        return $cached_data;
    }

    // Fetch content while faking a browser request
    $response = wp_remote_get($url, [
        'timeout' => 10,
        'redirection' => 5,
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36'
        ]
    ]);

    if (is_wp_error($response)) {
        return '<p style="color:red;">Error fetching changelog: ' . esc_html($response->get_error_message()) . '</p>';
    }

    $changelog_text = wp_remote_retrieve_body($response);
    if (!$changelog_text) {
        return '<p style="color:red;">No changelog data available.</p>';
    }

    // Handle Pastebin HTML response
    if (strpos(strtolower($changelog_text), '<html') !== false) {
        preg_match('/<pre.*?>(.*?)<\/pre>/is', $changelog_text, $matches);
        if (!empty($matches[1])) {
            $changelog_text = html_entity_decode(trim($matches[1]));
        } else {
            return '<p style="color:red;">Could not extract raw changelog text from the response.</p>';
        }
    }

    // Convert to UTF-8
    $changelog_text = mb_convert_encoding($changelog_text, 'UTF-8', 'auto');

    $changelog = rup_changelogger_parse_changelog_timeline($changelog_text);

    $cache_duration = apply_filters('rup_changelogger_cache_duration', 7 * DAY_IN_SECONDS);
    set_transient($transient_key, $changelog, $cache_duration);

    return $changelog;
}

function rup_changelogger_parse_changelog_timeline($text) {
    $lines = explode("\n", $text);
    $output = '<div class="changelog-timeline">';

    $version = '';
    $date = '';
    $entries = [];

    foreach ($lines as $line) {
        if (preg_match('/= ([\d\.]+)(?: \((.*?)\))? =/', $line, $matches)) {
            if (!empty($entries)) {
                $output .= rup_changelogger_format_changelog_entry_timeline($version, $date, $entries);
                $entries = [];
            }
            $version = $matches[1];
            $date = isset($matches[2]) ? $matches[2] : '';
        } elseif (preg_match('/(New|Updated|Fixed|Tweaked|Improvement|Security|Deprecated|Warning): (.+)/i', $line, $matches)) {
            $entries[] = ['type' => ucfirst(strtolower($matches[1])), 'text' => $matches[2]];
        }
    }

    if (!empty($entries)) {
        $output .= rup_changelogger_format_changelog_entry_timeline($version, $date, $entries);
    }

    $output .= '</div>';
    return $output;
}

function rup_changelogger_format_changelog_entry_timeline($version, $date, $entries) {
    $type_colors = apply_filters('rup_changelogger_label_colors', [
        'New' => '#28a745',
        'Updated' => '#343a40',
        'Fixed' => '#dc3545',
        'Tweaked' => '#007bff',
        'Improvement' => '#6f42c1',
        'Deprecated' => '#ff5733',
        'Security' => '#e83e8c',
        'Warning' => '#ffc107'
    ]);

    $output = "<div class='changelog-entry'>
        <div class='changelog-header'>
            <div class='changelog-version-box'>{$version}</div>
            <div class='changelog-date'>{$date}</div>
        </div>
        <div class='changelog-meta'>
            <ul class='changelog-items'>";

    foreach ($entries as $entry) {
        $color = isset($type_colors[$entry['type']]) ? $type_colors[$entry['type']] : '#6c757d';
        $label_class = 'changelog-label ' . strtolower($entry['type']);
        $output .= "<li><span class='{$label_class}'>{$entry['type']}</span> {$entry['text']}</li>";
    }

    $output .= '</ul></div></div>';
    return $output;
}

function rup_changelogger_timeline_shortcode($atts) {
    $atts = shortcode_atts(['url' => ''], $atts);
    if (empty($atts['url'])) {
        return 'No changelog URL provided.';
    }
    return rup_changelogger_fetch_changelog_data_timeline($atts['url']);
}
add_shortcode('rup_changelogger_timeline', 'rup_changelogger_timeline_shortcode');

function rup_changelogger_admin_toolbar($wp_admin_bar) {
    if (!is_admin()) return;
    $args = [
        'id'    => 'clear_changelog_cache',
        'title' => 'Clear Changelog Cache',
        'href'  => wp_nonce_url(admin_url('admin-post.php?action=clear_changelog_cache'), 'clear_changelog_cache'),
        'meta'  => ['class' => 'clear-changelog-cache']
    ];
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'rup_changelogger_admin_toolbar', 100);

function rup_changelogger_clear_cache() {
    check_admin_referer('clear_changelog_cache');

    global $wpdb;

    // Clear all transients that match the changelog pattern
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_cached_changelog_timeline_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_cached_changelog_timeline_%'");

    // Redirect back with a success message
    wp_redirect($_SERVER['HTTP_REFERER']);
    exit;
}
add_action('admin_post_clear_changelog_cache', 'rup_changelogger_clear_cache');


function rup_changelogger_enqueue_styles() {
    $custom_css = apply_filters('rup_changelogger_custom_css', '');
    echo '<style>
        .changelog-timeline {
            position: relative;
            border-left: 3px solid #ddd;
            padding-left: 55px;
            max-width: 700px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
    .changelog-timeline {
        border-left: none;
    }
}

        
        .changelog-entry {
            position: relative;
            margin-bottom: 50px;
            padding-bottom: 20px;
        }
        
        .changelog-header {
            display: flex;
            align-items: center;
            position: relative;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        
        .changelog-version-box {
            background: #333;
            color: white;
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
            font-size: 0.9em;
            font-weight: bold;
            margin-left: 15px;
            white-space: nowrap;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }

        .changelog-meta {
            margin-left: 25px;
        }
        
        .changelog-items {
            list-style: none;
            padding: 0;
            margin-left: 55px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .changelog-items li {
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            width: 100%;
            gap: 10px;
        }

        .changelog-label {
    color: white;
    padding: 8px 14px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-flex;
    min-width: 140px;
    text-align: center;
    margin-right: 10px;
    flex-shrink: 0;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    line-height: 1.2;
}

/* Ensure each label type gets proper coloring */
.changelog-label.new { background: #28a745 !important; }
.changelog-label.updated { background: #343a40 !important; }
.changelog-label.fixed { background: #dc3545 !important; }
.changelog-label.tweaked { background: #007bff !important; }
.changelog-label.improvement { background: #6f42c1 !important; }
.changelog-label.deprecated { background: #ff5733 !important; }
.changelog-label.security { background: #e83e8c !important; }

/* 🔥 Fix Warning Label Styling */
.changelog-label.warning {
    background: #ffc107 !important;
    color: #000 !important;
    font-weight: bold;
    text-transform: uppercase;
    border: 3px solid #ff8c00 !important;
    padding: 12px 18px !important;
    box-shadow: 0px 0px 12px rgba(255, 140, 0, 0.8) !important;
    font-size: 16px !important;
    letter-spacing: 1px !important;
    text-align: center !important;
    display: inline-block !important;
    min-width: 160px !important;
}

/* 🔥 Add Optional Pulse Animation for Warning */
@keyframes warningPulse {
    0% {
        transform: scale(1);
        box-shadow: 0px 0px 12px rgba(255, 140, 0, 0.8);
    }
    100% {
        transform: scale(1.05);
        box-shadow: 0px 0px 20px rgba(255, 140, 0, 1);
    }
}

.changelog-label.warning {
    animation: warningPulse 1.5s infinite alternate ease-in-out;
}


        .changelog-text {
            flex: 1;
            min-width: 250px;
            word-wrap: break-word;
            display: flex;
            align-items: center;
        }

        /* ✅ If text is too long, it starts beside the label and then wraps below */
        .changelog-items li {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: start;
            gap: 10px;
        }

        .changelog-label {
            justify-self: start;
        }

        .changelog-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ✅ Forces alignment so labels & text stay inline */
        .changelog-items li:not(:last-child) {
            border-bottom: 1px dashed #ddd;
            padding-bottom: 8px;
        }

        /* ✅ Mobile Improvements */
        @media (max-width: 768px) {
            .changelog-timeline {
                padding-left: 45px;
                max-width: 100%;
            }

            .changelog-version-box {
                left: -70px;
                padding: 6px 10px;
            }

            .changelog-date {
                font-size: 0.85em;
                margin-left: 18px;
            }

            .changelog-label {
                min-width: auto;
                padding: 6px 10px;
                font-size: 13px;
            }

            .changelog-items {
                margin-left: 40px;
            }

            /* ✅ Adjust grid for mobile */
            .changelog-items li {
                grid-template-columns: 1fr;
            }

            .changelog-label {
                display: inline-block;
                width: auto;
                text-align: left;
            }
        }

        @media (max-width: 480px) {
            .changelog-version-box {
                left: -55px;
            }

            .changelog-date {
                font-size: 0.8em;
                margin-left: 22px;
            }

            .changelog-label {
                font-size: 12px;
                padding: 5px 8px;
            }

            .changelog-items li {
                flex-direction: column;
                align-items: flex-start;
            }

            .changelog-label {
                width: auto;
                display: block;
                margin-bottom: 5px;
            }
        }

       /* 🔥 Adjusted Warning Label Styling */
.changelog-label.warning {
    background: #ffc107 !important; /* Bright Yellow */
    color: #000 !important; /* Black text for contrast */
    font-weight: bold;
    text-transform: uppercase;
    border: 2px solid #ff8c00 !important; /* Less prominent border */
    padding: 8px 12px !important; /* Reduce padding for consistency */
    box-shadow: 0px 0px 8px rgba(255, 140, 0, 0.6) !important; /* Softer glow */
    font-size: 14px !important; /* Match other labels */
    letter-spacing: 0.5px !important; /* Less aggressive spacing */
    text-align: center !important;
    display: inline-flex !important;
    min-width: 140px !important; /* Match others */
}

/* 🔥 Softer Pulse Effect */
@keyframes warningPulse {
    0% {
        transform: scale(1);
        box-shadow: 0px 0px 8px rgba(255, 140, 0, 0.6);
    }
    100% {
        transform: scale(1.02);
        box-shadow: 0px 0px 12px rgba(255, 140, 0, 0.8);
    }
}

.changelog-label.warning {
    animation: warningPulse 2s infinite alternate ease-in-out;
}



    ' . $custom_css . '</style>';
}
add_action('wp_head', 'rup_changelogger_enqueue_styles');

/* Added a Filter Can be Triggered by doing the following and applying a new key

add_filter('rup_changelogger_secret_key', function($key) {
    return 'MY_NEW_SECRET_KEY';
});
*/

function rup_changelogger_clear_cache_via_url() {
    // Allow filtering of the secret key
    $secret_key = apply_filters('rup_changelogger_secret_key', 'YOUR_SECRET_KEY');

    // Only trigger if the key is present and correct
    if (isset($_GET['key']) && $_GET['key'] === $secret_key) {
        global $wpdb;

        // Clear all transients that match the changelog pattern
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_cached_changelog_timeline_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_cached_changelog_timeline_%'");

        // Send a JSON response and terminate the script properly
        wp_send_json_success(['message' => 'Changelog cache cleared!']);
        exit; // Ensure the script terminates after sending the response
    }
}
add_action('template_redirect', 'rup_changelogger_clear_cache_via_url');


?>
