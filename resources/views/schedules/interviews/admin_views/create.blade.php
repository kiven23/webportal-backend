@extends('layouts.app')

@section('title', 'Add Interview Schedule')

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
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add interview schedule
            </h3>
          </div>
          <form method="post" action="{{ route('interview_sched.store') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

                  <div class="form-group {{ $errors->has('branch') ? 'has-error' : '' }}">
										<label>Branch</label>
										<select name="branch" class="form-control select2">
                      @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                      @endforeach
                    </select>
									</div>

				          <div class="form-group {{ $errors->has('applicant_name') ? 'has-error' : '' }}">
										<label>Name of Applicant</label>
										<input type="text" class="form-control" name="applicant_name" value="{{ old('applicant_name') }}" placeholder="Full Name">
										@if ($errors->has('applicant_name'))
											<span class="form-text text-danger">
												{{ $errors->first('applicant_name') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('contact_number') ? 'has-error' : '' }}">
										<label>Contact Number</label>
										<input type="text" class="form-control" name="contact_number" value="{{ old('contact_number') }}" placeholder="Contact Number">
										@if ($errors->has('contact_number'))
											<span class="form-text text-danger">
												{{ $errors->first('contact_number') }}
											</span>
										@endif
									</div>

				          <div class="form-group {{ $errors->has('position_applying') ? 'has-error' : '' }}">
										<label>Position Applying For</label>
										<input type="text" class="form-control" name="position_applying" value="{{ old('position_applying') }}" placeholder="Position">
										@if ($errors->has('position_applying'))
											<span class="form-text text-danger">
												{{ $errors->first('position_applying') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('approval_number') ? 'has-error' : '' }}">
										<label>Approval Number</label>
										<input type="text" class="form-control" name="approval_number" value="{{ old('approval_number') }}" placeholder="Approval Number">
										@if ($errors->has('approval_number'))
											<span class="form-text text-danger">
												{{ $errors->first('approval_number') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('agreement') ? 'has-error' : '' }}">
										<div class="checkbox icheck">
											<label>
												<input type="checkbox" name="agreement">
												<strong>I agree that all the ff. requirements are met:</strong>
												<ul>
													<li>Application for employment</li>
													<li>Resume</li>
													<li>Birth Certificate</li>
													<li>Exams with passing grade</li>
													<li>Background Investigation</li>
												</ul>
											</label>
										</div>
										@if ($errors->has('agreement'))
											<span class="form-text text-danger">
												{{ $errors->first('agreement') }}
											</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button value=0 name="savebtn" type="submit" class="btn btn-primary">Save & Add new</button>
									<button value=1 name="savebtn" type="submit" class="btn btn-danger">Save & Return</button>
									<a href="{{ route('interview_scheds.index') }}" class="btn btn-default pull-right">Back</a>
								</div>
							</div>
						</div>
					</form>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
$('.select2').select2();

$('input[type="checkbox"]').iCheck({
	checkboxClass: 'icheckbox_minimal-green',
});
</script>
@endpush