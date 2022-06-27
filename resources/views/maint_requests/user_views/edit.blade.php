@extends('layouts.app')

@section('title', 'Edit Maintenance Request')

@section('content')
<style>
  @media print {
    input[type=checkbox], input[type=radio] {
      opacity: 1 !important;
    }
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
      <li><a href="{{ route('maint_requests') }}">Maintenance Requests</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row mt-4 mb-4">
			<div class="col-md-12">
				<div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Edit maintenance request</h3>
          </div>

          <form
            id="frm"
            method="post"
            enctype="multipart/form-data"
            action="{{ route('maint_request.update', ['id' => $maint_request->id]) }}">
						{{ csrf_field() }}
						<div class="box-body">
              <div id="alert"></div>

              <div class="row">
                <div class="col-md-12 text-center">
                  <strong>ADDESSA CORPORATION</strong><br>
                  <strong>ENGINEERING & CONSTRUCTION DEPARTMENT</strong><br>
                </div>
                <div
                  class="col-md-12 text-center"
                  style="background-color:#466bff;
                         color:#ffffff;
                         padding: 3px 0;
                         margin: 5px;">
                  <strong>MAINTENANCE REQUEST FORM</strong>
                </div>
              </div>
							<div class="row">
								<div class="col-md-12">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>BRANCH:</label>
                        <span>{{ $branch }}</span>
                        <br>
                        <label>CONTACT NO.:</label>
                        <span>{{ $contact }}</span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>DATE:</label>
                        <span>{{ \Carbon\Carbon::now()->format('M d, Y') }}</span>
                        <br>
                        <label>REQUEST NO.:</label>
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
                            </label>

                            <textarea
                              name="nature-of-concern-sub-child-text"
                              class="form-control">{{ array_find('-textarea', $nature_concern_subs) }}</textarea>
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

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>DESCRIBE IN DETAIL / ACTION TAKEN:</label>
                        <textarea id="remarks" name="remarks" class="form-control">{{ $maint_request->remarks }}</textarea>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label>UPLOAD CANVAS</label>
                        <input type="file" name="canvas[]" class="form-control" multiple>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <label>UPLOADED CANVAS</label>
                      <ol class="file-lists">
                        @forelse ($maint_request->files as $file)
                          <li>
                            <a href="{{ asset('storage/'.$file->file_path) }}"
                              data-lightbox="{{ $file->maint_request_id }}"
                              data-title="{{ $file->file_name }}"
                            >{{ $file->file_name }}</a>
                            &nbsp;&nbsp;&nbsp;
                            <a
                              class="delete-file"
                              href="javascript:void(0);"
                              data-id="{{ $file->maint_request_id }}"
                              data-file-id="{{ $file->id }}"
                            ><i class="fa fa-trash text-danger" title="Delete File"></i></a>
                          </li>
                        @empty
                          <li id="file-empty">No uploaded file</li>
                        @endforelse
                      </ol>
                    </div>
                  </div>
								</div>
              </div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-md-12">
									<button type="submit" class="btn btn-primary">Update</button>
								</div>
							</div>
						</div>
					</form>
        </div>
      </div>
    </div>
  </section>
</div>
<form id="file-form" onsubmit="return confirm('Are you sure to delete this file?')">
  {{ csrf_field() }}
</form>
@stop

@push('scripts')
<script>
  $(document).ready(function () {

    $('ol').on('click', '.delete-file', function () {
      var form = $('#file-form');
      var id = $(this).data('id');
      var fileId = $(this).data('file-id');
      var url = "{{ route('maint_request.file.delete', ['id' => ':id', 'file_id' => ':file_id']) }}";
          url = url.replace(':id', id);
          url = url.replace(':file_id', fileId);
      form.attr('action', url);
      form.attr('method', 'POST');
      form.submit();
    });

  });
</script>

<script>
  $(document).ready(function () {

    var form        = $('#frm');
    var btn         = form.find('button[type="submit"]');
    var formMethod  = form.attr('method');
    var formAction  = form.attr('action');
    var formData    = form.get(0);

    form.submit(function (e) {
      e.preventDefault();

      btn.attr("disabled", true);
      btn.html("Updating <i class='fa fa-spinner fa-pulse'></i>");
      $('#alert').find('div.alert').remove();

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: formMethod,
        url: formAction,
        // data: form.serialize(),
        data: new FormData(formData),
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (response) {
          window.scrollTo(0,0);
          // show alert with success message
          var alert = '<div class="alert alert-dismissible alert-success"> \
                        <button type="button" class="close" data-dismiss="alert">&times;</button> \
                        <strong>Well Done!</strong> Request has been successfully updated. \
                      </div>';
          $('#alert').append(alert);
          var files = response.uploaded_files;
          $.each( files, function(key, value) {
            var id = value.id;
            var maint_request_id = value.maint_request_id;
            var file_name = value.file_name;
            var file_path = value.file_path;
            var url = "{{ asset('storage/:path') }}";
                url = url.replace(':path', file_path);

            var li = '<li><a href="'+url+'" data-lightbox="'+maint_request_id+'" data-title="'+file_name+'">'+file_name+'</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="delete-file" data-id="'+maint_request_id+'" data-file-id="'+id+'"><i class="fa fa-trash text-danger" title="Delete File"></i></a></li>';
            $('.file-lists').append(li);
            $('#file-empty').remove(); // remove empty message
          });
        },
        error: function (data) {
          window.scrollTo(0,0);
          // show alert with error message
          var alert = '<div class="alert alert-dismissible alert-danger"> \
                        <button type="button" class="close" data-dismiss="alert">&times;</button> \
                        <strong>Oops!</strong> Please correct all the errors below. \
                      </div>';
          $('#alert').append(alert);

          var data = JSON.stringify(data);
          var data = JSON.parse(data);
          var data = data.responseJSON;

          if (data.validator) { // check if there's validation error
            for (var key in data.validator) {
              if (data.validator.hasOwnProperty(key)) {
                $(document.getElementById(key)).parents('div.form-group').addClass('has-error');
                var flash_err = '<span class="form-text text-danger">'+data.validator[key]+'</span>';
                $(document.getElementById(key)).next().html(''); // reset to default
                $(flash_err).insertAfter(document.getElementById(key));
              }
            }
          }
        },
        complete: function () {
          window.scrollTo(0,0);
          btn.attr("disabled", false);
          btn.html("Update");
        }
      });
    });







    

    // Remove display errors
    $('input, textarea').on('keydown change', function () {
      $(this).parents('div.form-group').removeClass('has-error');
      $(this).parents('div.form-group').find('span').remove();
      $('#alert').find('div.alert').remove();
    });





    // Checkbox - Nature of concern
    $('input[name="nature-of-concern-sub-child[]"]').on('change', function () {
      $(this).parents('ul').prev('label').children().prop('checked', true);
      $('textarea[name="nature-of-concern-sub-child-text"]').val('');

      // add class unselected and remove class selected to outside siblings
      $('input[name="nature-of-concern-sub-child[]"]').not(this).parents('li').siblings().children().children().removeClass('selected').addClass('unselected');
      
      // add class selected and remove class unselected to self and siblings
      $(this).removeClass('unselected').addClass('selected');
      $(this).parents('li').siblings().children().children().removeClass('unselected').addClass('selected');

      // set checked to false for unselected input checkbox
      $('input.unselected').prop('checked', false);
    });

    // When textarea is filled
    $('textarea[name="nature-of-concern-sub-child-text"]').on('keydown', function () {
      $('input[name="nature-of-concern-sub-child[]"]').prop('checked', false);
      $(this).prev('label').children().prop('checked', true);
    });

    // Checkbox - Location
    $("ul.maint-checkbox input[type=checkbox]").on("change", function() {
      var checkboxValue = $(this).prop("checked");

      //call the recursive function for the first time
      decideParentsValue($(this));

      //Compulsorily apply check value Down in DOM
      $(this).closest("li").find(".maint-checkbox-sub label input[type=checkbox]").prop("checked", checkboxValue);
    });

    //the recursive function 
    function decideParentsValue(me) {
      var shouldTraverseUp = false;
      var checkedCount = 0;
      var myValue = me.prop("checked");

      //inspect my siblings to decide parents value
      $.each($(me).closest(".maint-checkbox-sub").children('li').children('label'), function() {
        var checkbox = $(this).children("input[type=checkbox]");
        if ($(checkbox).prop("checked")) {
          checkedCount = checkedCount + 1;
        }
      });

      //if I am checked and my siblings are also checked do nothing
      //OR
      //if I am unchecked and my any sibling is checked do nothing
      if ((myValue == true && checkedCount == 1) || (myValue == false && checkedCount == 0)) {
        shouldTraverseUp = true;
      }
      if (shouldTraverseUp == true) {
        var inputCheckBox = $(me).closest(".maint-checkbox-sub").siblings("label").children("input[type=checkbox]");
        inputCheckBox.prop("checked", me.prop("checked"));
        decideParentsValue(inputCheckBox);
      }
    }

  });
</script>
@endpush