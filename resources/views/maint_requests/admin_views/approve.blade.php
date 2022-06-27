@extends('layouts.app')

@section('title', 'Approve Maintenance Request')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Maintenance Requests
      <small>Manage maintenance requests</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('maint_request.approval.overlook') }}">Maintenance Requests</a></li>
      <li>
        <a href="{{ route($maint_request->user_id == \Auth::user()->id ? 'maint_requests' : 'maint_request.view', ['id' => $maint_request->id]) }}">
          View
        </a>
      </li>
      <li class="active">Approve</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm approve
            </h3>
          </div>
          <form method="post" action="{{ route('maint_request.approval.proceed_approve', ['id' => $maint_request->id]) }}">
            {{ csrf_field() }}
            <div class="box-body">
              @include ('errors.list')

              <div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
                <label>Instruction/Remarks:</label>
                <textarea type="text" name="remarks" class="form-control"></textarea>
              </div>

              @if ($errors->has('remarks'))
                <span class="form-text text-danger">
                  {{ $errors->first('remarks') }}
                </span>
                <br><br>
              @endif

              <span class="text-danger"><strong>Note: </strong> The action cannot be undo after you proceed.</span>
            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-success">Proceed</button>
              <a href="{{ route($maint_request->user_id == \Auth::user()->id ? 'maint_requests' : 'maint_request.view', ['id' => $maint_request->id]) }}"
                  class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection