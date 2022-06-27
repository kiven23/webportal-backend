@extends('layouts.app')

@section('title', 'Create Purchasing Report')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchasing Reports
      <small>Manage purchasing reports</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('report.purchasing.index') }}">Purchasing Reports</a></li>
      <li class="active">Create</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Add report</h3>
          </div>

        	<form method="post"
                action="{{ route('report.purchasing.store') }}"
                enctype="multipart/form-data"
          >
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">

                  @include('errors.list')
                  @include('successes.list')

                  <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control select2 select2-new">
                      @foreach ($file_types as $type)
                        <option
                          {{ old('type') == $type->id ? 'selected' : '' }}
                          value="{{ $type->id }}">{{ $type->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                    <label>File:</label>
                    <input type="file" multiple class="form-control" name="files[]">
                    @if ($errors->has('file'))
                      <span class="form-text text-danger">
                        {{ $errors->first('file') }}
                      </span>
                    @endif
                  </div>

                  <div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
                    <label>Remarks:</label>
                    <textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
                    @if ($errors->has('remarks'))
                      <span class="form-text text-danger">
                        {{ $errors->first('remarks') }}
                      </span>
                    @endif
                  </div>

                  <div class="form-group">
                    <label>
                      <span>To Company</span>
                      <a href="javascript:void(0);" id="to-user">or To User:</a>
                    </label>
                    <select class="form-control" name="company">
                      @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                      @endforeach
                    </select>

                    <select class="form-control" name="" style="display:none;">
                      @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-md-5">
									<button value=0 name="savebtn" type="submit" class="btn btn-primary">Save & Add new</button>
									<button value=1 name="savebtn" type="submit" class="btn btn-danger">Save & Return</button>
									<a href="{{ route('report.purchasing.index') }}" class="btn btn-default pull-right">Back</a>
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

    $('label').on('click', '#to-user', function() {
      var div = $($(this).parent()).parent(),
          span = $(this).parent().find('span');

      $(span).html('To User');
      $(this).attr('id', 'to-company').html('or To Company:');
      
      $(div).find('select').attr('name', '').hide();
      $(div).find('select').next().attr('name', 'user').show().select2();
    });

    $('label').on('click', '#to-company', function() {
      var div = $($(this).parent()).parent(),
          span = $(this).parent().find('span');

      $(span).html('To Company');
      $(this).attr('id', 'to-user').html('or To User:');
      
      $(div).find('select').attr('name', 'company').show();
      $(div).find('select').next().attr('name', '').hide();
    });
  });

  $('.select2').select2();
	$('.select2-new').select2({
    tags: true,
    createTag: function (params) {
      var term = $.trim(params.term);
      var count = 0
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
</script>
@endpush