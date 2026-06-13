<?php

namespace ELKLab\ELKAnalytics\Helpers;

use GeoIp2\Database\Reader;

class UserAgentHelper {
    protected static $dbPath = ELK_ANALYTICS_CACHE_DIR . '/GeoLite2-Country.mmdb';

    public static function getBrowserType($user_agent = null) {
        $user_agent = $user_agent ?? $_SERVER['HTTP_USER_AGENT'];
        $browser    = __('Unknown', 'elk-analytics');

        if (preg_match('/MSIE|Trident/i', $user_agent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Edg/i', $user_agent) && preg_match('/Chrome/i', $user_agent)) {
            $browser = 'Edge Chromium';
        } elseif (preg_match('/Edge/i', $user_agent)) {
            $browser = 'Edge Legacy';
        } elseif (preg_match('/Chrome/i', $user_agent) && preg_match('/Safari/i', $user_agent) && !preg_match('/Edg|OPR/i', $user_agent)) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/OPR|Opera/i', $user_agent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Chromium/i', $user_agent)) {
            $browser = 'Chromium';
        } elseif (preg_match('/Chrome/i', $user_agent) && !preg_match('/Edg|OPR/i', $user_agent)) {
            $browser = 'Chromium-based Browser';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/Safari/i', $user_agent) && !preg_match('/Chrome|OPR|Edg/i', $user_agent)) {
            $browser = 'Apple Safari';
        } elseif (preg_match('/Netscape/i', $user_agent)) {
            $browser = 'Netscape';
        } elseif (preg_match('/Brave/i', $user_agent)) {
            $browser = 'Brave';
        } elseif (preg_match('/Vivaldi/i', $user_agent)) {
            $browser = 'Vivaldi';
        }

        return $browser;
    }

    public static function getBrowserVersion($user_agent = null) {
        $user_agent = $user_agent ?? $_SERVER['HTTP_USER_AGENT'];
        $browser    = self::getBrowserType($user_agent);
        $version    = __('Unknown', 'elk-analytics');

        $known_patterns = [
            'Google Chrome'          => 'Chrome',
            'Edge Chromium'          => 'Edg',
            'Edge Legacy'            => 'Edge',
            'Chromium'               => 'Chromium',
            'Chromium-based Browser' => 'Chrome',
            'Mozilla Firefox'        => 'Firefox',
            'Apple Safari'           => 'Version',
            'Opera'                  => 'OPR|Opera',
            'Internet Explorer'      => 'MSIE|Trident',
            'Brave'                  => 'Chrome',
            'Vivaldi'                => 'Vivaldi',
            'Netscape'               => 'Netscape',
        ];

        if (isset($known_patterns[$browser])) {
            $pattern = '#(' . $known_patterns[$browser] . ')[/ ]+([0-9]+(?:\.[0-9]+)?)#';
            if (preg_match($pattern, $user_agent, $matches)) {
                $version = $matches[2];
            }
        }

        return $version;
    }

    public static function getOSType($user_agent = null) {
        $user_agent = $user_agent ?? $_SERVER['HTTP_USER_AGENT'];
        $os         = __('Unknown', 'elk-analytics');

        if (preg_match('/Windows NT|Win/i', $user_agent)) {
            $os = 'Windows';
        } elseif (preg_match('/Macintosh|Mac OS X|Mac OS/i', $user_agent)) {
            $os = 'Mac OS';
        } elseif (preg_match('/Linux/i', $user_agent) && !preg_match('/Android/i', $user_agent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $user_agent)) {
            $os = 'Android';
        } elseif (preg_match('/iPhone|iPad|iOS/i', $user_agent)) {
            $os = 'iOS';
        } elseif (preg_match('/BlackBerry/i', $user_agent)) {
            $os = 'BlackBerry';
        } elseif (preg_match('/Windows Phone/i', $user_agent)) {
            $os = 'Windows Phone';
        } elseif (preg_match('/webOS/i', $user_agent)) {
            $os = 'webOS';
        } elseif (preg_match('/Symbian/i', $user_agent)) {
            $os = 'Symbian';
        } elseif (preg_match('/Chrome OS/i', $user_agent)) {
            $os = 'Chrome OS';
        }

        return $os;
    }

    public static function getOSVersion($user_agent = null) {
        $user_agent = $user_agent ?? $_SERVER['HTTP_USER_AGENT'];
        $os         = self::getOSType($user_agent);
        $version    = __('Unknown', 'elk-analytics');

        switch ($os) {
            case 'Windows':
                $known_versions = [
                    'Windows NT 10.0' => 'Windows 10/11',
                    'Windows NT 6.3'  => 'Windows 8.1',
                    'Windows NT 6.2'  => 'Windows 8',
                    'Windows NT 6.1'  => 'Windows 7',
                    'Windows NT 6.0'  => 'Windows Vista',
                    'Windows NT 5.2'  => 'Windows XP x64',
                    'Windows NT 5.1'  => 'Windows XP',
                    'Windows NT 5.0'  => 'Windows 2000',
                    'Windows NT 4.0'  => 'Windows NT 4.0',
                    'Windows 98'      => 'Windows 98',
                    'Windows 95'      => 'Windows 95',
                    'Windows CE'      => 'Windows CE',
                ];
                $pattern = '#(Windows NT [0-9.]+|Windows 98|Windows 95|Windows CE)#';
                if (preg_match($pattern, $user_agent, $matches)) {
                    $version = $known_versions[$matches[1]] ?? $matches[1];
                }
                break;

            case 'Mac OS':
                if (preg_match('#Mac OS X ([0-9_]+)#', $user_agent, $matches)) {
                    $version = str_replace('_', '.', $matches[1]);
                }
                break;

            case 'Android':
                if (preg_match('#Android ([0-9.]+)#', $user_agent, $matches)) {
                    $version = $matches[1];
                }
                break;

            case 'iOS':
                if (preg_match('#(CPU OS|iPhone OS) ([0-9_]+)#', $user_agent, $matches)) {
                    $version = str_replace('_', '.', $matches[2]);
                }
                break;

            case 'Chrome OS':
                if (preg_match('#CrOS ([0-9.]+)#', $user_agent, $matches)) {
                    $version = $matches[1];
                }
                break;
        }

        return $version;
    }

    public static function getDevice($user_agent = null) {
        $user_agent = $user_agent ?? $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/bot|crawl|spider|slurp|mediapartners|bingpreview|facebookexternalhit|monitoring|googlebot|bingbot|yandexbot|duckduckbot|baiduspider|sogou|exabot|facebot|ia_archiver/i', $user_agent)) {
            return 'Bot';
        } elseif (preg_match('/tablet|ipad|playbook|silk/i', $user_agent) || (preg_match('/android/i', $user_agent) && !preg_match('/mobile/i', $user_agent))) {
            return 'Tablet';
        } elseif (preg_match('/mobile|iphone|ipod|android|blackberry|phone|opera mini|iemobile|windows phone|palm|symbian|nokia/i', $user_agent)) {
            return 'Mobile';
        } elseif (preg_match('/smarttv|googletv|appletv|hbbtv|netcast|viera|dlnadoc|roku|tv/i', $user_agent)) {
            return 'Smart TV';
        } elseif (preg_match('/wearable|watch/i', $user_agent)) {
            return 'Wearable';
        } elseif (preg_match('/console|nintendo|playstation|psp|xbox/i', $user_agent)) {
            return 'Console';
        } else {
            return 'Desktop';
        }
    }

    public static function downloadGeoLiteDatabase() {
        $apiKey = carbon_get_theme_option('elk_analytics_maxmind_api_key');
        if (empty($apiKey)) {
            return false;
        }

        $url          = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key={$apiKey}&suffix=tar.gz";
        $tempFilePath = self::$dbPath . '.tar.gz';

        try {
            $downloaded = file_put_contents($tempFilePath, fopen($url, 'r'));
            if ($downloaded === false) {
                unlink($tempFilePath);
                return false;
            }

            $phar = new \PharData($tempFilePath);
            $phar->extractTo(ELK_ANALYTICS_CACHE_DIR, null, true);
            unlink($tempFilePath);

            $extractedDir = glob(ELK_ANALYTICS_CACHE_DIR . '/GeoLite2-Country_*', GLOB_ONLYDIR);
            if (!empty($extractedDir) && is_dir($extractedDir[0])) {
                $extractedPath = $extractedDir[0] . '/GeoLite2-Country.mmdb';
                if (file_exists($extractedPath)) {
                    rename($extractedPath, self::$dbPath);
                }
                array_map('unlink', glob("{$extractedDir[0]}/*.*"));
                rmdir($extractedDir[0]);
            }

            return file_exists(self::$dbPath);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getCountry($ip_address = null) {
        $ip_address = $ip_address ?? $_SERVER['REMOTE_ADDR'];

        if (!file_exists(self::$dbPath) && !self::downloadGeoLiteDatabase()) {
            return __('Unknown', 'elk-analytics');
        }

        try {
            $reader = new Reader(self::$dbPath);
            $record = $reader->country($ip_address);
            return $record->country->isoCode ?? __('Unknown', 'elk-analytics');
        } catch (\Exception $e) {
            return __('Unknown', 'elk-analytics');
        }
    }

    public static function getCountryName($isoCode) {
        if (empty($isoCode) || strlen($isoCode) !== 2) {
            return __('Unknown', 'elk-analytics');
        }
        $locale      = get_locale();
        $countryName = \Locale::getDisplayRegion('-' . strtoupper($isoCode), $locale);
        return $countryName ?: __('Unknown', 'elk-analytics');
    }
}
