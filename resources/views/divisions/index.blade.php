@extends('layouts.app')

@section('title', 'Divisions')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Divisions
      <small>Manage division</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('regions') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Divisions</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Division Lists
            </h3>
            <div class="box-toolbar pull-right">
              <a href="{{ route('division.create') }}" class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i>&nbsp;
                  ADD
              </a>
            </div>
          </div>
          <div class="box-body table-responsive">
            @include ('errors.list')
            @include ('successes.list')

            <table class="table table-bordered table-hover" id="division-table">
              <thead>
                <th>#</th>
                <th>Division</th>
                <th>Departments</th>
                <th>Actions</th>
              </thead>
              <tbody>
                @foreach ($divisions as $index => $division)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $division->name }}</td>
                    <td style="cursor:pointer;">
                      <span
                        id="expandDept"
                        data-toggle="modal"
                        data-target="#exampleModal"
                        data-division="{{ $division->name }}"
                        data-departments="{{ $division->departments }}"
                      >
                        <span class="label label-warning">
                          {{ count($division->departments) > 1 ? $division->departments[0]->name : 'None' }}
                        </span>
                        @if (count($division->departments) > 1)
                          <span class="text-muted">&nbsp;(+{{ count($division->departments) - 1 }} others)</span>
                        @endif
                      </span>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a class="btn btn-default btn-xs" href="{{ route('division.edit', ['id' => $division->id]) }}">
                          <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-default btn-xs" href="{{ route('division.trash', ['id' => $division->id]) }}">
                          <i class="fa fa-trash"></i>
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
    </div>
  </div>
</div>

@stop

@push('scripts')
<script>
  $(function () {

    $('table').on('click', '#expandDept', function () {
      $('.modal-title').html('');
      var division = $(this).data('division');
      $('.modal-title').append(division);

      $('.modal-body').html('');
      var departments = $(this).data('departments');
      for (i = 0; i < departments.length; i++) {
        var el = '<span class="label label-warning">'+departments[i]['name']+'</span>&nbsp;';
        $('.modal-body').append(el);
      }
    });

    $('#division-table').DataTable({
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
