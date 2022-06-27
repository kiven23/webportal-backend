@extends('layouts.app')

@section('title', 'Assigned Users')

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
      <li><a href="{{ route('access_chart.officers', ['id' => $accesschart->id]) }}">{{ $accesschart->name }}</a></li>
      <li class="active">Assigned Users</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              Users assigned to <em>{{ $accesschart->name }} Chart</em>
            </h3>
          </div>
          <div class="box-body table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Employee</th>
                  <th>Branch</th>
                  <th>Department</th>
                  <th>Position</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($assigned_users as $index => $assigned_user)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $assigned_user->user->first_name }} {{ $assigned_user->user->last_name }}</td>
                    <td>{{ $assigned_user->branch->name }}</td>
                    <td>{{ $assigned_user->department ? $assigned_user->department->name : 'N/A' }}</td>
                    <td>{{ $assigned_user->position->name }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
