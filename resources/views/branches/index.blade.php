@extends('layouts.app')

@section('title', 'Branches')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Branches
    	<small>Manage branch</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Branches</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Branch Lists
            </h3>
            <div class="box-toolbar pull-right">
            	<div class="btn-group">
	            	<a href="{{ route('branch.create') }}" class="btn btn-primary btn-xs">
									<i class="fa fa-plus"></i>&nbsp;ADD
								</a>
								<a href="{{ route('regions') }}" class="btn btn-info btn-xs ml-1">
									<i class="fa fa-globe"></i>&nbsp;REGIONS
								</a>
								<a href="{{ route('branch-schedules.index') }}" class="btn btn-warning btn-xs ml-1">
									<i class="fa fa-clone"></i>&nbsp;SCHEDULES
								</a>
							</div>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')
						<table class="table table-bordered table-hover" id="branch-table">
							<thead>
								<tr>
                  <th>Name</th>
									<th>Machine Number</th>
									<th>Schedule</th>
									<th>Region</th>
									<th>Whs Code</th>
									<th>Assigned BM/OIC</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($branches as $index => $branch)
									<tr>
                    <td>{{ $branch->name }}</td>
										<td>{{ $branch->machine_number ? $branch->machine_number : 'None' }}</td>
										<td>
											@if ($branch->bsched_id)
												{{ Carbon\Carbon::parse($branch->schedule->time_from)->format('g:ia') }} - {{ Carbon\Carbon::parse($branch->schedule->time_to)->format('g:ia') }}
											@else
												None
											@endif
										</td>
										<td>{{ $branch->region_id ? $branch->region->name : 'None' }}</td>
										<td>{{ $branch->whscode ? $branch->whscode : 'None' }}</td>
										<td>{{ $branch->bm_oic ? $branch->bm_oic : 'None' }}</td>
										<td>
											<div class="btn-group">
												<a href="{{ route('branch.edit', ['id' => $branch->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
												<a href="{{ route('branch.trash', ['id' => $branch->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
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
    $('#branch-table').DataTable({
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
      columnDefs: [
          { responsivePriority: 1, targets: 0 },
          { responsivePriority: 2, targets: 3 },
          { responsivePriority: 3, targets: 5 },
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
