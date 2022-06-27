@extends('layouts.app')

@section('title', 'Import Inventory Reconciliation')

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
      <li><a href="{{ route('inventory.get_raw', ['id' => $inventory->id]) }}">Get Raw
		  </a></li>
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
          <form method="post" action="{{ route('inventory.import_proceed', ['id' => $inventory->id]) }}" enctype="multipart/form-data">
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
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button type="submit" class="btn btn-primary">Import</button>
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
@endsection
