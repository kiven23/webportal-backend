@extends('layouts.app')

@section('title', 'View Maintenance Request')

@section('content')
<style>
  @media print {
    body, html {
      font-size: 21px;
    }

    .print-hidden {
      display: none;
    }

    a[href]:after {
      content: none !important;
    }
  }

  hr {
    border: 5px solid #3c8dbc;
  }

  .maint-radio,
  .maint-checkbox-sub {
    list-style:none;
    padding:0 0 0 15px;
  }

  .maint-radio label,
  .maint-checkbox-sub label {
    font-weight: normal;
  }

  .maint-checkbox {
    list-style:none;
    padding:0;
  }
</style>
@php

  $nature_concerns = explode(":", $maint_request->nature_concern);
  $nature_concern_subs = explode(",", $nature_concerns[2]);

  $location = explode(":", $maint_request->location);
  $locations = explode(",", $location[0]);
  $location_subs = explode(",", $location[1]);

  // for Case in-sensitive array_search() with partial matches
  function array_find($needle, array $haystack) {
    foreach ($haystack as $key => $value) {
      if (false !== stripos($value, $needle)) {
        $new_val = explode("-", $value);
        $sliced = array_slice($new_val, 0, -1);
        return implode("", $sliced);
      }
    }
    return false;
  }

@endphp
<div class="content-wrapper">
  <section class="content-header">
    <h1>
    	Maintenance Request
    	<small>Manage maintenance request</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route($maint_request->user_id == \Auth::user()->id ? 'maint_requests' : ($is_approver ? 'maint_request.approval.pending' : 'maint_request.approval.overlook')) }}">Maintenance Requests</a></li>
      <li class="active">View</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-solid">
          <div class="box-body print-hidden">
            <span>
              @if ($maint_request->status === 1)
                <span class="label label-primary">RECEIVED</span>
              @elseif ($maint_request->status === 2)
                <span class="label label-warning">CANCELLED</span>
              @elseif ($maint_request->status === 3)
                <span class="label label-success">APPROVED</span>
              @elseif ($maint_request->status === 4)
                <span class="label bg-black">COMPLETED</span>
              @endif
            </span>
            @if ($maint_request->survey)
              @if ($maint_request->survey->rate === 1)
                <span class="label label-danger">Poor</span>
              @elseif ($maint_request->survey->rate === 2)
                <span class="label label-warning">Needs Improvement</span>
              @elseif ($maint_request->survey->rate === 3)
                <span class="label label-info">Satisfactory</span>
              @elseif ($maint_request->survey->rate === 4)
                <span class="label label-primary">Very Good</span>
              @elseif ($maint_request->survey->rate === 5)
                <span class="label label-success">Excellent</span>
              @endif
            @endif

            @if (\Auth::user()->hasPermissionTo('Maintenance Request Lists') &&
                 $maint_request->status !== 4)
              <span class="pull-right">
                @if ($escalatable && $is_approver)
                  <a href="{{ route('maint_request.approval.escalate', ['id' => $maint_request->id]) }}" class="spin btn btn-default btn-sm">
                    <i class="fa fa-level-up"></i>&nbsp;ESCALATE
                  </a>
                @endif
                <a href="{{ route('maint_request.approval.cancel', ['id' => $maint_request->id]) }}" class="spin btn btn-warning btn-sm">
                  {{ $maint_request->status === 2 ? 'REVOKE CANCELLED' : 'MARK AS CANCELLED' }}
                </a>
                @if ($maint_request->status === 1)
                  <a href="{{ route('maint_request.approval.approve', ['id' => $maint_request->id]) }}" class="spin btn btn-success btn-sm">
                    MARK AS APPROVED
                  </a>
                  <a href="{{ route('maint_request.trash', ['id' => $maint_request->id]) }}" class="spin btn btn-danger btn-sm" title="Delete">
                    <i class="fa fa-trash"></i>
                  </a>
                @endif
              </span>
            @else
              @if ($maint_request->status === 3)
                <span class="pull-right">
                  <a href="{{ route('maint_request.completion', ['id' => $maint_request->id]) }}" class="spin btn btn-default btn-sm">
                    <i class="fa fa-check-circle"></i>&nbsp;Proceed Completion
                  </a>
                </span>
              @endif
            @endif
          </div>
          <div class="box-body" id="print">
            <div id="alert"></div>

            <div class="row">
              <div class="col-md-12 text-center">
                <strong>ADDESSA CORPORATION</strong><br>
                <strong>ENGINEERING & CONSTRUCTION DEPARTMENT</strong><br>
              </div>
              <div class="col-md-12 text-center" style="margin-bottom: 5px;background-color:#3c8dbc !important;">
                <strong style="color:#ffffff !important;">MAINTENANCE REQUEST FORM</strong>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>BRANCH / HEAD OFFICE:</label>
                      <span>{{ $branch }}</span>
                      <br>
                      <label>CONTACT NO.:</label>
                      <span>{{ $contact }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>DATE:</label>
                      <span>{{ \Carbon\Carbon::parse($maint_request->created_at)->format('M d, Y') }}</span>
                      <br>
                      <label>REQUEST NO.:</label>
                      <input type="text" value="{{ $maint_request->req_no }}">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-3"><label>NATURE OF CONCERN</label></div>
                      <div class="col-md-3 maint-radio">
                        <label>
                          <input
                            {{ $nature_concerns[0] == "pre-approved" ? 'checked' : '' }}
                            type="radio"
                            name="nature-of-concern"
                            value="pre-approved">
                          PRE-APPROVED
                        </label>
                      </div>
                      <div class="col-md-3 maint-radio">
                        <label>
                          <input
                            {{ $nature_concerns[0] == "urgent" ? 'checked' : '' }}
                            type="radio"
                            name="nature-of-concern"
                            value="urgent">
                          URGENT
                        </label>
                      </div>
                      <div class="col-md-3 maint-radio">
                        <label>
                          <input
                            {{ $nature_concerns[0] == "regular" ? 'checked' : '' }}
                            type="radio"
                            name="nature-of-concern"
                            value="regular">
                          REGULAR
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-12">
                      <div id="concern"></div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "electrical" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="electrical">
                            ELECTRICAL
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('wire-exposed', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="wire-exposed">
                                WIRE - EXPOSED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('lights-busted', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="lights-busted">
                                LIGHTS - BUSTED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('outlet-busted', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="outlet-busted">
                                OUTLET - BUSTED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('switch-busted', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="switch-busted">
                                SWITCH - BUSTED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('fuse-busted', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="fuse-busted">
                                FUSE - BUSTED
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "floor" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="floor">
                            FLOOR
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('tiles-warped', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="tiles-warped">
                                TILES - WARPED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('tiles-cracked', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="tiles-cracked">
                                TILES - CRACKED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('floor-crack', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="floor-crack">
                                FLOOR - CRACK
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('water-damaged', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="water-damaged">
                                WATER DAMAGED
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "rest-room" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="rest-room">
                            REST ROOM
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('tiles-wall', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="tiles-wall">
                                TILES - WALL
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('tiles-floor', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="tiles-floor">
                                TILES - FLOOR
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('faucet-leak', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="faucet-leak">
                                FAUCET - LEAK
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('flush-faulty', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="flush-faulty">
                                FLUSH - FAULTY
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "doors" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="doors">
                            DOORS
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('glass-floor-broken', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="glass-floor-broken">
                                GLASS FLOOR - BROKEN
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('roll-up-broken', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="roll-up-broken">
                                ROLL UP - STUCK UP/BROKEN
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('hinge-broken', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="hinge-broken">
                                HINGE - BROKEN
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('jamb-damaged', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="jamb-damaged">
                                JAMB - DAMAGE
                              </label>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "display-racks" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="display-racks">
                            DISPLAY RACKS
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('no-racks', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="no-racks">
                                NO RACKS
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('cracked', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="cracked">
                                CRACKED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('chipped-edge', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="chipped-edge">
                                CHIPPED EDGE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('peeling-veneer', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="peeling-veneer">
                                PEELING VENEER
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "office-counter" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="office-counter">
                            OFFICE COUNTER
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('poor-condition', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="poor-condition">
                                POOR CONDITION
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('water-damage', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="water-damage">
                                WATER DAMAGE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('glass-broken', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="glass-broken">
                                GLASS - BROKEN
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "water-supply" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="water-supply">
                            WATER SUPPLY
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('no-water-supply', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="no-water-supply">
                                NO WATER SUPPLY
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('weak-pressure', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="weak-pressure">
                                WEAK PRESSURE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('unusual-water-bill', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="unusual-water-bill">
                                UNUSUAL WATER BILL
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "walls" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="walls">
                            WALLS
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('cracked', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="cracked">
                                CRACKED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('paint-bubbled', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="paint-bubbled">
                                PAINT - BUBBLED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('water-damage', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="water-damage">
                                WATER DAMAGE
                              </label>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "aircon" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="aircon">
                            AIRCON
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('poor-cooling', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="poor-cooling">
                                POOR COOLING
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('leaking', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="leaking">
                                LEAKING
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('defective', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="defective">
                                DEFECTIVE
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "ceiling" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="ceiling">
                            CEILING
                          </label>

                          <ul class="maint-radio">
                            <li>
                              <label>
                                <input
                                  {{ in_array('cracked', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="cracked">
                                CRACKED
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('water-damage', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="water-damage">
                                WATER DAMAGE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('sagged', $nature_concern_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="nature-of-concern-sub-child[]"
                                  value="sagged">
                                SAGGED
                              </label>
                            </li>
                          </ul>
                        </div>
                        <div class="col-md-3">
                          <label>
                            <input
                              {{ $nature_concerns[1] == "others" ? 'checked' : '' }}
                              type="radio"
                              name="nature-of-concern-sub"
                              value="others">
                            OTHERS
                          </label><br>

                          <textarea style="width:100%;resize:none;" name="nature-of-concern-sub-child-text">{{ array_find('-textarea', $nature_concern_subs) }}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group">
                    <div class="col-md-12">
                      <label>
                        <em><strong>LOCATION:</strong></em>
                      </label><br id="location">
                    </div>

                    <div class="col-md-3">
                      <ul class="maint-checkbox">
                        <li>
                          <label>
                            <input
                              {{ in_array('first-floor', $locations) ? 'checked' : '' }}
                              type="checkbox"
                              name="location[]"
                              value="first-floor">
                            FIRST FLOOR
                          </label>
                          <ul class="maint-checkbox-sub">
                            <li>
                              <label>
                                <input
                                  {{ in_array('1showroom', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="1showroom">
                                SHOWROOM
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('1acctg-office', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="1acctg-office">
                                ACCOUNTING OFFICE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('1whs', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="1whs">
                                WAREHOUSE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('1counter-area', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="1counter-area">
                                COUNTER AREA
                              </label>
                            </li>
                          </ul>
                        </li>
                      </ul>
                    </div>
                    <div class="col-md-3">
                      <ul class="maint-checkbox">
                        <li>
                          <label>
                            <input
                              {{ in_array('second-floor', $locations) ? 'checked' : '' }}
                              type="checkbox"
                              name="location[]"
                              value="second-floor">
                            SECOND FLOOR
                          </label>
                          <ul class="maint-checkbox-sub">
                            <li>
                              <label>
                                <input
                                  {{ in_array('2showroom', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="2showroom">
                                SHOWROOM
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('2acctg-office', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="2acctg-office">
                                ACCOUNTING OFFICE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('2whs', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="2whs">
                                WAREHOUSE
                              </label>
                            </li>
                          </ul>
                        </li>
                        <li>
                          <label>
                            <input
                              {{ in_array('others', $locations) ? 'checked' : '' }}
                              type="checkbox"
                              name="location[]"
                              value="others">
                            <input
                              value="{{ array_find('-textarea', $location_subs) }}"
                              type="text"
                              placeholder="OTHERS"
                              name="location-sub-text">
                          </label>
                        </li>
                      </ul>
                    </div>
                    <div class="col-md-3">
                      <ul class="maint-checkbox">
                        <li>
                          <label>
                            <input
                              {{ in_array('third-floor', $locations) ? 'checked' : '' }}
                              type="checkbox"
                              name="location[]"
                              value="third-floor">
                            THIRD FLOOR
                          </label>
                          <ul class="maint-checkbox-sub">
                            <li>
                              <label>
                                <input
                                  {{ in_array('3showroom', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="3showroom">
                                SHOWROOM
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('3acctg-office', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="3acctg-office">
                                ACCOUNTING OFFICE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('3whs', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="3whs">
                                WAREHOUSE
                              </label>
                            </li>
                          </ul>
                        </li>
                      </ul>
                    </div>
                    <div class="col-md-3">
                      <ul class="maint-checkbox">
                        <li>
                          <label>
                            <input
                              {{ in_array('fourth-floor', $locations) ? 'checked' : '' }}
                              type="checkbox"
                              name="location[]"
                              value="fourth-floor">
                            FOURTH FLOOR
                          </label>
                          <ul class="maint-checkbox-sub">
                            <li>
                              <label>
                                <input
                                  {{ in_array('4showroom', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="4showroom">
                                SHOWROOM
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('4acctg-office', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="4acctg-office">
                                ACCOUNTING OFFICE
                              </label>
                            </li>
                            <li>
                              <label>
                                <input
                                  {{ in_array('4whs', $location_subs) ? 'checked' : '' }}
                                  type="checkbox"
                                  name="location-sub[]"
                                  value="4whs">
                                WAREHOUSE
                              </label>
                            </li>
                          </ul>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <hr>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>DESCRIBE IN DETAIL / ACTION TAKEN:</label><br>
                      <textarea id="remarks" style="width:100%;height:100px;resize:none;" name="remarks">{{ $maint_request->remarks }}</textarea>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>REQUESTED BY MANAGER:</label>
                          {{ $maint_request->user->first_name }}
                          {{ $maint_request->user->last_name }}
                          <br>
                          <label>RECEIVED BY:</label>
                          <input type="text" style="width:70%;" value="{{ $maint_request->received_by_user ? $maint_request->received_by_user->name : '' }}">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          @if (count($maint_request->files) > 0)
                            @foreach ($maint_request->files as $index => $file)
                              @if ($index === 0)
                                <em>
                                  <a
                                    style="color:#000;font-weight:bold;font-style:normal;"
                                    href="{{ asset('storage/'.$file->file_path) }}"
                                    data-lightbox="{{ $file->maint_request_id }}"
                                    data-title="{{ $file->file_name }}"
                                  >
                                    CANVAS ATTACHED&nbsp;<i class="fa fa-check-square-o"></i>
                                  </a>
                                </em>
                              @else
                                <a class="print-hidden" href="{{ asset('storage/'.$file->file_path) }}" data-lightbox="{{ $file->maint_request_id }}" data-title="{{ $file->file_name }}"></a>
                              @endif
                            @endforeach
                          @else
                            <label style="visibility:hidden;">FIXER</label>
                          @endif
                          <br>
                          <label>DATE RECEIVED:</label>
                          <input type="text" style="width:70%;" value="{{ $maint_request->date_received ? \Carbon\Carbon::parse($maint_request->date_received)->format('M d, Y') : '' }}">
                        </div>
                      </div>
                    </div>

                    <div class="box box-solid box-primary">
                      <div class="box-header text-center" style="background-color:#3c8dbc !important;">
                        <strong style="color:#ffffff !important;">INSTRUCTIONS</strong>
                      </div>

                      <div class="box-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>DATE:</label>
                              <input
                                type="text"
                                value="{{ $maint_request->ins_date ? \Carbon\Carbon::parse($maint_request->ins_date)->format('M d, Y') : '' }}"
                                style="width:70%;border:1px solid transparent;border-bottom:1px solid #333;">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>VERBALLY APPROVED BY:</label>
                              <input type="text" style="width:60%;border:1px solid transparent;border-bottom:1px solid #333;">
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="text-center pull-right" style="border:1px solid #6e6e6e;border-radius:5px;padding: 5px;width:70%;">
                              <label>TIME OF APPROVAL:</label>
                              <input type="text" style="width:100%;" value="{{ $maint_request->ins_date ? \Carbon\Carbon::parse($maint_request->ins_date)->format('H:i a') : '' }}">
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-md-12">
                            <label>INSTRUCTIONS / APPROVAL:</label>
                            <textarea
                              style="width:100%;height:150px;resize:none;"
                            >{{ $maint_request->instruction ? $maint_request->instruction : '' }}</textarea>
                          </div>
                        </div>
                      </div>

                      <div class="box-footer">
                        <div class="row">
                          <div class="col-md-6">
                            <label>Recommended By:</label>
                          </div>
                          <div class="col-md-6">
                            <label>Approved By: {{ $maint_request->approved_by_user ? $maint_request->approved_by_user->name : '' }}</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@stop

@push('scripts')
<script>
  $(document).ready(function () {
    $('.spin').click(function () {
      $(this).html($(this).html() + '&nbsp;<i class="fa fa-spinner fa-spin"></i>');
      $(this).attr('disabled', true);
    });
    // Disable editing inside
    $('.box-body input, .box-body textarea').on('click keydown', function(){
      return false;
    });
  });
</script>
@endpush