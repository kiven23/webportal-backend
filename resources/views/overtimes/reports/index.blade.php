@extends('layouts.app')

@section('title', 'Generate Overtime Reports')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Reports
      <small>Manage overtime report</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Overtime Report</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              Overtime Report
            </h3>
          </div>
          <div class="box-body">
            @include('includes.reports.header')

            <hr />
            <a href="javascript:void(0);" id="exportbtn" class="btn btn-danger">
              <i class="fa fa-file-excel-o"></i>&nbsp;Export XLS
            </a>
          </div>
          <div class="box-body">
            <table id="otreport" class="table table-bordered table-condensed">
              <thead style="border: 1px solid transparent;">
                <tr>
                  <th style="border: 0;" colspan="6">ONLINE FILING OF OT & LOA</th>
                </tr>
                <tr>
                  <th style="border: 0;" colspan="6">WEB APP GENERATED REPORT</th>
                </tr>
                <tr>
                  <th style="border: 0;" colspan="6">
                    {{ strtoupper($branch_name) }} BRANCH
                    @if ($branch_sched != null && $branch_sched->schedule != null)
                      ({{ \Carbon\Carbon::parse($branch_sched->schedule->time_from)->format('h:i a') }} -
                      {{ \Carbon\Carbon::parse($branch_sched->schedule->time_to)->format('h:i a') }})
                    @endif
                  </th>
                </tr>
                <tr>
                  <th style="border: 0;" colspan="6">OVERTIME REPORT ({{ $start_display }} - {{ $end_display }})</th>
                </tr>
                <tr>
                  <th style="border: 0;" colspan="6"></th>
                </tr>
                <tr>
                  <th colspan="3">FILED OVERTIME</th>
                  <th colspan="2">BIOMETRIC</th>
                </tr>
                <tr>
                  <th>DATE FROM</th>
                  <th>DATE TO</th>
                  <th style="width: 500px;">REASON</th>
                  <th>DATE FROM</th>
                  <th>DATE TO</th>
                  <th>HOURS</th>
                </tr>
              </thead>
              @if (count($useremployments) > 0)
                <tbody>
                <?php $runningtotal = 0 ?>
                @foreach ($users as $user)
                  <?php $subtotal = 0 ?>
                  <tr><td colspan="6" class="hide" data-tableexport-display="always"></td></tr>
                  @foreach ($useremployments as $useremployment)
                    @if ($user->user_id === $useremployment->user_id)
                      <tr>
                        <td>{{ \Carbon\Carbon::parse($useremployment->date_from)->format('M d, Y h:i a') }}</td>
                        <td>{{ $useremployment->date_to === null ? 'onwards' : \Carbon\Carbon::parse($useremployment->date_to)->format('h:i a') }}</td>
                        <td>{{ $useremployment->reason }}</td>
                        <td>{{ \Carbon\Carbon::parse($useremployment->bio_datefrom)->format('h:i a') }}</td>
                        <td>{{ \Carbon\Carbon::parse($useremployment->bio_dateto)->format('h:i a') }}</td>
                        <td>
                          {{ $useremployment->pre_totaltime > 0 ? ($useremployment->pre_totaltime != 0 || $useremployment->post_totaltime > .5 ?
                             $useremployment->pre_totaltime + $useremployment->post_totaltime :
                             0) : $useremployment->post_totaltime
                          }}
                        </td>
                      </tr>
                      <?php
                        $runningtotal += ($useremployment->pre_totaltime > 0 ?
                                          ($useremployment->pre_totaltime != 0 || $useremployment->post_totaltime > .5 ?
                                          $useremployment->pre_totaltime + $useremployment->post_totaltime :
                                          0) : $useremployment->post_totaltime);
                        $subtotal += ($useremployment->pre_totaltime > 0 ?
                                      ($useremployment->pre_totaltime != 0 || $useremployment->post_totaltime > .5 ?
                                      $useremployment->pre_totaltime + $useremployment->post_totaltime :
                                      0) : $useremployment->post_totaltime);
                      ?>
                    @endif
                  @endforeach
                  <tr>
                    <th colspan="3">{{ strtoupper($user->last_name) }}, {{ strtoupper($user->first_name) }}</th>
                    <th colspan="2"><span class="pull-right">TOTAL</span></th>
                    <th>{{ $subtotal }}</th>
                  </tr>
                @endforeach
                </tbody>
                <tfoot style="color: red;">
                  <tr>
                    <th colspan="5"><span class="pull-right">OVERALL TOTAL</span></th>
                    <th>{{ $runningtotal }}</th>
                  </tr>
                </tfoot>
              @else
                <tbody>
                  <tr><td colspan="6">No records found.</td></tr>
                </tbody>
              @endif
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