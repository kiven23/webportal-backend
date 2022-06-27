@extends('layouts.app')

@section('title', 'Pending Charts')

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
      @if (\Auth::user()->branch->id != $branch_id)
				<li><a href="{{ route('pending.index_as', ['id' => $branch_id]) }}">{{ $branch_name }}</a></li>
			@endif
		  <li class="active">Charts</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Pending chart
            </h3>
          </div>
          <div class="box-body">
          	<div class="row">
							<div class="col-md-6">
								<form class="form" action="{{ route('pending.branch.filtered_chart', ['id' => $branch_id]) }}" method="post">
									{{ csrf_field() }}
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<div class="columns columns-right pull-left">
													<input type="text" id="filter-date" name="filterdate" class="form-control">
													<script type="text/javascript">
														$(function () {
															$('#filter-date').datetimepicker({
																timepicker:'false',
																format:'Y-m-d'
															});
														});
													</script>
												</div>
												<div class="pull-left search">
													<button class="btn btn-primary">
														<i class="fa fa-filter"></i>
														Filter
													</button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="col-md-6">
								<div class="btn-group pull-right" role="group" aria-label="Basic example">
								  <a href="{{ route('pending.branch.chart', ['id' => $branch_id]) }}" class="btn btn-info">Overall</a>
									@if ($filterdate1 !== '')
										<a href="{{ route('pending.branch.breakdown', ['id' => $branch_id, 'date' => $filterdate1]) }}" class="btn btn-info">Show Breakdown</a>
									@else
										<a href="javascript:void();" class="btn btn-info disabled">Show Breakdown</a>
									@endif
								</div>
							</div>

							<div class="col-md-12">
								<div id="container" style="height: 400px"></div>

								<div class="section-classic">
									<p>
										<strong>NOTE: </strong>
										The <code>SHOW BREAKDOWN</code> button will be enabled when you filter the date of your previous pending. This <code>button</code> will show the breakdown of your pending(s) as of the date you choose and you can re-add the pending you want to add to your current pending(s).
									</p>
								</div>
							</div>
						</div>
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
		var branch_name = {!! \App\Branch::where('id', $branch_id)->pluck('name') !!};
		var filterdate = {!! $filterdate2 !!};
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
	            text: 'Pending History (' + filterdate['date'] + ')',
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
	        	name: branch_name,
				colorByPoint: true,
	            data: data
	        }]
	    });
	});
</script>
@endpush
