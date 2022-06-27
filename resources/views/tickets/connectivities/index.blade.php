@extends('layouts.app')

@section('title', 'Connectivity Tickets')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Service Call Tickets
      <small>Manage connectivity tickets</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Connectivity Tickets</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Connectivity Ticket Lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('ticket.connectivity.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;
                  ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@if (\Request::is('connectivities/index/1'))
							<div class="alert alert-dismissible alert-danger">
							  <button type="button" class="close" data-dismiss="alert">&times;</button>
							  <strong>Delete failed! </strong> Model has a child record(s)
							</div>
						@else
							@include ('errors.list')
							@include ('successes.list')
						@endif

						<table class="table table-sm table-bordered table-hover" id="connectivity-table">
							<thead>
								<tr>
									<th>Branch</th>
									<th>Ticket #</th>
									<th class="newline">Problem</th>
									<th>Service Provider</th>
									<th>Service Type</th>
									<th>Service Category</th>
									<th>Reported By</th>
									<th>Position</th>
									<th>Accepted By</th>
									<th>Problem Reported/Traced</th>
									<th>Resolution Reported/Traced</th>
									<th>Total Hours</th>
									<th>Total Hours</th>
									<th>Problem Reported to ISP</th>
									<th>Provider Fault Ticket No.</th>
									<th class="newline">Last Update</th>
									<th>Status</th>
									<th>Rate</th>
									<th>Remarks</th>
									<th>Logged By</th>
									<th>Date Created</th>
									<th>Updated By</th>
									<th>Date Updated</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($connectivities as $index => $connectivity)
									<tr class="{{
										$connectivity->status === 1 ? 'text-danger' :
										($connectivity->status === 2 ? 'text-warning' : 'text-default')
									}}">
										<td class="align-top">
											<var><abbr title="Ticket #{{ $connectivity->ticket_number }}">{{ $connectivity->branch['name'] }}</abbr></var>
										</td>
										<td class="align-top">{{ $connectivity->ticket_number }}</td>
										<td class="align-top">{{ $connectivity->problem }}</td>
										<td class="align-top">{{ $connectivity->service_provider->name }}</td>
										<td class="align-top">{{ $connectivity->service_type->name }}</td>
										<td class="align-top">{{ $connectivity->service_category->name }}</td>
										<td class="align-top">{{ $connectivity->reported_by_name }}</td>
										<td class="align-top">{{ $connectivity->reported_by_position }}</td>
										<td class="align-top">
											{{ $connectivity->confirmedBy ? $connectivity->confirmedBy->full_name : '' }}
										</td>
										<td class="align-top">{{ $connectivity->problem_reported_ho }}</td>
										<td class="align-top">{{ $connectivity->resolution_reported }}</td>
										<td class="align-top">
											<?php

												$startDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $connectivity->problem_reported_ho);
												$endDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $connectivity->resolution_reported ? $connectivity->resolution_reported : $connectivity->problem_reported_ho);

												$days = $startDate->diffInDays($endDate);
												$hours = $startDate->copy()->addDays($days)->diffInHours($endDate);
												$minutes = $startDate->copy()->addDays($days)->addHours($hours)->diffInMinutes($endDate);
												$total_seconds = 0;
												$total_seconds += $startDate->diffInSeconds($endDate);

											?>
											{{ $total_seconds }}
										</td>
										<td></td>
										<td class="align-top">{{ $connectivity->problem_reported_isp }}</td>
										<td class="align-top">{{ $connectivity->provider_ticket }}</td>
										<td class="align-top">
											{!! nl2br(e($connectivity->remarks)) !!}
										</td>
										<td class="align-top">
											@if ($connectivity->status === 1)
												<span class="label bg-red">Open</span>
											@elseif ($connectivity->status === 2)
												<span class="label bg-orange">Pending</span>
											@else
												<span class="label bg-black">Closed</span>
											@endif
										</td>
										<td class="align-top">
											@if ($connectivity->survey)
												@if ($connectivity->survey->rate === 1)
													<span class="label bg-red">Poor</span>
												@elseif ($connectivity->survey->rate === 2)
													<span class="label bg-orange">Needs Improvements</span>
												@elseif ($connectivity->survey->rate === 3)
													<span class="label bg-light-blue">Satisfactory</span>
												@elseif ($connectivity->survey->rate === 4)
													<span class="label bg-blue">Very Good</span>
												@elseif ($connectivity->survey->rate === 5)
													<span class="label bg-green">Excellent</span>
												@endif
											@else
												<span class="label bg-gray">Not rated</span>
											@endif
										</td>
										<td>
											{{ $connectivity->survey ? $connectivity->survey->remarks : '' }}
										</td>
										<td class="align-top">
											<var><abbr title="Ticket #{{ $connectivity->ticket_number }}">{{ $connectivity->user->first_name }} {{ $connectivity->user->last_name }}</abbr></var>
										</td>
										<td class="align-top">{{ $connectivity->created_at }}</td>
										<td class="align-top">{{ $connectivity->updatedBy ? $connectivity->updatedBy->full_name : '' }}</td>
										<td class="align-top">{{ $connectivity->updated_at }}</td>
										<td class="align-top">
											<div class="btn-group">
											  <a href="{{ route('ticket.connectivity.edit', ['id' => $connectivity->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
													<a href="{{ route('ticket.connectivity.trash', ['id' => $connectivity->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
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
    $('#connectivity-table').append('<tfoot><tr><th>Total:</th><th colspan="23"></th></tr></tfoot>');
    $('#connectivity-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'scrollY'     : "300px",
			"order"		: [],
      responsive: {
          details: {
          		display: $.fn.dataTable.Responsive.display.modal( {
                  header: function ( row ) {
                      var data = row.data();
                      return data[0]+' - Ticket# '+data[1];
                  }
              } ),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                  tableClass: 'table'
              } ),
          }
      },
      columnDefs: [
					// { targets: [6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22], visible: false },
          { responsivePriority: 1, targets: 0 },
          { responsivePriority: 2, targets: 23 },
          { responsivePriority: 3, targets: 3 },
          { responsivePriority: 4, targets: 9 },
					{ targets: [1, 2], render: $.fn.dataTable.render.ellipsis(5) },
          {
						targets: 12,
						render: 	function ( data, type, row ) {
												var startDate = moment(row[9], "YYYY-MM-DD HH:mm:ss");
												var endDate = moment(row[10], "YYYY-MM-DD HH:mm:ss");
											  var days = endDate.diff(startDate, 'days');
											  var hours = endDate.diff(startDate, 'hours');
											  var minutes = endDate.diff(startDate, 'minutes');

											  return forHumans(days, hours, minutes);

											  function forHumans (days, hours, minutes) {
											  	var result = ""
											  	if (days > 0) {
											  		if (days > 1) {
											  			var result = result + " " + days + " days";
											  		} else {
											  			var result = result + " " + days + " day";
											  		}

											  		if (minutes > 0) {
											  			var result = result + ", ";
											  		} else if (hours > 0) {
											  			var result = result + " & ";
											  		}
											  	}

											  	if (hours > 0) {
											  		var hours = hours % 24;
											  		if (hours > 1) {
											  			var result = result + " " + hours + " hours";
											  		} else {
											  			var result = result + " " + hours + " hour";
											  		}

											  		if (minutes > 0) {
											  			var result = result + " & ";
											  		}
											  	}

											  	if (minutes > 0) {
											  		var minutes = minutes % 60;
											  		if (minutes > 1) {
											  			var result = result + " " + minutes + " minutes";
											  		} else {
											  			var result = result + " " + minutes + " minute";
											  		}
											  	}
											  	return result;
											  }
											}
					}
      ],
      dom: "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      lengthMenu: [
          [ 10, 25, 50, -1 ],
          [ '10', '25', '50', '100', 'All' ]
      ],
      buttons: [
      		{
					    extend: 'excel',
					    footer: true,
					    text: 'Excel',
					    exportOptions: {
					        stripNewlines: false, // important for linebreaks
					        rows: ':visible',
					    }
					},
					'colvis'
			],
      exportOptions: {
		    format: {
	        body: function ( data, row, column, node ) {
      			// `column` contains the footer node, apply the concat function and char(10) if its newline class
             if ($(column).hasClass('newline')) {
                //need to change double quotes to single
                data = data.replace( /"/g, "'" );
                //split at each new line
                splitData = data.split('<br>');
                data = '';
                for (i=0; i < splitData.length; i++) {
                    //add escaped double quotes around each line
                    data += '\"' + splitData[i] + '\"';
                    //if its not the last line add CHAR(13)
                    if (i + 1 < splitData.length) {
                        data += ', CHAR(10), ';
                    }
                }
                //Add concat function
                data = 'CONCATENATE(' + data + ')';
                return data;
            }
            return data;
          }
	      }
			},
			"footerCallback": function ( row, data, start, end, display ) {
          var api = this.api(), data;

          // Remove the formatting to get integer data for summation
          var intVal = function (i) {
              return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                      i : 0;
          };

          // Total over all pages
          total = api
              .column(11)
              .data()
              .reduce( function (a, b) {
              		return intVal(a) + intVal(b);
              }, 0 );

          // Total over this page
          pageTotal = api
              .column(11, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
              		return intVal(a) + intVal(b);
              }, 0 );

          // Update footer
          $( api.column(1).footer() ).html(
          		Math.floor(pageTotal / 86400) + " days, " +
          		Math.floor((pageTotal / 3600) % 24) + " hours & " +
          		Math.floor((pageTotal / 60) % 60) + " minutes " +

          		"(" + 
          		Math.floor(total / 86400) + " days, " +
          		Math.floor((total / 3600) % 24) + " hours & " +
          		Math.floor((total / 60) % 60) + " minutes total)"
          );
      },
    })
  })
</script>
@endpush
