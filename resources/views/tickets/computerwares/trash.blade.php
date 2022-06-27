@extends('layouts.app')

@section('title', 'Delete Computerware Ticket')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Service Call Tickets
      <small>Manage computerware tickets</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('ticket.computerwares') }}">Computerware Tickets</a></li>
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
							<li><strong>Ticket #: </strong>{{ $computerware->id }}</li>
							<li>
								<strong>Product: </strong>
								{{ $computerware->item->brand->name }}
								{{ $computerware->item->model }}
								{{ $computerware->product_item_serial_number }}
								({{ $computerware->item->category->name }})
							</li>
							<li><strong>Problem: </strong>{{ $computerware->problem }}</li>
							<li>
								<strong>Reported By: </strong>
								{{ $computerware->reported_by_name }}
								({{ $computerware->reported_by_position }})
							</li>
							<li><strong>Assigned Technical: </strong>{{ $computerware->assigned_tech }}</li>
							<li><strong>Status: </strong>{{ $computerware->report_status === 1 ? 'Open' : 'Closed' }}</li>
							<li><strong>Logged By: </strong>{{ $computerware->user->first_name }} {{ $computerware->user->last_name }}</li>
							<li>
								<strong>Date Logged: </strong>
								{{ \Carbon\Carbon::parse($computerware->created_at)->format('F d, Y (h:i a)') }}
							</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> After you proceed, the action cannot be undo.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('ticket.computerware.delete', ['id' => $computerware->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('ticket.computerwares') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
