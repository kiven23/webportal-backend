@extends('layouts.app')

@section('title', 'Edit Purchase Order File')

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
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">
              Edit file
            </h3>
          </div>

          <form id="dl-file-form" method="post" action="{{ route('purchase_order.file.download', ['id' => $file->id]) }}">
						{{ csrf_field() }}
					</form>

          <form method="post"
          			action="{{ route('purchase_order.file.update', ['id' => $file->id]) }}"
          			enctype="multipart/form-data">
						{{ csrf_field() }}
						{{ method_field('put') }}
						<div class="box-body">
							<div class="row">
		        		<div class="col-md-5">
				          @include ('errors.list')
				          @include ('successes.list')

				          <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
										<label>File</label>
										<br>
										<a href="javascript:document.getElementById('dl-file-form').submit();">{{ $file->file }}</a>
										<input id="file" class="form-control form-control-sm" type="file" name="file" value="{{ $file->file }}" autofocus>
										<span class="form-text text-info">
											Leave blank to leave unchange
											<span><a href="javascript:void(0);" id="reset_file" class="pull-right">reset file</a></span>
										</span>
										<br>
										@if ($errors->has('file'))
											<span class="form-text text-danger">
												{{ $errors->first('file') }}
											</span>
										@endif
									</div>

									<div class="form-group {{ $errors->has('po_number') ? 'has-error' : '' }}">
                    <label>P.O #:</label>
                    <input type="text" class="form-control" name="po_number" value="{{ $file->po_number ? $file->po_number : '' }}">
                    @if ($errors->has('po_number'))
                      <span class="form-text text-danger">
                        {{ $errors->first('po_number') }}
                      </span>
                    @endif
                  </div>

				          <div class="form-group {{ $errors->has('remarks') ? 'has-error' : '' }}">
				            <label>Remarks:</label>
				            <textarea class="form-control" name="remarks">{{ $file->remarks }}</textarea>
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
									<button type="submit" class="btn btn-primary">Update</button>
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
	$(document).ready(function () {
		$('#reset_file').click(function () {
			$('#file').val('');
		});
	});

	// ---------------------------------------------------------------------

	var url = '{{ route("purchase_order.file.edit-ajax", ["id" => ":id"]) }}',
  		fileId = '{{ $file->id }}',
  		url = url.replace(':id', fileId);

  $.get(url,
  function(data) {
  	if (data.to === 'company') {
  		$('label #to-company').click();
  		var name = document.getElementsByName('company')[0].name;
  	} else {
  		$('label #to-user').click();
  		var name = document.getElementsByName('user')[0].name;
  	}
  	var select = 'select'+'[name="'+name+'"]';
		$(select).val(data.id);
  });

  // ----------------------------------------------------------------------

  $('label').on('click', '#to-user', function() {
    var div = $($(this).parent()).parent(),
        span = $(this).parent().find('span');

    $(span).html('To User');
    $(this).attr('id', 'to-company').html('or To Company:');
    
    $(div).find('select').attr('name', '').hide();
    $(div).find('select').next().attr('name', 'user').show();
  });

  $('label').on('click', '#to-company', function() {
    var div = $($(this).parent()).parent(),
        span = $(this).parent().find('span');

    $(span).html('To Company');
    $(this).attr('id', 'to-user').html('or To User:');
    
    $(div).find('select').attr('name', 'company').show();
    $(div).find('select').next().attr('name', '').hide();
  });
</script>
@endpush
