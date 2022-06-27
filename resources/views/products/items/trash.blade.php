@extends('layouts.app')

@section('title', 'Delete Product Item')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Products
      <small>Manage product items</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('items') }}">Items</a></li>
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
							<li>
								<strong>Product: </strong>
								{{ $item->brand->name }}
								{{ $item->model }}
								({{ $item->category->name }})
							</li>
							<em>Computerware Ticket(s) under this record:</em>
							<ol>
								@foreach ($item->computerware_tickets as $computerware)
									<li>
										<ul>
											<li><strong>Ticket #: </strong>{{ $computerware->id }}</li>
											<li><strong>Branch: </strong>{{ $computerware->branch->name }}</li>
											<li><strong>Problem: </strong>{{ $computerware->problem }}</li>
											<li>
												<strong>Reported By: </strong>
												{{ $computerware->reported_by_name }}
												({{ $computerware->reported_by_position }})
											</li>
											<li><strong>Logged By: </strong>{{ $computerware->user->first_name }} {{ $computerware->user->last_name }}</li>
											<li>
												<strong>Date Logged: </strong>
												{{ \Carbon\Carbon::parse($computerware->created_at)->format('F d, Y (h:i a)') }}
											</li>
										</ul>
									</li>
								@endforeach
							</ol>
						</ul>

						<span class="text-danger"><strong>Note: </strong> After you proceed, the action cannot be undo.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('item.delete', ['id' => $item->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('items') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
