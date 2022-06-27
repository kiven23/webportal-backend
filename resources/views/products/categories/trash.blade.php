@extends('layouts.app')

@section('title', 'Delete Product Category')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Products
      <small>Manage product items</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('categories') }}">Categories</a></li>
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
							<li><strong>Name: </strong> {{ $category->name }}</li>
							<em>Item(s) under this record:</em>
							<ol>
								@forelse ($category->items as $item)
									<li>
										<ul>
											<li><strong>Model: </strong>{{ $item->model }}</li>
											<li><strong>Serial: </strong>{{ $item->serial ? $item->serial : '---' }}</li>
										</ul>
									</li>
								@empty
									<li>None</li>
								@endforelse
							</ol>
						</ul>

						<small><strong>Note: </strong> The action cannot be undo after you proceed.</small>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('category.delete', ['id' => $category->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('categories') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
