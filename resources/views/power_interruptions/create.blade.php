@extends('layouts.app')

@section('title', 'Add Power Interruption Log')

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
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add power interruption
            </h3>
          </div>
          <form method="post" action="{{ route('power_interruption.store') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-8">
									@include ('errors.list')
									@include ('successes.list')

									<div class="row">
										<div class="col-md-6">
											<!-- ------ -->
											<!-- BRANCH -->
											<!-- ------ -->
											<div class="form-group {{ $errors->has('branch_select') ? 'has-error' : ($errors->has('branch_input') ? 'has-danger' : ($errors->has('branch_duplicate') ? 'has-danger' : '')) }}">
												<label>Branch</label>
												<span id="branch_select">
													<!-- <a id="add_branch_btn" href="javascript:void(0);" class="pull-right"><em>(add new)</em></a> -->
													<select class="form-control select2" name="branch_select">
														@foreach ($branches as $branch)
															<option
																value="{{ $branch->id }}"
																{{ old('branch_select') == $branch->id ? 'selected' : '' }}
																>{{ $branch->name }}</option>
														@endforeach
													</select>
													@if ($errors->has('branch_select'))
														<span class="form-text text-danger">
															{{ $errors->first('branch_select') }}
														</span>
													@endif
												</span>

												<span id="branch_input">
													<a id="select_branch_btn" href="javascript:void(0);" class="pull-right"><em>(select existing)</em></a>
													<input class="form-control" type="text" name="branch_input" value="{{ old('branch_input') }}" placeholder="Branch">
													@if ($errors->has('branch_duplicate'))
														<span class="form-text text-danger">
															{{ $errors->first('branch_duplicate') }}
														</span>
													@endif
													@if ($errors->has('branch_input'))
														<span class="form-text text-danger">
															{{ $errors->first('branch_input') }}
														</span>
													@endif
												</span>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group {{ $errors->has('problem_reported') ? 'has-error' : '' }}">
						          	<label for="problem_reported">Problem Reported</label>
						            <input type="text" class="form-control" id="problem-reported" name="problem_reported" value="{{ old('problem_reported') }}">

						            <script type="text/javascript">
							            $(function () {
							            	$('#problem-reported').datetimepicker({
							            		format:'Y-m-d H:i'
							            	});
							            });
								        </script>

							        	@if ($errors->has('problem_reported'))
						            	<span class="form-text text-danger">
														{{ $errors->first('problem_reported') }}
													</span>
						            @endif
						          </div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('reported_by_name') ? 'has-error' : '' }}">
												<label>Reported By</label>
												<input class="form-control" type="text" name="reported_by_name" value="{{ old('reported_by_name') }}" placeholder="Complete Name" autofocus>
												@if ($errors->has('reported_by_name'))
													<span class="form-text text-danger">
														{{ $errors->first('reported_by_name') }}
													</span>
												@endif
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('reported_by_position') ? 'has-error' : '' }}">
												<label>Position (Optional)</label>
												<input class="form-control" type="text" name="reported_by_position" value="{{ old('reported_by_position') }}" placeholder="Position" autofocus>
												@if ($errors->has('reported_by_position'))
													<span class="form-text text-danger">
														{{ $errors->first('reported_by_position') }}
													</span>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('datetime_from') ? 'has-error' : '' }}">
						          	<label for="datetime_from">Date/Time From</label>
						            <input type="text" class="form-control" id="date-time-from" name="datetime_from" value="{{ old('datetime_from') }}">

						            <script type="text/javascript">
							            $(function () {
							            	$('#date-time-from').datetimepicker({
							            		format:'Y-m-d H:i'
							            	});
							            });
								        </script>

							        	@if ($errors->has('datetime_from'))
						            	<span class="form-text text-danger">
														{{ $errors->first('datetime_from') }}
													</span>
						            @endif
						          </div>
										</div>
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('datetime_to') ? 'has-error' : '' }}">
						          	<label for="datetime_to">Date/Time To</label>
						            <input type="text" class="form-control" id="date-time-to" name="datetime_to" value="{{ old('datetime_to') }}">

						            <script type="text/javascript">
							            $(function () {
							            	$('#date-time-to').datetimepicker({
							            		format:'Y-m-d H:i'
							            	});
							            });
								        </script>

							        	@if ($errors->has('datetime_to'))
						            	<span class="form-text text-danger">
														{{ $errors->first('datetime_to') }}
													</span>
						            @endif
						          </div>
										</div>
									</div>

				          <div class="form-group {{ $errors->has('remarks') ? 'has-danger' : '' }}">
										<label>Remarks (Optional)</label>
										<textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
										@if ($errors->has('remarks'))
											<span class="form-text text-danger">
												{{ $errors->first('remarks') }}
											</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-8">
									<button value=0 name="savebtn" type="submit" class="btn btn-primary">Save & Add new</button>
									<button value=1 name="savebtn" type="submit" class="btn btn-danger">Save & Return</button>
									<a href="{{ route('power_interruptions') }}" class="btn btn-default pull-right">Back</a>
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
  $(function () {
    $('#daterange').daterangepicker({ 'timePicker': true, 'timePickerIncrement': 30, 'format': 'MM/DD/YYYY h:mm A' })

		// Initialize Select2 Elements
    $('.select2').select2();
  })
</script>

<script>
		// -------
		// BRANCH
		// -------

		// defaults
		$('#branch_input').hide();

		@if (\Session::has('branch_select'))
			@if (\Session::get('branch_select') == 1)
				branch_select();
			@else
				branch_new();
			@endif
		@endif

		$('#add_branch_btn').click(function () {
			branch_new();
		});

		$('#select_branch_btn').click(function () {
			branch_select();
		});

		function branch_new () {
			$('#branch_select').hide();
			$('#branch_select select').attr('name', 'branch');

			$('#branch_input').show();
			$('#branch_input input').attr('name', 'branch_input').focus();
		}

		function branch_select () {
			$('#branch_input').hide();
			$('#branch_input input').attr('name', 'branch');

			$('#branch_select').show();
			$('#branch_select select').attr('name', 'branch_select').focus();
		}
</script>
@endpush
