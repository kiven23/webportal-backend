@extends('layouts.app')

@section('title', 'Approved Maintenance Requests')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Maintenance Requests
    	<small>Manage maintenance requests</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Approved MRFs</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Maintenance Requests
            </h3>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')
          	<table class="table table-bordered table-hover" id="maint-request-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Branch</th>
                  <th>Submitted By</th>
                  <th>Attachments</th>
									<th>Date Submitted</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($maint_requests as $index => $maint_request)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $maint_request->branch }}</td>
                    <td>{{ $maint_request->submitted_by }}</td>
                    <td>
                      @if ($maint_request->files)
                        @php
                          $files = explode(',',$maint_request->files);
                        @endphp

                        <div class='view-files'>
                          @foreach ($files as $index => $file)
                            @if ($index === 0)
                              <em>
                                <a href="{{ asset('storage/'.$file) }}"
                                  data-lightbox="{{ $maint_request->id }}"
                                  data-title="{{ $file }}"
                                >
                                  {{ count($files) }}
                                  attachment{{ count($files) > 1 ? 's' : '' }}
                                  <span class="fa fa-paperclip fa-flip-vertical"></span>
                                </a>
                              </em>
                            @else
                              <a href="{{ asset('storage/'.$file) }}" data-lightbox="{{ $maint_request->id }}" data-title="{{ $file }}"></a>
                            @endif
                          @endforeach
                        </div>
                      @endif
                    </td>
                    <td>{{ $maint_request->date_submitted }}</td>
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
                        <a href="{{ route('maint_request.view_approved', ['id' => $maint_request->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-eye"></i></a>
                      </div>
                    </td>
                  </tr>
                @endforeach
							</tbody>
						</table>
          </div>
        </div>
      </div>
    </div>
  </section>
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
  })
</script>
@endpush