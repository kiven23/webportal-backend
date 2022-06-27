@extends('layouts.app')

@section('title', 'Inventory Reconciliation Breakdown')

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
      <li class="active">Breakdown</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ $branch_name }} inventory breakdown
            </h3>
          </div>
          <div class="box-body">
          	<table class="table table-bordered" id="invty-recon-table">
							<thead>
								<tr>
									<th>BRAND</th>
									<th>MODEL</th>
									<th>SAP</th>
									<th>BRANCH</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($inventory_maps as $inventory_map)
									<tr>
										<td><span>{{ $inventory_map['brand'] }}</span></td>
										<td><span>{{ $inventory_map['model'] }}</span></td>
										<td>
											@if ($inventory_map['serial'] !== 0)
												{{ $inventory_map['serial'] ? $inventory_map['serial'] : 'NONE' }}
											@else
												{{ $inventory_map['quantity'] !== null ? $inventory_map['quantity'] : '---' }}
											@endif
										</td>
										<td>
											@if ($inventory_map['serial_branch'] !== 0)
												@if ($inventory_map['serial_branch'] !== null)
													{{ $inventory_map['serial_branch'] }}
												@else
													{{ $inventory_map['quantity_branch'] ? $inventory_map['quantity_branch'] : 'NONE' }}
												@endif
											@else
												{{ $inventory_map['quantity_branch'] !== null ? $inventory_map['quantity_branch'] : '---' }}
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
    $('#invty-recon-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'scrollX'     : true,
      'select'      : true,
      'aLengthMenu': [
          [5, 10, 25, 50, 100, 200, -1],
          [5, 10, 25, 50, 100, 200, "All"]
      ],
    })
  })
</script>
@endpush
