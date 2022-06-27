@extends('layouts.app')

@section('title', 'Delete Maintenance Request')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Maintenance Requests
      <small>Manage maintenance requests</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li>
        <a href="{{ route($maint_request->user_id == \Auth::user()->id ? 'maint_requests' : 'maint_request.view', ['id' => $maint_request->id]) }}">
          Maintenance Requests
        </a>
      </li>
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
						You are about to delete this record?<br>
						<span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
					</div>

					<div class="box-footer">
						<form method="post" action="{{ route('maint_request.delete', ['id' => $maint_request->id]) }}">
							{{ csrf_field() }}
							<button type="submit" class="btn btn-danger">Proceed</button>
              <a href="{{ route($maint_request->user_id == \Auth::user()->id ? 'maint_requests' : 'maint_request.view', ['id' => $maint_request->id]) }}"
                 class="btn btn-default">No, go back</a>
						</form>
					</div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection