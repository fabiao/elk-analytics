<?php

namespace ELKLab\ELKAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    protected $table = 'elk_analytics_events';

    protected $fillable = [
        'user_id',
        'event_type_id',
        'event_details',
        'url',
        'referrer',
        'post_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function eventType() {
        return $this->belongsTo(EventType::class);
    }
}
