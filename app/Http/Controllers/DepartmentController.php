<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Division;
use App\Department;
use App\UserEmployment as UEmployment;

use Session;
use Validator;

class DepartmentController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'department_clearance']);

        // for active routing state
        \View::share('is_department_route', true);
    }

    public function index () {
      $departments = Department::orderBy('division_id', 'asc')
                     ->with(['division' => function ($qry) {
                       $qry->select('id', 'name');
                     }])
                     ->get();
    	return view('departments.index', compact('departments'));
    }

    public function create () {
      $divisions = Division::orderBy('name', 'asc')->get();
    	return view('departments.create', compact('divisions'));
    }

    public function store (Request $req) {
    	$validator = Validator::make($req->all(), [
    			'name' => 'required|min:3|unique:departments,name',
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

    	$department = new Department;
    	$department->division_id = $req->division;
    	$department->name = $req->name;
    	$department->save();

    	$flash_message = [
                'title' => 'Well done!',
                'status' => 'success',
                'message' => $req->name . ' department has been successfully added into our database.',
            ];
    	Session::flash('create_success', $flash_message);

        if ($req->savebtn == 1) {
            return redirect()->route('department.create');
        }

    	return redirect()->route('departments.index');
    }

    public function edit ($id) {
    	$department = Department::find($id);
    	$divisions = Division::orderBy('name', 'asc')->get();
    	return view('departments.edit', compact('department', 'divisions'));
    }

    public function update ($id, Request $req) {
        $validator = Validator::make($req->all(), [
            'name' => 'required|min:3|unique:departments,name,' . $id,
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

        $department = Department::find($id);
        $department->name = $req->name;
        $department->division_id = $req->division;
        $department->update();

        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Department with ID# ' . $req->id . ' has been successfully updated.',
        ];
        Session::flash('update_success', $flash_message);

        return redirect()->route('departments.index');
    }

    public function trash ($id) {
        $department = Department::find($id);
        return view('departments.trash', compact('department'));
    }

    public function delete ($id) {
        $usersemployment = UEmployment::where('department_id', $id)->get();
        foreach ($usersemployment as $useremployment) {
            $useremployment->department_id = null;
            $useremployment->update();
        }

        $department = Department::find($id);
        $department_name = $department->name;
        $department->delete();

        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => $department_name . ' department has been successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('departments.index');
    }













    // api
    public function all () {
			$departments = Department::select('id', 'name')
										 ->with(['employees' => function ($qry) {
											 $qry->select('id', 'department_id');
										 }])
										 ->orderBy('name', 'asc')->get();
	  	return response()->json($departments, 200);
	  }

	  public function store_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:departments,name',
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

	  	$department = new Department;
	    $department->name = $req->name;
	  	$department->save();

			$department = Department::select('id', 'name')
									->with(['employees' => function ($qry) {
										$qry->select('id', 'department_id');
									}])
		    					->where('id', $department->id)
		              ->first();

	  	return response()->json($department, 200);
	  }

	  public function update_api (Request $req) {
	    $validator = Validator::make($req->all(), [
	      'name' => 'required|unique:departments,name,'.$req->id,
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

	    $department = Department::find($req->id);
	    $department->name = $req->name;
			$department->update();
			
			$department = Department::select('id', 'name')
										->with(['employees' => function ($qry) {
											$qry->select('id', 'department_id');
										}])
										->where('id', $department->id)
										->first();

	    return response()->json($department, 200);
	  }

	  public function delete_multiple (Request $req) {
	    $ids = $req;
	    $department = Department::whereIn('id', $ids)->select('id', 'name')->get();
	    $response = $department;
	    Department::whereIn('id', $ids)->delete();

	    return response()->json($response, 200);
	  }
}
