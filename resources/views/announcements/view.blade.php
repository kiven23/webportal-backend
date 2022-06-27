@extends('layouts.app')

@section('title', 'View Announcements')

@section('content')
<style>
/* for linesbreaks */
.body-wrapper {
  white-space: pre-wrap;
}
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Announcements
    	<small>Manage announcement</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('announcements.index') }}">Announcements</a></li>
      <li class="active">View</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Announcement Lists
            </h3>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')
          	<table class="table table-bordered table-hover" id="announcement-table">
							<thead>
								<tr>
									<th>#</th>
									<th>To</th>
									<th>Title</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($announcements as $index => $announcement)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $announcement->company->name }}</td>
										<td>{{ $announcement->title }}</td>
										<td>
                      <div class="btn-group">
                        <a
                          href="javascript:void(0);"
                          class="btn btn-default btn-xs"
                          title="View"
                          id="expandModal"
                          data-toggle="modal"
                          data-target="#exampleModal"
                          data-to="{{ $announcement->company->name }}"
                          data-title="{{ $announcement->title }}"
                          data-body="{{ $announcement->body }}"
                        >
                          <i class="fa fa-eye"></i>
                        </a>
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

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
      </div>
      <div class="modal-body" style="overflow-wrap: break-word;"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@stop

@push('scripts')
<script>
  $(function () {

    $('table').on('click', '#expandModal', function () {
      $('.modal-title').html('');
      var to = $(this).data('to');
      $('.modal-title').append("To: "+to);

      $('.modal-body').html('');
      var title = $(this).data('title');
      var body = $(this).data('body');
      var el = '<strong>'+title+'</strong><br><p class="body-wrapper">'+body+'</p>';
      $('.modal-body').append(el);
    });

    $('#announcement-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : false,
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
