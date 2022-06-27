@extends('layouts.app')

@section('title', 'Approve Filed Overtime')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Approvals
      <small>Manage approvals</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      @if (\Auth::user()->hasPermissionTo('Overlook Overtimes'))
        <li><a href="{{ route('approval.overlook') }}">Approval Lists - Overlook</a></li>
      @else
        <li><a href="{{ route('approval.pending') }}">Approval Lists</a></li>
      @endif
      <li class="active">Approve</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
      <div class="col-md-12">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title">
              Confirm approval
            </h3>
          </div>
          <form action="{{ route('approval.proceed_approve', ['id' => $pending->id]) }}" method="post">
            {{ csrf_field() }}

            <div class="box-body">
            You are about to approve a filed overtime with the following details:
              <ul>
                <li><strong>Employee: </strong> {{ $pending->user->first_name }} {{ $pending->user->last_name }}</li>
                <li>
                  <strong>Date:</strong>
                  {{ Carbon\Carbon::parse($pending->date_from)->format('F d, Y') }}
                  ({{ Carbon\Carbon::parse($pending->date_from)->format('h:i a') }} -
                  {{ $pending->date_to !== null ? Carbon\Carbon::parse($pending->date_to)->format('h:i a') : 'onwards' }})
                </li>
                <li>
                  <strong>Reason:</strong>
                  <?php
                    $reasons = explode(',', $pending->reason);
                    foreach ($reasons as $index => $reason) {
                      echo "<ul class='reason-list' style='margin:0;padding: 0 10px 0 25px;'>";
                      echo "<li>" . $reason . "</li>";
                      echo "</ul>";
                    }
                  ?>
                </li>
              </ul>
            </div>
            <div class="box-footer">
              <button type="submit" class="btn btn-danger">Proceed</button>
              <a href="{{ route('approval.pending') }}" class="btn btn-default">No, go back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection