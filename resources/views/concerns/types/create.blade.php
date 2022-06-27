@extends('layouts.app')

@section('title', 'Add Concern Types')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Concern Types
    	<small>Manage concern type</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="{{ route('concerns.index') }}">Concerns</a></li>
      <li><a href="{{ route('concerns.types.index') }}">Types</a></li>
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Add concern type</h3>
          </div>

        	<form method="post" action="{{ route('concern.type.store') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
										<label>Name</label>
										<input type="text" class="form-control" name="name" value="{{ old('name') }}">
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
									<button value=0 name="savebtn" type="submit" class="btn btn-primary">Save & Add new</button>
									<button value=1 name="savebtn" type="submit" class="btn btn-danger">Save & Return</button>
									<a href="{{ route('concerns.types.index') }}" class="btn btn-default pull-right">Back</a>
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
  $('.select2').select2();
</script>
@endpush