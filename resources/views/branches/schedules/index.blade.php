@extends('layouts.app')

@section('title', 'Branch Schedules')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Branch Schedules
    	<small>Manage branch schedule</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Branch Schedules</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Branch Schedules
            </h3>
            <div class="box-toolbar pull-right">
            	<div class="btn-group">
	            	<a href="{{ route('branch-schedule.create') }}" class="btn btn-primary btn-xs">
									<i class="fa fa-plus"></i>&nbsp;ADD
								</a>
							</div>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')
						<table class="table table-bordered table-hover" id="branch-sched-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Time From</th>
									<th>Time To</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($branch_schedules as $index => $bsched)
									<tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $bsched->time_from }}</td>
                    <td>{{ $bsched->time_to }}</td>
										<td>
											<div class="btn-group">
												<a href="{{ route('branch-schedule.edit', ['id' => $bsched->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
												<a href="{{ route('branch-schedule.trash', ['id' => $bsched->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
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
    $('#branch-sched-table').DataTable({
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
                      return data[0]+' - '+data[3];
                  }
              } ),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                  tableClass: 'table'
              } ),
          }
      },
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