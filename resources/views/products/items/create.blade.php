@extends('layouts.app')

@section('title', 'Add Product Item')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Products
      <small>Manage product items</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('items') }}">Items</a></li>
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add product item
            </h3>
          </div>
          <form method="post" action="{{ route('item.store', ['computerware_id']) }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="form-group {{ $errors->has('model') ? 'has-error' : '' }}">
										<label>Model</label>
										<input class="form-control" type="text" name="model" value="{{ old('model') }}" placeholder="Model" autofocus>
										@if ($errors->has('model'))
											<span class="form-text text-danger">
												{{ $errors->first('model') }}
											</span>
										@endif
									</div>

									<!-- ----- -->
									<!-- BRAND -->
									<!-- ----- -->
									<div class="form-group {{ $errors->has('brand_select') ? 'has-error' : ($errors->has('brand_input') ? 'has-error' : ($errors->has('brand_duplicate') ? 'has-error' : '')) }}">
										<label>Brand</label>
										<span id="brand_select">
											<a id="add_brand_btn" href="javascript:void(0);" class="pull-right"><em>(add new)</em></a>
											<select class="form-control select2" name="brand_select">
												@foreach ($brands as $brand)
													<option {{ $brand->id == old('brand_select') ? 'selected' : '' }}
																	value="{{ $brand->id }}">{{ $brand->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('brand_select'))
												<span class="form-text text-danger">
													{{ $errors->first('brand_select') }}
												</span>
											@endif
										</span>

										<span id="brand_input">
											<a id="select_brand_btn" href="javascript:void(0);" class="pull-right"><em>(select existing)</em></a>
											<input class="form-control" type="text" name="brand_input" value="{{ old('brand_input') }}" placeholder="Brand">
											@if ($errors->has('brand_duplicate'))
												<span class="form-text text-danger">
													{{ $errors->first('brand_duplicate') }}
												</span>
											@endif
											@if ($errors->has('brand_input'))
												<span class="form-text text-danger">
													{{ $errors->first('brand_input') }}
												</span>
											@endif
										</span>
									</div>

									<!-- -------- -->
									<!-- CATEGORY -->
									<!-- -------- -->
									<div class="form-group {{ $errors->has('category_select') ? 'has-error' : ($errors->has('category_input') ? 'has-error' : ($errors->has('category_duplicate') ? 'has-error' : '')) }}">
										<label>Category</label>
										<span id="category_select">
											<a id="add_category_btn" href="javascript:void(0);" class="pull-right"><em>(add new)</em></a>
											<select class="form-control select2" name="category_select">
												@foreach ($categories as $category)
													<option {{ $category->id == old('category_select') ? 'selected' : '' }}
																	value="{{ $category->id }}">{{ $category->name }}</option>
												@endforeach
											</select>
											@if ($errors->has('category_select'))
												<span class="form-text text-danger">
													{{ $errors->first('category_select') }}
												</span>
											@endif
										</span>

										<span id="category_input">
											<a id="select_category_btn" href="javascript:void(0);" class="pull-right"><em>(select existing)</em></a>
											<input class="form-control" type="text" name="category_input" value="{{ old('category_input') }}" placeholder="Category">
											@if ($errors->has('category_duplicate'))
												<span class="form-text text-danger">
													{{ $errors->first('category_duplicate') }}
												</span>
											@endif
											@if ($errors->has('category_input'))
												<span class="form-text text-danger">
													{{ $errors->first('category_input') }}
												</span>
											@endif
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button value=0 name="savebtn" type="submit" class="btn btn-primary">Save & Add new</button>
									<button value=1 name="savebtn" type="submit" class="btn btn-danger">Save & Return</button>
									<button value=2 name="savebtn" type="submit" class="btn btn-info" title="Generate Ticket"><i class="fa fa-ticket"></i></button>
									<a href="{{ route('items') }}" class="btn btn-default pull-right">Back</a>
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
	$(document).ready(function () {

		// Initialize Select2 Elements
    $('.select2').select2();


// -------------------
// ------ BRAND ------
// -------------------

		// defaults
		$('#brand_input').hide();

		@if (\Session::has('brand_select'))
			@if (\Session::get('brand_select') == 1)
				brand_select();
			@else
				brand_new();
			@endif
		@endif

		$('#add_brand_btn').click(function () {
			brand_new();
		});

		$('#select_brand_btn').click(function () {
			brand_select();
		});

		function brand_new () {
			$('#brand_select').hide();
			$('#brand_select select').attr('name', 'brand');

			$('#brand_input').show();
			$('#brand_input input').attr('name', 'brand_input').focus();
		}

		function brand_select () {
			$('#brand_input').hide();
			$('#brand_input input').attr('name', 'brand');

			$('#brand_select').show();
			$('#brand_select select').attr('name', 'brand_select').focus();
		}



// ----------------------
// ------ CATEGORY ------
// ----------------------

		// defaults
		$('#category_input').hide();

		@if (\Session::has('category_select'))
			@if (\Session::get('category_select') == 1)
				category_select();
			@else
				category_new();
			@endif
		@endif

		$('#add_category_btn').click(function () {
			category_new();
		});

		$('#select_category_btn').click(function () {
			category_select();
		});

		function category_new () {
			$('#category_select').hide();
			$('#category_select select').attr('name', 'category');

			$('#category_input').show();
			$('#category_input input').attr('name', 'category_input').focus();
		}

		function category_select () {
			$('#category_input').hide();
			$('#category_input input').attr('name', 'category');

			$('#category_select').show();
			$('#category_select select').attr('name', 'category_select').focus();
		}



	});
</script>
@endpush
