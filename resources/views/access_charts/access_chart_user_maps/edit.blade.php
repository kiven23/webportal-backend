@extends('layouts.app')

@section('title', 'Edit Access Chart Officer')

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
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit access chart officer
            </h3>
          </div>
          <form role="form" action="{{ route('access_chart_user.update', ['id' => $access_user->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>User</label>
                    <select name="user" class="form-control select2">
                      @foreach ($users as $user)
                        <option
                          {{ $access_user->user_id === $user->id ? 'selected' : '' }}
                          value="{{ $user->id }}">
                          {{ $user->first_name }}
                          {{ $user->last_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>Access Level</label>
                    <select name="level" class="form-control select2">
                      @for ($i = 1; $i <= $levels; $i++)
                        <option
                          {{ $access_user->access_level === $i ? 'selected' : '' }}
                          value="{{ $i }}">{{ $i }}</option>
                      @endfor
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-md-5">
                  <button type="submit" class="btn btn-primary">Update</button>
                  <a href="{{ route('access_chart.officers', ['id' => $access_user->accesschart_id]) }}" type="submit" class="btn btn-default pull-right">Cancel</a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
  $(document).ready(function () {
    // Initialize Select2 Elements
    $('.select2').select2();
  });
</script>
@endpush
