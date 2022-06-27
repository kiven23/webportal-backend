@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Permissions
      <small>Manage permission</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('permissions.index') }}">Permissions</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit permission
            </h3>
          </div>
          <form method="post" action="{{ route('permissions.update', ['id' => $permission->id]) }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
		        		<div class="col-md-5">
				          @include ('errors.list')
				          @include ('successes.list')

									<div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }}">
										<label>Permission</label>
										<input class="form-control" type="text" name="name" value="{{ $permission->name }}" placeholder="Name" autofocus>
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
									<a href="{{ route('permissions.index') }}" class="btn btn-default pull-right">Back</a>
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
