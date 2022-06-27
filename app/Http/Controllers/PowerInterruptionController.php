<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PowerInterruption as PowerI;
use App\Branch;
use Validator;
use Session;
use Carbon;

class PowerInterruptionController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'power_interruption_clearance']);

      // for active routing state
      \View::share('is_sc_power_interruption_route', true);
    }

    public function index () {
    	$power_interruptions = PowerI::orderBy('id', 'asc')->get();
        $total_seconds = 0;
    	return view('power_interruptions.index', compact('power_interruptions',
                                                         'total_seconds'));
    }

    public function create () {
    	$branches = Branch::orderBy('name', 'asc')->where('id', '!=', 1)->get();
    	return view('power_interruptions.create', compact('branches'));
    }

    public function store (Request $req) {
        $validator = Validator::make($req->all(), [
        		'reported_by_name' => 'required',
                'problem_reported' => 'required',
                'datetime_from' => 'required',
        		'datetime_to' => 'required',
        	]);
        // BRANCH DUPLICATE
        $check_branch = Branch::where('name', $req->branch_input)->first();
        if (count($check_branch) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('branch_duplicate', $duplicate);
            });
        }
    	// BRANCH EMPTY
        if (!$req->branch_select && $req->branch_input == '') {
            $empty = 'The Branch input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('branch_input', $empty);
            });
        }

    	if ($validator->fails()) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];

    		// BRANCH
            if ($req->branch_select) {
                Session::flash('branch_select', 1);
            } else {
                Session::flash('branch_select', 0);
            }

    		Session::flash('create_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	// BRANCH
    	if ($req->branch_input) {
            $brand = new Branch;
            $brand->name = $req->branch_input;
            $brand->save();
        }

    	$power_interruption = new PowerI;
    	$power_interruption->user_id = \Auth::user()->id;

    	// BRANCH
    	if ($req->branch_select) {
            $power_interruption->branch_id = $req->branch_select;
        } else {
            $power_interruption->branch_id = $brand->id;
        }

    	$power_interruption->reported_by_name = $req->reported_by_name;
    	$power_interruption->reported_by_position = $req->reported_by_position;
    	$power_interruption->problem_reported = $req->problem_reported;
    	$power_interruption->datetime_from = $req->datetime_from;
    	$power_interruption->datetime_to = $req->datetime_to;
    	$power_interruption->remarks = $req->remarks;
    	$power_interruption->save();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'New log has been successfully added into our database.',
		];
    	Session::flash('create_success', $flash_message);

    	if ($req->savebtn == 0) {
    		return redirect()->route('power_interruption.create');
    	} else {
    		return redirect()->route('power_interruptions');
    	}
    }

    public function edit ($id) {
    	$power_interruption = PowerI::find($id);
    	$branches = Branch::orderBy('name', 'asc')->where('id', '!=', 1)->get();
    	return view('power_interruptions.edit', compact('power_interruption', 'branches'));
    }

    public function update (Request $req, $id) {
        $validator = Validator::make($req->all(), [
        		'reported_by_name' => 'required',
        		'problem_reported' => 'required',
                'datetime_from' => 'required',
        		'datetime_to' => 'required',
        	]);
        // BRANCH DUPLICATE
        $check_branch = Branch::where('name', $req->branch_input)->first();
        if (count($check_branch) > 0) {
            $duplicate = 'Duplicate! Please choose another name.';
            $validator->after(function ($validator) use ($duplicate) {
                $validator->getMessageBag()->add('branch_duplicate', $duplicate);
            });
        }
    	// BRANCH EMPTY
        if (!$req->branch_select && $req->branch_input == '') {
            $empty = 'The Branch input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('branch_input', $empty);
            });
        }

    	if ($validator->fails()) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];

    		// BRANCH
            if ($req->branch_select) {
                Session::flash('branch_select', 1);
            } else {
                Session::flash('branch_select', 0);
            }

    		Session::flash('update_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	// BRANCH
    	if ($req->branch_input) {
            $brand = new Branch;
            $brand->name = $req->branch_input;
            $brand->save();
        }

    	$power_interruption = PowerI::find($id);
    	$power_interruption->user_id = \Auth::user()->id;

    	// BRANCH
    	if ($req->branch_select) {
            $power_interruption->branch_id = $req->branch_select;
        } else {
            $power_interruption->branch_id = $brand->id;
        }

    	$power_interruption->reported_by_name = $req->reported_by_name;
    	$power_interruption->reported_by_position = $req->reported_by_position;
    	$power_interruption->problem_reported = $req->problem_reported;
    	$power_interruption->datetime_from = $req->datetime_from;
    	$power_interruption->datetime_to = $req->datetime_to;
    	$power_interruption->remarks = $req->remarks;
    	$power_interruption->update();

    	$flash_message = [
			'title' => 'Well done!',
			'status' => 'success',
			'message' => 'Power interruption log has been successfully updated.',
		];
    	Session::flash('update_success', $flash_message);
    	return redirect()->route('power_interruptions');
    }

    public function trash ($id) {
        $power_interruption = PowerI::where('id', $id)->first();
        return view('power_interruptions.trash', compact('power_interruption'));
    }

    public function delete ($id) {
        $power_interruption = PowerI::find($id);
        $power_interruption_id = $power_interruption->id;
        $power_interruption->delete();

        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Power interruption with Log # ' . $power_interruption_id . ' has been successfully deleted from our records.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('power_interruptions');
    }















    // api
    public function all () {
      $powerinterruptions = PowerI::select('id',
                            'branch_id',
                            'user_id',
                            'datetime_from',
                            'datetime_to',
                            'reported_by_name',
                            'reported_by_position',
                            'remarks',
                            \DB::raw('TIMESTAMPDIFF(SECOND, datetime_from, datetime_to) as total_hours'))
                            ->with(['branch' => function ($qry) {
                              $qry->select('id', 'name');
                            }])
                            ->with(['user' => function ($qry) {
                              $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
                            }])
                            ->latest()
                            ->get();
    	return response()->json($powerinterruptions, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'datetime_from' => 'required',
        'datetime_to' => 'required',
        'reported_by_name' => 'required',
        'reported_by_position' => 'required',
        'remarks' => 'required',
      ]);

      if ($validator->fails()) {
        $errors = $validator->errors()->getMessages();
        $obj = $validator->failed();
        $result = [];
        foreach($obj as $input => $rules){
          $i = 0;
          foreach($rules as $rule => $ruleInfo){
            $key = $rule;
            $key = $input.'|'.strtolower($key);
            $result[$key] = $errors[$input][$i];
            $i++;
          }
        }
        return response()->json($result, 422);
      }

    	$powerinterruption = new PowerI;
      $powerinterruption->user_id = \Auth::user()->id;
      $powerinterruption->branch_id = $req->branch;
      $powerinterruption->datetime_from = $req->date . " " . $req->datetime_from;
      $powerinterruption->datetime_to = $req->date . " " . $req->datetime_to;
      $powerinterruption->problem_reported = Carbon\Carbon::now();
      $powerinterruption->reported_by_name = $req->reported_by_name;
      $powerinterruption->reported_by_position = $req->reported_by_position;
      $powerinterruption->remarks = $req->remarks;
    	$powerinterruption->save();

      $powerinterruption = PowerI::select('id',
                          'branch_id',
                          'user_id',
                          'datetime_from',
                          'datetime_to',
                          'reported_by_name',
                          'reported_by_position',
                          'remarks',
                          \DB::raw('TIMESTAMPDIFF(SECOND, datetime_from, datetime_to) as total_hours'))
                          ->with(['branch' => function ($qry) {
                            $qry->select('id', 'name');
                          }])
                          ->with(['user' => function ($qry) {
                            $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
                          }])
                          ->latest()
                          ->where('id', $powerinterruption->id)
                          ->first();

    	return response()->json($powerinterruption, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'datetime_from' => 'required',
        'datetime_to' => 'required',
        'reported_by_name' => 'required',
        'reported_by_position' => 'required',
        'remarks' => 'required',
      ]);

      if ($validator->fails()) {
        $errors = $validator->errors()->getMessages();
        $obj = $validator->failed();
        $result = [];
        foreach($obj as $input => $rules){
          $i = 0;
          foreach($rules as $rule => $ruleInfo){
            $key = $rule;
            $key = $input.'|'.strtolower($key);
            $result[$key] = $errors[$input][$i];
            $i++;
          }
        }
        return response()->json($result, 422);
      }

      $date_from = $req->date . " " . $req->datetime_from;
      $date_to = $req->date . " " . $req->datetime_to;

    	$powerinterruption = PowerI::find($req->id);
      $powerinterruption->user_id = \Auth::user()->id;
      $powerinterruption->branch_id = $req->branch;
      $powerinterruption->datetime_from = $date_from;
      $powerinterruption->datetime_to = $date_to;
      $powerinterruption->reported_by_name = $req->reported_by_name;
      $powerinterruption->reported_by_position = $req->reported_by_position;
      $powerinterruption->remarks = $req->remarks;
    	$powerinterruption->update();

      $powerinterruption = PowerI::select('id',
                          'branch_id',
                          'user_id',
                          'datetime_from',
                          'datetime_to',
                          'reported_by_name',
                          'reported_by_position',
                          'remarks',
                          \DB::raw('TIMESTAMPDIFF(SECOND, datetime_from, datetime_to) as total_hours'))
                          ->with(['branch' => function ($qry) {
                            $qry->select('id', 'name');
                          }])
                          ->with(['user' => function ($qry) {
                            $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
                          }])
                          ->latest()
                          ->where('id', $powerinterruption->id)
                          ->first();

    	return response()->json($powerinterruption, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $powerinterruption = PowerI::whereIn('id', $ids)
                          ->select('id', 'branch_id')
                          ->with(['branch' => function ($qry) {
                            $qry->select('id', 'name');
                          }])
                          ->get();
      $response = $powerinterruption;
      PowerI::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
