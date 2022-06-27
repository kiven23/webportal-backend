@extends('layouts.app')

@section('title', 'Delete Service Category')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Services
      <small>Manage service categories</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('service_categories') }}">Service Categories</a></li>
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
							<li><strong>Name: </strong> {{ $service_category->name }}</li>
						</ul>

						<em>Connectivity Tickets under this record:</em>
							<ol>
								@forelse ($service_category->connectivity_tickets as $connticket)
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

						<strong>Note: </strong> The action cannot be undo after you proceed.
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('service_category.delete', ['id' => $service_category->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('service_categories') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
