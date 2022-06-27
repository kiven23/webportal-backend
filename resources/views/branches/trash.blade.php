@extends('layouts.app')

@section('title', 'Delete Branch')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Branches
    	<small>Manage branch</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="{{ route('branches.index') }}">Branches</a></li>
      <li class="active">Trash</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Confirm delete</h3>
          </div>
          <div class="box-body">
          	You are about to delete a record with the following details:
						<ul>
							<li><strong>Machine Number: </strong> {{ $branch->machine_number ? $branch->machine_number : 'None' }}</li>
							<li><strong>Name: </strong> {{ $branch->name }}</li>
							<li><strong>Schedule: </strong> {{ $branch->bsched_id ? $branch->bsched_id : 'None' }}</li>
							<li><strong>Region: </strong> {{ $branch->region_id ? $branch->region->name : 'None' }}</li>
							<li><strong>Whs Code: </strong> {{ $branch->whscode ? $branch->whscode : 'None' }}</li>
							<li><strong>Assigned BM/OIC: </strong> {{ $branch->bm_oic ? $branch->bm_oic : 'None' }}</li>
							<em>Users under this record:</em>
							<ol>
								@forelse ($branch->users as $user)
									<li><strong>{{ $user->first_name }} {{ $user->last_name }}</strong></li>
								@empty
									<li>None</li>
								@endforelse
							</ol>

							<em>Computerware Tickets under this record:</em>
							<ol>
								@forelse ($branch->computerware_tickets as $compticket)
									<li>
										<ul>
											<li><strong>Ticket #: </strong>{{ $compticket->id }}</li>
											<li>
												<strong>Product: </strong>
												{{ $compticket->item->brand->name }}
												{{ $compticket->item->model }}
												({{ $compticket->item->category->name }})
											</li>
											<li><strong>Problem: </strong>{{ $compticket->problem }}</li>
											<li>
												<strong>Reported By: </strong>
												{{ $compticket->reported_by_name }}
												({{ $compticket->reported_by_position }})
											</li>
											<li><strong>Logged By: </strong>{{ $compticket->user->first_name }} {{ $compticket->user->last_name }}</li>
											<li>
												<strong>Date Logged: </strong>
												{{ \Carbon\Carbon::parse($compticket->created_at)->format('F d, Y (h:i a)') }}
											</li>
										</ul>
									</li>
								@empty
									<li>None</li>
								@endforelse
							</ol>

							<em>Connectivity Tickets under this record:</em>
							<ol>
								@forelse ($branch->connectivity_tickets as $connticket)
									<li>
										<ul>
											<li><strong>Ticket #: </strong>{{ $connticket->id }}</li>
											<li>
												<strong>Service Provider: </strong> {{ $connticket->service_provider->name }}
											</li>
											<li>
												<strong>Service Type: </strong> {{ $connticket->service_type->name }}
											</li>
											<li>
												<strong>Service Category: </strong> {{ $connticket->service_category->name }}
											</li>
											<li><strong>Problem: </strong>{{ $connticket->problem }}</li>
											<li>
												<strong>Reported By: </strong>
												{{ $connticket->reported_by_name }}
												({{ $connticket->reported_by_position }})
											</li>
											<li><strong>Logged By: </strong>{{ $connticket->user->first_name }} {{ $connticket->user->last_name }}</li>
											<li>
												<strong>Date Logged: </strong>
												{{ \Carbon\Carbon::parse($connticket->created_at)->format('F d, Y (h:i a)') }}
											</li>
										</ul>
									</li>
								@empty
									<li>None</li>
								@endforelse
							</ol>

							<em>Power Interruption Logs under this record:</em>
							<ol>
								@forelse ($branch->power_interruptions as $poweri)
									<li>
										<ul>
											<li><strong>Log #: </strong>{{ $poweri->id }}</li>
											<li>
												<strong>Problem Reported: </strong> {{ $poweri->problem_reported }}
											</li>
											<li>
												<strong>Date/Time From: </strong> {{ $poweri->datetime_from }}
											</li>
											<li>
												<strong>Date/Time To: </strong> {{ $poweri->datetime_to }}
											</li>
											<li>
												<strong>Reported By: </strong>
												{{ $poweri->reported_by_name }}
												({{ $poweri->reported_by_position }})
											</li>
											<li><strong>Logged By: </strong>{{ $poweri->user->first_name }} {{ $poweri->user->last_name }}</li>
											<li>
												<strong>Date Logged: </strong>
												{{ \Carbon\Carbon::parse($poweri->created_at)->format('F d, Y (h:i a)') }}
											</li>
										</ul>
									</li>
								@empty
									<li>None</li>
								@endforelse
							</ol>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
          </div>
          <div class="box-footer">
          	<form method="post" action="{{ route('branch.delete', ['id' => $branch->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('branches.index') }}" class="btn btn-default">No, go back</a>
						</form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
