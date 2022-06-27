@extends('layouts.app')

@section('title', 'Edit Customer File')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Customer Photo
      <small>Manage customer photo</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('customers') }}">Customer Lists</a></li>
      <li><a href="{{ route('customer.files', ['customer_id' => $customer->id]) }}">{{ $customer->first_name }} {{ $customer->last_name }} Files</a></li>
      <li class="active">Edit file</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit file
            </h3>
          </div>
          <form role="form" enctype="multipart/form-data" method="POST" action="{{ route('customer.file_update', ['customer_id' => $customer->id, 'file_id' => $file->id]) }}">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label>Customer</label>
                    <input id="name" type="text" class="form-control" readonly name="name" value="{{ $customer->first_name }} {{ $customer->last_name }}">
                  </div>

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label>Name</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ $file->name }}" placeholder="Name (optional)" autofocus>
                    @if ($errors->has('name'))
                      <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                      </span>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>File</label>
                    <input type="file" class="form-control" name="file">
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-md-5">
                  <button type="submit" class="btn btn-primary">Update</button>
                  <a href="{{ URL::previous() }}" class="btn btn-default pull-right">Cancel</a>
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
