@extends('layouts.app')

@section('title', 'Concern Categories')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Concern Categories
    	<small>Manage concern category</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('concerns.index') }}">Concerns</a></li>
      <li class="active">Categories</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Concern Categories
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('concern.category.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;ADD
              </a>
            </div>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')
          	<table class="table table-bordered table-hover" id="concern-category-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($concern_categories as $index => $concern_category)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $concern_category->name }}</td>
										<td>
                      <div class="btn-group">
  											<a href="{{ route('concern.category.edit', ['id' => $concern_category->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
  											<a href="{{ route('concern.category.trash', ['id' => $concern_category->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
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
    $('#concern-category-table').DataTable({
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