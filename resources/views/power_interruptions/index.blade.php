@extends('layouts.app')

@section('title', 'Power Interruption Logs')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Power Interruptions
      <small>Manage power interruptions</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Power Interruptions</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Power interruption lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('power_interruption.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;
                  ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')

						<table class="table table-bordered table-hover" id="power-interruption-table">
							<thead>
								<tr>
									<th>Branch</th>
									<th>Logged By</th>
									<th>Reported By</th>
									<th>Position</th>
									<th>From</th>
									<th>To</th>
									<th>Total Hours</th>
									<th>Remarks</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($power_interruptions as $index => $power_interruption)
									<tr>
										<td>{{ $power_interruption->branch['name'] }}</td>
										<td>{{ $power_interruption->user->first_name }} {{ $power_interruption->user->last_name }}</td>
										<td>{{ $power_interruption->reported_by_name }}</td>
										<td>{{ $power_interruption->reported_by_position }}</td>
										<td>{{ $power_interruption->datetime_from }}</td>
										<td>{{ $power_interruption->datetime_to }}</td>
										<td>
											<?php

												$startDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $power_interruption->datetime_from);
												$endDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $power_interruption->datetime_to ? $power_interruption->datetime_to : $power_interruption->datetime_from);

												$days = $startDate->diffInDays($endDate);
												$hours = $startDate->copy()->addDays($days)->diffInHours($endDate);
												$minutes = $startDate->copy()->addDays($days)->addHours($hours)->diffInMinutes($endDate);
												$total_seconds += $startDate->diffInSeconds($endDate);

											?>

											@if ($days)
												{{ $days }} {{ $days > 1 ? 'days' : 'day' }}{{ ($hours && $minutes <= 0) || $hours < 0 && $minutes ? ' &' : ($hours <= 0 && $minutes <= 0 ? '' : ',') }}
											@endif
											@if ($hours)
												{{ $hours }} {{ $hours > 1 ? 'hrs' : 'hr' }}
												{{ $minutes ? '&' : '' }}
											@endif
											@if ($minutes)
												{{ $minutes }} {{ $minutes > 1 ? 'mins' : 'min' }}
											@endif
										</td>
										<td>{{ $power_interruption->remarks }}</td>
										<td>
											<div class="btn-group">
												<a href="{{ route('power_interruption.edit', ['id' => $power_interruption->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
												<a href="{{ route('power_interruption.trash', ['id' => $power_interruption->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
  $(function () {
    $('#power-interruption-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'responsive'  : true,
      columnDefs: [
          { responsivePriority: 1, targets: 0 },
          { responsivePriority: 2, targets: 4 },
          { responsivePriority: 3, targets: 5 },
          { responsivePriority: 4, targets: 7 },
      ],
      'scrollY'       : "300px",
      dom: "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      lengthMenu: [
          [ 10, 25, 50, -1 ],
          [ '10', '25', '50', '100', 'All' ]
      ],
      buttons: [
          {
              extend: 'excelHtml5',
              exportOptions: {
                  columns: ':visible'
              }
          },
          'colvis'
      ]
    })
  })
</script>
@endpush
