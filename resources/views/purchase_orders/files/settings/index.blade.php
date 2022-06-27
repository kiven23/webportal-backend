@extends('layouts.app')

@section('title', 'File Settings')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Files
      <small>Manage file settings</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('files.index') }}">Files</a></li>
      <li class="active">Settings</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              File Settings
            </h3>
          </div>
          <div class="box-body">
            
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
