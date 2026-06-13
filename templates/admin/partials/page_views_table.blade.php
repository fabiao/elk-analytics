@php
  use ELKLab\ELKAnalytics\Models\EventType;
  use ELKLab\ELKAnalytics\Models\Event;
  use ELKLab\ELKAnalytics\Helpers\FilterHelper;
  use Carbon\Carbon;

  $wp_locale = get_locale();
@endphp

<div id="page-views-table"></div>

<hr>

<button id="export-csv" class="button button-secondary">{!! __('Export CSV', 'elk-analytics') !!}</button>
<button id="export-xlsx" class="button button-secondary">{!! __('Export XLSX', 'elk-analytics') !!}</button>
<button id="export-pdf" class="button button-secondary">{!! __('Export PDF', 'elk-analytics') !!}</button>

<script>
  jQuery(function($) {
    const locale = "{!! esc_js(substr($wp_locale, 0, 2)); !!}";

    const data = @json($data).map(item => {
      item.date = luxon.DateTime.fromISO(item.date).setLocale(locale);
      return item;
    });    

    let exportType = "";

    const table = new Tabulator("#page-views-table", {
      data: data,
      layout: "fitColumns",
      initialSort: [{ column: "date", dir: "desc" }],
      columns: [
        {
          title: "{!! __('Date', 'elk-analytics') !!}", 
          field: "date", 
          sorter: "datetime", 
          hozAlign: "center",
          formatter: "datetime",
          formatterParams: {
            outputFormat: 'd LLL',
            invalidPlaceholder: "",
            locale: locale
          },
          sorterParams: {
            format: "yyyy-MM-dd",
            alignEmptyValues: "bottom"
          },
          accessorDownload: function(value) {
            if (exportType === "csv") {
              return value.toFormat("yyyyMMdd");
            } else if (exportType === "xlsx") {
              return value.toJSDate();
            } else if (exportType === "pdf") {
              return value.toFormat("d LLL");
            }
            return value;
          }
        },
        {title: "{!! __('Unique visitors', 'elk-analytics') !!}", field: "unique_visitors", sorter: "number", hozAlign: "center"},
        {title: "{!! __('Page views', 'elk-analytics') !!}", field: "page_views", sorter: "number", hozAlign: "center"},
        {title: "{!! __('Average Pages per Visitor', 'elk-analytics') !!}", field: "average_pages_per_visitor", sorter: "number", hozAlign: "center", formatter: "money", formatterParams: {precision: 2}}
      ]
    });

    document.getElementById("export-csv").addEventListener("click", () => {
      exportType = "csv";
      table.download("csv", "elk_analytics_data.csv", { delimiter: ",", bom: true });
    });

    document.getElementById("export-xlsx").addEventListener("click", () => {
      exportType = "xlsx";
      table.download("xlsx", "elk_analytics_data.xlsx", { sheetName: "{!! __('ELK Analytics Data', 'elk-analytics') !!}" });
    });

    document.getElementById("export-pdf").addEventListener("click", () => {
      exportType = "pdf";
      table.download("pdf", "elk_analytics_data.pdf", {
        orientation: "portrait",
        title: "{!! __('ELK Analytics Data', 'elk-analytics') !!}"
      });
    });
  });
</script>
