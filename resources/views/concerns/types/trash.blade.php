@extends('layouts.app')

@section('title', 'Delete Concern Type')

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
      <li class="active">Trash</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">Confirm delete</h3>
          </div>

					<div class="box-body">
						You are about to delete a record with the following details:
						<ul>
							<li><strong>Name: </strong> {{ $concern_type->name }}</li>
						</ul>

						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('concern.type.delete', ['id' => $concern_type->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('concerns.types.index') }}" class="btn btn-default">No, go back</a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection