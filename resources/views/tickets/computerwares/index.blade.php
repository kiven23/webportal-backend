@extends('layouts.app')

@section('title', 'Computerware Tickets')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Service Call Tickets
      <small>Manage computerware tickets</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Computerware Tickets</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Computerware Ticket Lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('ticket.computerware.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;
                  ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@if (\Request::is('computerwares/index/1'))
							<div class="alert alert-dismissible alert-danger">
							  <button type="button" class="close" data-dismiss="alert">&times;</button>
							  <strong>Delete failed! </strong> Model has a child record(s)
							</div>
						@else
							@include ('errors.list')
							@include ('successes.list')
						@endif

						<table class="table table-bordered table-hover" id="computerware-table">
							<thead>
								<tr>
                  <th>Branch</th>
									<th>Ticket #</th>
									<th>Problem</th>
									<th>Product Category</th>
									<th>Brand</th>
									<th>Model</th>
									<th>Serial</th>
									<th>Reported By</th>
									<th>Position</th>
									<th>Date Reported</th>
									<th>Date Closed</th>
									<th>Logged By</th>
									<th>Assigned Tech.</th>
									<th>Remarks</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($computerwares as $index => $computerware)
									<tr>
                    <td>{{ $computerware->branch->name }}</td>
										<td>{{ $computerware->ticket_number }}</td>
										<td>{{ $computerware->problem }}</td>
										<td>{{ $computerware->item->category->name }}</td>
										<td>{{ $computerware->item->brand->name }}</td>
										<td>{{ $computerware->item->model }}</td>
										<td>{{ $computerware->product_item_serial_number }}</td>
										<td>{{ $computerware->reported_by_name }}</td>
										<td>{{ $computerware->reported_by_position }}</td>
										<td>{{ $computerware->created_at }}</td>
										<td>{{ $computerware->report_status === 2 ? $computerware->updated_at : '' }}</td>
										<td>
											<var><abbr title="Ticket #{{ $computerware->ticket_number }}">{{ $computerware->user->first_name }} {{ $computerware->user->last_name }}</abbr></var>
										</td>
										<td>{{ $computerware->assigned_tech }}</td>
										<td>{{ $computerware->remarks }}</td>
										<td>
											@if ($computerware->report_status === 1)
												<span class="label bg-green">Open</span>
											@else
												<span class="label bg-red">Closed</span>
											@endif
										</td>
										<td>
											<div class="btn-group">
                        <a class="btn btn-default btn-xs" href="{{ route('ticket.computerware.edit', ['id' => $computerware->id]) }}" title="Edit"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-default btn-xs" href="{{ route('ticket.computerware.trash', ['id' => $computerware->id]) }}" title="Delete"><i class="fa fa-trash"></i></a>
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
    $('#computerware-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
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
          { responsivePriority: 2, targets: 14 },
          { responsivePriority: 3, targets: 9 },
          { responsivePriority: 4, targets: 10 },
          { responsivePriority: 5, targets: 3 },
      ],
      'scrollY'     : "300px",
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