@extends('layouts.app')

@section('title', 'Overtime')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Overtimes
      <small>Manage overtime filing</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Overtimes</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Filed Overtime
            </h3>
            <div class="box-toolbar pull-right">
            	<a href="{{ route('overtime.create') }}" class="btn btn-primary btn-xs">
            		<i class="fa fa-plus"></i>&nbsp;ADD
            	</a>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')

						<table class="table table-bordered table-hover" id="ot-table">
							<thead>
								<th>Date</th>
								<th>Reason</th>
								<th>Progress</th>
								<th>Status</th>
								<th>Actions</th>
							</thead>
							<tbody>
								@foreach ($overtimes as $index => $overtime)
									<tr>
										<td class="align-top">
											{{ Carbon\Carbon::parse($overtime->date_from)->format('F d, Y') }}
											({{ Carbon\Carbon::parse($overtime->date_from)->format('h:i a') }} -
											@if ($overtime->date_to === null)
												onwards)
											@else
												{{ Carbon\Carbon::parse($overtime->date_to)->format('h:i a') }})
											@endif
											@if ($overtime->working_dayoff)
												<span>- Working Dayoff</span>
											@endif
										</td>
										<td class="align-top">
											<?php
												$reasons = explode(',', $overtime->reason);
											?>
											@foreach ($reasons as $reason)
												<ul class="reason-list">
													<li>{{ $reason }}</li>
												</ul>
											@endforeach
										</td>
										<td class="align-top">
											@if ($overtime->status !== 4)
												@foreach ($approvers as $index => $approver)
													<i
														class="icon-pending fa
															{{ $overtime->waiting_for > $approver['level'] ? 'fa-check-circle' : 'fa-circle-o' }}
														" data-toggle="tooltip" data-container="body"
														title="
															@foreach ($approver['user'] as $user)
																{{ isset($overtime->officers_approved[$index]) ? ($user == $overtime->officers_approved[$index]['approver'] ? '*' . $user . '*' : $user) : $user }}
																@if ($user != end($approver['user']))
																	/
																@endif
															@endforeach
													"></i>
												@endforeach
											@else
												@foreach ($overtime->officers_approved as $officer_approved)
													<i
														class="icon-approved fa fa-check-circle" data-toggle="tooltip"
														data-container="body"
														title="
															{{ $officer_approved->approver }}
														"></i>
												@endforeach
											@endif
										</td>
										<td class="align-top">
											@if ($overtime->status == 1)
                        <span class="label label-default">Pending</span>
											@elseif ($overtime->status == 2)
												<abbr title=""
															data-container="body"
															data-toggle="popover"
															data-placement="left"
															data-content="{{ $overtime->remarks_user->first_name }} {{ $overtime->remarks_user->last_name }} - {{ $overtime->remarks }}"
															data-original-title="Overtime Returned">
                          <span class="label label-warning">Returned</span>
                        </abbr>
											@elseif ($overtime->status == 3)
												<abbr title=""
															data-container="body"
															data-toggle="popover"
															data-placement="left"
															data-content="{{ $overtime->remarks_user->first_name }} {{ $overtime->remarks_user->last_name }} - {{ $overtime->remarks }}"
															data-original-title="Overtime Rejected">
                          <span class="label label-danger">Rejected</span>
                        </abbr>
											@elseif ($overtime->status == 4)
                        <span class="label label-success">Approved</span>
											@endif
										</td>
										<td class="align-top">
											@if ($overtime->status === 1)
												@if (!count($overtime->officers_approved) > 0)
													<a href="{{ route('overtime.edit', ['id' => $overtime->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
												@endif
											@endif

											@if ($overtime->status === 2)
												<a href="{{ route('overtime.edit', ['id' => $overtime->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
											@endif
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
    $('#ot-table').DataTable({
      'paging'      	: true,
      'lengthChange'	: true,
      'searching'   	: true,
      'ordering'    	: true,
      'info'        	: true,
      'select'        : true,
      responsive: {
          details: {
          		display: $.fn.dataTable.Responsive.display.modal( {
                  header: function ( row ) {
                      var data = row.data();
                      return data[0]+' - '+data[3];
                  }
              } ),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                  tableClass: 'table'
              } ),
          }
      },
      columnDefs: [
          { responsivePriority: 1, targets: 0 },
          { responsivePriority: 2, targets: 2 },
          { responsivePriority: 3, targets: 3 },
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

    $('#ot-table tbody').on('mouseover', 'tr', function () {
	    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        html: true,
	    });

	    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        html: true,
        placement: 'left',
	    });
		});
  })
</script>
@endpush