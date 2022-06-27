<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ConcernCategory;

use Session;
use Validator;

class ConcernCategoryController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'concern_category_clearance']);

      // for active routing state
      \View::share('is_concern_category_route', true);
    }

    public function index () {
      $concern_categories = ConcernCategory::select('id', 'name')->get();
      return view('concerns.categories.index', compact('concern_categories'));
    }

    public function create () {
      return view('concerns.categories.create');
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:concern_categories,name',
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

      $concern_category = new ConcernCategory;
      $concern_category->name = $req->name;
      $concern_category->save();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern category "' . $concern_category->name . '" has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);

      if ($req->savebtn == 0) { return redirect()->route('concern.category.create');
      } else { return redirect()->route('concerns.categories.index'); }
    }

    public function edit ($id) {
      $concern_category = ConcernCategory::where('id', $id)->select('id', 'name')->first();
      return view('concerns.categories.edit', compact('concern_category'));
    }

    public function update (Request $req, $id) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:concern_categories,name,'.$id,
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

      $concern_category = ConcernCategory::find($id);
      $concern_category->name = $req->name;
      $concern_category->update();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern category has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('concerns.categories.index');
    }

    public function trash ($id) {
      $concern_category = ConcernCategory::where('id', $id)->select('id', 'name')->first();
      return view('concerns.categories.trash', compact('concern_category'));
    }

    public function delete ($id) {
      $concern_category = ConcernCategory::find($id);
      $concern_category_name = $concern_category->name;
      try {
        ConcernCategory::find($id)->delete();
      } catch (\Exception $e) {
        return redirect()->route('concerns.categories.index', ['err' => '1']);
      }

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern category "' . $concern_category_name . '" has been successfully delete from our records.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->route('concerns.categories.index');
    }
}
