<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

class DateController extends Controller
{
    public function get_date () {
      $dateNow = Carbon::now();
      return response()->json($dateNow, 200);
    }
}
