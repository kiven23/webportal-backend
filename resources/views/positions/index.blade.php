@extends('layouts.app')

@section('title', 'Positions')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Positions
      <small>Manage position</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('regions') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Positions</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Position Lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('position.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;
                  ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
            @include ('errors.list')
            @include ('successes.list')

            <table class="table table-bordered table-hover" id="position-table">
              <thead>
                <th>#</th>
                <th>Position</th>
                <th>Actions</th>
              </thead>
              <tbody>
                @foreach ($positions as $index => $position)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $position->name }}</td>
                    <td>
                      <div class="btn-group">
                        <a class="btn btn-default btn-xs" href="{{ route('position.edit', ['' => $position->id]) }}"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-default btn-xs" href="{{ route('position.trash', ['' => $position->id]) }}"><i class="fa fa-trash"></i></a>
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
    $('#position-table').DataTable({
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
