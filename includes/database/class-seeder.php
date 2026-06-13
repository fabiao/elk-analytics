<?php

namespace ELKLab\ELKAnalytics\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class Seeder {
    public static function seedEventTypes(): void {
        CapsuleManager::boot();

        $event_types = apply_filters('elk_analytics_event_types', ['contact_form', 'reservation_form', 'page_view']);

        foreach ($event_types as $type) {
            $exists = Capsule::table('elk_analytics_event_types')->where('name', $type)->exists();
            if (!$exists) {
                Capsule::table('elk_analytics_event_types')->insert(['name' => $type]);
            }
        }
    }
}
