@extends('layouts.app')

@section('title', 'Edit Division')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Divisions
      <small>Manage division</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('regions') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('divisions.index') }}">Divisions</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit division
            </h3>
          </div>
          <form role="form" action="{{ route('division.update', ['id' => $division->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>Division</label>
                    <input class="form-control" type="text" name="name" value="{{ $division->name }}">
                    @if ($errors->has('name'))
                      <span class="form-text text-danger">
                        {{ $errors->first('name') }}
                      </span>
                    @endif
                  </div>

                  <div class="form-group {{ $errors->has('departments') ? 'has-error' : '' }}">
                    <label>Departments</label>
                    <select name="departments[]" multiple class="form-control select2">
                      @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('departments'))
                      <span class="form-text text-danger">
                        {{ $errors->first('departments') }}
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
                  <a class="btn btn-default pull-right" href="{{ route('divisions.index') }}">Cancel</a>
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
    var depts = {{ json_encode($division->departments->pluck('id')) }};
    $('.select2').select2({
      'placeholder': 'Select departments'
    }).select2('val', [depts]);
  });
</script>
@endpush
