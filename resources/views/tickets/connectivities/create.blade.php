@extends('layouts.app')

@section('title', 'Create Connectivity Ticket')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Service Call Tickets
      <small>Manage connectivity tickets</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('ticket.connectivities') }}">Connectivity Tickets</a></li>
      <li class="active">Create ticket</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Create connectivity Ticket
            </h3>
          </div>
          <form method="post" action="{{ route('ticket.connectivity.store') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-8">
									@include ('errors.list')
									@include ('successes.list')

									<div class="row">
										<div class="col-md-12">
											<div class="form-group {{ $errors->has('problem') ? 'has-error' : '' }}">
												<label>Problem</label>
												<textarea class="form-control" type="text" name="problem" placeholder="Problem" autofocus>{{ old('problem') }}</textarea>
												@if ($errors->has('problem'))
													<span class="form-text text-danger">
														{{ $errors->first('problem') }}
													</span>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<!-- ---------------- -->
											<!-- SERVICE PROVIDER -->
											<!-- ---------------- -->
											<div class="form-group {{ $errors->has('service_provider_select') ? 'has-error' : ($errors->has('service_provider_input') ? 'has-error' : ($errors->has('service_provider_duplicate') ? 'has-error' : '')) }}">
												<label>Service Provider</label>
												<span id="service_provider_select">
													<a id="add_service_provider_btn" href="javascript:void(0);" class="pull-right"><em>(add new)</em></a>
													<select class="form-control select2" name="service_provider_select">
														@foreach ($service_providers as $service_provider)
															<option
																value="{{ $service_provider->id }}"
																{{ old('service_provider_select') == $service_provider->id ? 'selected' : '' }}
																>{{ $service_provider->name }}</option>
														@endforeach
													</select>
													@if ($errors->has('service_provider_select'))
														<span class="form-text text-danger">
															{{ $errors->first('service_provider_select') }}
														</span>
													@endif
												</span>

												<span id="service_provider_input" style="display:none;">
													<a id="select_service_provider_btn" href="javascript:void(0);" class="pull-right"><em>(select existing)</em></a>
													<input class="form-control" type="text" name="service_provider_input" value="{{ old('service_provider_input') }}" placeholder="Service Provider">
													@if ($errors->has('service_provider_duplicate'))
														<span class="form-text text-danger">
															{{ $errors->first('service_provider_duplicate') }}
														</span>
													@endif
													@if ($errors->has('service_provider_input'))
														<span class="form-text text-danger">
															{{ $errors->first('service_provider_input') }}
														</span>
													@endif
												</span>
											</div>
										</div>

										<div class="col-md-6">

											<!-- ------------ -->
											<!-- SERVICE TYPE -->
											<!-- ------------ -->
											<div class="form-group {{ $errors->has('service_type_select') ? 'has-error' : ($errors->has('service_type_input') ? 'has-error' : ($errors->has('service_type_duplicate') ? 'has-error' : '')) }}">
												<label>Service Type</label>
												<span id="service_type_select">
													<a id="add_service_type_btn" href="javascript:void(0);" class="pull-right"><em>(add new)</em></a>
													<select class="form-control select2" name="service_type_select">
														@foreach ($service_types as $service_type)
															<option
																value="{{ $service_type->id }}"
																{{ old('service_type_select') == $service_type->id ? 'selected' : '' }}
																>{{ $service_type->name }}</option>
														@endforeach
													</select>
													@if ($errors->has('service_type_select'))
														<span class="form-text text-danger">
															{{ $errors->first('service_type_select') }}
														</span>
													@endif
												</span>

												<span id="service_type_input" style="display:none;">
													<a id="select_service_type_btn" href="javascript:void(0);" class="pull-right"><em>(select existing)</em></a>
													<input class="form-control" type="text" name="service_type_input" value="{{ old('service_type_input') }}" placeholder="Service Type">
													@if ($errors->has('service_type_duplicate'))
														<span class="form-text text-danger">
															{{ $errors->first('service_type_duplicate') }}
														</span>
													@endif
													@if ($errors->has('service_type_input'))
														<span class="form-text text-danger">
															{{ $errors->first('service_type_input') }}
														</span>
													@endif
												</span>
											</div>
										</div>
									</div>


									<div class="row">
										<div class="col-md-6">
											<!-- ---------------- -->
											<!-- SERVICE CATEGORY -->
											<!-- ---------------- -->
											<div class="form-group {{ $errors->has('service_category_select') ? 'has-error' : ($errors->has('service_category_input') ? 'has-error' : ($errors->has('service_category_duplicate') ? 'has-error' : '')) }}">
												<label>Service Category</label>
												<span id="service_category_select">
													<a id="add_service_category_btn" href="javascript:void(0);" class="pull-right"><em>(add new)</em></a>
													<select class="form-control select2" name="service_category_select">
														@foreach ($service_categories as $service_category)
															<option
																value="{{ $service_category->id }}"
																{{ old('service_category_select') == $service_category->id ? 'selected' : '' }}
																>{{ $service_category->name }}</option>
														@endforeach
													</select>
													@if ($errors->has('service_category_select'))
														<span class="form-text text-danger">
															{{ $errors->first('service_category_select') }}
														</span>
													@endif
												</span>

												<span id="service_category_input" style="display:none;">
													<a id="select_service_category_btn" href="javascript:void(0);" class="pull-right"><em>(select existing)</em></a>
													<input class="form-control" type="text" name="service_category_input" value="{{ old('service_category_input') }}" placeholder="Service Category">
													@if ($errors->has('service_category_duplicate'))
														<span class="form-text text-danger">
															{{ $errors->first('service_category_duplicate') }}
														</span>
													@endif
													@if ($errors->has('service_category_input'))
														<span class="form-text text-danger">
															{{ $errors->first('service_category_input') }}
														</span>
													@endif
												</span>
											</div>
										</div>
										<div class="col-md-6">
											<!-- ------ -->
											<!-- BRANCH -->
											<!-- ------ -->
											<div class="form-group {{ $errors->has('branch_select') ? 'has-error' : ($errors->has('branch_input') ? 'has-error' : ($errors->has('branch_duplicate') ? 'has-error' : '')) }}">
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

												<span id="branch_input" style="display:none;">
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
												<label>Position</label>
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
											<div class="form-group {{ $errors->has('problem_reported') ? 'has-error' : '' }}">
					            	<label for="problem_reported">Problem Reported/Traced</label>
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
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('resolution_reported') ? 'has-error' : '' }}">
					            	<label for="resolution_reported">Resolution Reported/Traced</label>
					              <input type="text" class="form-control" id="resolution-reported" name="resolution_reported" value="{{ old('resolution_reported') }}">

						            <script type="text/javascript">
							            $(function () {
							            	$('#resolution-reported').datetimepicker({
							            		format:'Y-m-d H:i'
							            	});
							            });
								        </script>

							        	@if ($errors->has('resolution_reported'))
					              	<span class="form-text text-danger">
					              		{{ $errors->first('resolution_reported') }}
					              	</span>
					              @endif
					            </div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('problem_reported_to_isp') ? 'has-error' : '' }}">
					            	<label for="problem_reported_to_isp">Problem Reported to ISP</label>
					              <input type="text" class="form-control" id="problem-reported-to-isp" name="problem_reported_to_isp" value="{{ old('problem_reported_to_isp') }}">

						            <script type="text/javascript">
							            $(function () {
							            	$('#problem-reported-to-isp').datetimepicker({
							            		format:'Y-m-d H:i'
							            	});
							            });
								        </script>

							        	@if ($errors->has('problem_reported_to_isp'))
					              	<p class="help-block">
					              		{{ $errors->first('problem_reported_to_isp') }}
					              	</p>
					              @endif
					            </div>
										</div>
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('provider_ticket') ? 'has-error' : '' }}">
												<label>Provider Ticket</label>
												<input class="form-control" type="text" name="provider_ticket" value="{{ old('provider_ticket') }}" placeholder="Provider Ticket" autofocus>
												@if ($errors->has('provider_ticket'))
													<span class="form-text text-danger">
														{{ $errors->first('provider_ticket') }}
													</span>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
												<label>Last Update</label>
												<textarea rows="1" class="form-control" type="text" name="remarks" placeholder="Last Update" autofocus>{{ old('remarks') }}</textarea>
												@if ($errors->has('remarks'))
													<span class="form-text text-danger">
														{{ $errors->first('remarks') }}
													</span>
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-8">
									<button type="submit" class="btn btn-primary">Create</button>
									<a href="{{ route('ticket.connectivities') }}" class="btn btn-default pull-right">Back</a>
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
	$(document).ready(function () {

		// Initialize Select2 Elements
    $('.select2').select2();

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


		// ----------------
		// SERVICE PROVIDER
		// ----------------

		// defaults
		$('#service_provider_input').hide();

		@if (\Session::has('service_provider_select'))
			@if (\Session::get('service_provider_select') == 1)
				service_provider_select();
			@else
				service_provider_new();
			@endif
		@endif

		$('#add_service_provider_btn').click(function () {
			service_provider_new();
		});

		$('#select_service_provider_btn').click(function () {
			service_provider_select();
		});

		function service_provider_new () {
			$('#service_provider_select').hide();
			$('#service_provider_select select').attr('name', 'service_provider');

			$('#service_provider_input').show();
			$('#service_provider_input input').attr('name', 'service_provider_input').focus();
		}

		function service_provider_select () {
			$('#service_provider_input').hide();
			$('#service_provider_input input').attr('name', 'service_provider');

			$('#service_provider_select').show();
			$('#service_provider_select select').attr('name', 'service_provider_select').focus();
		}

		// ------------
		// SERVICE TYPE
		// ------------

		// defaults
		$('#service_type_input').hide();

		@if (\Session::has('service_type_select'))
			@if (\Session::get('service_type_select') == 1)
				service_type_select();
			@else
				service_type_new();
			@endif
		@endif

		$('#add_service_type_btn').click(function () {
			service_type_new();
		});

		$('#select_service_type_btn').click(function () {
			service_type_select();
		});

		function service_type_new () {
			$('#service_type_select').hide();
			$('#service_type_select select').attr('name', 'service_type');

			$('#service_type_input').show();
			$('#service_type_input input').attr('name', 'service_type_input').focus();
		}

		function service_type_select () {
			$('#service_type_input').hide();
			$('#service_type_input input').attr('name', 'service_type');

			$('#service_type_select').show();
			$('#service_type_select select').attr('name', 'service_type_select').focus();
		}

		// ----------------
		// SERVICE CATEGORY
		// ----------------

		// defaults
		$('#service_category_input').hide();

		@if (\Session::has('service_category_select'))
			@if (\Session::get('service_category_select') == 1)
				service_category_select();
			@else
				service_category_new();
			@endif
		@endif

		$('#add_service_category_btn').click(function () {
			service_category_new();
		});

		$('#select_service_category_btn').click(function () {
			service_category_select();
		});

		function service_category_new () {
			$('#service_category_select').hide();
			$('#service_category_select select').attr('name', 'service_category');

			$('#service_category_input').show();
			$('#service_category_input input').attr('name', 'service_category_input').focus();
		}

		function service_category_select () {
			$('#service_category_input').hide();
			$('#service_category_input input').attr('name', 'service_category');

			$('#service_category_select').show();
			$('#service_category_select select').attr('name', 'service_category_select').focus();
		}

	});
</script>
@endpush
