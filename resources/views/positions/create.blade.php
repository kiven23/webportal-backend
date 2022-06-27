@extends('layouts.app')

@section('title', 'Add Position')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Positions
      <small>Manage position</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('regions') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('positions.index') }}">Positions</a></li>
      <li class="active">Add new</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              Add position
            </h3>
          </div>
          <form role="form" action="{{ route('position.store') }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label>Position</label>
                    <input class="form-control" type="text" name="name" value="{{ old('name') }}" autofocus>
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
                  <a class="btn btn-default pull-right" href="{{ route('positions.index') }}">Back</a>
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
