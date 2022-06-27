@extends('layouts.app')

@section('title', 'Create Purchase Order File')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchase Order Files
      <small>Manage purchase order files</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('purchase_orders.files.index') }}">Purchase Order Files</a></li>
      <li class="active">Create</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Add po file</h3>
          </div>

        	<form method="post"
                action="{{ route('purchase_order.file.store') }}"
                enctype="multipart/form-data"
          >
            {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-md-5">

                  @include('errors.list')
                  @include('successes.list')

                  <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                    <label>File:</label>
                    <input type="file" class="form-control" name="file">
                    @if ($errors->has('file'))
                      <span class="form-text text-danger">
                        {{ $errors->first('file') }}
                      </span>
                    @endif
                  </div>

                  <div class="form-group {{ $errors->has('po_number') ? 'has-error' : '' }}">
                    <label>P.O #:</label>
                    <input type="text" class="form-control" name="po_number">
                    @if ($errors->has('po_number'))
                      <span class="form-text text-danger">
                        {{ $errors->first('po_number') }}
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
									<a href="{{ route('purchase_orders.files.index') }}" class="btn btn-default pull-right">Back</a>
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
</script>
@endpush