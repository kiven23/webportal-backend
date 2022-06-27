@extends('layouts.app')

@section('title', 'Confirm Connectivity Ticket')

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
      <li class="active">Confirm</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm connection uptime
            </h3>
          </div>
          <div class="box-body">
						You are about to confirm connection uptime with the following details:
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
							<li>
								<strong>Status: </strong>
								@if ($connectivity->status === 1)
									<span class="label bg-green">Open</span>
								@elseif ($connectivity->status === 2)
									<span class="label bg-orange">Pending</span>
								@else
									<span class="label bg-red">Closed</span>
								@endif
							</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> After you proceed, the action cannot be undo.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('ticket.connectivity.confirm_proceed', ['id' => $connectivity->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-success">Proceed</button>
							<a href="{{ route('ticket.branch.connectivities') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
