<?php

namespace ELKLab\ELKAnalytics\Services;

use ELKLab\ELKAnalytics\Models\User;
use ELKLab\ELKAnalytics\Models\Event;
use ELKLab\ELKAnalytics\Models\EventType;
use ELKLab\ELKAnalytics\Helpers\UserAgentHelper;
use ELKLab\ELKAnalytics\Helpers\PostTypesHelper;
use ELKLab\ELKAnalytics\Helpers\PluginsHelper;

class EventsManager {
    private static function registerOrGetUser() {
        $ip         = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $session    = $_COOKIE['PHPSESSID'] ?? null;
        $now        = date('Y-m-d H:i:s');

        $existing_user = User::where('ip', $ip)->where('user_agent', $user_agent)->first();
        if ($existing_user) {
            return $existing_user->id;
        }

        $new_user = User::create([
            'ip'              => $ip,
            'user_agent'      => $user_agent,
            'browser_type'    => UserAgentHelper::getBrowserType(),
            'browser_version' => UserAgentHelper::getBrowserVersion(),
            'os_type'         => UserAgentHelper::getOSType(),
            'os_version'      => UserAgentHelper::getOSVersion(),
            'device'          => UserAgentHelper::getDevice(),
            'country'         => UserAgentHelper::getCountry(),
            'session_id'      => $session,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        return $new_user->id;
    }

    public static function registerPostEvent() {
        if (is_admin()) {
            return null;
        }
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return null;
        }
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return null;
        }

        $user_id    = static::registerOrGetUser();
        $event_type = EventType::where('name', 'page_view')->first();
        $referrer   = $_SERVER['HTTP_REFERER'] ?? null;
        $url        = $_SERVER['REQUEST_URI'];
        $now        = date('Y-m-d H:i:s');

        if (is_front_page()) {
            $event = Event::create([
                'user_id'       => $user_id,
                'event_type_id' => $event_type->id,
                'url'           => '/',
                'referrer'      => $referrer,
                'post_id'       => get_the_ID(),
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            return $event->id;
        }

        if (is_home()) {
            $event = Event::create([
                'user_id'       => $user_id,
                'event_type_id' => $event_type->id,
                'url'           => $url,
                'referrer'      => $referrer,
                'post_id'       => get_option('page_for_posts'),
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            return $event->id;
        }

        if (is_archive() || is_tax() || is_category() || is_tag()) {
            $event = Event::create([
                'user_id'       => $user_id,
                'event_type_id' => $event_type->id,
                'url'           => $url,
                'referrer'      => $referrer,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            return $event->id;
        }

        $post_types = apply_filters('elk_analytics_post_types', PostTypesHelper::getPublicPostTypes());
        if (!is_singular($post_types)) {
            return null;
        }

        $event = Event::create([
            'user_id'       => $user_id,
            'event_type_id' => $event_type->id,
            'url'           => $url,
            'referrer'      => $referrer,
            'post_id'       => get_the_ID(),
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        return $event->id;
    }

    public static function registerFormEvent($contact_form) {
        $user_id    = static::registerOrGetUser();
        $event_type = EventType::where('name', 'contact_form')->first();
        $now        = date('Y-m-d H:i:s');

        $submission = \WPCF7_Submission::get_instance();
        $form_data  = apply_filters('elk_analytics_form_data', $submission->get_posted_data(), $contact_form);

        $event = Event::create([
            'user_id'        => $user_id,
            'event_type_id'  => $event_type->id,
            'event_details'  => json_encode($form_data),
            'url'            => $_SERVER['REQUEST_URI'],
            'referrer'       => $_SERVER['HTTP_REFERER'] ?? null,
            'post_id'        => get_the_ID(),
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        return $event->id;
    }

    public static function handleReservationSubmit() {
        static::registerReservationEvent();
        wp_send_json_success();
    }

    public static function registerReservationEvent() {
        $user_id      = static::registerOrGetUser();
        $event_type   = EventType::where('name', 'reservation_form')->first();
        $now          = date('Y-m-d H:i:s');
        $event_details = apply_filters('elk_analytics_reservation_details', $_POST['event_details']);

        $event = Event::create([
            'user_id'       => $user_id,
            'event_type_id' => $event_type->id,
            'event_details' => json_encode($event_details),
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        return $event->id;
    }
}
