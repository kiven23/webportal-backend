<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\AccessLevel;
use App\AccessChart;

use Session;
use Validator;

class AccessLevelController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'access_chart_clearance']);
    }

    public function edit ($accesschart_id) {
    	$access_chart = AccessChart::find($accesschart_id);
    	$access_level = AccessLevel::first();
    	return view('access_charts.access_levels.edit', compact('access_level', 'access_chart'));
    }

    public function update ($id, Request $req) {
    	$rules = [
    		  'level' => 'required|min:3|numeric',
    	];
    	$messages = [
      		'level.required' => 'You must set a level',
      		'level.min' => 'Minimum level is 3',
      		'level.numeric' => 'Level must be an interger',
    	];
    	$validator = Validator::make($req->all(), $rules, $messages);

    	if ($validator->fails()) {
    		$flash_message = 'Please correct the errors below.';
    		Session::flash('update_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$access_level = AccessLevel::find($id);
    	$access_level->level = $req->level;
    	$access_level->update();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Access Level updated to ' . $req->level . '.',
      ];
      Session::flash('update_success', $flash_message);
    	return redirect()->route('access_chart.officers', ['id' => $req->accesschart_id]);
    }
}
