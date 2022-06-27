@extends('layouts.app')

@section('title', 'Generate Daily Time Record Reports')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reports
      <small>Manage DTR report</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('regions') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">DTR</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              DTR Report
            </h3>
          </div>
          <div class="box-body">
            @include('includes.reports.header')

            <hr />
            <button class="btn btn-danger" onclick="print()"><i class="fa fa-print"></i> Print DTR</button>
          </div>
          <div class="box-body table-responsive">
            <div class="row" id="tbl" style="font-family: calibri; font-size: 18px;">
              @if ($useremployments)
              <table style="width: 100%;">
                <?php $count = 1; ?>

                @if ($count % 2 != 1)
                  </tr>
                @endif

                @foreach ($entries as $key => $entry)
                  @if ($count % 2 == 1)
                    <tr>
                  @endif
                  <td style="padding: 20px;">
                    <table class="dtr-tbl" style="border-collapse: collapse; width: 100%;">
                      <thead>
                        <tr>
                          <th style="text-align: left; border: 1px solid #000; width: 90px;"><img style="width: 70px;" src="{{ asset('images/logo.png') }}"></th>
                          <th colspan="4" style="text-align: center; border: 1px solid #000;">
                            ADDESSA CORPORATION <br>
                            DAILY TIME RECORD
                          </th>
                        </tr>
                        <tr>
                          <th style="text-align: left; border: 1px solid #000;">BRANCH:</th>
                          <th colspan="4" style="border: 1px solid #000; text-align: center;">{{ strtoupper($branch_name) }}</th>
                        </tr>
                        <tr>
                          <th style="text-align: left; border: 1px solid #000;">NAME:</th>
                          <th colspan="4" style="border: 1px solid #000; text-align: center;">
                            {{ strtoupper($entry['last_name']) }}, {{ strtoupper($entry['first_name']) }}
                          </th>
                        </tr>
                        <tr>
                          <th style="text-align: left; border: 1px solid #000;">DEPT:</th>
                          <th colspan="4" style="border: 1px solid #000; text-align: center;">{{ strtoupper($entry['department']) }}</th>
                        </tr>
                        <tr>
                          <th style="text-align: left; border: 1px solid #000;">POSITION:</th>
                          <th colspan="4" style="border: 1px solid #000; text-align: center;">{{ strtoupper($entry['position']) }}</th>
                        </tr>
                        <tr>
                          <th style="text-align: left; border: 1px solid #000;" colspan="2">PERIOD COVERED:</th>
                          <th colspan="3" style="border: 1px solid #000; text-align: center;">{{ strtoupper($start_display) }}-{{ strtoupper($end_display) }}</th>
                        </tr>
                        <tr>
                          <th style="border: 1px solid #000;"></th>
                          <th style="border: 1px solid #000;" colspan="2" class="text-center">A.M</th>
                          <th style="border: 1px solid #000;" colspan="2" class="text-center">P.M</th>
                          <th style="border: 1px solid #000;" class="text-center">TARD</th>
                          <th style="border: 1px solid #000;" class="text-center">OT</th>
                        </tr>
                      </thead>
                      <tbody class="text-center" style="text-align: center; border: 1px solid #000;">
                        @for ($jaa = 0; $jaa < $datediff; $jaa++)
                          <tr>
                            <td style="border: 1px solid #000;">
                              {{ \Carbon\Carbon::parse($entry['tito'][$jaa]['date'])->format('n/j/y') }}
                              </td>
                            <td style="border: 1px solid #000;">
                              @if ($entries[$key]['tito'][$jaa]['am_in'])
                                {{ \Carbon\Carbon::parse($entries[$key]['tito'][$jaa]['am_in'])->format('g:i') }}
                              @else
                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
                              @endif
                            </td>
                            <td style="border: 1px solid #000;">
                              @if ($entries[$key]['tito'][$jaa]['am_out'])
                                {{ \Carbon\Carbon::parse($entries[$key]['tito'][$jaa]['am_out'])->format('g:i') }}
                              @else
                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
                              @endif
                            </td>
                            <td style="border: 1px solid #000;">
                              @if ($entries[$key]['tito'][$jaa]['pm_in'])
                                {{ \Carbon\Carbon::parse($entries[$key]['tito'][$jaa]['pm_in'])->format('g:i') }}
                              @else
                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
                              @endif
                            </td>
                            <td style="border: 1px solid #000;">
                              @if ($entries[$key]['tito'][$jaa]['pm_out'])
                                {{ \Carbon\Carbon::parse($entries[$key]['tito'][$jaa]['pm_out'])->format('g:i') }}
                              @else
                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
                              @endif
                            </td>
                            <td style="border: 1px solid #000;"></td>
                            <td style="border: 1px solid #000;"></td>
                          </tr>
                        @endfor
                      </tbody>
                    </table>
                    <table style="border: 1px solid #000; border-top: none; width: 100%;">
                      <tbody>
                        <tr><td style="padding: 10px 0 0 10px;">DAY-OFF:</td></tr>
                        <tr><td style="padding-left: 10px;">OFFICIAL OFFICE HOUR:</td></tr>
                        <tr>
                          <td style="padding-left: 10px;">Prepared by: _______________</td>
                          <td>&nbsp;&nbsp;&nbsp;&nbsp;Verified by: _______________</td>
                        </tr>
                        <tr>
                          <td style="padding-left: 10px;">(Employee/Signature)</td>
                          <td>&nbsp;&nbsp;&nbsp;&nbsp;(BAIC)</td>
                        </tr>
                        <tr>
                          <td colspan="2" style="padding: 10px 0; text-align: center;">
                            Approved by:<br>
                            (BM/OIC)
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  @if ($count % 2 == 0)
                    </tr>
                  @endif
                  <?php $count++; ?>
                @endforeach

              </table>

              <div style="padding: 0 20px;">
                <strong>COMPUTATION OF TARDINESS</strong><br>
                <em style="margin-left: 20px;">* 1 MINUTE LATE = 30 MINS.</em><br>
                <em style="margin-left: 20px;">* 30 MINUTES LATE = 1 HR.</em><br>
              </div>

            </div>

            {{ $entries->links() }} <br>
            <button class="btn btn-danger" onclick="print()"><i class="fa fa-print"></i> Print DTR</button>
            
            @endif
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

<!-- Script for printing report in new window tab -->
<script>
  function print() {
      var divText = document.getElementById("tbl").outerHTML;
      var myWindow = window.open('', '', 'width=500,height=500');
      var doc = myWindow.document;
      doc.open();
      doc.write(divText);
      doc.close();
  }
</script>

<!-- Script for saving div as image -->
<script>
// $(function() {
//   $("#btnSave").click(function() {
//     html2canvas($("#tbl"), {
//       onrendered: function(canvas) {
//         // enable console.log to check canvas width & height
//         // console.log(canvas);

//         window.open(canvas.toDataURL());
//         // saveAs(canvas.toDataURL(), 'canvas.png');
//       }
//     });
//   });

//   function saveAs(uri, filename) {
//     var link = document.createElement('a');
//     if (typeof link.download === 'string') {
//       link.href = uri;
//       link.download = filename;

//       //Firefox requires the link to be in the body
//       document.body.appendChild(link);

//       //simulate click
//       link.click();

//       //remove the link when done
//       document.body.removeChild(link);
//     } else {
//       window.open(uri);
//     }
//   }
// });
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
var branch = '{{ $branch_name }}';
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
