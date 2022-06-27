<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

use App\File;
use App\AccessChartUserMap AS AccessUser;

use DB;

trait FileTrait {

	public function is_po_file_approver ($file_id) {
    $arr = [];

    $check_file = File::where('id', $file_id)->where('status', 0)->first();
    $waiting_for = $check_file ? $check_file->waiting_for : null;
    $po_accesschart_id = $check_file ? $check_file->po_accesschart_id : null;
    $access_level = AccessUser::where('accesschart_id', $po_accesschart_id)
          ->where('user_id', \Auth::user()->id)
          ->pluck('access_level')
          ->first();

    if ($check_file) { // check file first
      if (!\Auth::user()->hasPermissionTo('Overlook Purchase Order Files') && $waiting_for !== $access_level) {
        array_push($arr, false);
      } else {
        array_push($arr, true); // if user is po file approver
      }
    } else {
      array_push($arr, false);
    }

    if ($waiting_for === $access_level) {
      array_push($arr, true); // if user is po file approver
    } else {
      array_push($arr, false);
    }

    return $arr;
	}

}

?>
