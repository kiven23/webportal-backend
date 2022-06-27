@extends('layouts.app')

@section('title', 'Add Branch Schedule')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Branch Schedules
    	<small>Manage branch schedules</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('branch-schedules.index') }}">Branch Schedules</a></li>
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Add branch schedule
            </h3>
          </div>
          <form method="post" action="{{ route('branch-schedule.store') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

                  <div class="form-group timepicker {{ $errors->has('time_from') ? 'has-error' : '' }}">
                    <label for="time_from">Time From</label>
                    <input id="time_from"
                          name="time_from"
                          type="text"
                          class="form-control"
                          value="{{ old('time_from') }}">
                    @if ($errors->has('time_from'))
                      <p class="help-block">
                        {{ $errors->first('time_from') }}
                      </p>
                    @endif
                  </div>

                  <div class="form-group timepicker {{ $errors->has('time_to') ? 'has-error' : '' }}">
                    <label for="time_to">Time To</label>
                    <input id="time_to"
                          name="time_to"
                          type="text"
                          class="form-control"
                          value="{{ old('time_to') }}">
                    @if ($errors->has('time_to'))
                      <p class="help-block">
                        {{ $errors->first('time_to') }}
                      </p>
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
									<a href="{{ route('branch-schedules.index') }}" class="btn btn-default pull-right">Back</a>
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

  //Timepicker
  $('#time_from').timepicker({
    showInputs: true,
  });
  $('#time_to').timepicker({
    showInputs: true,
  });

</script>
@endpush