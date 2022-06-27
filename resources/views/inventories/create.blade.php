@extends('layouts.app')

@section('title', 'Add Inventory Reconciliation')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reconciliation
      <small>Manage inventory reconciliation</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('inventories') }}">Inventory Recon</a></li>
      <li class="active">Import</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              Import
            </h3>
          </div>
          <form method="post" action="{{ route('inventory.store') }}" enctype="multipart/form-data">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
										<label>Upload file</label>
										<input class="form-control form-control-sm" type="file" name="file" autofocus>
										@if ($errors->has('file'))
											<span class="form-text text-danger">
												{{ $errors->first('file') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('branch_id') ? 'has-error' : '' }}">
										<label>Branch</label>
										<select class="form-control select2" name="branch_id">
											@foreach ($branches as $branch)
												<option value="{{ $branch->id }}">{{ $branch->name }}</option>
											@endforeach
										</select>
										@if ($errors->has('branch_id'))
											<span class="form-text text-danger">
												{{ $errors->first('branch_id') }}
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
									<a href="{{ route('inventories') }}" class="btn btn-default pull-right">Back</a>
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
	});
</script>
@endpush
