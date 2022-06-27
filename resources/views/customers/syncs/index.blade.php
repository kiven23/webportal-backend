@extends('layouts.app')

@section('title', 'Sync Customer')

@section('content')
<div class="row mt-4 mb-4">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('customers') }}">Droplist</a></li>
      <li class="breadcrumb-item active">Sync Data</li>
		</ol>
	</div>

	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Date</th>
						<th>Birth Date</th>
						<th>Operations</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($lists as $list)
						<tr>
							<td>{{ $list->first_name }}</td>
							<td>{{ $list->last_name }}</td>
							<td>{{ $list->created_at }}</td>
							<td>{{ $list->birth_date }}</td>
							<td>
								<a href="{{ route('customer.sync.proceed', ['id' => $list->id]) }}" class="btn btn-primary btn-sm">
								<span class="glyphicon glyphicon-transfer"></span>
								Sync
								</a>
							</td>
						</tr>
					@empty
						<tr><td colspan="5">No records found.</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection