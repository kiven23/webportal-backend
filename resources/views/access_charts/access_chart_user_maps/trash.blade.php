@extends('layouts.app')

@section('title', 'Delete Access Chart Officer')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Access Charts
      <small>Manage access chart</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('access_charts.index') }}">Access Charts</a></li>
      <li><a href="{{ route('access_chart.officers', ['id' => $access_user->accesschart_id]) }}">{{ $access_user->accesschart->name }}</a></li>
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
            You are about to delete the following:
            <ul>
              <li>
                <em style="color: red;">{{ $access_user->user->first_name }} {{ $access_user->user->last_name }}</em>
                as Approving Officer under the <strong>{{ $access_user->accesschart->name }}</strong></li>
            </ul>
          </div>
          <div class="box-footer">
            <a href="{{ route('access_chart_user.delete', ['id' => $access_user->id]) }}" class="btn btn-danger">Proceed</a>
            <a href="{{ route('access_chart.officers', ['id' => $access_user->accesschart_id]) }}" class="btn btn-default">No, go back</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
