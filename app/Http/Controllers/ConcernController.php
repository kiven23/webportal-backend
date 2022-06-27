<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ConcernCategory;
use App\ConcernType;
use App\Concern;
use App\Branch;
use App\User;

use Session;
use Validator;

class ConcernController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'concern_clearance']);

      // for active routing state
      \View::share('is_concern_route', true);
    }

    public function index () {
      $concerns = Concern::select('id',
                                  'branch_id',
                                  'concern_type_id',
                                  'concern_category_id',
                                  'reported_by',
                                  'database',
                                  'cause',
                                  'date_solved',
                                  'remarks',
                                  'resolution',
                                  'status')
                  ->with(['branch' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['category' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->get();
      return view('concerns.index', compact('concerns'));
    }

    public function create () {
      $branches = Branch::select('id', 'name')->get();
      $types = ConcernType::select('id', 'name')->get();
      $categories = ConcernCategory::select('id', 'name')->get();
      $users = User::select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'))->get();
      return view('concerns.create', compact('branches', 'types', 'categories', 'users'));
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
        'user' => 'required',
        'remarks' => 'required',
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('create_fail', $flash_message);
        return redirect()->back()->withErrors($validator)->withInput();
      }

      if (!is_numeric($req->type)) {
        $concern_type = new ConcernType;
        $concern_type->name = $req->type;
        $concern_type->save();
        $type = $concern_type->id;
      } else { $type = $req->type; }

      if (!is_numeric($req->category)) {
        $concern_category = new ConcernCategory;
        $concern_category->name = $req->category;
        $concern_category->save();
        $category = $concern_category->id;
      } else { $category = $req->category; }

      $concern = new Concern;
      $concern->branch_id = $req->branch;
      $concern->reported_by = $req->user;
      $concern->concern_type_id = $type;
      $concern->concern_category_id = $category;
      $concern->database = $req->database;
      $concern->cause = $req->cause;
      $concern->remarks = $req->remarks;
      $concern->save();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'New concern has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);

      if ($req->savebtn == 0) {
        return redirect()->route('concern.create');
      } else { return redirect()->route('concerns.index'); }
    }

    public function edit ($id) {
      $branches = Branch::select('id', 'name')->get();
      $types = ConcernType::select('id', 'name')->get();
      $categories = ConcernCategory::select('id', 'name')->get();
      $users = User::select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'))->get();

      $concern = Concern::select('id',
                                 'branch_id',
                                 'concern_type_id',
                                 'concern_category_id',
                                 'reported_by',
                                 'database',
                                 'cause',
                                 'remarks',
                                 'resolution',
                                 'date_solved',
                                 'status')
                  ->where('id', $id)
                  ->first();
      return view('concerns.edit', compact('concern', 'branches', 'types', 'categories', 'users'));
    }

    public function update (Request $req, $id) {
      $validator = Validator::make($req->all(), [
        'user' => 'required',
        'remarks' => 'required',
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('update_fail', $flash_message);
        return redirect()->back()->withErrors($validator)->withInput();
      }

      if (!is_numeric($req->type)) {
        $concern_type = new ConcernType;
        $concern_type->name = $req->type;
        $concern_type->save();
        $type = $concern_type->id;
      } else { $type = $req->type; }

      if (!is_numeric($req->category)) {
        $concern_category = new ConcernCategory;
        $concern_category->name = $req->category;
        $concern_category->save();
        $category = $concern_category->id;
      } else { $category = $req->category; }

      $concern = Concern::find($id);
      $concern->branch_id = $req->branch;
      $concern->reported_by = $req->user;
      $concern->concern_type_id = $type;
      $concern->concern_category_id = $category;
      $concern->database = $req->database;
      $concern->cause = $req->cause;
      $concern->remarks = $req->remarks;
      $concern->resolution = $req->resolution;
      $concern->date_solved = $req->date_solved;
      if ($req->date_solved) {
        $concern->status = 1;
      } else { $concern->status = 0; }
      $concern->update();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('concerns.index');
    }

    public function trash ($id) {
      $concern = Concern::select('id',
                                  'branch_id',
                                  'concern_type_id',
                                  'concern_category_id',
                                  'reported_by',
                                  'database',
                                  'cause',
                                  'date_solved',
                                  'remarks',
                                  'resolution',
                                  'status')
                  ->with(['branch' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->with(['category' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                  ->where('id', $id)
                  ->first();
      
      return view('concerns.trash', compact('concern'));
    }

    public function delete ($id) {
      $concern = Concern::find($id);
      $concern->delete();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern has been successfully deleted from our records.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->route('concerns.index');
    }
}