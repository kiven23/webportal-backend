@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Manage user accounts</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Users</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              User Accounts
            </h3>
            <div class="box-toolbar pull-right">
              <div class="btn-group">
                <a href="{{ route('user.create') }}" class="btn btn-primary btn-xs">
                  <i class="fa fa-user-plus"></i>&nbsp;
                    ADD
                </a>
                <a href="{{ route('employment_details.index') }}" class="btn btn-info btn-xs">
                  <i class="fa fa-file-o"></i>&nbsp;
                    EMPLOYMENT
                </a>
                <a href="{{ route('authorizations.index') }}" class="btn btn-warning btn-xs">
                  <i class="fa fa-check"></i>&nbsp;
                    AUTHORIZATION
                </a>
              </div>
            </div>
          </div>
          <div class="box-body table-responsive">
            @include ('errors.list')
            @include ('successes.list')
            <table class="table table-bordered table-hover" id="user-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Company</th>
                  <th>Branch</th>
                  <th data-field="actions">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($users as $index => $user)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->company ? $user->company->name : 'N/A' }}</td>
                    <td>{{ $user->branch ? $user->branch->name : 'N/A' }}</td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('user.edit', ['id' => $user->id]) }}" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-pencil"></i></a>
                        <a href="{{ route('user.trash', ['id' => $user->id]) }}" class="btn btn-default btn-xs" title="Delete"><i class="fa fa-trash"></i></a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <div class="row hidden">
              <div class="col-md-6">
                <form action="{{ route('user.import') }}" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                    <label for="file">Import File</label>
                    <input type="file" class="form-control" name="file">
                    @if ($errors->has('file'))
                      <p class="help-block">
                        {{ $errors->first('file') }}
                      </p>
                    @endif
                  </div>
                  <button id="importbtn" class="btn btn-default">
                    <span class="fa fa-upload"></span>
                    Import
                  </button>

                  <span id="processing"><i class="fa fa-spinner fa-pulse fa-fw"></i> Please wait...</span>
                </form>
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
<script>
  $(function () {
    $('#user-table').DataTable({
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
  });

// Processing
$('#processing').hide();
$('#importbtn').click(function() {
    $(this).hide();
    $('#processing').show();
});
</script>
@endpush
