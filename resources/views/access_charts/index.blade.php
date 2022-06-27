@extends('layouts.app')

@section('title', 'Access Charts')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Access Charts
      <small>Manage access chart</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Access Charts</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Access Charts
            </h3>
            <div class="box-toolbar pull-right">
              <div class="btn-group">
                <a href="{{ route('access_chart.create') }}" class="btn btn-primary btn-xs">
                  <i class="fa fa-plus"></i>&nbsp;ADD
                </a>
              </div>
            </div>
          </div>
          <div class="box-body table-responsive">

            @include ('successes.list')
            @include ('errors.list')

            <table class="table table-bordered table-hover" id="accesschart-table">
              <thead>
                <th>#</th>
                <th>Name</th>
                <th>Access For</th>
                <th>Approving Officers</th>
                <th>Actions</th>
              </thead>
              <tbody>
                @foreach ($access_charts as $index => $access_chart)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $access_chart->name }}</td>
                    <td>
                      @if ($access_chart->access_for === 0)
                        Overtime & Leave of absences
                      @elseif ($access_chart->access_for === 1)
                        MRF
                      @else
                        Purchase Order Files
                      @endif
                    </td>
                    <td>
                      @if ($access_chart->accessusersmap->count() === 0)
                        <span class="label label-danger">None</span>
                      @else
                        <span class="label label-success">
                          {{ $access_chart->accessusersmap->count() }}
                        </span>
                      @endif
                    </td>
                    <td>
                      <div class="btn-group">
                        <a class="btn btn-default btn-xs" title="View" href="{{ route('access_chart.officers', ['id' => $access_chart->id]) }}">
                          <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-default btn-xs" title="Edit" href="{{ route('access_chart.edit', ['id' => $access_chart->id]) }}">
                          <i class="fa fa-pencil"></i>
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
@stop

@push('scripts')
<script>
  $(function () {
    $('#accesschart-table').DataTable({
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