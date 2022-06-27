@extends('layouts.app')

@section('title', 'Delete Division')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Divisions
      <small>Manage division</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('divisions.index') }}">Divisions</a></li>
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
          <form action="{{ route('division.delete', ['id' => $division->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <strong>You are about to delete the following:</strong>
              <ul>
                <li>Division: <em>{{ $division->name }}</em></li>
                @if (count($division->usersemployment) > 0)
                  <li>
                    <span>Division of employee(s) will be set to <span class="label label-danger">null</span></span>
                  </li>
                @endif
              </ul>
              @if (count($division->departments) > 0)
                <strong>Department(s) under this division:</strong>
                <ul>
                  @foreach ($division->departments as $dept)
                    <li>
                      <em>{{ $dept->name }}</em>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
            <div class="box-footer">
              <button type="submit" class="btn btn-danger">Proceed</button>
              <a href="{{ URL::previous() }}" class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
