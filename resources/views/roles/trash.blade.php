@extends('layouts.app')

@section('title', 'Delete Role')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Roles
    	<small>Manage role</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('roles.index') }}">Roles</a></li>
      <li class="active">Trash</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Confirm delete
            </h3>
          </div>
					<div class="box-body">
						You are about to delete a record with the following details:
						<ul>
							<li><strong>Role: </strong> {{ $role->name }}</li>
							<em>Permission(s) under this role:</em>
							<ol>
								@forelse ($role->permissions as $permission)
									<li><strong>{{ $permission->name }}</strong></li>
								@empty
									<li>None</li>
								@endforelse
							</ol>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('roles.destroy', ['id' => $role->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('roles.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="row mt-4 mb-4">
	<div class="col-md-12">
		<ol class="breadcrumb">
		  <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
		  <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
		  <li class="breadcrumb-item active">Trash</li>
		</ol>
	</div>

	<div class="col-md-6">
		<div class="card border-danger">
			<div class="card-header text-white bg-danger">CONFIRM DELETE</div>

			<div class="card-body">
				You are about to delete a record with the following details:
				<ul>
					<li><strong>Role: </strong> {{ $role->name }}</li>
					<em>Permission(s) under this role:</em>
					<ol>
						@forelse ($role->permissions as $permission)
							<li><strong>{{ $permission->name }}</strong></li>
						@empty
							<li>None</li>
						@endforelse
					</ol>
				</ul>

				<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
			</div>

			<div class="card-footer">
				<form method="post" action="{{ route('roles.destroy', ['id' => $role->id]) }}">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger">Proceed</button>
					<a href="{{ route('roles.index') }}" class="btn btn-secondary">No, go back</a>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
