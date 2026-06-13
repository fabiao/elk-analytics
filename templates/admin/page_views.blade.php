@php
  use ELKLab\ELKAnalytics\Models\Event;
  use ELKLab\ELKAnalytics\Helpers\FilterHelper;
  
  $filter = new FilterHelper('elk-analytics-page-views', $_GET['min_date'] ?? null, $_GET['max_date'] ?? null);

  $data = Event::selectRaw('DATE(created_at) as date, COUNT(DISTINCT user_id) as unique_visitors, COUNT(id) as page_views')
    ->where('event_type_id', $filter->event_type_id)
    ->where('created_at', '>=', $filter->min_date)
    ->where('created_at', '<=', $filter->max_date)
    ->groupBy('date')
    ->orderBy('date', 'asc')
    ->get()
    ->map(function ($day) {
        $day->average_pages_per_visitor = $day->unique_visitors > 0 ? $day->page_views / $day->unique_visitors : 0;
        return $day;
    });
@endphp

<div class="wrap">
  <h1>{!! __('Page Views', 'elk-analytics') !!}</h1>
  <hr>
  @include('admin.partials.filter', ['filter' => $filter])
  <hr>
  @include('admin.partials.page_views_chart', ['filter' => $filter, 'data' => $data])
  <hr>
  @include('admin.partials.page_views_table', ['filter' => $filter, 'data' => $data])
</div>