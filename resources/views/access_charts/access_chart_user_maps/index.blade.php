@extends('layouts.app')

@section('title', 'Access Chart Officers')

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
      <li class="active">{{ $access_chart->name }}</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ $access_chart->name }} access chart
            </h3>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')

            {{ $errors->has('any') ? $errors->all() : '' }}
            <div class="row">
              <div class="col-md-5">
                <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Assign by User</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Assign by BDP</a></li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                      <form role="form" action="{{ route('access_chart_user.store', ['id' => $access_chart->id]) }}" method="post">
                        {{ csrf_field() }}
                        <!-- ------------ -->
                        <!-- User -->
                        <!-- ------------ -->
                        <div id="user">
                          <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
                            <label>User</label>
                            {{-- <em class="pull-right"><a href="javascript:void();" id="usernewbtn">(add new)</a></em> --}}
                            <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user">
                              <option value="">None</option>
                              @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                  {{ $user->first_name }}
                                  {{ $user->last_name }}
                                </option>
                              @endforeach
                            </select>
                            @if ($errors->has('user'))
                              <span class="form-text text-danger">
                                {{ $errors->first('user') }}
                              </span>
                            @endif
                          </div>
                        </div>
                        <div id="user_new" style="display:none;">
                          <div class="form-group">
                            <label>User</label>
                            <em class="pull-right"><a href="javascript:void();" id="userbtn">(Select existing)</a></em>
                          </div>
                          <div class="form-group {{ $errors->has('user_new_fname') ? 'has-error' : '' }}">
                            <label>First Name</label>
                            <input type="text" id="fname" class="form-control {{ $errors->has('user_new_fname') ? 'is-invalid' : '' }}" value="{{ old('user_new_fname') }}">
                            @if ($errors->has('user_new_fname'))
                              <span class="form-text text-danger">
                                {{ $errors->first('user_new_fname') }}
                              </span>
                            @endif
                          </div>
                          <div class="form-group {{ $errors->has('user_new_lname') ? 'has-error' : '' }}">
                            <label>Last Name</label>
                            <input type="text" id="lname" class="form-control {{ $errors->has('user_new_lname') ? 'is-invalid' : '' }}" value="{{ old('user_new_lname') }}">
                            @if ($errors->has('user_new_lname'))
                              <span class="form-text text-danger">
                                {{ $errors->first('user_new_lname') }}
                              </span>
                            @endif
                          </div>
                          <div class="form-group {{ $errors->has('user_new_email') ? 'has-error' : '' }}">
                            <label>Email</label>
                            <input type="email" id="email" class="form-control {{ $errors->has('user_new_email') ? 'is-invalid' : '' }}" value="{{ old('user_new_email') }}">
                            @if ($errors->has('user_new_email'))
                              <span class="form-text text-danger">
                                {{ $errors->first('user_new_email') }}
                              </span>
                            @endif
                          </div>
                          <div class="form-group {{ $errors->has('user_new_password') ? 'has-error' : '' }}">
                            <label>Password</label>
                            <input type="password" id="password" class="form-control {{ $errors->has('user_new_password') ? 'is-invalid' : '' }}" value="{{ old('user_new_password') }}">
                            @if ($errors->has('user_new_password'))
                              <span class="form-text text-danger">
                                {{ $errors->first('user_new_password') }}
                              </span>
                            @endif
                          </div>
                          <div class="form-group {{ $errors->has('branch') ? 'has-error' : '' }}">
                            <label>Branch</label>
                            <select name="branch" class="form-control select2">
                              @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <!-- ------------------- -->
                        <!-- END :: User -->
                        <!-- ------------------- -->
  
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                          <label>Access Level</label> <em class="pull-right"><a href="{{ route('access_level.edit', ['accesschart_id' => $access_chart->id]) }}">(edit)</a></em>
                          <select name="level" class="form-control select2">
                            @for ($i = 1; $i <= $levels; $i++)
                              <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                          </select>
                        </div>
    
                        <button
                          type="submit"
                          class="btn btn-success"
                          name="assignbtn" value="1">Assign</button>
                          @if (count($access_user->accessusersmap) > 0)
                            @if ($max_level >= 1 && $max_level < $access_level->level)
                              <button
                                type="submit"
                                class="btn btn-primary"
                                name="assignbtn"
                                value="2">Assign & Adjust</button>
                            @endif
                          @endif
                        <a class="btn btn-default pull-right" href="{{ route('access_charts.index') }}">Back</a>
                      </form>
                    </div>
                    <div class="tab-pane active" id="tab_2">
                      <form role="form" action="{{ route('access_chart_user.store_bdp', ['id' => $access_chart->id]) }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                          <label>Branch</label>
                          <select name="branch" class="form-control select2">
                            @foreach ($branches as $branch)
                              <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                          </select>
                        </div>
  
                        <div class="form-group">
                          <label>Department</label>
                          <select name="department" class="form-control select2">
                            <option value="0">N/A</option>
                            @foreach ($departments as $department)
                              <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                          </select>
                        </div>
  
                        <div class="form-group">
                          <label>Position</label>
                          <select name="position" class="form-control select2">
                            @foreach ($positions as $position)
                              <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                          </select>
                        </div>
  
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                          <label>Access Level</label> <em class="pull-right"><a href="{{ route('access_level.edit', ['accesschart_id' => $access_chart->id]) }}">(edit)</a></em>
                          <select name="level" class="form-control select2">
                            @for ($i = 1; $i <= $levels; $i++)
                              <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                          </select>
                        </div>
  
                        <button
                          type="submit"
                          class="btn btn-success"
                          name="assignbtn" value="1">Assign</button>
                          @if (count($access_user->accessusersmap) > 0)
                            @if ($max_level >= 1 && $max_level < $access_level->level)
                              <button
                                type="submit"
                                class="btn btn-primary"
                                name="assignbtn"
                                value="2">Assign & Adjust</button>
                            @endif
                          @endif
                        <a class="btn btn-default pull-right" href="{{ route('access_charts.index') }}">Back</a>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-7">
                <div class="box box-info box-solid">
                  <div class="box-header with-border">
                    <strong>{{ $access_chart->name }}</strong>
                  </div>
                  <div class="box-body">
                    <ul>
                      @if (count($access_user->accessusersmap) > 0)
                        @foreach ($access_user->accessusersmap as $accessusersmap)
                          <li style="line-height: 25px;">
                            @if ($accessusersmap->user_id)
                              Approving Officer {{ $accessusersmap->access_level }} -
                              {{ $accessusersmap->user->first_name }}
                              {{ $accessusersmap->user->last_name }}
                              <a href="{{ route('access_chart_user.trash', ['id' => $accessusersmap->id]) }}" class="pull-right">| Delete</a>
                              <a href="{{ route('access_chart_user.edit', ['id' => $accessusersmap->id]) }}" class="pull-right">Edit&nbsp;</a>
                            @else
                              No Approving Officer assigned
                            @endif
                          </li>
                        @endforeach
                      @else
                        <li>No Approving Officer assigned</li>
                      @endif
                    </ul>
                  </div>
                </div>

                <div class="box box-primary box-solid">
                  <div class="box-header with-border">
                    <strong>Assign <em>{{ $access_chart->name }}</em> Chart To</strong>
                    <a
                      class="pull-right"
                      href="{{ route('access_chart_user.assigned_users', ['id' => $access_chart->id]) }}">view assigned users</a>
                  </div>
                  <div class="box-body">
                    <form
                      role="form"
                      action="{{ route('access_chart_user.assign_to', ['id' => $access_chart->id]) }}"
                      method="post"
                      id="assignForm">
                      {{ csrf_field() }}
                      <div class="form-group">
                        <label>Branch</label>
                        <select name="branch" class="form-control select2">
                          <option value="0">All</option>
                          @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                          @endforeach
                        </select>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Department</label>
                            <select name="department" class="form-control select2">
                              <option value="0">N/A</option>
                              @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Position</label>
                            <select name="position" class="form-control select2">
                              <option value="0" selected>All</option>
                              @foreach ($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>

                      <button
                        type="submit"
                        class="btn btn-primary"
                        name="assignbtn" value="1">Assign</button>
                      <button
                        type="submit"
                        class="btn btn-warning assignBtn">Re-assign</button>
                    </form>
                  </div>
                </div>
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

  // Hack for Select2 bug inside Bootstrap Tab
  $('#tab_2').removeClass('active');

	// -----------
	// User Script
	// -----------
	@if (Session::has('set_user'))
		var set_user = 1;
	@else
		var set_user = 0;
	@endif

	if (set_user == 1) {
		user_new();
	} else {
		user();
	};
	$('#usernewbtn').click(function () {
		user_new();
	});
	$('#userbtn').click(function () {
		user();
	});

	function user () {
		$('#user_new').hide();
		$('#user_new input#fname').attr('name', 'user_disabled');

		$('#user').show();
		$('#user select').attr('name', 'user').focus();
	}

	function user_new () {
		$('#user').hide();
		$('#user select').attr('name', 'user_disabled');

		$('#user_new').show();
		$('#user_new input#fname').attr('name', 'user_new_fname').focus();
		$('#user_new input#lname').attr('name', 'user_new_lname');
		$('#user_new input#email').attr('name', 'user_new_email');
		$('#user_new input#password').attr('name', 'user_new_password');
		$('#user_new select#role').attr('name', 'user_new_role');
	}
	// ------------------
	// END :: User Script
	// ------------------

  $(document).ready(function () {
    $('.assignBtn').click(function (e) {
      e.preventDefault();

      if (confirm('Re-assigning will remove all the current assigned users. Still want to proceed?')) {
        $('#assignForm').submit();
      }
    });
  });
</script>
@endpush
