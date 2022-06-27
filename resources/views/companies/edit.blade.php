@extends('layouts.app')

@section('title', 'Edit Company')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Companies
      <small>Manage company</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('companies.index') }}">Companies</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit company
            </h3>
          </div>
          <form method="post" action="{{ route('company.update', ['id' => $company->id]) }}">
						{{ csrf_field() }}
						{{ method_field('put') }}
						<div class="box-body">
							<div class="row">
		        		<div class="col-md-5">
				          @include ('errors.list')
				          @include ('successes.list')

									<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
										<label>Name</label>
										<input class="form-control" type="text" name="name" value="{{ $company->name }}">
										@if ($errors->has('name'))
											<span class="form-text text-danger">
												{{ $errors->first('name') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
										<label>Address</label>
										<input class="form-control" type="text" name="address" value="{{ $company->address }}">
										@if ($errors->has('address'))
											<span class="form-text text-danger">
												{{ $errors->first('address') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('contact') ? 'has-error' : '' }}">
										<label>Contact</label>
										<input class="form-control" type="text" name="contact" value="{{ $company->contact }}">
									</div>

									<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
										<label>Email</label>
										<input class="form-control" type="text" name="email" value="{{ $company->email }}">
									</div>
								</div>
							</div>
		        </div>

		        <div class="box-footer">
		        	<div class="row">
		        		<div class="col-md-5">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{ route('companies.index') }}" class="btn btn-default pull-right">Back</a>
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
