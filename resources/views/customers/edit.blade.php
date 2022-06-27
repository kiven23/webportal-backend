@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')

<style>
  .cropit-preview {
    background-color: #f8f8f8;
    background-size: cover;
    border: 1px solid #ccc;
    border-radius: 3px;
    margin-top: 7px;
    width: 250px;
    height: 250px;
  }

  .cropit-preview-image-container {
    cursor: move;
  }

  .image-size-label {
    margin-top: 10px;
  }

</style>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Customers
      <small>Manage customer photo</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('customers') }}">Droplists</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-4">
      	<div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab">Upload</a></li>
            <li><a href="#tab_2" data-toggle="tab">Take a Photo</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
            	<input class="form-control" type="file" id="customer-photo-upload" accept="image/*">
	            <div style="margin: 10px 0 10px 0;">
		            <div class="btn-group" role="group" aria-label="rotate">
									<button class="image-rotate btn btn-sm btn-default" data-deg="-90">Rotate Left</button>
							    <button class="image-rotate btn btn-sm btn-default" data-deg="90">Rotate Right</button>
								</div>
								<div class="btn-group pull-right">
									<button id="image-submit" class="export btn btn-primary btn-sm">Snap</button>
								</div>
							</div>

							<div id="customer-photo"></div>

						  <div id="result"></div>

							<!-- <div class="image-editor">
					    	<input type="file" class="cropit-image-input">
					    	<div class="cropit-preview"></div>

					    	<div class="image-size-label">
					      		Resize image
					    	</div>

					    	<input type="range" class="cropit-image-zoom-input">
					    	<button class="rotate-ccw btn btn-default btn-sm">Rotate counterclockwise</button>
					    	<button class="rotate-cw btn btn-default btn-sm">Rotate clockwise</button>

					    </div>

					    <script>
					      $(function() {
					        $('.image-editor').cropit({
					          imageState: {
					            src: 'http://lorempixel.com/500/400/',
					          },
					        });

					        $('.rotate-cw').click(function() {
					          $('.image-editor').cropit('rotateCW');
					        });
					        $('.rotate-ccw').click(function() {
					          $('.image-editor').cropit('rotateCCW');
					        });

					        $('.export').click(function() {
					          var imageData = $('.image-editor').cropit('export');
					          document.getElementById("picture_datauri_img").src=imageData;
							  		document.getElementById("picture_datauri_textarea").value=imageData;
					        });
					      });
					    </script> -->
            </div>
            <div class="tab-pane" id="tab_2">
            	<div id="my_camera" ></div>

							<!-- Configure a few settings and attach camera -->
							<script language="JavaScript">
								Webcam.set({
									// live preview size
									width: 320,
									height: 240,

									// device capture size
									dest_width: 320,
									dest_height: 240,

									// final cropped size
									crop_width: 240,
									crop_height: 240,

									// format and quality
									image_format: 'jpeg',
									jpeg_quality: 90
								});
								Webcam.attach('#my_camera');
							</script>
							<input type="button" value="Take Snapshot" onClick="take_snapshot()" class="btn btn-block btn-primary">
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-8">
      	<div class="box box-warning box-solid">
					<div class="box-header with-border">
						<strong>Customer photo &amp; Info</strong>
					</div>

					<div class="box-body">
		        @include ('errors.list')
						@include ('successes.list')

						<form action="{{ route('customer.update', ['id' => $customer->id]) }}" method="post" class="form-horizontal">
						 	{{ csrf_field() }}
						 	<div class="form-group {{ $errors->has('picture') ? 'has-error' : '' }}">
								<label class="control-label col-sm-2">Picture</label>
								<div class="col-sm-10">
									<img src="{{ $customer->picture ? $customer->picture : asset('images/placeholders/customer-placeholder.png') }}" id="picture_datauri_img" class="img-thumbnail" style="width: 2in; height: 2in;">
									<br>
									<textarea name="picture" id="picture_datauri_textarea" hidden>{{ $customer->picture ? $customer->picture : asset('images/placeholders/customer-placeholder.png') }}</textarea>
									@if ($errors->has('picture'))
										<span class="form-text text-danger">{{ $errors->first('picture') }}</span>
									@endif
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-2" for="title">Title</label>
								<div class="col-sm-10">
									<select name="title" class="form-control">
										<option {{ $customer->title === 'Mr' ? 'selected' : '' }} value="Mr">Mr.</option>
										<option {{ $customer->title === 'Mrs' ? 'selected' : '' }} value="Mrs">Mrs.</option>
										<option {{ $customer->title === 'Miss' ? 'selected' : '' }} value="Miss">Miss</option>
									</select>
								</div>
							</div>

							<div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
								<label class="control-label col-sm-2" for="first_name">First Name</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="first_name" value="{{ $customer->first_name }}">
									@if ($errors->has('first_name'))
										<span class="form-text text-danger">{{ $errors->first('first_name') }}</span>
									@endif
								</div>
							</div>

							<div class="form-group {{ $errors->has('middle_name') ? 'has-error' : '' }}">
								<label class="control-label col-sm-2" for="middle_name">Middle Name</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="middle_name" value="{{ $customer->middle_name }}">
									@if ($errors->has('middle_name'))
										<span class="form-text text-danger">{{ $errors->first('middle_name') }}</span>
									@endif
								</div>
							</div>

							<div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
								<label class="control-label col-sm-2" for="last_name">Last Name</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="last_name" value="{{ $customer->last_name }}">
									@if ($errors->has('last_name'))
										<span class="form-text text-danger">{{ $errors->first('last_name') }}</span>
									@endif
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-2" for="suffix">Suffix</label>
								<div class="col-sm-10">
									<select name="suffix" class="form-control">
										<option {{ $customer->suffix === 0 ? 'selected' : '' }} value="0">None</option>
										<option {{ $customer->suffix === 'Jr' ? 'selected' : '' }} value="Jr">Jr.</option>
										<option {{ $customer->suffix === 'Sr' ? 'selected' : '' }} value="Sr">Sr.</option>
										<option {{ $customer->suffix === 'I' ? 'selected' : '' }} value="I">I</option>
										<option {{ $customer->suffix === 'II' ? 'selected' : '' }} value="II">II</option>
										<option {{ $customer->suffix === 'III' ? 'selected' : '' }} value="III">III</option>
									</select>
								</div>
							</div>

							<div class="form-group {{ $errors->has('contact_number') ? 'has-error' : '' }}">
		          	<label class="control-label col-sm-2" for="contact_number">Contact #</label>
		            <div class="col-md-10">
								<input type='text' class="form-control" name="contact_number" value="{{ $customer->contact_number }}" />
		              @if ($errors->has('contact_number'))
										<span class="form-text text-danger">{{ $errors->first('contact_number') }}</span>
									@endif
		            </div>
		          </div>

							<div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
								<label class="control-label col-sm-2" for="address">Address</label>
								<div class="col-md-10">
								<input type='text' class="form-control" name="address" value="{{ $customer->address }}" />
									@if ($errors->has('address'))
										<span class="form-text text-danger">{{ $errors->first('address') }}</span>
									@endif
								</div>
							</div>

		          <div class="form-group {{ $errors->has('birth_date') ? 'has-error' : '' }}">
		          	<label class="control-label col-sm-2" for="birth_date">Date of Birth</label>
		            <div class="col-md-10">
		            	<div class='input-group date'>
		                <input id='birthdate' type='text' class="form-control" name="birth_date" value="{{ $customer->birth_date }}" />
		                <span class="input-group-addon">
		                  <span class="fa fa-calendar"></span>
		                </span>
		              </div>
		              @if ($errors->has('birth_date'))
										<span class="form-text text-danger">{{ $errors->first('birth_date') }}</span>
									@endif
		            </div>
		          </div>
		          <script type="text/javascript">
		            $(function () {
		              $('#birthdate').datetimepicker({
		                timepicker:false,
		                format:'Y-m-d'
		              });
		            });
		          </script>
						<!-- form line -->
					</div>

					<div class="box-footer">
						<button type="submit" class="btn btn-warning btn-block">
							<span class="glyphicon glyphicon-print"></span>
							Update &amp; Print
						</button>
						</form> <!-- form end -->
					</div>
				</div>
      </div>
    </div>
  </section>
</div>

<!-- Code to handle taking the snapshot and displaying it locally -->
<script language="JavaScript">
	function take_snapshot() {
		// take snapshot and get image data
		Webcam.snap( function(data_uri) {
			// display results in page
			// document.getElementById('results').innerHTML =
			// 	'<img src="'+data_uri+'"/>' +
			// 	'<textarea style="visibility:hidden;" name="picture">'+data_uri+'</textarea>';
			document.getElementById("picture_datauri_img").src=data_uri;
			document.getElementById("picture_datauri_textarea").value=data_uri;
		} );
	}
</script>

<!-- CROPPIE -->
<script type="text/javascript">
	var $customerPhotoPreview = $('#customer-photo').croppie({
    viewport: {
      width: 250,
      height: 250,
      type: 'square'
    },
    boundary: {
      width: 300,
      height: 300
    },
    enableOrientation: true
  });

function readFile(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (event) {
      $customerPhotoPreview.croppie('bind', {
        url: event.target.result
      });
    };

    reader.readAsDataURL(input.files[0]);
  } else {
    alert('Sorry - your browser doesn\'t support the FileReader API');
  }
}

$('.image-rotate').on('click', function(ev) {
  $customerPhotoPreview.croppie('rotate', parseInt($(this).data('deg')));
});

$('#image-submit').on('click', function(ev) {
  $customerPhotoPreview.croppie('result', {
      type: 'canvas',
      size: 'original'
  }).then(function (data_uri) {
      document.getElementById("picture_datauri_img").src=data_uri;
			document.getElementById("picture_datauri_textarea").value=data_uri;
  });
});

$('#customer-photo-upload').on('change', function() { readFile(this); });
</script>
@endsection
