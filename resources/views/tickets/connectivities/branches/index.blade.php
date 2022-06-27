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
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
            @include ('successes.list')

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
									<th>Problem Reported to ISP</th>
									<th>Provider Fault Ticket No.</th>
									<th class="newline">Last Update</th>
									<th>Status</th>
									<th>Ratings</th>
									<th>Remarks</th>
									<th>Logged By</th>
									<th>Date Created</th>
									<th>Updated By</th>
									<th>Date Updated</th>
									<th>Close Ticket/Reviews</th>
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
                      {{ $connectivity->confirmed_by ? $connectivity->confirmedBy->full_name : '' }}
                    </td>
										<td class="align-top">{{ $connectivity->problem_reported_ho }}</td>
										<td class="align-top">{{ $connectivity->resolution_reported }}</td>
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
                    <td>
                      @if ($connectivity->status === 2)
                        <a href="{{ route('ticket.connectivity.confirm', ['id' => $connectivity->id]) }}"
                           class="btn btn-default btn-xs"
                           title="Confirm"><i class="text-warning fa fa-check"></i></a>
                      @elseif ($connectivity->status === 3)
                        @if (\Auth::user()->hasPermissionTo('Rate Connectivity Tickets'))
                          <a href="{{ route('ticket.connectivity.rate', ['id' => $connectivity->id]) }}"
                             class="btn btn-default btn-xs"
                             title="Rate"><i class="fa fa-star"></i></a>
                        @endif
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
    $('#connectivity-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'scrollY'      : "300px",
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
          { responsivePriority: 1, targets: 0 },
          { responsivePriority: 2, targets: 21 },
          { responsivePriority: 3, targets: 3 }
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