@extends('layouts.app')

@section('title', 'Add Customer File')

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
      <li class="active">Add file</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add file
            </h3>
          </div>
          <form role="form" enctype="multipart/form-data" method="POST" action="{{ route('customer.file_store', ['customer_id' => $customer->id]) }}">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label>Customer</label>
                    <input id="name" type="text" class="form-control" readonly name="name" value="{{ $customer->first_name }} {{ $customer->last_name }}">
                  </div>

                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label>Name</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Name (optional)" autofocus>
                    @if ($errors->has('name'))
                      <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                      </span>
                    @endif
                  </div>

                  <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                    <label>File</label>
                    <input type="file" class="form-control" name="file">
                    @if ($errors->has('file'))
                      <span class="form-text text-danger">
                        <strong>{{ $errors->first('file') }}</strong>
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
                  <a href="{{ route('customer.files', ['customer_id' => $customer->id]) }}" class="btn btn-default pull-right">Back</a>
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
