@extends('layouts.app',['class'=> 'off-canvas-sidebar'])

@section('content')
<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}

</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
am4core.ready(function() {
var hits = [
@foreach($hits as $hit)
    ['{{ $hit->date }}', {{ $hit->cnt }}],
@endforeach
];

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv", am4charts.XYChart);

// Add data
chart.data = generateChartData();

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
dateAxis.renderer.minGridDistance = 50;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "downloads";
series.dataFields.dateX = "date";
series.strokeWidth = 2;
series.minBulletDistance = 10;
series.tooltipText = "{valueY}";
series.tooltip.pointerOrientation = "vertical";
series.tooltip.background.cornerRadius = 20;
series.tooltip.background.fillOpacity = 0.5;
series.tooltip.label.padding(12,12,12,12)

// Add scrollbar
chart.scrollbarX = new am4charts.XYChartScrollbar();
chart.scrollbarX.series.push(series);

// Add cursor
chart.cursor = new am4charts.XYCursor();
chart.cursor.xAxis = dateAxis;
chart.cursor.snapToSeries = series;

function generateChartData() {
    var chartData = [];
    for (var i = 0; i < hits.length; i++) {
        var newDate = new Date(hits[i][0]);
        chartData.push({
            date: newDate,
            downloads: hits[i][1] 
        });
    }
    return chartData;
}

}); // end am4core.ready()

</script>

<div class="container">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
            <div class="card-header card-header-primary"><h4 class="card-title"><a href="/reports" style="color:#fff;">Reports</a> :: {{ $name }} report</h4></div>
            <div class="card-body">
		<div class="form-group row">
		   <div class="col-md-4">
		<form name="get_uploads_report" method="get" action="">
			<select name="collection_id" class="form-control">
			   <option value="">Select Collection</option>
			   @foreach($collection_list as $link)
			     <option value="{{ $link['id'] }}" @if($collection_id == $link['id']) selected @endif>{{ $link['name'] }}</option>
			   @endforeach
			</select>
		   </div>
		   <div class="col-md-4">
			<input type="submit" name="get_report" value="Get Report" class="btn btn-primary">
		   </div>
		</form>
		</div>
                    <div id="chartdiv"></div>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection
