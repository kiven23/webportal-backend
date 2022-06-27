@extends('layouts.app')

@section('title', 'Inventory Reconciliation Raw Files')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reconciliation
      <small>Manage inventory reconciliation</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('inventories') }}">Inventory Recon</a></li>
      <li class="active">Get raw</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Get raw
            </h3>
            @if (\Auth::user()->branch->machine_number !== 103 &&
								 \Auth::user()->hasPermissionTo('Import Inventories'))
							<div class="box-toolbar pull-right">
                <a href="{{ route('inventory.import_branch', ['id' => $inventory_maps[0]->inventory_id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-download"></i> IMPORT</a>
              </div>
						@endif
          </div>
          <div class="box-body table-responsive">
          	<table class="table table-bordered table-hover" id="invty-recon-table">
							<thead>
								<tr>
									<th>BRAND</th>
									<th>MODEL</th>
									<th>SERIAL</th>
									<th>QUANTITY</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($inventory_maps as $inventory_map)
									<tr class="clickable-row">
										<td><pre>{{ $inventory_map->brand }}</pre></td>
										<td><pre>{{ $inventory_map->model }}</pre></td>
										<td></td>
										<td></td>
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
    $('#invty-recon-table').DataTable({
      'paging'        :   true,
      'lengthChange'  :   true,
      'searching'     :   true,
      'ordering'      :   true,
      'info'          :   true,
      'autoWidth'     :   true,
      'scrollY'       :   "300px",
      'scrollX'       :   true,
      'scrollCollapse':   true,
      'fixedHeader'   :   true,
      'columnDefs': [
          { 'width': 160, 'targets': 0 },
          { 'width': 150, 'targets': 1 },
          { 'width': 200, 'targets': 2 },
          { 'width': 165, 'targets': 3 },
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
              extend: 'excelHtml5',
              text:   'Export to Excel'
          },
      ]
    });
  })
</script>
@endpush