@extends('layouts.app')

@section('title', 'Service types')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Services
      <small>Manage service types</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Service Types</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Service type lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('service_type.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@if (\Request::is('service/types/index/1'))
							<div class="alert alert-dismissible alert-danger">
							  <button type="button" class="close" data-dismiss="alert">&times;</button>
							  <strong>Delete failed! </strong> Model has a child record(s)
							</div>
						@else
							@include ('errors.list')
							@include ('successes.list')
						@endif

						<table class="table table-bordered table-hover" id="service-type-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($service_types as $index => $service_type)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $service_type->name }}</td>
										<td>
                      <div class="btn-group">
  											<a href="{{ route('service_type.edit', ['id' => $service_type->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
  											<a href="{{ route('service_type.trash', ['id' => $service_type->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
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
    $('#service-type-table').DataTable({
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

