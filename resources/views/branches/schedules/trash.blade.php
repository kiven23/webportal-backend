@extends('layouts.app')

@section('title', 'Delete Branch Schedule')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Branch Schedules
    	<small>Manage branch schedule</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="{{ route('branch-schedules.index') }}">Branch Schedules</a></li>
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
							<li><strong>Time From: </strong> {{ $branch_schedule->time_from }}</li>
							<li><strong>Time To: </strong> {{ $branch_schedule->time_to }}</li>
							<em>Branches under this record:</em>
							<ol>
                @forelse ($branch_schedule->branches as $branch)
									<li><strong>{{ $branch->name ? $branch->name : 'None' }}</strong></li>
								@empty
									<li>None</li>
								@endforelse
							</ol>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
          </div>
          <div class="box-footer">
          	<form method="post" action="{{ route('branch-schedule.delete', ['id' => $branch_schedule->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('branch-schedules.index') }}" class="btn btn-default">No, go back</a>
						</form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection