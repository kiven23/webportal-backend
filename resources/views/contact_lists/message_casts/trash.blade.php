@extends('layouts.app')

@section('title', 'Delete Contact List')

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
          <form method="post" action="{{ route('contact_list.message_cast.delete', ['id' => $contactlist->id]) }}">
						{{ csrf_field() }}
						<div class="box-body">
							You are about to delete a record with the following details:
							<ul>
								<li>Name: {{ $contactlist->name }}</li>
								<li>Contact Number: {{ $contactlist->contact_number }}</li>
								<li>Location: {{ $contactlist->location ? $contactlist->location : 'None' }}</li>
							</ul>
							<span class="text-danger"><strong>Note: </strong> Once you proceed, the action cannot be undo.</span>
						</div>
						<div class="box-footer">
							<button type="submit" class="btn btn-danger">Proceed</button>
							<a href="{{ route('contact_lists.message_casts') }}" class="btn btn-default">No, go back</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
