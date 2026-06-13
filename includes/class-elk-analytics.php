<?php

namespace ELKLab\ELKAnalytics;

use ELKLab\ELKAnalytics\Admin\Admin;
use ELKLab\ELKAnalytics\Admin\Options;
use ELKLab\ELKAnalytics\Helpers\PluginsHelper;
use ELKLab\ELKAnalytics\Helpers\PostTypesHelper;
use ELKLab\ELKAnalytics\Helpers\UserAgentHelper;
use ELKLab\ELKAnalytics\Services\EventsManager;

class ElkAnalytics {
    private bool $enable_posts;
    private bool $enable_contact_form;
    private bool $enable_reservations;

    public function __construct() {
        $this->enable_posts = (bool) apply_filters('elk_analytics_enable_posts', true);

        $this->enable_contact_form = (bool) apply_filters(
            'elk_analytics_enable_contact_form',
            PluginsHelper::isPluginActive('contact-form-7/wp-contact-form-7.php')
        );

        $this->enable_reservations = (bool) apply_filters(
            'elk_analytics_enable_reservations',
            PostTypesHelper::postTypeExists('apartment')
        );

        new Admin();
        new Options();

        $this->initCrons();
        $this->registerEventHooks();
    }

    private function initCrons() {
        add_action('elk_analytics_download_geolite_database', [UserAgentHelper::class, 'downloadGeoLiteDatabase']);
        if (!wp_next_scheduled('elk_analytics_download_geolite_database')) {
            wp_schedule_event(time(), 'daily', 'elk_analytics_download_geolite_database');
        }
    }

    private function registerEventHooks() {
        if ($this->enable_posts) {
            add_action('template_redirect', [EventsManager::class, 'registerPostEvent']);
        }

        if ($this->enable_contact_form) {
            add_action('wpcf7_mail_sent', [EventsManager::class, 'registerFormEvent']);
        }

        if ($this->enable_reservations) {
            add_action('wp_ajax_register_form_submit',        [EventsManager::class, 'handleReservationSubmit']);
            add_action('wp_ajax_nopriv_register_form_submit', [EventsManager::class, 'handleReservationSubmit']);
        }
    }
}
