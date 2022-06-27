@extends('layouts.app')

@section('title', 'Add Permission')

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
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add permission
            </h3>
          </div>
          <form method="post" action="{{ route('permissions.store') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
		        		<div class="col-md-5">
				          @include ('errors.list')
				          @include ('successes.list')

									<div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }}">
										<label>Permission</label>
										<input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Name" autofocus>
										@if ($errors->has('name'))
											<span class="form-text text-danger">
												{{ $errors->first('name') }}
											</span>
										@endif
									</div>

				          <div class="form-group">
				            <label for="">Assign permission to roles</label><br>
				            @foreach ($roles as $role)
				            	<div class="checkbox icheck">
				            		<label>
				                	{{ Form::checkbox('roles[]',  $role->id ) }}
				                	{{ $role->name }}
				                </label>
			                </div>
				            @endforeach
				          </div>
				        </div>
				      </div>
		        </div>

		        <div class="box-footer">
		        	<div class="row">
		        		<div class="col-md-5">
									<button value=0 name="savebtn" type="submit" class="btn btn-primary">Save & Add new</button>
									<button value=1 name="savebtn" type="submit" class="btn btn-danger">Save & Return</button>
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
@stop

@push('scripts')
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
    });
  });
</script>
@endpush
