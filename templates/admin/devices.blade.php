@php
  use ELKLab\ELKAnalytics\Models\Event;
  use ELKLab\ELKAnalytics\Models\User;
  use ELKLab\ELKAnalytics\Helpers\FilterHelper;
  
  $filter = new FilterHelper('elk-analytics-devices', $_GET['min_date'] ?? null, $_GET['max_date'] ?? null);

  $data = User::where('created_at', '>=', $filter->min_date)
    ->where('created_at', '<=', $filter->max_date)
    ->get();
@endphp

<div class="wrap">
  <h1>{!! __('Devices', 'elk-analytics') !!}</h1>
  <hr>
  @include('admin.partials.filter', ['filter' => $filter])
  <hr>
  @include('admin.partials.devices_chart', ['filter' => $filter])
</div>
