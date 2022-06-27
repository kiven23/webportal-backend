@extends('layouts.app')

@section('title', 'Duplicate Inventory Reconciliation')

@section('content')
<div class="row mb-4 mt-4">

	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
		  <li class="breadcrumb-item"><a href="{{ route('inventories') }}">Inventory</a></li>
		  <li class="breadcrumb-item"><a href="{{ route('inventory.edit_branch', ['id' => $inventory_map->inventory->id]) }}">{{ \Carbon\Carbon::parse($inventory_map->inventory->created_at)->format('F d, Y') }}
		  </a></li>
		  <li class="breadcrumb-item active">Duplicate</li>
		</ol>

		@include ('errors.list')
		@include ('successes.list')
	</div>

	<div class="col-md-6">
		<div class="card border-primary">
			<div class="card-header bg-primary text-white">DUPLICATE</div>
			<form action="{{ route('inventory.store_branch', ['id' => $inventory_map->inventory->id]) }}" method="post">
				{{ csrf_field() }}
				<div class="card-body">

					@if (\Session::has('inventory_store_branch_fail'))
						<div class="alert alert-dismissible alert-{{ \Session::get('inventory_store_branch_fail.status') }}">
						  <button type="button" class="close" data-dismiss="alert">&times;</button>
						  <strong>{{ \Session::get('inventory_store_branch_fail.title') }}</strong> {{ \Session::get('inventory_store_branch_fail.message') }}
						</div>
					@elseif (\Session::has('inventory_store_branch_success'))
						<div class="alert alert-dismissible alert-{{ \Session::get('inventory_store_branch_success.status') }}">
						  <button type="button" class="close" data-dismiss="alert">&times;</button>
						  <strong>{{ \Session::get('inventory_store_branch_success.title') }}</strong> {{ \Session::get('inventory_store_branch_success.message') }}
						</div>
					@endif

					<div class="form-group {{ $errors->has('brand') ? 'has-danger' : '' }}">
						<label>Brand</label>
						<input class="form-control" type="text" name="brand" value="{{ $inventory_map->brand }}" placeholder="Brand" readonly>
						@if ($errors->has('brand'))
							<span class="help-text text-danger">
								{{ $errors->first('brand') }}
							</span>
						@endif
					</div>

					<div class="form-group {{ $errors->has('model') ? 'has-danger' : '' }}">
						<label>Model</label>
						<input class="form-control" type="text" name="model" value="{{ $inventory_map->model }}" placeholder="Model" readonly>
						@if ($errors->has('model'))
							<span class="help-text text-danger">
								{{ $errors->first('model') }}
							</span>
						@endif
					</div>

					<div class="form-group {{ $errors->has('serial_branch') ? 'has-danger' : '' }}">
						<label>Serial</label>
						<input class="form-control" type="text" name="serial_branch" value="{{ $inventory_map->serial }}" placeholder="Serial">
						@if ($errors->has('serial_branch'))
							<span class="help-text text-danger">
								{{ $errors->first('serial_branch') }}
							</span>
						@endif
					</div>
				</div>

				<div class="card-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
