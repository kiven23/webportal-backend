@extends('layouts.app')

@section('title', 'Delete Connectivity Ticket')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Service Call Tickets
      <small>Manage connectivity tickets</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('ticket.connectivities') }}">Connectivity Tickets</a></li>
      <li class="active">Trash</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm delete
            </h3>
          </div>
          <div class="box-body">
						You are about to delete a record with the following details:
						<ul>
							<li><strong>Ticket #: </strong>{{ $connectivity->id }}</li>
							<li>
								<strong>Service Provider: </strong> {{ $connectivity->service_provider->name }}
							</li>
							<li>
								<strong>Service Type: </strong> {{ $connectivity->service_type->name }}
							</li>
							<li>
								<strong>Service Category: </strong> {{ $connectivity->service_category->name }}
							</li>
							<li><strong>Problem: </strong>{{ $connectivity->problem }}</li>
							<li>
								<strong>Reported By: </strong>
								{{ $connectivity->reported_by_name }}
								({{ $connectivity->reported_by_position }})
							</li>
							<li><strong>Logged By: </strong>{{ $connectivity->user->first_name }} {{ $connectivity->user->last_name }}</li>
							<li>
								<strong>Date Logged: </strong>
								{{ \Carbon\Carbon::parse($connectivity->created_at)->format('F d, Y (h:i a)') }}
							</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> After you proceed, the action cannot be undo.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('ticket.connectivity.delete', ['id' => $connectivity->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('ticket.connectivities') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
