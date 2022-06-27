@extends('layouts.app')

@section('title', 'Inventory Reconciliations')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reconciliation
      <small>Manage inventory reconciliation</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Inventory Recon</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Inventory Recon
            </h3>
            @if (\Auth::user()->branch->machine_number === 103 &&
								 \Auth::user()->hasPermissionTo('Create Inventories'))
							<div class="box-toolbar pull-right">
	              <div class="btn-group">
	                <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-xs">
						        <i class="fa fa-download"></i>&nbsp;IMPORT
						      </a>
	              </div>
	            </div>
						@endif
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')

						<table class="table table-bordered table-hover" id="inventory-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Sent By</th>
									<th>Date</th>
									<th>Branch</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($inventories as $index => $inventory)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $inventory->user->first_name }} {{ $inventory->user->last_name }}</td>
										<td>{{ \Carbon\Carbon::parse($inventory->created_at)->diffForHumans() }}</td>
										<td>{{ $inventory->branch->name }}</td>
										<td>
											@if ($inventory->inventory_maps->max('quantity_branch') || $inventory->inventory_maps->max('serial_branch'))
												<span class="label bg-green">Updated</span>
											@else
												<span class="label bg-red">Empty</span>
											@endif
										</td>
										<td>
											<div class="btn-group">
												@if (\Auth::user()->hasPermissionTo('Show Inventory Breakdown'))
													<a href="{{ route('inventory.breakdown_view', ['id' => $inventory->id]) }}" class="btn btn-default btn-xs" title="Breakdown View"><i class="fa fa-eye"></i></a>
												@endif

												@if (\Auth::user()->hasPermissionTo('Show Inventory Discrepancies'))
													<a href="{{ route('inventory.discrepancy', ['id' => $inventory->id]) }}" class="btn btn-default btn-xs" title="Discrepancy"><i class="fa fa-file-text"></i></a>
												@endif

												@if (\Auth::user()->hasPermissionTo('Delete Inventories') &&
														 \Auth::user()->branch->machine_number === 103)
													<a href="{{ route('inventory.trash', ['id' => $inventory->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
												@endif

												@if (\Auth::user()->hasAnyPermission('Get Inventory Raw Files', 'Import Inventories'))
													<a href="{{ route('inventory.get_raw', ['id' => $inventory->id]) }}" class="btn btn-default btn-xs" title="Get Raw"><i class="fa fa-file-o"></i></a>
												@endif

												@if (\Auth::user()->hasPermissionTo('View Inventories'))
													<a href="{{ route('inventory.view', ['id' => $inventory->id]) }}" class="btn btn-default btn-xs" title="View"><i class="fa fa-eye"></i></a>
												@endif
													<!-- <a href="{{ route('inventory.edit_branch', ['id' => $inventory->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a> -->
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
    $('#inventory-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'responsive'  : true,
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
