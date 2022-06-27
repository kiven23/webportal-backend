@extends('layouts.app')

@section('title', 'Complete Interview Schedule')

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
      <li class="active">Complete</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <form method="post" action="{{ route('interview_sched.complete_proceed', ['id' => $isched->id]) }}">
          {{ csrf_field() }}
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">
                Confirm completion
              </h3>
            </div>
            <div class="box-body">
              @include ('errors.list')
              @include ('successes.list')

              You are about to complete a record with the following details:
              <ul>
                <li><strong>Applicant Name: </strong> {{ $isched->applicant_name }}</li>
                <li><strong>Contact Number: </strong> {{ $isched->contact_number }}</li>
                <li><strong>Position Applying For: </strong> {{ $isched->position_applying }}</li>
                <li><strong>Approval Number: </strong> {{ $isched->approval_number }}</li>
              </ul>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group {{ $errors->has('interview_date') ? 'has-error' : '' }}">
                    <input
                      type="text"
                      class="form-control"
                      id="interview-date"
                      name="interview_date"
                      value="{{ $isched->interview_date }}"
                      placeholder="Date of Interview"
                    >

                    <script type="text/javascript">
                      $(function () {
                        $('#interview-date').datetimepicker({
                          format:'Y-m-d'
                        });
                      });
                    </script>

                    @if ($errors->has('interview_date'))
                      <span class="form-text text-danger">
                        {{ $errors->first('interview_date') }}
                      </span>
                    @endif
                  </div>
                </div>
              </div>

              <span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
            </div>

            <div class="box-footer">
              <div class="btn-group">
                <button value=1 name="complete_btn" type="submit" class="btn btn-default"><i class="fa fa-check-circle"></i>&nbsp;Passed</button>
                <button value=2 name="complete_btn" type="submit" class="btn btn-default"><i class="fa fa-times-circle"></i>&nbsp;Failed</button>
              </div>
              <a href="{{ route('interview_scheds.index') }}" class="btn btn-default">No, go back</a>
            </div>
          </div>
        </form>
			</div>
		</div>
	</section>
</div>
@endsection
