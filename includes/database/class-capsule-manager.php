<?php

namespace ELKLab\ELKAnalytics\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class CapsuleManager {
    private static bool $booted = false;

    public static function boot(): void {
        if (self::$booted) {
            return;
        }

        global $wpdb;

        $host       = DB_HOST;
        $port       = 3306;
        $unixSocket = '';
        $collation  = 'utf8mb4_unicode_ci';

        if (defined('DB_CHARSET')) {
            if (DB_CHARSET === 'utf8mb4') {
                $collation = 'utf8mb4_unicode_ci';
            } elseif (DB_CHARSET === 'utf8') {
                $collation = 'utf8_unicode_ci';
            } else {
                $collation = DB_CHARSET . '_unicode_ci';
            }
        }

        if (defined('DB_COLLATE') && DB_COLLATE !== '') {
            $collation = DB_COLLATE;
        }

        if (strpos(DB_HOST, ':') !== false) {
            if (preg_match('/^(.*):(\d+)$/', DB_HOST, $matches)) {
                $host = $matches[1];
                $port = defined('DB_PORT') ? (int) DB_PORT : (int) $matches[2];
            } elseif (preg_match('/^(.*):(.+\.sock)$/', DB_HOST, $matches)) {
                $host       = $matches[1];
                $unixSocket = $matches[2];
            }
        }

        $connection = [
            'driver'    => 'mysql',
            'host'      => $host,
            'port'      => $port,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => DB_CHARSET,
            'collation' => $collation,
            'prefix'    => $wpdb->prefix,
        ];

        if ($unixSocket) {
            $connection['unix_socket'] = $unixSocket;
        }

        $capsule = new Capsule();
        $capsule->addConnection($connection);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$booted = true;
    }
}
