@extends('layouts.app')

@section('title', 'Edit User Employement Details')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Manage user employment</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('users.index') }}">Users</a></li>
      <li><a href="{{ route('employment_details.index') }}">Employment</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Edit employment details</h3>
          </div>
          <form id="employment-form" role="form" action="{{ route('employment_detail.update', ['id' => $employment_detail->id]) }}" method="post">
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                  @include ('errors.list')
                  @include ('successes.list')
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Employee</label>
                            <input type="text" class="form-control" disabled readonly value="{{ $employment_detail->user->first_name }} {{ $employment_detail->user->last_name }}">
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group {{ $errors->has('sss') ? 'has-error' : '' }}">
                            <label>SSS</label>
                            <input type="number" name="sss" class="form-control" value="{{ $employment_detail->sss }}">
                            @if ($errors->has('sss'))
                              <span class="form-text text-danger">
                                {{ $errors->first('sss') }}
                              </span>
                            @endif
                          </div>
                        </div>
                        <div class="col-md-6">
                          <!-- ------- -->
                          <!-- PAYROLL -->
                          <!-- ------- -->
                          <div class="form-group {{ $errors->has('payroll') ? 'has-error' : ($errors->has('branch_input') ? 'has-error' : ($errors->has('branch_duplicate') ? 'has-error' : '')) }}">
                            <label>Payroll</label>
                            <select id="payroll" class="form-control select2" name="payroll">
                              <option {{ $employment_detail->payroll === 0 ? 'selected' : '' }} value="0">Cash</option>
                              <option {{ $employment_detail->payroll === 1 ? 'selected' : '' }} value="1">ATM</option>
                            </select>
                            @if ($errors->has('payroll'))
                              <span class="form-text text-danger">
                                {{ $errors->first('payroll') }}
                              </span>
                            @endif
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <!-- ------------ -->
                        <!-- Access Chart -->
                        <!-- ------------ -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>OTLOA Access Chart</label>
                              <select class="form-control select2" name="otloa_access_chart">
                                <option value="0">None</option>
                                @foreach ($otloa_access_charts as $otloa_access_chart)
                                  <option
                                    {{ $employment_detail->accesschart ? ($employment_detail->accesschart->id === $otloa_access_chart->id ? 'selected' : '') : '' }}
                                    value="{{ $otloa_access_chart->id }}">{{ $otloa_access_chart->name }}</option>
                                @endforeach
                              </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>MRF Access Chart</label>
                              <select class="form-control select2" name="mrf_access_chart">
                                <option value="0">None</option>
                                @foreach ($mrf_access_charts as $mrf_access_chart)
                                  <option
                                    {{ $employment_detail->mrf_accesschart ? ($employment_detail->mrf_accesschart->id === $mrf_access_chart->id ? 'selected' : '') : '' }}
                                    value="{{ $mrf_access_chart->id }}">{{ $mrf_access_chart->name }}</option>
                                @endforeach
                              </select>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <!-- ------------ -->
                        <!-- Access Chart -->
                        <!-- ------------ -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>PO File Access Chart</label>
                            <select class="form-control select2" name="po_file_access_chart">
                              <option value="0">None</option>
                              @foreach ($po_file_access_charts as $po_file_access_chart)
                                <option
                                  {{ $employment_detail->po_file_accesschart ? ($employment_detail->po_file_accesschart->id == $po_file_access_chart->id ? 'selected' : '') : '' }}
                                  value="{{ $po_file_access_chart->id }}">{{ $po_file_access_chart->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <select disabled class="form-control select2"></select>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <!-- ------ -->
                          <!-- Branch -->
                          <!-- ------ -->
                          <div class="form-group {{ $errors->has('branch_select') ? 'has-error' : ($errors->has('branch_input') ? 'has-error' : ($errors->has('branch_duplicate') ? 'has-error' : '')) }}">
                            <label>Branch</label>
                              <select id="branch_select" class="form-control select2" name="branch_select[]">
                                @foreach ($branches as $branch)
                                  <option
                                      {{ $employment_detail->branch ?
                                        ($employment_detail->branch->id === $branch->id ?
                                        'selected' : '') : (
                                        $employment_detail->user->branch->id === $branch->id ?
                                        'selected' : '') }}
                                      value="{{ $branch->id }},{{ $branch->machine_number }}">{{ $branch->name }}</option>
                                @endforeach
                              </select>
                              @if ($errors->has('branch_select'))
                                <span class="form-text text-danger">
                                  {{ $errors->first('branch_select') }}
                                </span>
                              @endif
                          </div>
                        </div>
                        <div class="col-md-6">
                          <!-- -------- -->
                          <!-- Position -->
                          <!-- -------- -->
                          <div class="form-group {{ $errors->has('position') ? 'has-error' : ($errors->has('position_input') ? 'has-error' : ($errors->has('position_duplicate') ? 'has-error' : '')) }}">
                            <label>Position</label>
                              <select class="form-control select2 select2-new" name="position">
                                @foreach ($positions as $position)
                                  <option
                                      {{ $employment_detail->position ? ($employment_detail->position->id === $position->id ? 'selected' : '') : '' }}
                                      value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                              </select>
                              @if ($errors->has('position'))
                                <span class="form-text text-danger">
                                  {{ $errors->first('position') }}
                                </span>
                              @endif
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <!-- ---------- -->
                          <!-- Department -->
                          <!-- ---------- -->
                          <div class="form-group {{ $errors->has('department') ? 'has-error' : ($errors->has('department_input') ? 'has-error' : ($errors->has('department_duplicate') ? 'has-error' : '')) }}">
                            <label>Department</label>
                              <select class="form-control select2 select2-new" name="department">
                                <option value="0">N/A</option>
                                @foreach ($departments as $department)
                                  <option
                                      {{ $employment_detail->department ? ($employment_detail->department->id === $department->id ? 'selected' : '') : '' }}
                                      value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                              </select>
                              @if ($errors->has('department'))
                                <span class="form-text text-danger">
                                  {{ $errors->first('department') }}
                                </span>
                              @endif
                          </div>
                        </div>
                        <div class="col-md-6">
                          <!-- ---------- -->
                          <!-- Division -->
                          <!-- ---------- -->
                          <div class="form-group {{ $errors->has('division') ? 'has-error' : '' }}">
                            <label>Division</label>
                            <select class="form-control select2 select2-new" name="division">
                              <option value="0">N/A</option>
                              @foreach ($divisions as $division)
                                <option
                                    {{ $employment_detail->division ? ($employment_detail->division->id === $division->id ? 'selected' : '') : '' }}
                                    value="{{ $division->id }}">{{ $division->name }}</option>
                              @endforeach
                            </select>
                            @if ($errors->has('division'))
                              <span class="form-text text-danger">
                                {{ $errors->first('division') }}
                              </span>
                            @endif
                          </div>
                        </div>
                      </div>

                      <!-- -------- -->
                      <!-- SCHEDULE -->
                      <!-- -------- -->
                      <div id="sched_div" class="row">
                        <div class="col-md-6">
                          <div class="form-group timepicker {{ $errors->has('time_from') ? 'has-error' : '' }}">
                            <label for="time_from">Time From</label>
                            <input id="time_from"
                                  name="time_from"
                                  type="text"
                                  class="form-control"
                                  value="{{ $employment_detail->time_from ? $employment_detail->time_from : old('time_from') }}">
                            @if ($errors->has('time_from'))
                              <p class="help-block">
                                {{ $errors->first('time_from') }}
                              </p>
                            @endif
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group timepicker {{ $errors->has('time_to') ? 'has-error' : '' }}">
                            <label for="time_to">Time To</label>
                            <input id="time_to"
                                  name="time_to"
                                  type="text"
                                  class="form-control"
                                  value="{{ $employment_detail->time_to ? $employment_detail->time_to : old('time_to') }}">
                            @if ($errors->has('time_to'))
                              <p class="help-block">
                                {{ $errors->first('time_to') }}
                              </p>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>

              <div class="col-md-6">
                <form id="upload-customer" action="{{ route('employment_detail.upload_customer', ['id' => $employment_detail->id]) }}" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="form-group {{ $errors->has('customer_file') ? 'has-error' : '' }}">
                    <label for="customer_file">Upload Customer (.xls)</label>
                    <input
                      name="customer_file"
                      type="file"
                      class="form-control"
                    >
                    @if ($errors->has('customer_file'))
                      <p class="help-block">
                        {{ $errors->first('customer_file') }}
                      </p>
                    @endif
                  </div>

                  <div class="form-group">
                    <a
                      onclick="event.preventDefault(); document.getElementById('upload-customer').submit();"
                      href="javascript:void(0);"
                      class="btn btn-primary"
                    >Upload</a>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="box-footer">
            <div class="row">
              <div class="col-md-6">
                <a
                  onclick="event.preventDefault(); document.getElementById('employment-form').submit();"
                  href="javascript:void(0);"
                  class="btn btn-primary"
                >Update</a>
                <a href="{{ route('employment_details.index') }}" type="submit" class="btn btn-default pull-right">Cancel</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script type="text/javascript">

  // Initialize Select2 Elements
  $('.select2').select2();
  $('.select2-new').select2({
    tags: true,
    createTag: function (params) {
      var term = $.trim(params.term);
      var count = 0;
      var existsVar = false;
      //check if there is any option already
      if($('#keywords option').length > 0){
        $('#keywords option').each(function(){
          if ($(this).text().toUpperCase() == term.toUpperCase()) {
            existsVar = true
            return false;
          }else{
            existsVar = false
          }
        });
        if(existsVar){
          return null;
        }
        return {
          id: params.term,
          text: params.term,
          newTag: true
        }
      }
      //since select has 0 options, add new without comparing
      else{
        return {
          id: params.term,
          text: params.term,
          newTag: true
        }
      }
    },
    maximumInputLength: 255,
    closeOnSelect: true
  });

  //Timepicker
  $('#time_from').timepicker({
    showInputs: true,
  });
  $('#time_to').timepicker({
    showInputs: true,
  });

</script>
@endpush
