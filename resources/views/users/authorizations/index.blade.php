@extends('layouts.app')

@section('title', 'User Authorizations')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Manage user authorizations</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('users.index') }}">Users</a></li>
      <li class="active">Authorization</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Authorization</h3>
            <div class="box-toolbar pull-right">
              <div class="btn-group">
                <a href="{{ route('authorization.assign') }}" class="btn btn-primary btn-xs">
                  <i class="fa fa-user-plus"></i>&nbsp;
                    ASSIGN ROLES
                </a>
              </div>
            </div>
          </div>
          <div class="box-body table-responsive">
          	@include ('errors.list')
						@include ('successes.list')
          	<table class="table table-bordered table-hover" id="auth-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Email</th>
									<th>Branch</th>
									<th>Department</th>
									<th>Position</th>
									<th>Roles</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($users as $index => $user)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>{{ $user->first_name }} {{ $user->last_name }}</td>
										<td>{{ $user->email }}</td>
										<td>{{ $user->branch ? $user->branch->name : '' }}</td>
										<td>
                      {{ $user->employment ? $user->employment->department['name'] : '' }}
                    </td>
										<td>
                      {{ $user->employment ? $user->employment->position['name'] : '' }}
                    </td>
			              <td style="cursor:pointer;">
			                <span
                        id="expandRole"
                        data-toggle="modal"
                        data-target="#exampleModal"
                        data-user="{{ $user->first_name }} {{ $user->last_name }}"
                        data-roles="{{ $user->roles }}"
                      >
                        <span class="label label-warning">
                          {{ count($user->roles) > 0 ? $user->roles[0]->name : 'None' }}
                        </span>
                        @if (count($user->roles) > 1)
                          <span class="text-muted">&nbsp;(+{{ count($user->roles) - 1 }} others)</span>
                        @endif
                      </span>
			              </td>
										<td>
											<a href="{{ route('authorization.edit', ['id' => $user->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
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

    $('table').on('click', '#expandRole', function () {
      $('.modal-title').html('');
      var user = $(this).data('user');
      $('.modal-title').append(user);

      $('.modal-body').html('');
      var roles = $(this).data('roles');
      for (i = 0; i < roles.length; i++) {
        var el = '<span class="label label-warning">'+roles[i]['name']+'</span>&nbsp;';
        $('.modal-body').append(el);
      }
    });

    $('#auth-table').DataTable({
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
      columnDefs: [
          { targets: [3, 4, 5], visible: false },
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
