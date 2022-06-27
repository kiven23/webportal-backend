@extends('layouts.app')

@section('title', 'Delete Interview Schedule')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Scheduling
      <small>Manage interview schedules</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('interview_scheds.index') }}">Interview Schedules</a></li>
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
		          <li><strong>Applicant Name: </strong> {{ $isched->applicant_name }}</li>
		          <li><strong>Contact Number: </strong> {{ $isched->contact_number }}</li>
		          <li><strong>Position Applying For: </strong> {{ $isched->position_applying }}</li>
		          <li><strong>Requirements on Hand: </strong> {{ $isched->requirements_on_hand }}</li>
							<li><strong>Approval Number: </strong> {{ $isched->approval_number }}</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('interview_sched.delete', ['id' => $isched->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('interview_scheds.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
