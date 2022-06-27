@extends('layouts.app')

@section('title', 'User Employement Details')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Users
      <small>Manage user employment</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('users.index') }}">Users</a></li>
      <li class="active">Employment</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Employment details</h3>
          </div>
          <div class="box-body table-responsive">
            @include ('errors.list')
            @include ('successes.list')

            @if (\Session::has('duplicates'))
              <h3>Duplicate entries found:</h3>
              <ol>
                @foreach(\Session::get('duplicates') as $i => $duplicate)
                  <li>{{ $duplicate->name }} - {{ $duplicate->contact_number }}</li>
                @endforeach
              </ol>
            @endif
            <table id="employmentdetail-table" class="table table-bordered table-hover">
              <thead>
                <th>#</th>
                <th>Employee</th>
                <th>SSS</th>
                <th>OTLOA AC</th>
                <th>MRF AC</th>
                <th>PO File AC</th>
                <th>Division</th>
                <th>Department</th>
                <th>Position</th>
                <th>Branch</th>
                <th>Payroll</th>
                <th>Actions</th>
              </thead>
              <tbody>
                @foreach ($employment_details as $index => $employment_detail)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                      {{ $employment_detail->user->first_name }}
                      {{ $employment_detail->user->last_name }}
                    </td>
                    <td>
                      @if ($employment_detail->sss)
                        {{ $employment_detail->sss }}
                      @else
                        <span class="label label-danger">None</span>
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->accesschart)
                        {{ $employment_detail->accesschart->name }}
                      @else
                        <span class="label label-danger">None</span>
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->mrf_accesschart)
                        {{ $employment_detail->mrf_accesschart->name }}
                      @else
                        <span class="label label-danger">None</span>
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->po_file_accesschart)
                        {{ $employment_detail->po_file_accesschart->name }}
                      @else
                        <span class="label label-danger">None</span>
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->division_id)
                        {{ $employment_detail->division->name }}
                      @else
                        @if ($employment_detail->department['division'])
                          {{ $employment_detail->department->division->name }}
                        @else
                          <span class="label label-danger">N/A</span>
                        @endif
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->department)
                        {{ $employment_detail->department->name }}
                      @else
                        <span class="label label-danger">N/A</span>
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->position)
                        {{ $employment_detail->position->name }}
                      @else
                        <span class="label label-danger">None</span>
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->branch)
                        {{ $employment_detail->branch->name }}
                      @else
                        <span class="label label-danger">N/A</span>
                      @endif
                    </td>
                    <td>
                      @if ($employment_detail->payroll !== null)
                        @if ($employment_detail->payroll == 0)
                          Cash
                        @else
                          ATM
                        @endif
                      @else
                        Not assigned
                      @endif
                    </td>
                    <td>
                      <a href="{{ route('employment_detail.edit', ['id' => $employment_detail->id]) }}" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>
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
    $('#employmentdetail-table').DataTable({
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
      responsive: {
          details: {
          		display: $.fn.dataTable.Responsive.display.modal( {
                  header: function ( row ) {
                      var data = row.data();
                      return 'Employment Details';
                  }
              } ),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                  tableClass: 'table'
              } ),
          }
      },
      columnDefs: [
          { targets: [2, 3, 4, 5], visible: false },
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
