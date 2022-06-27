@extends('layouts.app')

@section('title', 'Delete Department')

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
          <form action="{{ route('department.delete', ['id' => $department->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <strong>You are about to delete the following:</strong>
              <ul>
                <li>Department: <em>{{ $department->name }}</em></li>
                @if (count($department->usersemployment) > 0)
                  <li>
                    <span>Department of employee(s) will be set to <span class="label label-danger">null</span></span>
                  </li>
                @endif
              </ul>
              @if (count($department->usersemployment) > 0)
                <strong>Employee(s) under this department:</strong>
                <ul>
                  @foreach ($department->usersemployment as $useremployment)
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
              <a href="{{ URL::previous() }}" class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
