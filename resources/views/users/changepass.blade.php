@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Change password</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('users.index') }}">Users</a></li>
      <li class="active">Change password</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Change password</h3>
          </div>
          <form method="post" action="{{ route('user.changepass_proceed') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="form-group {{ $errors->has('currentpassword') ? 'has-error' : '' }}">
										<label>Current Password</label>
										<input class="form-control" type="password" name="currentpassword" placeholder="Current Password" autofocus>
										@if ($errors->has('currentpassword'))
											<span class="form-text text-danger">
												{{ $errors->first('currentpassword') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('newpassword') ? 'has-error' : '' }}">
										<label>New Password</label>
										<input class="form-control" type="password" name="newpassword" placeholder="New Password">
										@if ($errors->has('newpassword'))
											<span class="form-text text-danger">
												{{ $errors->first('newpassword') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('newpassword_confirmation') ? 'has-error' : '' }}">
										<label>Confirm Password</label>
										<input class="form-control" type="password" name="newpassword_confirmation" placeholder="Confirm Password">
									</div>

									<span class="text-danger"><strong>Note:</strong> You will be logged out after you change your password.</span>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button type="submit" class="btn btn-danger">Proceed</button>
									<a href="{{ route('home') }}" class="btn btn-default">No, go back</a>
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
