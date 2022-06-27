@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Departments
      <small>Manage department</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('regions') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('departments.index') }}">Departments</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit department
            </h3>
          </div>
          <form role="form" action="{{ route('department.update', ['id' => $department->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group {{ $errors->has('division') ? 'has-error' : '' }}">
                    <label>Division</label>
                    <select name="division" class="form-control select2">
                      <option value="0">None</option>
                      @foreach ($divisions as $division)
                        <option
                          {{ $division->id === $department->division_id ? 'selected' : '' }}
                          value="{{ $division->id }}">{{ $division->name }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('division'))
                      <span class="form-text text-danger">
                        {{ $errors->first('division') }}
                      </span>
                    @endif
                  </div>

                  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>Department</label>
                    <input class="form-control" type="text" name="name" value="{{ $department->name }}">
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
                  <a class="btn btn-default pull-right" href="{{ route('departments.index') }}">Cancel</a>
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
    $('.select2').select2();
  });
</script>
@endpush
