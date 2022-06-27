@extends('layouts.app')

@section('title', 'Approval Lists')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Approvals
      <small>Manage approvals</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Approval Lists</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-6">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              Overtime Pending Approvals
            </h3>
          </div>
          <div class="box-body">
            @include ('errors.list')
            @include ('successes.list')

            @forelse ($otpendings as $otpending)
              <div class="box box-solid box-default">
                <div class="box-header with-border">
                  <em>
                    <abbr title=""
                          data-container="body"
                          data-html="true"
                          data-toggle="popover"
                          data-placement="right"
                          data-trigger="hover"
                          data-content="<strong>Department: </strong>{{ $otpending->department }}<br>
                                        <strong>Position: </strong>{{ $otpending->position }}"
                          data-original-title="Employee Details"
                          aria-describedby="popover419705">{{ $otpending->employee }}</abbr>
                  </em>
                  <small class="float-right">
                    sent&nbsp;{{ \Carbon\Carbon::parse($otpending->created_at)->diffForHumans() }}
                    ({{ \Carbon\Carbon::parse($otpending->created_at)->format('M d, Y') }})
                  </small>
                </div>
                <div class="box-body">
                  {{ Carbon\Carbon::parse($otpending->date_from)->format('F d, Y') }}
                  ({{ Carbon\Carbon::parse($otpending->date_from)->format('h:i a') }} -
                  {{ $otpending->date_to !== null ? Carbon\Carbon::parse($otpending->date_to)->format('h:i a') : 'onwards' }})
                  @if ($otpending->working_dayoff)
                    <span class="label label-danger">Working Dayoff</span>
                  @endif
                  <br>
                  <?php
                    $reasons = explode(',', $otpending->reason);
                    foreach ($reasons as $index => $reason) {
                      echo "<ul class='reason-list' style='margin:0;padding: 0 10px 0 25px;'>";
                      echo "<li>" . $reason . "</li>";
                      echo "</ul>";
                    }
                  ?>
                </div>
                <div class="box-footer">
                  <a href="{{ route('approval.approve', ['id' => $otpending->id]) }}" class="btn btn-primary btn-sm">Approve</a>
                  <span class="float-right">
                    <a href="{{ route('approval.return', ['id' => $otpending->id]) }}" class="btn btn-warning btn-sm">Return</a>
                    <a href="{{ route('approval.reject', ['id' => $otpending->id]) }}" class="btn btn-danger btn-sm">Reject</a>
                  </span>
                </div>
              </div>
            @empty
              <span><em>No records found</em></span>
            @endforelse
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">
              LOA Pending Approvals
            </h3>
          </div>
          <div class="box-body">
            <span><em>No records found</em></span>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
  $().ready(function(){
    $('div abbr').popover({container: "body"});
  });
</script>
@endpush