<?php

namespace ELKLab\ELKAnalytics\Helpers;

class PluginsHelper {
    public static function getPluginsDetails() {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return get_plugins();
    }

    public static function getPlugins() {
        return array_keys(self::getPluginsDetails());
    }

    public static function getActivePlugins() {
        return get_option('active_plugins');
    }

    public static function getInactivePlugins() {
        $plugins       = self::getPluginsDetails();
        $activePlugins = self::getActivePlugins();
        return array_keys(array_diff_key($plugins, array_flip($activePlugins)));
    }

    public static function getPluginData($pluginName) {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return get_plugin_data(WP_PLUGIN_DIR . '/' . $pluginName);
    }

    public static function isPluginActive($pluginName) {
        return in_array($pluginName, static::getActivePlugins());
    }
}
