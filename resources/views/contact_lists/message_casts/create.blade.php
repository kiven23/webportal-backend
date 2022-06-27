@extends('layouts.app')

@section('title', 'Add Contact List')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Message Cast
      <small>Mange contact lists</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('contact_lists.message_casts') }}">Contact Lists</a></li>
		  <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add contact
            </h3>
          </div>
          <form method="post" action="{{ route('contact_list.message_cast.store') }}">
						{{ csrf_field() }}
					  <div class="box-body">
					  	<div class="row">
					  		<div class="col-md-5">
									@include ('errors.list')
							  	@include ('successes.list')

							    <div class="form-group row {{ $errors->has('name') ? 'has-error' : '' }}">
										<label for="name" class="col-md-3 col-form-label">Name</label>
										<div class="col-md-9">
											<input type="text" name="name" class="form-control" value="{{ old('name') }}" autofocus>
											@if ($errors->has('name'))
												<span class="form-text text-danger">
													{{ $errors->first('name') }}
												</span>
											@endif
										</div>
									</div>

									<div class="form-group row {{ $errors->has('contact_number') ? 'has-error' : '' }}">
										<label for="contact_number" class="col-md-3 col-form-label">Contact #</label>
										<div class="col-md-9">
											<input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}">
											@if ($errors->has('contact_number'))
												<span class="form-text text-danger">
													{{ $errors->first('contact_number') }}
												</span>
											@endif
										</div>
									</div>

									<div class="form-group row">
										<label for="location" class="col-md-3 col-form-label">Location</label>
										<div class="col-md-9">
											<input type="location" name="location" class="form-control" value="{{ old('location') }}">
											@if ($errors->has('location'))
												<span class="form-text text-danger">
													{{ $errors->first('location') }}
												</span>
											@endif
										</div>
									</div>
								</div>
							</div>
					  </div>

					  <div class="box-footer">
					  	<div class="row">
					  		<div class="col-md-5">
							  	<button type="submit" value="0"	name="savebtn" class="btn btn-primary">Save & Add new</button>
							  	<button type="submit" value="1"	name="savebtn" class="btn btn-danger">Save & Return</button>
							  	<a href="{{ route('contact_lists.message_casts') }}" class="btn btn-default pull-right">Back</a>
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
