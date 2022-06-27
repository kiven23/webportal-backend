@extends('layouts.app')

@section('title', 'Edit Computerware Ticket')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Service Call Tickets
      <small>Manage computerware tickets</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('ticket.computerwares') }}">Computerware Tickets</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit computerware ticket
            </h3>
          </div>
          <form method="post" action="{{ route('ticket.computerware.update', ['id' => $computerware->id]) }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-8">
									@include ('errors.list')
									@include ('successes.list')
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									<!-- ---- -->
									<!-- ITEM -->
									<!-- ---- -->
									<div class="form-group {{ $errors->has('item_select') ? 'has-error' : ($errors->has('item_input') ? 'has-error' : '') }}">
										<label>Item</label>
										<span id="item_select">
											<a href="{{ route('item.create.computerware.edit', ['id' => $computerware->id]) }}" class="pull-right"><em>(add new)</em></a>
											<select class="form-control select2" name="item_select">
												@foreach ($items as $item)
													<option
														value="{{ $item->id }}"
														@if (\Session::has('item'))
															@if (\Session::get('item') === $item->id)
																selected
															@endif
														@else
															@if ($computerware->product_item_id === $item->id)
																selected
															@endif
														@endif
														>
														{{ $item->brand->name }}
														{{ $item->model }} ({{ $item->category->name }})
													</option>
												@endforeach
											</select>
											@if ($errors->has('item_select'))
												<span class="form-text text-danger">
													{{ $errors->first('item_select') }}
												</span>
											@endif
										</span>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('product_item_serial_number') ? 'has-error' : '' }}">
										<label>Serial Number</label>
										<input class="form-control" type="text" name="product_item_serial_number" value="{{ $computerware->product_item_serial_number }}" placeholder="Serial Number" autofocus>
										@if ($errors->has('product_item_serial_number'))
											<span class="form-text text-danger">
												{{ $errors->first('product_item_serial_number') }}
											</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group {{ $errors->has('reported_by_name') ? 'has-error' : '' }}">
										<label>Reported By</label>
										<input class="form-control" type="text" name="reported_by_name" value="{{ $computerware->reported_by_name }}" placeholder="Complete Name" autofocus>
										@if ($errors->has('reported_by_name'))
											<span class="form-text text-danger">
												{{ $errors->first('reported_by_name') }}
											</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('reported_by_position') ? 'has-error' : '' }}">
										<label>Position</label>
										<input class="form-control" type="text" name="reported_by_position" value="{{ $computerware->reported_by_position }}" placeholder="Position" autofocus>
										@if ($errors->has('reported_by_position'))
											<span class="form-text text-danger">
												{{ $errors->first('reported_by_position') }}
											</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
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
														{{ $computerware->branch_id == $branch->id ? 'selected' : '' }}
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

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('assigned_tech') ? 'has-error' : '' }}">
										<label>Assigned Tech.</label>
										<input class="form-control" type="text" name="assigned_tech" value="{{ $computerware->assigned_tech }}" placeholder="Complete Name" autofocus>
										@if ($errors->has('assigned_tech'))
											<span class="form-text text-danger">
												{{ $errors->first('assigned_tech') }}
											</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									
								</div>

								<div class="col-md-4">
									
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group {{ $errors->has('problem') ? 'has-error' : '' }}">
										<label>Problem</label>
										<textarea class="form-control" type="text" name="problem" placeholder="Problem" autofocus>{{ $computerware->problem }}</textarea>
										@if ($errors->has('problem'))
											<span class="form-text text-danger">
												{{ $errors->first('problem') }}
											</span>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
										<label>Remarks</label>
										<textarea class="form-control" type="text" name="remarks" placeholder="Remarks" autofocus>{{ $computerware->remarks }}</textarea>
										@if ($errors->has('remarks'))
											<span class="form-text text-danger">
												{{ $errors->first('remarks') }}
											</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group {{ $errors->has('report_status') ? 'has-error' : '' }}">
										<label>Report Status</label>
										<select name="report_status" class="form-control select2">
											<option {{ $computerware->report_status === 1 ? 'selected' : '' }} value="1">Open</option>
											<option {{ $computerware->report_status === 2 ? 'selected' : '' }} value="2">Closed</option>
										</select>
										@if ($errors->has('report_status'))
											<span class="form-text text-danger">
												{{ $errors->first('report_status') }}
											</span>
										@endif
									</div>
								</div>
							</div>

						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-8">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{ route('ticket.computerwares') }}" class="btn btn-default pull-right">Back</a>
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

	});
</script>
@endpush
