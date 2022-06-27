@extends('layouts.app')

@section('title', 'Edit Service Category')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Services
      <small>Manage service categories</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('service_categories') }}">Service Categories</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit service category
            </h3>
          </div>
          <form method="post" action="{{ route('service_category.update', ['id' => $service_category->id]) }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
										<label>Name</label>
										<input class="form-control" type="text" name="name" value="{{ $service_category->name }}" placeholder="Name">
										@if ($errors->has('name'))
											<span class="form-text text-danger">
												{{ $errors->first('name') }}
											</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{ route('service_categories') }}" class="btn btn-default pull-right">Cancel</a>
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
