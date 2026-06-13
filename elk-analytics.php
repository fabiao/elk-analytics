<?php
/**
 * Plugin Name: ELK Analytics
 * Description: A simple yet powerful analytics plugin for WordPress, providing easy-to-read insights and visualizations to help you monitor website performance.
 * Version: 1.0
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * Tested up to: 6.6.2
 * Author: Gabriele Coquillard @ ELK-Lab
 * Author URI: https://www.elk-lab.com
 * Text Domain: elk-analytics
 * Domain Path: /languages
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Tags: analytics, tracking, website analytics, events, insights, visualization, data visualization
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ELK_ANALYTICS_VERSION',           '1.0.0');
define('ELK_ANALYTICS_PLUGIN_FILE',       __FILE__);
define('ELK_ANALYTICS_PLUGIN_DIR',        plugin_dir_path(__FILE__));
define('ELK_ANALYTICS_PLUGIN_ASSETS_DIR', ELK_ANALYTICS_PLUGIN_DIR . 'assets/');
define('ELK_ANALYTICS_PLUGIN_TEMPLATES_DIR', ELK_ANALYTICS_PLUGIN_DIR . 'templates/');
define('ELK_ANALYTICS_CACHE_DIR',         WP_CONTENT_DIR . '/uploads/elk-analytics-cache');
define('ELK_ANALYTICS_PLUGIN_URL',        plugin_dir_url(__FILE__));
define('ELK_ANALYTICS_PLUGIN_BASENAME',   plugin_basename(__FILE__));

require_once ELK_ANALYTICS_PLUGIN_DIR . 'vendor/autoload.php';

register_activation_hook(ELK_ANALYTICS_PLUGIN_FILE, ['ELKLab\ELKAnalytics\Database\Migrations', 'run']);
register_activation_hook(ELK_ANALYTICS_PLUGIN_FILE, ['ELKLab\ELKAnalytics\Database\Seeder',     'seedEventTypes']);
register_activation_hook(ELK_ANALYTICS_PLUGIN_FILE, ['ELKLab\ELKAnalytics\Admin\Admin',         'registerRoles']);
register_activation_hook(ELK_ANALYTICS_PLUGIN_FILE, ['ELKLab\ELKAnalytics\Admin\Admin',         'registerCapabilities']);

add_action('plugins_loaded', function () {
    load_plugin_textdomain('elk-analytics', false, dirname(ELK_ANALYTICS_PLUGIN_BASENAME) . '/languages');
    \ELKLab\ELKAnalytics\Database\CapsuleManager::boot();
    \ELKLab\ELKAnalytics\Database\Migrations::run();
    new \ELKLab\ELKAnalytics\ElkAnalytics();
});
