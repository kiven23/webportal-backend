@extends('layouts.app')

@section('title', 'Overtime Overlooks')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Approvals
      <small>Manage approvals</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Overlooks</li>
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

            @forelse ($pendings as $index => $pending)
              <div class="box box-solid box-default">
                <div class="box-header">
                  <em>
                    <abbr title=""
                          data-container="body"
                          data-html="true"
                          data-toggle="popover"
                          data-placement="right"
                          data-trigger="hover"
                          data-content="<strong>Department: </strong>{{ $pending->user->employment->department->name }}<br>
                                        <strong>Position: </strong>{{ $pending->user->employment->position->name }}"
                          data-original-title="Employee Details"
                          aria-describedby="popover419705">{{ $pending->user->first_name }} {{ $pending->user->last_name }}</abbr>
                  </em>&nbsp;
                  @foreach ($pending->officers_approved as $officer_approved)
                    <i
                      class="icon-approved fa fa-check-circle" data-toggle="tooltip"
                      title="
                        {{ $officer_approved->approver }}
                      "></i>
                  @endforeach
                  <small class="float-right">{{ \Carbon\Carbon::parse($pending->created_at)->diffForHumans() }}</small>
                </div>
                <div class="box-body">
                  {{ Carbon\Carbon::parse($pending->date_from)->format('F d, Y') }}
                  ({{ Carbon\Carbon::parse($pending->date_from)->format('h:i a') }} -
                  {{ $pending->date_to !== null ? Carbon\Carbon::parse($pending->date_to)->format('h:i a') : 'onwards' }})
                  <br>
                   <?php
                    $reasons = explode(',', $pending->reason);
                    foreach ($reasons as $index => $reason) {
                      echo "<ul class='reason-list' style='margin:0;padding: 0 10px 0 25px;'>";
                      echo "<li>" . $reason . "</li>";
                      echo "</ul>";
                    }
                  ?>
                </div>
                <div class="box-footer">
                  <a href="{{ route('approval.approve', ['id' => $pending->id]) }}" class="btn btn-primary btn-sm">Approve</a>
                  <span class="float-right">
                    <a href="{{ route('approval.return', ['id' => $pending->id]) }}" class="btn btn-warning btn-sm">Return</a>
                    <a href="{{ route('approval.reject', ['id' => $pending->id]) }}" class="btn btn-danger btn-sm">Reject</a>
                  </span>
                </div>
              </div>
            @empty
              <em>No records found</em>
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
    $('i').tooltip({container: "body"});
    $('div abbr').popover({container: "body"});
  });
</script>
@endpush