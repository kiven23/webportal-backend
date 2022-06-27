@extends('layouts.app')

@section('title', 'Reports - Employee Per Position')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Reports
    	<small>Employee Per Position</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Employee Per Position</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
                Employee Per Position
            </h3>
            <span class="pull-right">
            <a
              href="{{ route('report.employee_per_position_print') }}"
              target="_blank"
              class="btn btn-danger btn-xs"><i class="fa fa-print"></i>&nbsp;PRINT</a>
            </span>
          </div>
          <div class="box-body">
            @php
              $report->render();
            @endphp
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection