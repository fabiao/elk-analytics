@php
  use ELKLab\ELKAnalytics\Models\Event;
  use ELKLab\ELKAnalytics\Models\EventType;
  use ELKLab\ELKAnalytics\Helpers\FilterHelper;
  
  $filter = new FilterHelper('elk-analytics-pages', $_GET['min_date'] ?? null, $_GET['max_date'] ?? null);

  $data = Event::selectRaw('post_id, COUNT(*) as page_views')
    ->where('event_type_id', EventType::where('name', 'page_view')->value('id'))
    ->where('created_at', '>=', $filter->min_date)
    ->where('created_at', '<=', $filter->max_date)
    ->groupBy('post_id')
    ->orderBy('page_views', 'desc')
    ->get()
    ->map(function ($event) {
        $post = get_post($event->post_id);
        return [
            'post_id' => $event->post_id,
            'title' => $post ? $post->post_title : __('Unknown', 'elk-analytics'),
            'type' => $post ? get_post_type($post) : __('Unknown', 'elk-analytics'),
            'permalink' => $post ? get_permalink($post) : '#',
            'page_views' => $event->page_views,
        ];
    });
@endphp

<div class="wrap">
  <h1>{!! __('Pages', 'elk-analytics') !!}</h1>
  <hr>
  @include('admin.partials.filter', ['filter' => $filter])
  <hr>
  @include('admin.partials.pages_table', ['filter' => $filter, 'data' => $data])
</div>