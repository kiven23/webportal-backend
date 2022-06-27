@extends('layouts.app')

@section('title', 'Overall Pending Chart')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Pendings
      <small>Manage pending monitoring</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('pendings') }}">Pendings</a></li>
      <li class="active">Charts</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              Pending overall chart
            </h3>
          </div>
          <div class="box-body table-responsive">
          	<div id="container" style="height: 400px;"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script type="text/javascript">
	$(function () {
		var data = {!! $pendingCollect !!};
		var data2 = {!! $drilldownCollect !!};
	    $('#container').highcharts({
	        chart: {
            type: 'column',
		        options3d: {
                    enabled: true,
                    alpha: 5,
                    beta: 0,
                    depth: 50
                }
            },
            tooltip: {
	            headerFormat: '<span style="font-size:14px;font-weight:bold;">{series.name}</span><br>',
	            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.percentage:.2f} %</b><br/>',
	            followPointer: true
	        },
	        plotOptions: {
	            series: {
	                borderWidth: 0,
	                dataLabels: {
	                    enabled: true,
	                    format: '{point.y:.f}'
	                }
	            }
	        },
	        title: {
	            text: 'Overall Pending',
	        },
	        xAxis: {
	            categories: []
	        },
	        yAxis: {
                title: {
                    text: "Pending Count"
                }
            },

	        series: [{
	        	name: 'Pending',
				colorByPoint: true,
	            data: data
	        }],
	        drilldown: {
	            series: data2
	        }
	    });
	});
</script>
@endpush
