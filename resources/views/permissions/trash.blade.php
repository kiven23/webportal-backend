@extends('layouts.app')

@section('title', 'Delete Permission')

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
							<li><strong>Permission: </strong> {{ $permission->name }}</li>
							<em>Parent role(s) of this permission:</em>
							<ol>
								@forelse ($permission->roles as $role)
									<li><strong>{{ $role->name }}</strong></li>
								@empty
									<li>None</li>
								@endforelse
							</ol>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('permissions.destroy', ['id' => $permission->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('permissions.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
