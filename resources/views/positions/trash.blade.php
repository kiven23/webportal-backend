@extends('layouts.app')

@section('title', 'Delete Position')

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
          <form action="{{ route('position.delete', ['id' => $position->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <strong>You are about to delete the following:</strong>
              <ul>
                <li>Position: <em>{{ $position->name }}</em></li>
                @if (count($position->usersemployment) > 0)
                  <li>
                    <span>Position of employee(s) will be set to <span class="label label-danger">null</span></span>
                  </li>
                @endif
              </ul>
              @if (count($position->usersemployment) > 0)
                <strong>Employee(s) under this record:</strong>
                <ul>
                  @foreach ($position->usersemployment as $useremployment)
                    <li>
                      <em>{{ $useremployment->user->first_name }}</em>
                      <em>{{ $useremployment->user->last_name }}</em>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
            <div class="box-footer">
              <button type="submit" class="btn btn-danger">Proceed</button>
              <a href="{{ route('positions.index') }}" class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
