@extends('layouts.app')

@section('title', 'Maintenance Requests')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Maintenance Requests
    	<small>Manage maintenance requests</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Maintenance Requests</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Request Lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('maint_request.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;ADD
              </a>
            </div>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')
          	<table class="table table-bordered table-hover" id="maint-request-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Submitted By</th>
									<th>Attachments</th>
									<th>Date Submitted</th>
                  <th>Progress</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@forelse ($maint_requests as $index => $maint_request)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>
                      {{ $maint_request->user->first_name }}
                      {{ $maint_request->user->last_name }}
                    </td>
                    <td>
                      @if (count($maint_request->files) > 0)
                        <div class='view-files'>
                          @foreach ($maint_request->files as $index => $file)
                            @if ($index === 0)
                              <em>
                                <a href="{{ asset('storage/'.$file->file_path) }}"
                                  data-lightbox="{{ $file->maint_request_id }}"
                                  data-title="{{ $file->file_name }}"
                                >
                                  {{ count($maint_request->files) }}
                                  attachment{{ count($maint_request->files) > 1 ? 's' : '' }}
                                  <span class="fa fa-paperclip fa-flip-vertical"></span>
                                </a>
                              </em>
                            @else
                              <a href="{{ asset('storage/'.$file->file_path) }}" data-lightbox="{{ $file->maint_request_id }}" data-title="{{ $file->file_name }}"></a>
                            @endif
                          @endforeach
                        </div>
                      @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($maint_request->created_at)->format('M d, Y g:i a') }}</td>
                    <td>
											@if ($maint_request->status < 3)
                        @foreach ($approvers as $index => $approver)
													<i
														class="icon-pending fa
															{{ $maint_request->waiting_for > $approver['level'] ? 'fa-check-circle' : 'fa-circle-o' }}
														" data-toggle="tooltip" data-container="body"
														title="
															@foreach ($approver['user'] as $user)
																{{ isset($maint_request->officers_approved[$index]) ? ($user == $maint_request->officers_approved[$index]['approver'] ? '*' . $user . '*' : $user) : $user }}
																@if ($user != end($approver['user']))
																	/
																@endif
															@endforeach
													"></i>
												@endforeach
											@else
												@foreach ($maint_request->officers_approved as $officer_approved)
													<i
														class="icon-approved fa fa-check-circle" data-toggle="tooltip"
														data-container="body"
														title="
															{{ $officer_approved->approver }}
														"></i>
												@endforeach
											@endif
										</td>
                    <td>
                      @if ($maint_request->status === 0)
                        <span class="label label-default">Pending</span>
                      @elseif ($maint_request->status === 1)
                        <span class="label label-primary">Received</span>
                      @elseif ($maint_request->status === 2)
                        <span class="label label-warning">Cancelled</span>
                      @elseif ($maint_request->status === 3)
                        <span class="label label-success">Approved</span>
                      @elseif ($maint_request->status === 4)
                        <span class="label bg-black">Completed</span>
                      @endif
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('maint_request.view', ['id' => $maint_request->id]) }}" class="btn btn-default btn-xs" title="View"><i class="fa fa-eye"></i></a>
                        @if ($maint_request->status === 0)
  											  <a href="{{ route('maint_request.edit', ['id' => $maint_request->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
                          <a href="{{ route('maint_request.trash', ['id' => $maint_request->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
                        @elseif ($maint_request->status === 3)
                          <a href="{{ route('maint_request.completion', ['id' => $maint_request->id]) }}" class="btn btn-default btn-xs" title="Proceed Completion"><i class="fa fa-check-circle"></i></a>
                        @endif
                      </div>
										</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center">No records found.</td></tr>
								@endforelse
							</tbody>
						</table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
@stop

@push('scripts')
<script>
  $(function () {

    $('#maint-request-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'responsive'  : true,
      'scrollY'       : "300px",
      dom: "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
      lengthMenu: [
          [ 10, 25, 50, -1 ],
          [ '10', '25', '50', '100', 'All' ]
      ],
      buttons: [
          {
              extend: 'excelHtml5',
              exportOptions: {
                  columns: ':visible'
              }
          },
          'colvis'
      ]
    })

    $('#maint-request-table tbody').on('mouseover', 'tr', function () {
	    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        html: true,
	    });

	    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        html: true,
        placement: 'left',
	    });
		});
  })
</script>
@endpush
