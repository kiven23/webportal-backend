<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ConcernType;

use Session;
use Validator;

class ConcernTypeController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'concern_type_clearance']);

      // for active routing state
      \View::share('is_concern_type_route', true);
    }

    public function index () {
      $concern_types = ConcernType::select('id', 'name')->get();
      return view('concerns.types.index', compact('concern_types'));
    }

    public function create () {
      return view('concerns.types.create');
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:concern_types,name',
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

      $concern_type = new ConcernType;
      $concern_type->name = $req->name;
      $concern_type->save();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern type "' . $concern_type->name . '" has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);

      if ($req->savebtn == 0) { return redirect()->route('concern.type.create');
      } else { return redirect()->route('concerns.types.index'); }
    }

    public function edit ($id) {
      $concern_type = ConcernType::where('id', $id)->select('id', 'name')->first();
      return view('concerns.types.edit', compact('concern_type'));
    }

    public function update (Request $req, $id) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:concern_types,name,'.$id,
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

      $concern_type = ConcernType::find($id);
      $concern_type->name = $req->name;
      $concern_type->update();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern type has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('concerns.types.index');
    }

    public function trash ($id) {
      $concern_type = ConcernType::where('id', $id)->select('id', 'name')->first();
      return view('concerns.types.trash', compact('concern_type'));
    }

    public function delete ($id) {
      $concern_type = ConcernType::find($id);
      $concern_type_name = $concern_type->name;
      try {
        ConcernType::find($id)->delete();
      } catch (\Exception $e) {
        return redirect()->route('concerns.types.index', ['err' => '1']);
      }

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Concern type "' . $concern_type_name . '" successfully deleted from our records.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->route('concerns.types.index');
    }
}
