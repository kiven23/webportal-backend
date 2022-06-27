@extends('layouts.app')

@section('title', 'Edit User Authorizations')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Manage user accounts</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('users.index') }}">Users</a></li>
      <li><a href="{{ route('authorizations.index') }}">Authorization</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Edit authorization</h3>
          </div>
          {{ Form::model($user, array('route' => array('authorization.update', $user->id), 'method' => 'POST')) }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group {{ $errors->has('first_name') ? 'has-danger' : '' }}">
                        <label>User</label>
                        <input class="form-control"
                               type="text"
                               value="{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})"
                               readonly="true">
                      </div>

                      <div class="form-group {{ $errors->has('roles') ? 'has-error' : '' }}">
                        <label>Select Roles</label>
                        <select name="roles[]" multiple class="form-control" id="multipleWithSearch">
                          @foreach ($roles as $role)
                            <option
                              class="super-admin"
                              value="{{ $role->id }}">{{ $role->name }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('roles'))
                          <span class="form-text text-danger">
                            {{ $errors->first('roles') }}
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-md-5">
                  <button type="submit" class="btn btn-primary">Update</button>
                  <a href="{{ route('authorizations.index') }}" class="btn btn-default pull-right">Cancel</a>
                </div>
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
  $(function () {
    var roles = {{ json_encode($user->roles->pluck('id')) }};

		$("#multipleWithSearch").select2({
			placeholder: "Select permissions",
			placeholderForSearch: "Filter permissions", // additional placeholder for search box
			closeOnSelect: false,
			// Make selection-box similar to single select
			selectionAdapter: $.fn.select2.amd.require("CustomSelectionAdapter"),
			templateSelection: (data) => {
				return `Selected ${data.selected.length} out of ${data.all.length}`;
			},
			// Add search box in dropdown
			dropdownAdapter: $.fn.select2.amd.require("CustomDropdownAdapter")
		}).select2('val', [roles]);

    $('input').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
    });
  });
</script>
@endpush
