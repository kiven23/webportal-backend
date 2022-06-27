@extends('layouts.app')

@section('title', 'Customer Files')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Customer Photo
      <small>Manage customer photo</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('customers') }}">Customer Lists</a></li>
      <li class="active">Files</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ ucwords(strtolower($customer->first_name)) }} {{ ucwords(strtolower($customer->last_name)) }} Files
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('customer.file_add', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;
                Add
              </a>
            </div>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')

            <table class="table table-bordered" id="files-table">
              <thead>
                <tr>
                  <th data-field="rowid" data-sortable="true">#</th>
                  <th data-field="id" data-sortable="true" data-visible="false">ID</th>
                  <th data-field="name" data-sortable="true">Name</th>
                  <th data-field="file" data-sortable="true">File</th>
                  <th data-field="actions">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($files as $index => $file)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $file->id }}</td>
                    <td>{{ $file->name }}</td>
                    <td><a href="{{ route('customer.file_download', ['customer_id' => $file->customer_id, 'file_id' => $file->id]) }}">Download</a></td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('customer.file_edit', ['customer_id' => $file->customer_id, 'file_id' => $file->id]) }}" class="btn btn-default btn-sm"><span class="fa fa-pencil"></span></a>
                        <a href="{{ route('customer.file_trash', ['customer_id' => $file->customer_id, 'file_id' => $file->id]) }}" class="btn btn-default btn-sm"><span class="fa fa-trash"></span></a>
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
    $('#files-table').DataTable({
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
