<?php
if ($argc < 7) {
    echo "Usage: php setup-plugin.php <plugin_dir> <plugin_name> <description> <function_prefix> <plugin_slug_underscores> <lowercase_prefix>\n";
    exit(1);
}

$plugin_dir = $argv[1];
$plugin_name = $argv[2];
$description = $argv[3];
$function_prefix = $argv[4];
$plugin_slug_underscores = $argv[5];
$lowercase_prefix = $argv[6];

function replace_in_file($file, $search_replace) {
    $content = file_get_contents($file);
    foreach ($search_replace as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    file_put_contents($file, $content);
}

$search_replace = [
    "My Plugin" => $plugin_name,
    "A lightweight WordPress plugin starter template." => $description,
    "function my_plugin_" => "function {$lowercase_prefix}_",
    "MY_PLUGIN_" => strtoupper($function_prefix) . strtoupper($plugin_slug_underscores) . "_",
    "define('MY_PLUGIN_VERSION'" => "define('{$function_prefix}{$plugin_slug_underscores}_VERSION'",
    "define('MY_PLUGIN_DIR'" => "define('{$function_prefix}{$plugin_slug_underscores}_DIR'",
    "define('MY_PLUGIN_URL'" => "define('{$function_prefix}{$plugin_slug_underscores}_URL'",
    "register_activation_hook(__FILE__, 'my_plugin_activate')" => "register_activation_hook(__FILE__, '{$lowercase_prefix}_activate')",
    "register_deactivation_hook(__FILE__, 'my_plugin_deactivate')" => "register_deactivation_hook(__FILE__, '{$lowercase_prefix}_deactivate')",
    "update_option('my_plugin_activated'" => "update_option('{$lowercase_prefix}_activated'",
    "delete_option('my_plugin_activated'" => "delete_option('{$lowercase_prefix}_activated'"
];

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($plugin_dir));
foreach ($files as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === "php") {
        replace_in_file($file->getPathname(), $search_replace);
    }
}

echo "Plugin files have been successfully updated.\n";
