@extends('layouts.app')

@section('title', 'Delete Power Interruption Log')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Power Interruptions
      <small>Manage power interruptions</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('power_interruptions') }}">Power Interruptions</a></li>
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
							<li><strong>LOG #: </strong> {{ $power_interruption->id }}</li>
							<li><strong>Branch: </strong> {{ $power_interruption->branch->name }}</li>
							<li><strong>Logged By: </strong> {{ $power_interruption->user->first_name }} {{ $power_interruption->user->first_name }}</li>
							<li><strong>Reported By: </strong> {{ $power_interruption->reported_by_name }} ({{ $power_interruption->reported_by_position }})</li>
							<li><strong>Date/Time From: </strong> {{ $power_interruption->datetime_from }}</li>
							<li><strong>Date/Time To: </strong> {{ $power_interruption->datetime_to }}</li>
							<li><strong>Remarks: </strong> {{ $power_interruption->remarks }}</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('power_interruption.delete', ['id' => $power_interruption->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('power_interruptions') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
