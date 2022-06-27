@extends('layouts.app')

@section('title', 'File Overtime')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Overtimes
      <small>Manage overtime filing</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('overtimes') }}">Overtimes</a></li>
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add overtime
            </h3>
          </div>
          <form role="form" action="{{ route('overtime.store') }}" method="post">
				  	{{ csrf_field() }}
					  <div class="box-body">
					  	<div class="row">
					  		<div class="col-md-8">
							  	@include ('errors.list')
				    			@include ('successes.list')

				    			<strong>May I request for an OVERTIME</strong>

				    			<div class="row">
				    				<div class="col-md-6">
				    					<div class="form-group {{ $errors->has('date_from') ? 'has-error' : '' }}">
								      	<label for="date_from">From</label>
								        <input type="text"
								        			 class="form-control {{ $errors->has('date_from') ? 'is-invalid' : '' }}"
								        			 id="date_from"
								        			 name="date_from"
								        			 value="{{ old('date_from') }}">

								        <script type="text/javascript">
								          $(function () {
								          	$('#date_from').datetimepicker();
								          });
								        </script>

								      	@if ($errors->has('date_from'))
								        	<span class="form-text text-danger">
								        		{{ $errors->first('date_from') }}
								        	</span>
								        @endif
								      </div>
				    				</div>
				    				<div class="col-md-6">
				    					<div class="form-group {{ $errors->has('date_to') ? 'has-error' : '' }}">
								      	<label for="date_to">To</label>
								        <input type="text"
								        			 class="form-control {{ $errors->has('date_to') ? 'is-invalid' : '' }}"
								        			 id="date_to"
								        			 {{ \Session::get('onwards') === true ? 'disabled' : '' }}
								        			 name="date_to"
								        			 value="{{ old('date_to') }}">

								        <script type="text/javascript">
								          $(function () {
								            $('#date_to').datetimepicker({
								            	datepicker:false,
								            	format:'H:i',
								            });
								          });
								        </script>

								  			@if ($errors->has('date_to'))
								        	<span class="form-text text-danger">
								        		{{ $errors->first('date_to') }}
								        	</span>
								        @endif
								        <div id="onwards_wdo" class="overtime_checkbox">
													<label class="onwards"><input type="checkbox" name="onwards" {{ old('onwards') ? 'checked' : '' }}>&nbsp;Onwards</label>&nbsp;&nbsp;&nbsp;
													<label class="wdo"><input type="checkbox" name="working_dayoff" {{ old('working_dayoff') ? 'checked' : '' }}>&nbsp;Working Day Off</label>
												</div>
								      </div>
				    				</div>
				    			</div>

				    			<hr>

				    			<strong>for the Following Reasons:</strong>

						      <div class="row">
						      	<div class="col-md-6">
						      		<div class="form-group {{ $errors->has('tactical_event') ? 'has-danger' : '' }}">
								        <label>Due To Tactical Event At</label>
								        <textarea class="form-control {{ $errors->has('tactical_event') ? 'is-invalid' : '' }}"
						        					type="text"
						        					name="tactical_event"
						        					value="{{ old('tactical_event') }}" >{{ old('tactical_event') }}</textarea>
								      </div>
						      	</div>
						      	<div class="col-md-6">
						      		<div class="form-group {{ $errors->has('assist_department') ? 'has-danger' : '' }}">
								        <label>To Assist Other Department</label>
								        <textarea class="form-control {{ $errors->has('assist_department') ? 'is-invalid' : '' }}"
						        					type="text"
						        					name="assist_department"
						        					value="{{ old('assist_department') }}" >{{ old('assist_department') }}</textarea>
								      </div>
						      	</div>
						      </div>

						      <div class="row">
						      	<div class="col-md-6">
						      		<div class="form-group {{ $errors->has('travel_to_field') ? 'has-danger' : '' }}">
								        <label>Travel To Field</label>
								        <textarea class="form-control {{ $errors->has('travel_to_field') ? 'is-invalid' : '' }}"
						        					type="text"
						        					name="travel_to_field"
						        					value="{{ old('travel_to_field') }}" >{{ old('travel_to_field') }}</textarea>
								      </div>
						      	</div>
						      	<div class="col-md-6">
						      		<div class="form-group {{ $errors->has('admin_instruction') ? 'has-danger' : '' }}">
								        <label>Admin Instruction To</label>
								        <textarea class="form-control {{ $errors->has('admin_instruction') ? 'is-invalid' : '' }}"
						        					type="text"
						        					name="admin_instruction"
						        					value="{{ old('admin_instruction') }}" >{{ old('admin_instruction') }}</textarea>
								      </div>
						      	</div>
						      </div>

						      <div class="row">
						      	<div class="col-md-6">
						      		<div class="overtime_checkbox">
												<label><input type="checkbox" name="individual_target">&nbsp;To Attain Individual Target</label>
											</div>
						      	</div>
						      	<div class="col-md-6">
						      		<div class="overtime_checkbox">
												<label><input type="checkbox" name="pending_units">&nbsp;To Deliver Pending Units</label>
											</div>
						      	</div>
						      	<div class="col-md-6">
						      		<div class="overtime_checkbox">
												<label><input type="checkbox" name="department_target">&nbsp;To Attain Department Target</label>
											</div>
						      	</div>
						      	<div class="col-md-6">
						      		<div class="overtime_checkbox">
												<label><input type="checkbox" name="after_sales_service">&nbsp;Due To After Sales Service</label>
											</div>
						      	</div>
						      	<div class="col-md-6">
						      		<div class="overtime_checkbox">
												<label><input type="checkbox" name="deadline">&nbsp;To Finish Deadlines</label>
											</div>
						      	</div>
						      	<div class="col-md-6">
						      		<div class="overtime_checkbox">
												<label><input type="checkbox" name="client_concern">&nbsp;Attend To Client Concern/Customer Service</label>
											</div>
						      	</div>
						      </div>
						      @if ($errors->has('reason_empty'))
					        	<span class="form-text text-danger">
					        		{{ $errors->first('reason_empty') }}
					        	</span>
					        @endif
					      </div>
					    </div>
					  </div>

					  <div class="box-footer">
					  	<div class="row">
					  		<div class="col-md-8">
							    <button type="submit" class="btn btn-primary">Proceed</button>
				      		<a href="{{ route('overtimes') }}" class="btn btn-default pull-right">Cancel</a>
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
<script type="text/javascript">
$(document).ready(function(){
	$('input').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
  });

	$('#onwards_wdo .onwards').click(function () {
		var onwards = $("#onwards_wdo input[name=onwards]").is(':checked');
		date_to(onwards);
	});

	function date_to (onwards) {
		if (onwards === true) {
			$('#date_to').attr('disabled', true);
		} else {
			$('#date_to').attr('disabled', false);
		}
	}
});
</script>
@endpush