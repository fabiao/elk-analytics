<?php

namespace ELKLab\ELKAnalytics\Admin;

use ELKLab\ELKAnalytics\Helpers\TemplateHelper;

class Admin {
    public function __construct() {
        $this->addMenuPage();
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScriptsAndStyles']);
    }

    public static function registerRoles() {
        if (!get_role('elk_analytics_manager')) {
            add_role('elk_analytics_manager', __('Analytics Manager', 'elk-analytics'), [
                'read' => true,
            ]);
        }
        if (!get_role('elk_analytics_user')) {
            add_role('elk_analytics_user', __('Analytics User', 'elk-analytics'), [
                'read' => true,
            ]);
        }
    }

    public static function registerCapabilities() {
        $read_roles  = apply_filters('elk_analytics_read_roles', ['administrator', 'editor', 'elk_analytics_manager', 'elk_analytics_user']);
        $write_roles = apply_filters('elk_analytics_write_roles', ['administrator', 'elk_analytics_manager']);

        foreach ($read_roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->add_cap('read_elk_analytics');
            }
        }

        foreach ($write_roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->add_cap('write_elk_analytics');
            }
        }
    }

    private function addMenuPage() {
        add_action('admin_menu', function () {
            add_menu_page(
                __('ELK Analytics', 'elk-analytics'),
                __('ELK Analytics', 'elk-analytics'),
                'read_elk_analytics',
                'elk-analytics',
                [$this, 'renderLandingPage'],
                'dashicons-chart-area',
                80
            );

            add_submenu_page(
                'elk-analytics',
                __('Page views', 'elk-analytics'),
                __('Page views', 'elk-analytics'),
                'read_elk_analytics',
                'elk-analytics-page-views',
                [$this, 'renderPageViewsPage']
            );

            add_submenu_page(
                'elk-analytics',
                __('Devices', 'elk-analytics'),
                __('Devices', 'elk-analytics'),
                'read_elk_analytics',
                'elk-analytics-devices',
                [$this, 'renderDevicesPage']
            );

            add_submenu_page(
                'elk-analytics',
                __('Pages', 'elk-analytics'),
                __('Pages', 'elk-analytics'),
                'read_elk_analytics',
                'elk-analytics-pages',
                [$this, 'renderPagesPage']
            );
        });
    }

    public function enqueueAdminScriptsAndStyles() {
        $screen = get_current_screen();

        $data_screens = [
            'elk-analytics_page_elk-analytics-page-views',
            'elk-analytics_page_elk-analytics-devices',
            'elk-analytics_page_elk-analytics-pages',
        ];

        if (!in_array($screen->id, $data_screens, true)) {
            return;
        }

        wp_enqueue_script('luxon',               ELK_ANALYTICS_PLUGIN_URL . 'assets/js/luxon.min.js',                      ['jquery'],                             '3.5.0', true);
        wp_enqueue_script('jspdf',               ELK_ANALYTICS_PLUGIN_URL . 'assets/js/jspdf.umd.min.js',                  ['jquery'],                             '2.5.2', true);
        wp_enqueue_script('jspdf-autotable',     ELK_ANALYTICS_PLUGIN_URL . 'assets/js/jspdf.plugin.autotable.min.js',     ['jquery', 'jspdf'],                    '3.8.4', true);
        wp_enqueue_script('sheetjs',             ELK_ANALYTICS_PLUGIN_URL . 'assets/js/xlsx.full.min.js',                  ['jquery'],                             '0.18.5', true);
        wp_enqueue_script('tabulator',           ELK_ANALYTICS_PLUGIN_URL . 'assets/js/tabulator.min.js',                  ['jquery', 'luxon', 'jspdf', 'jspdf-autotable', 'sheetjs'], '6.3.0', true);
        wp_enqueue_script('chartjs',             ELK_ANALYTICS_PLUGIN_URL . 'assets/js/chart.umd.min.js',                  ['jquery'],                             '4.4.6', true);
        wp_enqueue_script('chartjs-datalabels',  ELK_ANALYTICS_PLUGIN_URL . 'assets/js/chartjs-plugin-datalabels.min.js',  ['jquery', 'chartjs'],                  '2.2.0', true);

        wp_enqueue_style('tabulator', ELK_ANALYTICS_PLUGIN_URL . 'assets/css/tabulator.min.css', [], '6.3.0');
        wp_enqueue_style('elk-grid',  ELK_ANALYTICS_PLUGIN_URL . 'assets/css/grid.css',          [], '0.0.1');
    }

    public function renderLandingPage() {
        echo TemplateHelper::render('admin.landing');
    }

    public function renderPageViewsPage() {
        echo TemplateHelper::render('admin.page_views');
    }

    public function renderDevicesPage() {
        echo TemplateHelper::render('admin.devices');
    }

    public function renderPagesPage() {
        echo TemplateHelper::render('admin.pages');
    }
}
