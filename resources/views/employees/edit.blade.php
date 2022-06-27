@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Employees
      <small>Manage employee</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('employees.index') }}"><i class="fa fa-dashboard"></i> Employees</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit employee
            </h3>
          </div>
          <form role="form" action="{{ route('employee.update', ['id' => $employee->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  @include ('errors.list')
                  @include ('successes.list')

                  <div class="form-group">
                    <label>Employee</label>
                    <input type="text" class="form-control" disabled readonly value="{{ $employee->user->first_name }} {{ $employee->user->last_name }}">
                  </div>

                  <div class="form-group">
                    <label>Position</label>
                    <input class="form-control" type="text" value="{{ $employee->position ? $employee->position->name : 'Not Assigned' }}" disabled readonly>
                  </div>

                  <div class="form-group">
                    <label>Branch</label>
                    <input class="form-control" type="text" value="{{ $employee->branch ? $employee->branch->name : 'Not Assigned' }}" disabled readonly>
                  </div>

                  <div class="form-group">
                    <label>Remarks</label>
                    <input class="form-control" name="remarks" type="text" value="{{ $employee->remarks }}">
                    @if ($errors->has('remarks'))
                      <span class="form-text text-danger">
                        {{ $errors->first('remarks') }}
                      </span>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>Last Date Reported</label>
                    <input id="last-date-reported" class="form-control" name="last_date_reported" type="text" value="{{ $employee->last_date_reported }}">
                    @if ($errors->has('last_date_reported'))
                      <span class="form-text text-danger">
                        {{ $errors->first('last_date_reported') }}
                      </span>
                    @endif
                    <script type="text/javascript">
                      $(function () {
                        $('#last-date-reported').datetimepicker({
                          format:'Y-m-d H:i'
                        });
                      });
                    </script>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-md-5">
                  <button type="submit" class="btn btn-primary">Update</button>
                  <a href="{{ route('employees.index') }}" type="submit" class="btn btn-default pull-right">Cancel</a>
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