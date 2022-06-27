@extends('layouts.app')

@section('title', 'Add Access Chart')

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
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add access chart
            </h3>
          </div>
          <form role="form" action="{{ route('access_chart.store') }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group">
                    <label>Access For</label>
                    <select name="access_for" class="form-control" autofocus>
                      <option value="0">Overtime & Leave of absences</option>
                      <option value="1">MRF</option>
                      <option value="2">Purchase Order Files</option>
                    </select>
                  </div>

                  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>Name</label>
                    <input class="form-control" type="text" name="name" value="{{ old('name') }}">
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
                  <button
                      type="submit"
                      class="btn btn-primary"
                      name="savebtn"
                      value="1">Save & New</button>
                  <button
                      type="submit"
                      class="btn btn-danger"
                      name="savebtn"
                      value="2">Save & Return</button>
                  <a href="{{ route('access_charts.index') }}" class="btn btn-default pull-right">Cancel</a>
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
