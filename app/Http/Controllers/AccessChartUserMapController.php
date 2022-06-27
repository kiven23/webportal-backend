<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Position;
use App\Department;
use App\AccessLevel;
use App\AccessChart;
use App\AccessChartUserMap as AccessUser;
use App\UserEmployment as UEmployment;

use Session;
use Validator;
use App\Traits\AccessChartCategorizer;

class AccesschartUserMapController extends Controller
{

    use AccessChartCategorizer;

    public function __construct () {
        $this->middleware(['auth', 'access_chart_clearance']);
    }

    public function store ($accesschart_id, Request $req) {
        // -----
        // User
        // -----
        if ($req->user_new_fname) {
            $validator = Validator::make($req->all(), [
                'user_new_fname' => 'required',
                'user_new_lname' => 'required',
                'user_new_email' => 'required|unique:users,email',
                'user_new_password' => 'required',
            ]);

            $set_user = 1;
            Session::flash('set_user', $set_user);
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

            // Store new user
            $user = new User;
            $user->branch_id = $req->branch;
            $user->first_name = $req->user_new_fname;
            $user->last_name = $req->user_new_lname;
            $user->email = $req->user_new_email;
            $user->password = bcrypt($req->user_new_password);
            $user->save();

            // Store user's employment
            $useremployment = new UEmployment;
            $useremployment->branch_id = $user->branch_id;
            $useremployment->user_id = $user->id;
            $useremployment->save();
            $accessuser_user_id = $user->id;
        } else {
            $validator = Validator::make($req->all(), [
                'user' => 'required',
                'level' => 'required',
            ]);

            if ($validator->fails()) {
                $flash_message = [
                    'title' => 'Oops!',
                    'status' => 'danger',
                    'message' => 'Please correct the errors below.',
                ];
                Session::flash('create_fail', $flash_message);
                return redirect()->back()
                                 ->withErrors($validator)
                                 ->withInput();
            }
            $accessuser_user_id = $req->user;
        }
        // -----------
        // END :: User
        // -----------

        // ---------------------------------------------------
        // Check if user already exist in the current chart
        // ---------------------------------------------------
        $check_user = AccessUser::where('accesschart_id', $accesschart_id)->where('user_id', $req->user)->first();

        if ($check_user) {
            $flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'User already exist in the chart.',
            ];
            Session::flash('create_fail', $flash_message);
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        // ------------------------------------------------------------
        // End of checking if user already exist in the specific chart
        // ------------------------------------------------------------

        if ($req->assignbtn == 2) {
            $accessusers = AccessUser::where('accesschart_id', $accesschart_id)
                            ->where('access_level', '>=', $req->level)->get();
            foreach ($accessusers as $accessuser) {
                $accessuser->access_level = $accessuser->access_level + 1;
                $accessuser->update();
            }
        }

      	$accessuser = new AccessUser;
      	$accessuser->accesschart_id = $accesschart_id;
      	$accessuser->user_id = $accessuser_user_id;
      	$accessuser->access_level = $req->level;
      	$accessuser->save();

      	$flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'You have successfully assigned an officer.',
        ];
      	Session::flash('create_success', $flash_message);
      	return redirect()->route('access_chart.officers', ['id' => $accesschart_id]);
    }

    public function store_bdp ($accesschart_id, Request $req) {
        $user = \DB::table('users AS u')
                ->select('u.id',
                        'u.branch_id',
                        \DB::raw('CONCAT(first_name," ",last_name) AS full_name'))
                ->join('user_employments AS ue', 'ue.user_id', '=', 'u.id')
                ->where('u.branch_id', $req->branch)
                ->where('ue.department_id', $req->department)
                ->where('ue.position_id', $req->position)
                ->first();
        
        if (!$user) {
            $flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'User not found.',
            ];
            Session::flash('create_fail', $flash_message);
            return redirect()->back();
        }
        
        if ($req->assignbtn == 2) {
            $accessusers = AccessUser::where('accesschart_id', $accesschart_id)
                            ->where('access_level', '>=', $req->level)->get();
            foreach ($accessusers as $accessuser) {
                $accessuser->access_level = $accessuser->access_level + 1;
                $accessuser->update();
            }
        }

        $accessuser = new AccessUser;
        $accessuser->accesschart_id = $accesschart_id;
        $accessuser->user_id = $user->id;
        $accessuser->access_level = $req->level;
        $accessuser->save();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'You have successfully assigned an officer.',
        ];
        Session::flash('create_success', $flash_message);
        return redirect()->route('access_chart.officers', ['id' => $accesschart_id]);
    }

    public function edit ($id) {
        $users = User::orderBy('first_name', 'asc')->where('email', '!=', 'alexela8882@gmail.com')->get();
        $access_user = AccessUser::where('id', $id)->with('accesschart')->first();
        $access_level = AccessLevel::first();
        $max_level = $this->max_level($access_user->accesschart_id);

        if ($max_level) {
            if ($max_level < $access_level->level) {
                if ($max_level === 1) {
                    $levels = $max_level;
                } else {
                    $levels = 2; // fixed @ 14:55 of 09/30/18
                }
            } else {
                $levels = $max_level;
            }
        } else {
            $levels = 1;
        }

        return view('access_charts.access_chart_user_maps.edit',
                compact('access_user',
                        'users',
                        'access_level',
                        'max_level',
                        'levels'));
    }

    public function update ($id, Request $req) {
        $validator = Validator::make($req->all(), [
            'user' => 'required',
            'level' => 'required',
        ]);

        // ------------------------------------------------
        // Check if user already exist in the current chart
        // ------------------------------------------------
        $access_user = AccessUser::find($id);
        $check_user = AccessUser::where('accesschart_id', $access_user->accesschart_id)->where('user_id', $req->user)->first();
        if (count($check_user) > 0 && $check_user->access_level == $req->level) {
            $flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'User already exist in the chart.',
            ];
            Session::flash('update_fail', $flash_message);
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        // ----------------------------------------------------------
        // End of checking if user already exist in the current chart
        // ----------------------------------------------------------


        // --------------------------------------
        // Access level 1 must be at least 1 left
        // --------------------------------------
        $check_officer = AccessUser::where('accesschart_id', $access_user->accesschart_id)->where('access_level', 1)->get();
        if ($access_user->access_level == 1) {
            if (count($check_officer) <= 1 && $req->level != 1) {
                $flash_message = [
                    'title' => 'Oops!',
                    'status' => 'danger',
                    'message' => 'Access level 1 must have at least 1 officer.',
                ];
                Session::flash('update_fail', $flash_message);
                return redirect()->back();
            }
        } elseif ($access_user->user_id == $req->user) {
            $check_officer_level = AccessUser::where('accesschart_id', $access_user->accesschart_id)
                                          ->where('user_id', $req->user)
                                          ->where('access_level', $req->level)->first();
            if (count($check_officer_level) > 0) {
                $flash_message = [
                    'title' => 'Oops!',
                    'status' => 'danger',
                    'message' => 'User already exist in the chart 2.',
                ];
                Session::flash('update_fail', $flash_message);
                return redirect()->back();
            }
        }

        if ($validator->fails()) {
            $flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'Please correct the errors below.',
            ];
            Session::flash('update_fail', $flash_message);
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        // ---------------------------------------------
        // END :: Access level 1 must be at least 1 left
        // ---------------------------------------------

        $access_user = AccessUser::find($id);
        $access_user->user_id = $req->user;
        $access_user->access_level = $req->level;
        $access_user->update();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Approving Officer successfully updated.',
        ];
        Session::flash('update_success', $flash_message);
        return redirect()->route('access_chart.officers', ['id' => $access_user->accesschart_id]);
    }

    public function trash ($id) {
        $access_user = AccessUser::find($id);
        return view('access_charts.access_chart_user_maps.trash', compact('access_user'));
    }

    public function delete ($id) {
        $accessuser = AccessUser::find($id);
        $accesschart_id = $accessuser->accesschart_id;
        // check officer with same level
        $samelevel = AccessUser::where('accesschart_id', $accesschart_id)
                        ->where('id', '!=', $id)
                        ->where('access_level', $accessuser->access_level)->get();

        if (!count($samelevel) > 0) {
            // adjust the upper officers if there is no officer with same level
            $upper_officers = AccessUser::where('accesschart_id', $accesschart_id)
                            ->where('access_level', '>', $accessuser->access_level)->get();
            foreach ($upper_officers as $upper_officer) {
                $upper_officer->access_level = ($upper_officer->access_level - 1);
                $upper_officer->update();
            }
        }
        $accessuser->delete();
        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Delete successful.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('access_chart.officers', ['id' => $accesschart_id]);
    }

    public function chart () {
        $useremployments = \DB::select('SELECT aum.access_level,
                                              group_concat(u.first_name, " ", u.last_name SEPARATOR " / ") AS name
                          FROM user_employments AS ue
                          LEFT JOIN accesschart_user_maps AS aum ON aum.accesschart_id=ue.accesschart_id
                          LEFT JOIN users AS u ON u.id=aum.user_id

                          WHERE ue.user_id=:user_id

                          GROUP BY aum.access_level
                          ',
                          [
                            'user_id' => Auth::user()->id
                          ]);

        return view('approvingofficers.index', compact('useremployments'));
    }

    public function assign_to (Request $req, $accesschart_id) {
        // for dynamic column - subject for trait
        $access_for = AccessChart::where('id', $accesschart_id)->pluck('access_for')->first();
        if ($access_for === 0) {
          $accesschart_column = 'accesschart_id';
        } elseif ($access_for === 1) {
          $accesschart_column = 'mrf_accesschart_id';
        } else {
          $accesschart_column = 'po_file_accesschart_id';
        }
      
        // reset accesschart
        if (!$req->assignbtn) {
            UEmployment::where($accesschart_column, $accesschart_id)
            ->update([$accesschart_column => null]);
        }

        // assign accesschart
        if ($req->branch == 0) {
          if ($req->position == 0) {
              $uemployment = UEmployment::where('department_id', $req->department)
                            ->update([$accesschart_column => $accesschart_id]);
          } else {
              $uemployment = UEmployment::where('department_id', $req->department)
                            ->where('position_id', $req->position)
                            ->update([$accesschart_column => $accesschart_id]);
          }
        } else {
          if ($req->position == 0) {
              $uemployment = UEmployment::where('branch_id', $req->branch)
                            ->where('department_id', $req->department)
                            ->update([$accesschart_column => $accesschart_id]);
          } else {
              $uemployment = UEmployment::where('branch_id', $req->branch)
                            ->where('department_id', $req->department)
                            ->where('position_id', $req->position)
                            ->update([$accesschart_column => $accesschart_id]);
          }
        }
        

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Assigning access chart successful.',
        ];
        Session::flash('create_success', $flash_message);
        return redirect()->route('access_chart.officers', ['id' => $accesschart_id]);
    }

    public function assigned_users ($accesschart_id) {
        // for dynamic column - subject for trait
        $access_for = AccessChart::where('id', $accesschart_id)->pluck('access_for')->first();
        if ($access_for === 0) {
          $accesschart_column = 'accesschart_id';
        } elseif ($access_for === 1) {
          $accesschart_column = 'mrf_accesschart_id';
        } else {
          $accesschart_column = 'po_file_accesschart_id';
        }

        $assigned_users = UEmployment::where($accesschart_column, $accesschart_id)->get();
        $accesschart = AccessChart::find($accesschart_id);

        if (count($assigned_users) <= 0) {
            $flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'No users assigned in to this chart.',
            ];
            Session::flash('update_fail', $flash_message);
            return redirect()->route('access_chart.officers', ['id' => $accesschart_id]);
        }
        return view('access_charts.access_chart_user_maps.assigned_users', compact('assigned_users', 'accesschart'));
    }
}
