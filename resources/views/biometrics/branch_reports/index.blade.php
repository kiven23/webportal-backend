@extends('layouts.app')

@section('title', 'Generate Biometric Reports')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reports
      <small>Manage biometric report</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('regions') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Biometrics</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Biometric Report
            </h3>
          </div>
          <div class="box-body">
            @include('includes.reports.header')

            <hr />
            <button onclick="exportpdf()" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i>&nbsp;Export PDF</button>
          </div>
          <div class="box-body table-responsive">
            <table class="table table-bordered" id="biometric-table">
              <thead>
                <th>No.</th>
                <th>Name</th>
                <th>Date/Time</th>
                <th>Status</th>
                <th>Location ID</th>
              </thead>
              <tbody>
                @if ($biometrics)
                  @foreach ($biometrics as $index => $biometric)
                    <tr>
                      <td>{{ $biometric->sss }}</td>
                      <td>{{ $biometric->last_name }}, {{ $biometric->first_name }}</td>
                      <td>{{ $biometric->datetime }}</td>
                      <td>{{ $biometric->status }}</td>
                      <td>{{ $biometric->location }}</td>
                    </tr>
                  @endforeach
                @endif
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
    $('#daterange').daterangepicker()

    // Initialize Select2 Elements
    $('.select2').select2();
  })
</script>

<script>
  $(function () {
    $('#biometric-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'scrollX'     : true,
      'aLengthMenu': [
          [10, 25, 50, 100, 200, -1],
          [10, 25, 50, 100, 200, "All"]
      ],
    })
  })
</script>

<script>
    function exportpdf() {
      var doc = new jsPDF('p','px','letter');
      var elem = document.getElementById("biometric-table");
      var res = doc.autoTableHtmlToJson(elem);

      var header = "ADDESSA CORPORATION";

      var title = '{{ $branch->name }} Biometric Logs';
      var subtitle = '{{ $start_display }}-{{ $end_display }}';

      doc.setFontSize(8);
      doc.setFontType('bold');
      doc.text(header, 200, 16);

      doc.setFontSize(8);
      doc.setFontType('bold');
      doc.text(title, 30, 46);

      doc.setFontSize(8);
      doc.setFontType('bold');
      doc.text(subtitle, 30, 56);

      doc.autoTable(res.columns, res.data,
                    {
                      startY: 66,
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

<script>
	$(function () {
    $('#startDate').datetimepicker({
      format: 'YYYY-MM-DD'
    });
    $('#endDate').datetimepicker({
      format: 'YYYY-MM-DD',
      useCurrent: false //Important! See issue #1075
    });
    $("#startDate").on("dp.change", function (e) {
      $('#endDate').data("DateTimePicker").minDate(e.date);
    });
    $("#endDate").on("dp.change", function (e) {
      $('#startDate').data("DateTimePicker").maxDate(e.date);
    });
  });


// ---------------
// TABLE EXPORT JS
// ---------------
var branch = '{{ $branch->name }}';
var date = '({{ $start_display }} - {{ $end_display }})';
$('#exportbtn').click(function () {
    $('#otreport').tableExport({
        fileName: branch + ' - Overtime Report' + date,
        type: 'xls',
        jspdf: {orientation: 'l',
          margins: {right: 10, left: 10, top: 40, bottom: 40},
          autotable: {styles: {fillColor: 'inherit',
                               textColor: 'inherit',
                               fillStyle: 'DF',
                               lineColor: 200,
                               lineWidth: 0.1},
                      tableWidth: 'auto'}}
    });
});

// Processing
$('#processing').hide();
$('#importbtn').click(function() {
    $(this).hide();
    $('#processing').show();
});

</script>
@endpush
