@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Roles
    	<small>Manage role</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Roles</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
            	Role Lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('roles.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')
          	<table class="table table-bordered table-hover" id="role-table">
							<thead>
								<tr>
									<th data-field="rowid" data-sortable="true">#</th>
									<th data-field="role" data-sortable="true">Role</th>
									<th data-field="permission" data-sortable="true">Permissions</th>
									<th data-field="actions">Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($roles as $index => $role)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $role->name }}</td>
			              <td style="cursor:pointer;">
											<span
                        id="expandPerm"
                        data-toggle="modal"
                        data-target="#exampleModal"
                        data-role="{{ $role->name }}"
                        data-permissions="{{ $role->permissions }}"
                      >
                        <span class="label label-warning">
                          {{ count($role->permissions) > 0 ? $role->permissions[0]->name : 'None' }}
                        </span>
                        @if (count($role->permissions) > 1)
                          <span class="text-muted">&nbsp;(+{{ count($role->permissions) - 1 }} others)</span>
                        @endif
                      </span>
										</td>
										<td>
                      <div class="btn-group">
  											<a href="{{ route('roles.edit', ['id' => $role->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
                        @if ($role->id !== 1)
  											 <a href="{{ route('roles.trash', ['id' => $role->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
                        @endif
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
    </div>
  </div>
</div>
@stop

@push('scripts')
<script>
  $(function () {

    $('table').on('click', '#expandPerm', function () {
      $('.modal-title').html('');
      var role = $(this).data('role');
      $('.modal-title').append(role);

      $('.modal-body').html('');
      var permissions = $(this).data('permissions');
      for (i = 0; i < permissions.length; i++) {
        var el = '<span class="label label-warning">'+permissions[i]['name']+'</span>&nbsp;';
        $('.modal-body').append(el);
      }
    });

    $('#role-table').DataTable({
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
