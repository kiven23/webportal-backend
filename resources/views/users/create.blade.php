@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Manage user accounts</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('users.index') }}">Users</a></li>
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Add user</h3>
          </div>
          <form method="post" action="{{ route('user.store') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-6">
									@include ('errors.list')
									@include ('successes.list')
									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
												<label>First Name</label>
												<input class="form-control" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" autofocus>
												@if ($errors->has('first_name'))
													<span class="form-text text-danger">
														{{ $errors->first('first_name') }}
													</span>
												@endif
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
												<label>Last Name</label>
												<input class="form-control" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name">
												@if ($errors->has('last_name'))
													<span class="form-text text-danger">
														{{ $errors->first('last_name') }}
													</span>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
												<label>Email</label>
												<input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email">
												@if ($errors->has('email'))
													<span class="form-text text-danger">
														{{ $errors->first('email') }}
													</span>
												@endif
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
												<label>Password</label>
												<input class="form-control" type="password" name="password" value="{{ old('password') }}" placeholder="Password">
												@if ($errors->has('password'))
													<span class="form-text text-danger">
														{{ $errors->first('password') }}
													</span>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group {{ $errors->has('branch') ? 'has-error' : '' }}">
												<label>Branch</label>
												<span id="branch">
													<select class="form-control select2" name="branch">
														<option value="0">N/A</option>
														@foreach ($branches as $branch)
															<option value="{{ $branch->id }}">{{ $branch->name }}</option>
														@endforeach
													</select>
													@if ($errors->has('branch'))
														<span class="form-text text-danger">
															{{ $errors->first('branch') }}
														</span>
													@endif
												</span>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group {{ $errors->has('company') ? 'has-error' : '' }}">
												<label>Company</label>
												<span>
													<select class="form-control select2" name="company">
														@foreach ($companies as $company)
															<option value="{{ $company->id }}">{{ $company->name }}</option>
														@endforeach
													</select>
													@if ($errors->has('company'))
														<span class="form-text text-danger">
															{{ $errors->first('company') }}
														</span>
													@endif
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-6">
									<button type="submit" name="savebtn" value=0 class="btn btn-primary">Save & Add new</button>
									<button type="submit" name="savebtn" value=1 class="btn btn-danger">Save & Return</button>
									<a href="{{ route('users.index') }}" class="btn btn-default pull-right">Back</a>
								</div>
							</div>
						</div>
					</form>
        </div>
      </div>
    </div>
  </div>
</section>
@stop

@push('scripts')
<script>
	$(document).ready(function () {
		// Initialize Select2 Elements
		$('.select2').select2();
	});
</script>
@endpush
