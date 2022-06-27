<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\FileType;

use Session;
use Validator;

class FileTypeController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'file_type_clearance']);

      // for active routing state
      \View::share('is_file_type_route', true);
    }

    public function index () {
      $file_types = FileType::select('id', 'name')->get();
      return view('files.types.index', compact('file_types'));
    }

    public function create () {
      return view('files.types.create');
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:file_types,name',
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

      $file_type = new FileType;
      $file_type->name = $req->name;
      $file_type->save();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => $file_type->name . ' file type has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);
      
      if ($req->savebtn == 0) {
        return redirect()->route('file.type.create');
      } else {
        return redirect()->route('file.type.index');
      }
    }

    public function edit ($id, Request $req) {
      $file_type = FileType::select('id', 'name')->where('id', $id)->first();
      return view('files.types.edit', compact('file_type'));
    }

    public function update ($id, Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:file_types,name,'.$id,
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('update_fail', $flash_message);
        return redirect()->back()->withInput()->withErrors($validator);
      }

      $file_type = FileType::find($id);
      $file_type->name = $req->name;
      $file_type->update();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'One (1) record has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('file.type.index');
    }

    public function trash($id) {
      $file_type = FileType::select('id', 'name')
                   ->with(['files' => function ($qry) {
                    $qry->select('type_id', 'file');
                   }])
                   ->where('id', $id)
                   ->first();
      return view('files.types.trash', compact('file_type'));
    }

    public function delete ($id) {
      $file_type = FileType::find($id);
      $name = $file_type->name;

      try {
        $file_type->delete();
      } catch (\Exception $e) {
        return redirect()->route('file.type.index', ['err' => '1']);
      }

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => $name . ' file type has been successfully delete from our records.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->route('file.type.index');
    }
}
