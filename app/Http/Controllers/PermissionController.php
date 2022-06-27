<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Session;
use Validator;

class PermissionController extends Controller
{
    // api
    public function all () {
    	$perms = Permission::select('id', 'name')->with('roles')->get();
    	return response()->json($perms, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:permissions,name',
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

    	$perm = new Permission;
      $perm->name = $req->name;
    	// $perm->guard_name = 'api';
    	$perm->guard_name = 'web'; // temporary for webportal 8.0
    	$perm->save();

      $perm = Permission::select('id', 'name')
              ->with('roles')
              ->where('id', $perm->id)
              ->first();

    	return response()->json($perm, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'name' => 'required|unique:permissions,name,'.$req->id,
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

      $perm = Permission::find($req->id);
      $perm->name = $req->name;
      // $perm->guard_name = 'api';
      $perm->guard_name = 'web'; // temporary for webportal 8.0
      $perm->update();

      $perm = Permission::select('id', 'name')
              ->with('roles')
              ->where('id', $perm->id)
              ->first();

      return response()->json($perm, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $perm = Permission::whereIn('id', $ids)->select('id', 'name')->get();
      $response = $perm;
      Permission::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
