@extends('layouts.app')

@section('title', 'Delete Customer File')

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
      <li class="active">Trash</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm delete
            </h3>
          </div>
          <form role="form" enctype="multipart/form-data" method="POST" action="{{ route('customer.file_delete', ['customer_id' => $customer->id, 'file_id' => $file->id]) }}">
              {{ csrf_field() }}
            <div class="box-body">
              <p>You are about to delete the the file with the following details:</p>

              <ul>
                <li><strong>Customer: </strong><i>{{ $customer->first_name }} {{ $customer->last_name }}</i></li>
                <li><strong>File: </strong><i><a href="{{ route('customer.file_download', ['customer_id' => $file->customer_id, 'file_id' => $file->id]) }}">{{ $file_name }}</a></i></li>
              </ul>
            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-danger">Proceed</button>
              <a href="{{ URL::previous() }}" class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
