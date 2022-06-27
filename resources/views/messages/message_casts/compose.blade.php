@extends('layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Message Cast
      <small>Compose message</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Compose Message</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-body">
          	@if (\Session::has('status'))
							<div class="alert alert-{{ \Session::get('status.status') }}" role="alert">
								{{ \Session::get('status.message') }}
							</div>
						@endif
          	<div class="row">
          		<div class="col-md-4">
          			<form action="{{ route('message.message_cast.send') }}" method="post">
									{{ csrf_field() }}
									<div class="box box-primary box-solid">
										<div class="box-header with-border">
											<strong>Compose message</strong>
										</div>
										<div class="box-body">
											@include ('errors.list')
											@include ('successes.list')
											@if (\Session::has('response'))
												<div class="alert alert-{{ \Session::get('response.status') }}" role="alert">
													{{ \Session::get('response.message') }}
												</div>
											@endif

											<!-- <div class="form-group {{ $errors->has('send_to') ? 'has-error' : '' }}">
												<label for="send_to" class="col-form-label">Send to</label>
												<span class="pull-right">
													<small><a href="{{ route('contact_list.message_cast.create') }}">(add contact)</a></small>
												</span>
												<select id="select-links" placeholder="Click here to select contact" name="send_to[]"></select>
												@if ($errors->has('send_to'))
													<span class="form-text text-danger">
														{{ $errors->first('send_to') }}
													</span>
												@endif
											</div> -->

											<div class="form-group {{ $errors->has('send_to') ? 'has-danger' : '' }}">
                        <label for="select_employee" class="col-form-label">Select Employee</label>

                        <select name="select_employee" class="form-control select2 select2-employees">
													<option value=0>All</option>
													@foreach ($employees as $employee)
                            <option value="{{ $employee->user_id }}">{{ $employee->name }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('select_employee'))
                          <span class="form-text text-danger">
                            {{ $errors->first('select_employee') }}
                          </span>
                        @endif
                      </div>

											<div class="form-group {{ $errors->has('send_to') ? 'has-danger' : '' }}">
                        <label for="send_to" class="col-form-label">Send to</label>
												<span class="pull-right">
													<div id="checkbox">
														<label>
															<input type="checkbox">&nbsp;
															Select All
														</label>
													</div>
													<!-- <small><a href="{{ route('contact_list.message_cast.create') }}">(add contact)</a></small> -->
                        </span>

                        <select name="send_to[]" multiple class="form-control select2 select2-contacts">
                          
                        </select>
                        @if ($errors->has('send_to'))
                          <span class="form-text text-danger">
                            {{ $errors->first('send_to') }}
                          </span>
                        @endif
                      </div>

											<div class="form-group {{ $errors->has('message') ? 'has-error' : '' }}">
												<label for="message" class="col-form-label">Message</label>
												<textarea name="message" class="form-control" placeholder="Message">{{ \Session::get('message') }}</textarea>
												@if ($errors->has('message'))
													<span class="form-text text-danger">
														{{ $errors->first('message') }}
													</span>
												@endif
											</div>
										</div>
										<div class="box-footer">
											<button id="sendbtn" class="btn btn-primary"><i class="fa fa-send"></i> Send Message</button>
										</div>
									</div>
								</form>
          		</div>

          		<div class="col-md-8">
          			<div class="box box-warning box-solid">
									<div class="box-header with-border">
										<strong>Message</strong>
									</div>
									<div class="box-body">
										<table class="table table-sm table-condensed" id="message-table">
											<thead>
												<tr>
													<th data-field="rowid" data-sortable="true">#</th>
													<th data-field="id" data-sortable="true" data-visible="false">ID</th>
													<th data-field="send_to" data-sortable="true">Send To</th>
													<th data-field="message" data-sortable="true">Message</th>
													<th data-field="response" data-sortable="true">Response</th>
													<th data-field="actions">Actions</th>
												</tr>
											</thead>
											<tbody>
												@foreach ($messages as $index => $message)
													<tr>
														<td><small>{{ $index + 1 }}</small></td>
														<td><small>{{ $message->id }}</small></td>
														<td><small>{{ $message->send_to == '["0"]' ? 'All' : ($message->total > 1 ? $message->total . ' contacts' : $message->total . ' contact') }}</small></td>
														<td><small>{{ $message->message }}</small></td>
														<td><small>{{ $message->response }}</small></td>
														<td>
															<a href="{{ route('message.message_cast.check_status', ['id' => $message->id]) }}" class="btn btn-info btn-sm">Check</a>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
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
	$(document).ready(function () {
		$('.select2-employees').change();
		$('textarea').click(function () {
			$('.alert').hide();
		});
		$('#select-links-selectized').focus(function () {
			$('.alert').hide();
		});
	});
</script>

<script type="text/javascript">

	$('#checkbox').iCheck({
		checkboxClass: 'icheckbox_minimal-blue',
	});

	$("#checkbox label input").on('ifChanged', function(){
		if(this.checked) {
			$(".select2-contacts").find('option').prop("selected",true);
			$(".select2-contacts").trigger('change');
    } else {
			$(".select2-contacts").find('option').prop("selected",false);
			$(".select2-contacts").trigger('change');
    }
	});

	$(".select2-contacts").select2({
		data: null,
		placeholder: "Select Contacts",
		placeholderForSearch: "Filter contacts", // additional placeholder for search box
		closeOnSelect: false,
		// Make selection-box similar to single select
		selectionAdapter: $.fn.select2.amd.require("CustomSelectionAdapter"),
		templateSelection: (data) => {
			return `Selected ${data.selected.length} out of ${data.all.length}`;
		},
		// Add search box in dropdown
		dropdownAdapter: $.fn.select2.amd.require("CustomDropdownAdapter")
	});

	var selectEmployees = $('.select2-employees');
	selectEmployees.select2();
	selectEmployees.on('change', function () {
		var user_id = this.value;
		var uri = '{{ route("message.message_cast.contacts_ajax", ["user_id" => ":user_id"]) }}';
		var url = uri.replace(':user_id', user_id);

		// reset select
		$(".select2-contacts").select2('data', {}); // clear out values selected
		$(".select2-contacts").select2({ allowClear: true }); // re-init to show default status
		$(".select2-contacts option").remove();

		// reset checkbox
		$("#checkbox label div").removeClass('checked');
		$("#checkbox label input").prop('checked', false);

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'POST',
			url: url,
			dataType: 'json',
			processData: false,
			contentType: false,
			success: function (data) {
				console.log(data);
				var myData = $.map(data, function (obj) {
					obj.id = obj.contact_number; // replace name with the property used for the text
					obj.text = obj.text || obj.name; // replace name with the property used for the text
					return obj;
				});

				$(".select2-contacts").select2({
					data: myData,
					placeholder: "Select Contacts",
					placeholderForSearch: "Filter contacts", // additional placeholder for search box
					closeOnSelect: true,
					scrollAfterSelect: false,
					// Make selection-box similar to single select
					selectionAdapter: $.fn.select2.amd.require("CustomSelectionAdapter"),
					templateSelection: (data) => {
						return `Selected ${data.selected.length} out of ${data.all.length}`;
					},
					// Add search box in dropdown
					dropdownAdapter: $.fn.select2.amd.require("CustomDropdownAdapter")
				});
			},
			error: function (err) {
				var response = err.responseText;
				console.log(response);
			},
			complete: function () {
				console.log('completed.');
			}
		});
	});
</script>

<script>
  $(function () {
    $('#message-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'autoWidth'   : false,
      'scrollX'     : true,
      'aLengthMenu': [
          [10, 25, 50, 100, 200, -1],
          [10, 25, 50, 100, 200, "All"]
      ],
    })
  })
</script>
@endpush
