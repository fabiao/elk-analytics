<?php

namespace ELKLab\ELKAnalytics\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class Migrations {
    const DB_VERSION = '1';

    public static function run(): void {
        CapsuleManager::boot();

        if (get_option('elk_analytics_db_version') === self::DB_VERSION) {
            return;
        }

        if (!Capsule::schema()->hasTable('elk_analytics_users')) {
            Capsule::schema()->create('elk_analytics_users', function ($table) {
                $table->id();
                $table->string('ip');
                $table->string('user_agent');
                $table->string('browser_type')->nullable();
                $table->string('browser_version')->nullable();
                $table->string('os_type')->nullable();
                $table->string('os_version')->nullable();
                $table->string('device')->nullable();
                $table->string('country')->nullable();
                $table->string('session_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Capsule::schema()->hasTable('elk_analytics_event_types')) {
            Capsule::schema()->create('elk_analytics_event_types', function ($table) {
                $table->id();
                $table->string('name');
            });
        }

        if (!Capsule::schema()->hasTable('elk_analytics_events')) {
            Capsule::schema()->create('elk_analytics_events', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained('elk_analytics_users');
                $table->foreignId('event_type_id')->constrained('elk_analytics_event_types');
                $table->text('event_details')->nullable();
                $table->string('url')->nullable();
                $table->string('referrer')->nullable();
                $table->string('post_id')->nullable();
                $table->timestamps();
            });
        }

        update_option('elk_analytics_db_version', self::DB_VERSION);
    }
}
