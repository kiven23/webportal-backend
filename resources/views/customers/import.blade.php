@extends('layouts.app')

@section('title', 'Import Customers')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Customers
      <small>Import customers</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('customers') }}">Droplists</a></li>
      <li class="active">Import</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Import
            </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                @include ('errors.list')
                @include ('successes.list')

                <form id="upload-customer" action="{{ route('customer.import_proceed') }}" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="form-group {{ $errors->has('customer_file') ? 'has-error' : '' }}">
                    <label for="customer_file">Upload Customer (.xls)</label>
                    <input
                      name="customer_file"
                      type="file"
                      class="form-control"
                    >
                    @if ($errors->has('customer_file'))
                      <p class="help-block">
                        {{ $errors->first('customer_file') }}
                      </p>
                    @endif
                  </div>

                  <div class="form-group">
                    <a
                      onclick="event.preventDefault(); document.getElementById('upload-customer').submit();"
                      href="javascript:void(0);"
                      class="btn btn-primary"
                    >Upload</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
