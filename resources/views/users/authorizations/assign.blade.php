@extends('layouts.app')

@section('title', 'Assign User Authorizations')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Manage user authorizations</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('users.index') }}">Users</a></li>
      <li><a href="{{ route('authorizations.index') }}">Authorization</a></li>
      <li class="active">Assign</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Assign roles</h3>
          </div>
          <form method="post" action="{{ route('authorization.assign_proceed') }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="row">
								<div class="col-md-5">
									@include ('errors.list')
									@include ('successes.list')

									<div class="form-group {{ $errors->has('roles') ? 'has-danger' : '' }}">
										<label>Roles</label>
										<select name="roles[]" multiple class="form-control select2 select2-roles">
                      @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('roles'))
                      <span class="form-text text-danger">
                        {{ $errors->first('roles') }}
                      </span>
                    @endif
									</div>

                  <div class="form-group {{ $errors->has('companies') ? 'has-danger' : '' }}">
										<label>Companies</label>
										<select name="companies[]" multiple class="form-control select2 select2-companies">
                      @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('companies'))
                      <span class="form-text text-danger">
                        {{ $errors->first('companies') }}
                      </span>
                    @endif
									</div>
								</div>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-5">
									<button value=0 name="savebtn" type="submit" class="btn btn-primary">Assign & Assign new</button>
									<button value=1 name="savebtn" type="submit" class="btn btn-danger">Assign & Return</button>
									<a href="{{ route('service_categories') }}" class="btn btn-default pull-right">Back</a>
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
  $(function () {
    $(".select2-roles").select2({
			placeholder: "Select roles",
			placeholderForSearch: "Filter roles", // additional placeholder for search box
			closeOnSelect: false,
			// Make selection-box similar to single select
			selectionAdapter: $.fn.select2.amd.require("CustomSelectionAdapter"),
			templateSelection: (data) => {
				return `Selected ${data.selected.length} out of ${data.all.length}`;
			},
			// Add search box in dropdown
			dropdownAdapter: $.fn.select2.amd.require("CustomDropdownAdapter")
    });
    
    $(".select2-companies").select2({
			placeholder: "Select companies",
			placeholderForSearch: "Filter companies", // additional placeholder for search box
			closeOnSelect: false,
			// Make selection-box similar to single select
			selectionAdapter: $.fn.select2.amd.require("CustomSelectionAdapter"),
			templateSelection: (data) => {
				return `Selected ${data.selected.length} out of ${data.all.length}`;
			},
			// Add search box in dropdown
			dropdownAdapter: $.fn.select2.amd.require("CustomDropdownAdapter")
    });
  });
</script>
@endpush