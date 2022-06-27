@extends('layouts.app')

@section('title', 'Edit Access Chart')

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
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit access chart
            </h3>
          </div>
          <form role="form" action="{{ route('access_chart.update', ['id' => $access_chart->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group">
                    <label>Access For</label>
                    <select name="access_for" class="form-control" autofocus>
                      <option
                        value="0"
                        {{ $access_chart->access_for === 0 ? 'selected' : '' }}
                        >Overtime & Leave of absences</option>
                      <option
                        value="1"
                        {{ $access_chart->access_for === 1 ? 'selected' : '' }}
                        >MRF</option>
                      <option
                        value="2"
                        {{ $access_chart->access_for === 2 ? 'selected' : '' }}
                        >Purchase Order Files</option>
                    </select>
                  </div>

                  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>Name</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" value="{{ $access_chart->name }}">
                    @if ($errors->has('name'))
                      <span class="form-text text-danger">
                        {{ $errors->first('name') }}
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
                  <a href="{{ route('access_charts.index') }}" type="submit" class="btn btn-default pull-right">Cancel</a>
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
