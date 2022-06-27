@extends('layouts.app')

@section('content')
<div class="row">

	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li><a href="{{ url('/') }}">Dashboard</a></li>
		  <li class="active">Sync Data</li>
		</ol>
	</div>

	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<strong>Local Data</strong>
			</div>

			<div class="panel-body">

				@if (Session::has('sync_success'))
					<div class="alert alert-success alert-dismissable fade in">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>{{ Session::get('sync_success') }}</strong>
					</div>
				@endif

				<div class="table-responsive">
					<table class="table table-stripe table-hover table-sm">
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
										<a href="{{ route('client.sync', ['id' => $list->id]) }}" class="btn btn-primary btn-sm">
										<span class="glyphicon glyphicon-transfer"></span>
										Sync
										</a>
									</td>
								</tr>
							@empty
								<tr><td>No records found.</td></tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
