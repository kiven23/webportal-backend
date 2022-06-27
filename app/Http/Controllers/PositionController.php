<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Position;
use App\UserEmployment as UEmployment;

use Session;
use Validator;

class PositionController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'position_clearance']);

        // for active routing state
        \View::share('is_position_route', true);
    }

    public function index () {
      	$positions = Position::orderBy('name', 'asc')->get();
      	return view('positions.index', compact('positions'));
    }

    public function create () {
    	 return view('positions.create');
    }

    public function store (Request $req) {
      	$validator = Validator::make($req->all(), [
      			'name' => 'required|min:3|unique:positions,name',
      		]);

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

      	$position = new Position;
      	$position->name = $req->name;
      	$position->save();

      	$flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => $req->name . ' position has been successfully added into our database.',
        ];
      	Session::flash('create_success', $flash_message);

        if ($req->savebtn == 1) {
            return redirect()->route('position.create');
        } else {
            return redirect()->route('positions.index');
        }
    }

    public function edit ($id) {
      	$position = Position::find($id);
      	return view('positions.edit', compact('position'));
    }

    public function update ($id, Request $req) {
      	$validator = Validator::make($req->all(), [
    			'name' => 'required|min:3|unique:positions,name,' . $id,
    		]);

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

      	$position = Position::find($id);
      	$position->name = $req->name;
      	$position->update();

      	$flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Position with ID# ' . $req->id . ' has been successfully added into our database.',
        ];
      	Session::flash('update_success', $flash_message);

      	return redirect()->route('positions.index');
    }

    public function trash ($id) {
        $position = Position::find($id);
        return view('positions.trash', compact('position'));
    }

    public function delete ($id) {
        $usersemployment = UEmployment::where('position_id', $id)->get();
        foreach ($usersemployment as $useremployment) {
            $useremployment->position_id = null;
            $useremployment->update();
        }

        $position = Position::find($id);
        $position_name = $position->name;
        $position->delete();
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Position ' . $position_name . ' has been successfully added into our database.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('positions.index');
    }















    // api
    public function all () {
			$positions = Position::select('id', 'name')
									 ->with(['employees' => function ($qry) {
										 $qry->select('position_id');
									 }])
									 ->get();
	  	return response()->json($positions, 200);
	  }

	  public function store_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:positions,name',
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

	  	$position = new Position;
	    $position->name = $req->name;
	  	$position->save();

	    $position = Position::select('id', 'name')
									->where('id', $position->id)
									->with(['employees' => function ($qry) {
										$qry->select('position_id');
									}])
		              ->first();

	  	return response()->json($position, 200);
	  }

	  public function update_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:positions,name,'.$req->id,
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

			$position = Position::select('id', 'name')
								->with(['employees' => function ($qry) {
									$qry->select('position_id');
								}])
	              ->where('id', $req->id)->first();
	    $position->name = $req->name;
	    $position->update();

	    return response()->json($position, 200);
	  }

	  public function delete_multiple (Request $req) {
	    $ids = $req;
			$position = Position::whereIn('id', $ids)
									->select('id', 'name')
									->with(['employees' => function ($qry) {
										$qry->select('position_id');
									}])
									->get();
	    $response = $position;
	    Position::whereIn('id', $ids)->delete();

	    return response()->json($response, 200);
	  }
}
