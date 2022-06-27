@extends('layouts.app')

@section('title', 'Concerns')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Concerns
    	<small>Manage concern</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Concerns</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Concern Lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('concern.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;ADD
              </a>
              <div class="btn-group">
                <a href="{{ route('concerns.types.index') }}" class="btn btn-default btn-xs">
                  <strong>TYPES</strong>
                </a>
                <a href="{{ route('concerns.categories.index') }}" class="btn btn-default btn-xs">
                  <strong>CATEGORIES</strong>
                </a>
              </div>
            </div>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')
          	<table class="table table-bordered table-hover" id="concern-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Branch</th>
									<th>Type</th>
									<th>Category</th>
									<th>Reported By</th>
									<th>Database</th>
									<th>Cause</th>
									<th>Remarks</th>
									<th>Resolution</th>
									<th>Date Solved</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($concerns as $index => $concern)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $concern->branch->name }}</td>
										<td>{{ $concern->type->name }}</td>
										<td>{{ $concern->category->name }}</td>
										<td>{{ $concern->reported_by }}</td>
										<td>{{ $concern->database ? $concern->database : 'N/A' }}</td>
										<td>{{ $concern->cause ? $concern->cause : 'N/A' }}</td>
										<td>{{ $concern->remarks }}</td>
										<td>{{ $concern->resolution }}</td>
										<td>{{ $concern->date_solved }}</td>
										<td>
                      @if ($concern->status === 0)
                        <span class="label label-success">Open</span>
                      @else
                        <span class="label label-danger">Closed</span>
                      @endif
                    </td>
										<td>
                      <div class="btn-group">
  											<a href="{{ route('concern.edit', ['id' => $concern->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
  											<a href="{{ route('concern.trash', ['id' => $concern->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
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
    $('#concern-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'select'      : true,
      'responsive'  : true,
      'scrollY'       : "300px",
      responsive: {
          details: {
          		display: $.fn.dataTable.Responsive.display.modal( {
                  header: function ( row ) {
                      var data = row.data();
                      return data[0]+' - Ticket# '+data[1];
                  }
              } ),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                  tableClass: 'table'
              } ),
          }
      },
      columnDefs: [
          { targets: [4,8,9], visible: false },
      ],
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
