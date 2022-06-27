<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

use App\File;
use App\MaintRequest;
use App\AccessChartUserMap AS AccessUser;

use DB;

trait MaintRequestTrait {

	public function is_mrf_approver ($mrf_id) {
    $auth_id = \Auth::user()->id;
    $maint_request = MaintRequest::find($mrf_id);
    $status = $maint_request->status;
    $is_request_owner = $maint_request->user_id === $auth_id ? true : false;

    $waiting_for = $maint_request->waiting_for;
    $auth_access_level = AccessUser::where('user_id', $auth_id)
                        ->where('accesschart_id', $maint_request->accesschart_id)
                        ->pluck('access_level')
                        ->first();
    if ($auth_access_level && $waiting_for !== $auth_access_level) {
      return false;
    } else {
      // abort when status is already completed or cancelled before proceeding
      if (!$is_request_owner &&
          ($status == 3 ||
            $status == 4)) {
        return false;
      } else { return true; }
    }
  }
  
  public function can_cancel_approve_delete ($mrf_id, $method) {
    $auth_id = \Auth::user()->id;
    $maint_request = MaintRequest::where('id', $mrf_id)->first();
    $status = $maint_request->status;

    $waiting_for = $maint_request->waiting_for;
    $access_level = AccessUser::where('accesschart_id', $maint_request->accesschart_id)
            ->where('user_id', $auth_id)
            ->pluck('access_level')
            ->first();
    if (!\Auth::user()->hasPermissionTo('Overlook Maintenance Requests')) {
      if ($access_level && $waiting_for !== $access_level) {
        return false;
      } else { return true; }
    } else {
      if ($method == $status && $status == 2) { // for cancel method
        // abort when status is already approved or completed before proceeding
        if ($status == 3 || $status == 4) {
          return 'first';
        } else { return 'second'; }
      } elseif ($method == $status && $status == 3) { // for approved
        // abort when status is already cancelled or completed before proceeding
        if ($status == 2 || $status == 4) {
          return 'third';
        } else { return 'fourth'; }
      } elseif ($method == $status && $status == 4) { // for completed
        // abort when status is already cancelled or completed before proceeding
        if ($status == 2 || $status == 3) {
          return 'fifth';
        } else { return 'sixth'; }
      } else { return 'seventh'; }
    }
  }

  public function is_escalatable ($mrf_id) {
    $auth_id = \Auth::user()->id;
    $maint_request = MaintRequest::find($mrf_id);
    $waiting_for = $maint_request->waiting_for;
    $status = $maint_request->status;

    $is_one_of_approvers = AccessUser::where('user_id', $auth_id)
                        ->where('accesschart_id', $maint_request->accesschart_id)
                        ->first();
    $auth_access_level = $is_one_of_approvers ? $is_one_of_approvers->access_level : null;
    $max_level = AccessUser::where('accesschart_id', $maint_request->accesschart_id)->max('access_level');
    if (!$is_one_of_approvers ||
        ($auth_access_level && $waiting_for !== $auth_access_level) ||
        $max_level == $maint_request->waiting_for) {
      return false;
    } else {
      // abort when status is already completed or cancelled before proceeding
      if ($status == 2 || $status == 4) {
        return false;
      } else { return true; }
    }
  }

}

?>
