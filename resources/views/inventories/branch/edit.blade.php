@extends('layouts.app')

@section('title', 'Edit Inventory Reconciliation')

@section('content')
<div class="row mt-4 mb-4">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
		  <li class="breadcrumb-item"><a href="{{ route('inventories') }}">Inventory</a></li>
		  <li class="breadcrumb-item active">{{ \Carbon\Carbon::parse($inventory_maps[0]->inventory->created_at)->format('F d, Y') }}</li>
		</ol>

		@include ('errors.list')
		@include ('successes.list')
	</div>

	<div class="col-md-12 mb-4">
		<h4>{{ \Carbon\Carbon::parse($inventory_maps[0]->inventory->created_at)->format('F d, Y') }}</h4>
		<div class="toolbar">
			<a href="{{ route('inventory.create_branch', ['id' => $inventory_maps[0]->inventory_id]) }}" class="btn btn-primary"><span class="fa fa-plus"></span> Add New</a>
		</div>
		<table id="inventory-table" class="table table-bordered table-fluid">
			<thead>
				<tr>
					<th colspan="2"></th>
					<th colspan="2">SERIAL/QUANTITY</th>
				</tr>
				<tr>
					<th data-field="brand" data-sortable="true">BRAND</th>
					<th data-field="model" data-sortable="true">MODEL</th>
					<th data-field="serialqty" data-sortable="true">SAP</th>
					<th data-field="serialqty_branch" data-sortable="true">BRANCH</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($inventory_maps as $index => $inventory_map)
					<tr>
						<td>{{ $inventory_map['brand'] }}</td>
						<td>{{ $inventory_map['model'] }}</td>
						<td>
							@if ($inventory_map['serial'])
								<span class="text-danger">{{ $inventory_map['serial'] }}</span>
							@else
								<span class="text-danger">{{ $inventory_map['quantity'] ? $inventory_map['quantity'] : 'None' }}</span>
							@endif
						</td>
						<td>
							@if ($inventory_map['quantity'])
								<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
									<button
										type="submit"
										value="{{ $inventory_map['model'] }}"
										href="javascript:void(0);" class="qtybtn btn btn-primary">Update</button>
									<input
										style="padding-left: 10px;"
										type="number"
										value="{{ $inventory_map['quantity_branch'] ? $inventory_map['quantity_branch'] : '0' }}" min="0">
									<input type="hidden" class="inv_id" value="{{ $inventory_map['inventory_id'] }}">
								</div>
							@else
								@if ($inventory_map['serial'] === null)
									<form action="{{ route('inventory.trash_branch', ['id' => $inventory_map['id']]) }}" method="post">
										<span class="text-primary">
											{{ $inventory_map['serial_branch'] }}
										</span>
										{{ csrf_field() }}
										<button class="btn btn-sm btn-danger pull-right" title="Remove item"><i class="fa fa-trash"></i></button>
									</form>
								@else
									<div class="btn-group">
										<div class="btn-group" role="group" aria-label="Basic example">
										  <button
										  	value="{{ $inventory_map['id'] }}" type="submit"
										  	class="nonebtn btn {{ $inventory_map['serial_branch'] == null ? 'btn-danger' : 'btn-secondary' }}">None</button>
										  <button
										  	value="{{ $inventory_map['id'] }}" type="submit"
										  	class="ownedbtn btn {{ $inventory_map['serial_branch'] != null ? 'btn-success' : 'btn-secondary' }}">Owned</button>
										</div>

										<a
											href="{{ route('inventory.duplicate_branch', ['id' => $inventory_map['id']]) }}"
											title="Duplicate">
											<button class="btn btn-default"><i class="fa fa-copy"></i></button>
											</a>
										</form>
									</div>
								@endif
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<div class="mt-2">
		</div>
	</div>
</div>
@stop

@push('scripts')
<script>
    var $table = $('#inventory-table'),
      full_screen = false;

    $(document).ready(function(){

        // Arrangement is important for the actions to work
        window.operateEvents = {
          'click .edit': function (e, value, row, index) {
            var url = 'branches/' + row.id + '/edit';
            window.location = url;
            // alert('You click edit icon, row: ' + JSON.stringify(row));
            // console.log(value, row, index);
          },
          'click .trash': function (e, value, row, index) {
            var url = 'branches/' + row.id + '/trash';
            window.location = url;
            // alert('You click trash icon, row: ' + JSON.stringify(row.id));
            // console.log(value, row, index);
          }
        };

        $table.bootstrapTable({
        	exportOptions: {},
          toolbar: ".toolbar",
          showRefresh: true,
          showExport: true,
          search: true,
          showToggle: true,
          showColumns: true,
          pagination: true,
          striped: true,
          pageSize: 10,
          pageList: [10,25,50,100,'ALL']
        });

        $(window).resize(function () {
            $table.bootstrapTable('resetView');
        });



        // ACTION BUTTONS
        $('table').on('click', '.nonebtn', function() {

			    var id = this.value;
			    var self = this;

			    $(this).html('loading...');
			    $.ajax({
			      url: '/inventoryrecon/public/inventories/' + id + '/update_branch',
			      type: 'POST',
			      data: {
			      	'_token': '{{ csrf_token() }}',
			    		'btn': 'none'
			    	},
			      success: function(response) {
			        swal({
							  position: 'top-right',
							  title: 'Well done!',
							  text: 'Your work has been saved',
							  type: 'success',
							  showConfirmButton: false,
							  timer: 1500,
							  toast: true
							});
							$(self).addClass('btn-danger');

					    $(self).siblings().removeClass('btn-success');
					    $(self).siblings().addClass('btn-secondary');
							$(self).html('None');
			      },
			      error: function (err) {
			      	swal(
							  'Oops...',
							  'Something went wrong.',
							  'error'
							);
							$(self).html('None');
			      }
			    });
			  });

				$('table').on('click', '.ownedbtn', function() {

			    var id = this.value;
			    var self = this;

			    $(this).html('loading...');
			    $.ajax({
			      url: '/inventoryrecon/public/inventories/' + id + '/update_branch',
			      type: 'POST',
			      data: {
			      	'_token': '{{ csrf_token() }}',
			    		'btn': 'owned'
			    	},
			      success: function(response) {
			        // alert('success!!!');
			        swal({
							  position: 'top-right',
							  title: 'Well done!',
							  text: 'Your work has been saved',
							  type: 'success',
							  showConfirmButton: false,
							  timer: 1500,
							  toast: true
							});
							$(self).addClass('btn-success');

					    $(self).siblings().removeClass('btn-danger');
					    $(self).siblings().addClass('btn-secondary');
							$(self).html('Owned');
			      },
			      error: function (err) {
			      	swal(
							  'Oops...',
							  'Something went wrong.',
							  'error'
							);
							$(self).html('Owned');
			      }
			    });
			  });

			  // UPDATE QUANTITY
			  $('table').on('click', '.qtybtn', function() {
			    var qty = $(this).siblings().val();
			    var inv_id = $('.inv_id').val();
			    var model = this.value;

			    var self = this;
			    $(this).html('updating...');
			    $.ajax({
			      url: '/inventoryrecon/public/inventories/' + inv_id + '/update_branch_qty',
			      type: 'POST',
			      data: {
			      	'_token': '{{ csrf_token() }}',
			    		'qty': qty,
			    		'model': model
			    	},
			      success: function(response) {
			        swal({
							  position: 'top-right',
							  title: 'Well done!',
							  text: 'Your work has been saved',
							  type: 'success',
							  showConfirmButton: false,
							  timer: 1500,
							  toast: true
							});
							$(self).html('Update');
			      },
			      error: function (err) {
			      	swal(
							  'Oops...',
							  'Something went wrong.',
							  'error'
							)
							$(self).html('Update');
			      }
			    });
			  });
    });

    function operateFormatter(value, row, index) {
        return [
            '<a data-toggle="tooltip" title="Edit" class="table-actions edit btn btn-primary btn-sm" href="javascript:void(0);">',
                '<i class="fa fa-edit"></i> Edit',
            '</a> ',

            '<a data-toggle="tooltip" title="Remove" class="table-actions trash btn btn-danger btn-sm" href="javascript:void(0);">',
                '<i class="fa fa-trash"></i> Trash',
            '</a>'
        ].join('');
    }
</script>
@endpush
