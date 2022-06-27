@extends('layouts.app')

@section('title', 'Delete Concern')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Concerns
    	<small>Manage concern</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('concerns.index') }}">Concerns</a></li>
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
							<li><strong>Branch: </strong> {{ $concern->branch->name }}</li>
							<li><strong>Reported By: </strong> {{ $concern->reported_by }}</li>
							<li><strong>Type: </strong> {{ $concern->type->name }}</li>
							<li><strong>Category: </strong> {{ $concern->category->name }}</li>
							<li><strong>Database: </strong> {{ $concern->database ? $concern->database : 'N/A' }}</li>
							<li><strong>Cause: </strong> {{ $concern->cause ? $concern->cause : 'N/A' }}</li>
							<li><strong>Remarks: </strong> {{ $concern->remarks }}</li>
							<li><strong>Resolution: </strong> {{ $concern->resolution }}</li>
							<li><strong>Date Solved: </strong> {{ $concern->date_solved }}</li>
							<li>
								<strong>Status: </strong>
								@if ($concern->status === 0)
									<span class="label label-success">Open</span>
								@else
									<span class="label label-danger">Closed</span>
								@endif
							</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('concern.delete', ['id' => $concern->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('concerns.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection