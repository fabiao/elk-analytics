<?php

namespace ELKLab\ELKAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'elk_analytics_users';

    protected $fillable = [
        'ip',
        'user_agent',
        'browser_type',
        'browser_version',
        'os_type',
        'os_version',
        'device',
        'country',
        'session_id',
    ];

    public function events() {
        return $this->hasMany(Event::class);
    }
}
