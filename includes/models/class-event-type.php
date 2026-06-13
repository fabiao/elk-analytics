<?php

namespace ELKLab\ELKAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model {
    protected $table = 'elk_analytics_event_types';

    protected $fillable = [
        'name',
    ];

    public function events() {
        return $this->hasMany(Event::class);
    }
}
