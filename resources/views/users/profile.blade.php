@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Profile
      <small>Manage profile</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Profile</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
        	<div class="box-header with-border">
        		<h3 class="box-title">Profile</h3>
        	</div>
        	<div class="box-body">
        		@include ('errors.list')
						@include ('successes.list')

        		<div class="row">
        			<div class="col-md-6">
	        			<div class="box box-warning box-solid">
	        				<div class="box-header with-border">
	        					<h3 class="box-title">Edit profile</h3>
	        				</div>
	        				<form action="{{ route('user.profile-update') }}" method="post">
	        					{{ csrf_field() }}
	        					{{ method_field('put') }}
		        				<div class="box-body">
		        					<div class="form-group {{ $errors->has('company_address') ? 'has-error' : '' }}">
												<label>Company Address</label>
												<input class="form-control" type="text" name="company_address" value="{{ $profile->company ? $profile->company->address : '' }}">
												@if ($errors->has('company_address'))
													<span class="form-text text-danger">
														{{ $errors->first('company_address') }}
													</span>
												@endif
											</div>

		        					<div class="form-group">
												<label>Extn. Email 1</label>
												<input class="form-control" type="text" name="extn_email1" value="{{ $profile->extn_email1 }}">
											</div>

											<div class="form-group">
												<label>Extn. Email 2</label>
												<input class="form-control" type="text" name="extn_email2" value="{{ $profile->extn_email2 }}">
											</div>

											<div class="form-group">
												<label>Extn. Email 3</label>
												<input class="form-control" type="text" name="extn_email3" value="{{ $profile->extn_email3 }}">
											</div>
		        				</div>
		        				<div class="box-footer">
		        					<div class="row">
												<div class="col-md-5">
													<button type="submit" class="btn btn-warning">Update</button>
												</div>
											</div>
		        				</div>
	        				</form>
	        			</div>
	        		</div>
        			<div class="col-md-6">
        				<div class="box box-danger box-solid">
				          <div class="box-header with-border">
				            <h3 class="box-title">Change password</h3>
				          </div>
				          <form method="post" action="{{ route('user.changepass') }}">
										{{ csrf_field() }}
										{{ method_field('put') }}
										<div class="box-body">
											<div class="form-group {{ $errors->has('currentpassword') ? 'has-error' : '' }}">
												<label>Current Password</label>
												<input class="form-control" type="password" name="currentpassword" placeholder="Current Password">
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
										<div class="box-footer">
											<div class="row">
												<div class="col-md-5">
													<button type="submit" class="btn btn-danger">Proceed</button>
												</div>
											</div>
										</div>
									</form>
				        </div>
        			</div>
        		</div>
        	</div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection