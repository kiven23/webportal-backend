@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Employees
      <small>Manage employee</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Employees</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Employees
            </h3>
            <div class="box-toolbar pull-right">
              <button onclick="exportpdf()" class="btn btn-danger btn-xs"><i class="fa fa-file-pdf-o"></i>&nbsp;Export PDF</button>
            </div>
          </div>
          <div class="box-body table-responsive">
            @include ('errors.list')
            @include ('successes.list')

            <table class="table table-bordered table-hover" id="employee-table">
              <thead>
                  <th>#</th>
                  <th>Employee</th>
                  <th>Position</th>
                  <th>Branch</th>
                  <th>Remarks</th>
                  <th>Last Date Reported</th>
                  @if (\Auth::user()->hasPermissionTo('Edit Employees'))
                    <th>Actions</th>
                  @endif
              </thead>
              <tbody>
                @foreach ($employees as $index => $employee)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                      {{ $employee->user->first_name }}
                      {{ $employee->user->last_name }}
                    </td>
                    <td>
                      @if ($employee->position)
                        {{ $employee->position->name }}
                      @else
                        Not assigned
                      @endif
                    </td>
                    <td>{{ $employee->branch ? $employee->branch->name : 'N/A' }}</td>
                    <td>{{ $employee->remarks }}</td>
                    <td>
                      @if ($employee->last_date_reported)
                        {{ \Carbon\Carbon::parse($employee->last_date_reported )->format('F d, Y g:ia') }}
                      @endif
                    </td>
                    @if (\Auth::user()->hasPermissionTo('Edit Employees'))
                      <td>
                        <a href="{{ route('employee.edit', ['id' => $employee->id]) }}" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>
                      </td>
                    @endif
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
    $('#employee-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'order'       : [],
      'info'        : true,
      'select'      : true,
      'responsive'  : true,
      'scrollY'     : "300px",
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

  function exportpdf() {
    var doc = new jsPDF('p','px','letter');
    var elem = document.getElementById("employee-table");
    var res = doc.autoTableHtmlToJson(elem);

    var header = "ADDESSA CORPORATION";

    var title = 'Employees Update';

    doc.setFontSize(8);
    doc.setFontType('bold');
    doc.text(header, 250, 16);

    doc.setFontSize(8);
    doc.setFontType('bold');
    doc.text(title, 30, 46);

    doc.setFontSize(8);
    doc.setFontType('bold');

    doc.autoTable(res.columns, res.data,
                  {
                    startY: 55,
                    theme: 'grid',
                    styles: {
                      overflow: 'linebreak',
                      fontSize: 7,
                      fillColor: [255, 255, 255],
                      textColor: [0, 0, 0],
                      lineColor: [0, 0, 0],
                      lineWidth: .1,
                    }
                  },
                  );
    doc.output("dataurlnewwindow");
  }
</script>
@endpush