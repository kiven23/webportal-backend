<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;

use Session;
use Validator;

//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthorizationController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'user_auth_clearance']);
      // for active routing state
      \View::share('is_auth_route', true);
    }

    public function index () {
      $users = User::orderBy('first_name', 'asc')
              ->select(['users.id',
                        'users.branch_id',
                        'users.first_name',
                        'users.last_name',
                        'users.email'])
              ->with(['branch' => function ($qry) {
                $qry->select('id', 'name');
              }])
              ->with(['employment' => function ($qry) {
                $qry->select('user_id', 'position_id', 'department_id')
                    ->with(['position' => function ($pos) {
                      $pos->select('id', 'name');
                    }])
                    ->with(['department' => function ($dept) {
                      $dept->select('id', 'name');
                    }]);
              }])
              ->where('id', '!=', 1)
              ->get();
      return view('users.authorizations.index', compact('users'));
    }

    public function edit($id) {
      abort_unless($id != 1, '403');

      $user = User::findOrFail($id);
      $roles = Role::get();
      return view('users.authorizations.edit', compact('user', 'roles'));
    }

    public function update (Request $request, $id) {
      abort_unless($id != 1, '403');

      $user = User::findOrFail($id);
      $roles = $request['roles'];
      if (isset($roles)) {
        $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
      }
      else {
        $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
      }

      $flash_message = [
        'title' => 'Well done!',
        'status' => 'success',
        'message' => 'User authorization has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);

      return redirect()->route('authorizations.index');
    }

    public function assign () {
      $roles = Role::select('id', 'name')->where('id', '!=', 1)->get(); // exclude super admin in bulk assign
      $companies = Company::select('id', 'name')->get();
      return view('users.authorizations.assign', compact('roles', 'companies'));
    }

    public function assign_proceed (Request $req) {
      $validator = Validator::make($req->all(), [
        'roles' => 'required',
        'companies' => 'required',
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('create_fail', $flash_message);
        return redirect()->back()->withInput()->withErrors($validator);
      }

      $user_ids = [];

      $roles = $req['roles'];
      $companies = $req['companies'];
      $users = User::whereIn('company_id', $companies)->get();
      if (isset($roles)) {
        foreach ($users as $user) {
          array_push($user_ids, $user);
          $user->assignRole($roles);
          // $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
        }
      }

      $flash_message = [
        'title' => 'Well Done!!!',
        'status' => 'success',
        'message' => 'Roles assigned successfully.',
      ];
      Session::flash('create_success', $flash_message);
      if ($req->savebtn == 0) {
        return redirect()->route('authorization.assign');
      } else { return redirect()->route('authorizations.index'); }
    }
}
