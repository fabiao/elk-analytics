@php
  use ELKLab\ELKAnalytics\Models\Event;
  use ELKLab\ELKAnalytics\Helpers\FilterHelper;
  use Carbon\Carbon;

  $wp_locale = get_locale();
@endphp

<div style="position:relative;width:100%;aspect-ratio:2;max-height:600px;">
  <canvas id="analyticsChart"></canvas>
</div>

<script>
  jQuery(function($) {
    const locale = "<?= esc_js(substr($wp_locale, 0, 2)); ?>";

    const data = @json($data).map(item => {
      item.date = luxon.DateTime.fromISO(item.date).setLocale(locale).toFormat("d LLL");
      return item;
    });

    const labels = data.map(item => item.date);
    const uniqueVisitors = data.map(item => item.unique_visitors);
    const pageViews = data.map(item => item.page_views);
    const averagePagesPerVisitor = data.map(item => item.average_pages_per_visitor);

    const ctx = document.getElementById('analyticsChart').getContext('2d');
    const analyticsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: "{!! __('Total Page Views', 'elk-analytics') !!}",
            data: pageViews,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            fill: false,
            tension: 0.1,
          },
          {
            label: "{!! __('Unique Visitors', 'elk-analytics') !!}",
            data: uniqueVisitors,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: false,
            tension: 0.1,
          },
          {
            label: "{!! __('Average Pages per Visitor', 'elk-analytics') !!}",
            data: averagePagesPerVisitor,
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: false,
            tension: 0.1,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: "{!! __('Page Views & Visitor Analytics', 'elk-analytics') !!}"
          }
        },
        scales: {
          x: {
            title: {
              display: true,
              text: "{!! __('Date', 'elk-analytics') !!}"
            }
          },
          y: {
            title: {
              display: true,
              text: "{!! __('Count', 'elk-analytics') !!}"
            },
            beginAtZero: true
          }
        }
      }
    });
  });
</script>
