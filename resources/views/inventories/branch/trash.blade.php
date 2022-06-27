@extends('layouts.app')

@section('title', 'Delete Inventory Reconciliation')

@section('content')
<div class="row mt-4 mb-4">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
		  <li class="breadcrumb-item"><a href="{{ route('inventories') }}">Inventory</a></li>
		  <li class="breadcrumb-item active">
		  	{{ \Carbon\Carbon::parse($inventory_map->created_at)->format('F d, Y') }}
		  </li>
		  <li class="breadcrumb-item active">Trash</li>
		</ol>
	</div>

	<div class="col-md-6">
		<div class="card border-danger">
			<div class="card-header text-white bg-danger">CONFIRM DELETE</div>

			<div class="card-body">
				You are about to delete a record with the following details:

				<ul>
					<li><strong>Brand: </strong> {{ $inventory_map->brand }}</li>
					<li><strong>Model: </strong> {{ $inventory_map->model }}</li>
					<li><strong>Serial: </strong> {{ $inventory_map->serial_branch }}</li>
				</ul>

				<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
			</div>

			<div class="card-footer">
				<form method="post" action="{{ route('inventory.delete_branch', ['id' => $inventory_map->id]) }}">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger">Proceed</button>
					<a href="{{ route('inventory.edit_branch', ['id' => $inventory_map->inventory->id]) }}" class="btn btn-secondary">No, go back</a>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
