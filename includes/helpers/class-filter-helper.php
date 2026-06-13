<?php

namespace ELKLab\ELKAnalytics\Helpers;

use Carbon\Carbon;
use ELKLab\ELKAnalytics\Models\EventType;
use ELKLab\ELKAnalytics\Models\Event;

class FilterHelper {
    public $page;
    public $min_date;
    public $max_date;
    public $filter_min_date;
    public $filter_max_date;
    public $event_type_id;

    public function __construct($page, $min_date = null, $max_date = null) {
        $this->page = $page;

        $this->event_type_id = EventType::where('name', 'page_view')->value('id');

        $first_event_date       = $this->getFirstEventDate();
        $this->filter_min_date  = $first_event_date ? $first_event_date->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $this->filter_max_date  = Carbon::now()->endOfDay();

        $this->min_date = self::parseDate($min_date, $this->filter_min_date)->startOfDay();
        $this->max_date = self::parseDate($max_date, $this->filter_max_date)->endOfDay();

        $this->min_date = $this->min_date->lt($this->filter_min_date) ? $this->filter_min_date : $this->min_date;
        $this->max_date = $this->max_date->gt($this->filter_max_date) ? $this->filter_max_date : $this->max_date;
    }

    private function getFirstEventDate() {
        $first_event = Event::where('event_type_id', $this->event_type_id)->orderBy('created_at', 'asc')->first();
        return $first_event ? $first_event->created_at : null;
    }

    private static function parseDate($date, $default) {
        if ($date instanceof Carbon) {
            return $date;
        } elseif (is_string($date)) {
            $output = Carbon::createFromFormat('Y-m-d', $date);
            return $output instanceof Carbon ? $output : $default;
        }
        return $default;
    }
}
