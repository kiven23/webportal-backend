<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Branch;
use App\Position;
use App\Department;
use App\AccessChart;
use App\AccessLevel;
use App\AccesschartUserMap as AccessUser;

use Session;
use Validator;
use App\Traits\AccessChartCategorizer;

class AccessChartController extends Controller
{

    use AccessChartCategorizer;

    public function __construct () {
        $this->middleware(['auth', 'access_chart_clearance']);

        // for active routing state
        \View::share('is_access_chart_route', true);
    }

    public function index () {
    	$access_charts = AccessChart::orderBy('name', 'asc')->get();
    	return view('access_charts.index', compact('access_charts'));
    }

    public function create () {
    	return view('access_charts.create');
    }

    public function store (Request $req) {
    	$rules = [
    		'name' => 'required|min:3|unique:access_charts,name',
    		'access_for' => 'required',
    	];

    	$messages = [
    		'name.required' => 'Please set a name for the access chart',
    		'name.min' => 'Access Chart name should be at least 3 characters long',
    		'access_for.required' => 'Please select access for.',
    	];

    	$validator = Validator::make($req->all(), $rules, $messages);

    	if ($validator->fails()) {
    		$flash_message = [
            'title' => 'Oops!',
            'status' => 'danger',
            'message' => 'Please correct all the errors below.',
        ];
    		Session::flash('create_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$accesschart = new AccessChart;
    	$accesschart->name = $req->name;
    	$accesschart->access_for = $req->access_for;
    	$accesschart->save();

    	$flash_message = [
          'title' => 'Well done!',
          'status' => 'success',
          'message' => $req->name . ' access chart has been successfully added into our database.',
      ];
    	Session::flash('create_success', $flash_message);

        if ($req->savebtn == 1) {
            return redirect()->route('access_chart.create');
        } else {
            return redirect()->route('access_charts.index');
        }
    }

    public function edit ($id) {
    	$access_chart = AccessChart::find($id);
    	return view('access_charts.edit', compact('access_chart'));
    }

    public function update ($id, Request $req) {
    	$rules = [
    		'name' => 'required|min:3|unique:access_charts,name,' . $id,
    		'access_for' => 'required',
    	];

    	$messages = [
    		'name.required' => 'Please set a name for the access chart',
    		'name.min' => 'Access Chart name should be at least 3 characters long',
    		'access_for.required' => 'Please select access for.',
    	];

    	$validator = Validator::make($req->all(), $rules, $messages);

    	if ($validator->fails()) {
    		$flash_message = [
            'title' => 'Oops!',
            'status' => 'danger',
            'message' => 'Please correct all the errors below.',
        ];
    		Session::flash('update_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$access_chart = AccessChart::find($id);
    	$access_chart->name = $req->name;
    	$access_chart->access_for = $req->access_for;
    	$access_chart->update();

    	$flash_message = [
                'title' => 'Well done!',
                'status' => 'success',
                'message' => 'Access chart with ID# ' . $id . ' has been successfully added into our database.',
            ];
    	Session::flash('update_success', $flash_message);
    	return redirect()->route('access_charts.index');
    }

    public function officers ($id) {
        $branches = Branch::orderBy('name', 'asc')->get();
        $departments = Department::orderBy('name', 'asc')->get();
        $positions = Position::orderBy('name', 'asc')->get();

        $access_chart = AccessChart::where('id', $id)->with('accessusersmap')->first();
        $users = User::where('email', '!=', 'alexela8882@gmail.com')->orderBy('first_name', 'asc')->get();
        $access_level = AccessLevel::first();
        $max_level = $this->max_level($id);

        if ($max_level) {
            if ($max_level < $access_level->level) {
                $levels = $max_level + 1;
            } else {
                $levels = $max_level;
            }
        } else {
            $levels = 1;
        }

        $access_user = AccessChart::where('id', $id)->with(['accessusersmap' => function ($qry) {
            $qry->orderBy('access_level', 'asc')->get();
        }])->first();
        return view('access_charts.access_chart_user_maps.index',
                compact('branches',
                        'departments',
                        'positions',
                        'access_user',
                        'users',
                        'access_level',
                        'levels',
                        'max_level',
                        'access_chart'));
    }
}
