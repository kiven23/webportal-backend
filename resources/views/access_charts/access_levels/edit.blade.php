@extends('layouts.app')

@section('title', 'Edit Access Level')

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
      <li><a href="{{ route('access_chart.officers', ['id' => $access_chart->id]) }}">{{ $access_chart->name }}</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit access chart level
            </h3>
          </div>
          <form role="form" action="{{ route('access_level.update', ['id' => $access_level->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group {{ $errors->has('level') ? 'has-error' : '' }}">
                    <!-- access chart id -->
                    <input type="hidden" value="{{ $access_chart->id }}" name="accesschart_id" readonly="true">

                    <label>Level</label>
                    <input class="form-control {{ $errors->has('level') ? 'is-invalid' : '' }}" type="number" name="level" value="{{ $access_level->level }}">
                    @if ($errors->has('level'))
                      <span class="form-text text-danger">
                        {{ $errors->first('level') }}
                      </span>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-md-5">
                  <button type="submit" class="btn btn-primary">Update</button>
                  <a href="{{ route('access_chart.officers', ['id' => $access_chart->id]) }}" type="submit" class="btn btn-default pull-right">Cancel</a>
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
