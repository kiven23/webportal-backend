<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Division;
use App\Department;

use Session;
use Validator;

class DivisionController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'division_clearance']);

      // for active routing state
      \View::share('is_division_route', true);
    }

    public function index () {
      $divisions = Division::with('departments')->get();
      return view('divisions.index', compact('divisions'));
    }

    public function create () {
      $departments = Department::all();
      return view('divisions.create', compact('departments'));
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:divisions,name',
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

      $division = new Division;
      $division->name = $req->name;
      $division->save();

      if ($req->departments) {
        $update_depts = Department::whereIn('id', $req->departments)->update(['division_id' => $division->id]);
      }

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => $division->name . ' division has been successfully added in our records.',
      ];
      Session::flash('create_success', $flash_message);
      if ($req->savebtn == 1) {
        return redirect()->route('division.create');
      } else {
        return redirect()->route('divisions.index');
      }
    }

    public function edit ($id, Request $req) {
      $division = Division::where('id', $id)
                  ->with(['departments' => function ($qry) {
                    $qry->select('id', 'division_id', \DB::raw('CONCAT(name) AS text'));
                  }])
                  ->first();
      $departments = Department::orderBy('name', 'asc')->get();

      return view('divisions.edit', compact('division', 'departments'));
    }

    public function update ($id, Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:divisions,name,'.$id,
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

      $division = Division::find($id);
      $division->name = $req->name;
      $division->update();

      if ($req->departments) {
        $update_depts = Department::whereIn('id', $req->departments)->update(['division_id' => $division->id]);
      }

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'New Division has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('divisions.index');
    }

    public function trash ($id) {
      $division = Division::select('id', 'name')
                  ->with(['departments' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['usersemployment' => function ($qry) {
                    $qry->select('id');
                  }])
                  ->where('id', $id)
                  ->first();
      return view('divisions.trash', compact('division'));
    }

    public function delete ($id) {
      $division = Division::find($id);
      $division_name = $division->name; // get name for flash message
      $deptIds = $division->departments->pluck('id'); // get ids for bulk update

      // update departments under this division
      Department::whereIn('id', $deptIds)->update(['division_id' => 0]);

      // delete division
      $division->delete();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => $division_name . ' division has been successfully removed from our records.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->route('divisions.index');
    }
















    // api
    public function all () {
      $divisions = Division::select('id', 'name')
                   ->with(['departments' => function ($qry) {
                     $qry->select('id', 'division_id', 'name');
                   }])
                   ->get();
      return response()->json($divisions, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:divisions,name',
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

      $division = new Division;
      $division->name = $req->name;
      $division->save();

      if ($req->departments) {
        // supply department(s) with division id
        Department::whereIn('id', $req->departments)->update(['division_id' => $division->id]);
      }

      $division = Division::select('id', 'name')
                  ->where('id', $division->id)
                  ->with(['departments' => function ($qry) {
                    $qry->select('id', 'division_id', 'name');
                  }])
                  ->first();

      return response()->json($division, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:divisions,name,'.$req->id,
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

      $division = Division::find($req->id);
      $division->name = $req->name;
      $division->update();

      // set division_id to zero before update
      Department::where('division_id', $division->id)->update(['division_id' => 0]);

      if ($req->departments) {
        // supply department(s) with division id
        Department::whereIn('id', $req->departments)->update(['division_id' => $division->id]);
      }

      $division = Division::select('id', 'name')
                ->with(['departments' => function ($qry) {
                  $qry->select('id', 'division_id', 'name');
                }])
                ->where('id', $division->id)->first();

      return response()->json($division, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $division = Division::whereIn('id', $ids)->select('id', 'name')->get();
      $response = $division;
      Division::whereIn('id', $ids)->delete();

      // set department(s) division_id to zero
      Department::whereIn('division_id', $ids)->update(['division_id' => 0]);

      return response()->json($response, 200);
    }
}
