@extends('layouts.app')

@section('title', 'Edit Branch')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Branches
    	<small>Manage branch</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
		  <li><a href="{{ route('branches.index') }}">Branches</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Edit branch</h3>
          </div>
          <form method="post" action="{{ route('branch.update', ['id' => $branch->id]) }}">
						{{ csrf_field() }}
          	<div class="box-body">
							<div class="row">
								<div class="col-md-5">

									<!-- START OF MESSAGE NOTIFICATION -->
										@include ('errors.list')
										@include ('successes.list')
									<!-- END OF MESSAGE NOTIFICATION -->

									<div class="form-group {{ $errors->has('machine_number') ? 'has-error' : '' }}">
										<label>Machine Number</label>
										<input class="form-control" type="text" name="machine_number" value="{{ $branch->machine_number }}" placeholder="Machine Number">
										@if ($errors->has('machine_number'))
											<span class="form-text text-danger">
												{{ $errors->first('machine_number') }}
											</span>
										@endif
									</div>

									<!-- ------ -->
									<!-- REGION -->
									<!-- ------ -->
									<div class="form-group {{ $errors->has('region_select') ? 'has-error' : ($errors->has('region_input') ? 'has-error' : ($errors->has('region_duplicate') ? 'has-error' : '')) }}">
										<label>Region</label>
										<span id="region_select">
											<a id="add_region_btn" href="javascript:void(0);" class="pull-right"><em>(add new)</em></a>
											<select class="form-control select2" name="region_select">
												@foreach ($regions as $region)
													<option {{ $region->id === $branch->region_id ? 'selected' : '' }} value="{{ $region->id }}">{{ $region->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('region_select'))
												<span class="form-text text-danger">
													{{ $errors->first('region_select') }}
												</span>
											@endif
										</span>

										<span id="region_input">
											<a id="select_region_btn" href="javascript:void(0);" class="pull-right"><em>(select existing)</em></a>
											<input class="form-control" type="text" name="region_input" value="{{ old('region_input') }}" placeholder="Region">
											@if ($errors->has('region_duplicate'))
												<span class="form-text text-danger">
													{{ $errors->first('region_duplicate') }}
												</span>
											@endif
											@if ($errors->has('region_input'))
												<span class="form-text text-danger">
													{{ $errors->first('region_input') }}
												</span>
											@endif
										</span>
									</div>

									<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
										<label>Name</label>
										<input class="form-control" type="text" name="name" value="{{ $branch->name }}" placeholder="Name">
										@if ($errors->has('name'))
											<span class="form-text text-danger">
												{{ $errors->first('name') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('whscode') ? 'has-error' : '' }}">
										<label>Whs Code</label>
										<input class="form-control" type="text" name="whscode" value="{{ $branch->whscode ? $branch->whscode : old('whscode') }}" placeholder="Whs Code">
										@if ($errors->has('whscode'))
											<span class="form-text text-danger">
												{{ $errors->first('whscode') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('bm_oic') ? 'has-error' : '' }}">
										<label>BM/OIC</label>
										<input class="form-control" type="text" name="bm_oic" value="{{ $branch->bm_oic ? $branch->bm_oic : old('bm_oic') }}" placeholder="Complete Name">
										@if ($errors->has('bm_oic'))
											<span class="form-text text-danger">
												{{ $errors->first('bm_oic') }}
											</span>
										@endif
									</div>

									<div class="form-group">
				            <label>Schedule</label>
				            <select name="bsched_id" class="form-control select2">
				            	<option value="0">None</option>
				            	@foreach ($bscheds as $bsched)
				            		<option value="{{ $bsched->id }}">
				            			{{ \Carbon\Carbon::parse($bsched->time_from)->format('h:i a') }} -
				                            {{ \Carbon\Carbon::parse($bsched->time_to)->format('h:i a') }}
				            		</option>
				            	@endforeach
				            </select>
				          </div>
								</div>
							</div>
	          </div>
	          <div class="box-footer">
	          	<div class="row">
	          		<div class="col-md-5">
	          			<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{ route('branches.index') }}" class="btn btn-default pull-right">Cancel</a>
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

		// --------------------
		// ------ REGION ------
		// --------------------

		// defaults
		$('#region_input').hide();

		@if (\Session::has('region_select'))
			@if (\Session::get('region_select') == 1)
				region_select();
			@else
				region_new();
			@endif
		@endif

		$('#add_region_btn').click(function () {
			region_new();
		});

		$('#select_region_btn').click(function () {
			region_select();
		});

		function region_new () {
			$('#region_select').hide();
			$('#region_select select').attr('name', 'region');

			$('#region_input').show();
			$('#region_input input').attr('name', 'region_input').focus();
		}

		function region_select () {
			$('#region_input').hide();
			$('#region_input input').attr('name', 'region');

			$('#region_select').show();
			$('#region_select select').attr('name', 'region_select').focus();
		}
	});
</script>
@endpush
