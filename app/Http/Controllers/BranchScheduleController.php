<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Branch;
use App\BranchSchedule;
use App\DataTables\BranchSchedulesDataTable;
use App\DataTables\BranchSchedulesDataTablesEditor;

use Session;
use Validator;
use Carbon\Carbon;

class BranchScheduleController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'branch_sched_clearance']);
        // for active routing state
        \View::share('is_bsched_route', true);
    }
    
    public function index () {
      $branch_schedules = BranchSchedule::select('id', 'time_from', 'time_to')->get();
      return view('branches.schedules.index', compact('branch_schedules'));
    }

    public function create () {
      return view('branches.schedules.create');
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(), [
        'time_from' => 'required',
        'time_from' => 'required',
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

      $branch_schedule = new BranchSchedule;
      $branch_schedule->time_from = Carbon::parse($req->time_from)->format('H:i:s');
      $branch_schedule->time_to = Carbon::parse($req->time_to)->format('H:i:s');
      $branch_schedule->save();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'New schedule has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);

      if ($req->savebtn == 0) {
        return redirect()->route('branch-schedule.create');
      } else { return redirect()->route('branch-schedules.index'); }
    }

    public function edit ($id) {
      $branch_schedule = BranchSchedule::where('id', $id)
                         ->select('id', 'time_from', 'time_to')
                         ->first();
      return view('branches.schedules.edit', compact('branch_schedule'));
    }

    public function update (Request $req, $id) {
      $validator = Validator::make($req->all(), [
        'time_from' => 'required',
        'time_from' => 'required',
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'dangr',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('update_fail', $flash_message);
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $branch_schedule = BranchSchedule::find($id);
      $branch_schedule->time_from = Carbon::parse($req->time_from)->format('H:i:s');
      $branch_schedule->time_to = Carbon::parse($req->time_to)->format('H:i:s');
      $branch_schedule->update();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Schedule has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('branch-schedules.index');
    }

    public function trash ($id) {
      $branch_schedule = BranchSchedule::where('id', $id)
                         ->select('id', 'time_from', 'time_to')
                         ->with(['branches'], function ($qry) {
                           $qry->select('id', 'name');
                         })
                         ->first();
      return view('branches.schedules.trash', compact('branch_schedule'));
    }

    public function delete ($id) {
      $branch_schedule = BranchSchedule::find($id);
      $sched_text = $branch_schedule->time_from . ' ' . $branch_schedule->time_to;
      $branch_schedule->delete();

      // update affected branches
      Branch::where('bsched_id', $id)->update([
        'bsched_id' => null
      ]);

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Schedule ' . $sched_text . ' has been successfully delete from our records.',
      ];

      Session::flash('delete_success', $flash_message);
      return redirect()->route('branch-schedules.index');
    }

















    // api
    public function all () {
    	$bscheds = BranchSchedule::select('id',
                   'time_from',
                   'time_to',
                   \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ',TIME_FORMAT(time_to, '%h:%i %p')) AS time"))
                  ->with(['branches' => function ($qry) {
                    $qry->select('bsched_id', 'name');
                  }])
                  ->get();
    	return response()->json($bscheds, 200);
    }

    public function store_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'time_from' => 'required',
        'time_to' => 'required',
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

    	$bsched = new BranchSchedule;
      $bsched->time_from = $req->time_from;
      $bsched->time_to = $req->time_to;
    	$bsched->save();

      $bsched = BranchSchedule::select('id',
                  'time_from',
                  'time_to',
                  \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ',TIME_FORMAT(time_to, '%h:%i %p')) AS time"))
                  ->with(['branches' => function ($qry) {
                    $qry->select('bsched_id', 'name');
                  }])
                  ->where('id', $bsched->id)
                  ->first();

    	return response()->json($bsched, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'time_from' => 'required',
        'time_to' => 'required',
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

      $bsched = BranchSchedule::find($req->id);
      $bsched->time_from = $req->time_from;
      $bsched->time_to = $req->time_to;
      $bsched->update();

      $bsched = BranchSchedule::select('id',
                'time_from',
                'time_to',
                \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ',TIME_FORMAT(time_to, '%h:%i %p')) AS time"))
                ->with(['branches' => function ($qry) {
                  $qry->select('bsched_id', 'name');
                }])
                ->where('id', $req->id)->first();

      return response()->json($bsched, 200);
    }

    public function delete_multiple (Request $req) {
      $ids = $req;
      $bsched = BranchSchedule::whereIn('id', $ids)
                ->select('id', 'time_from', 'time_to')
                ->with(['branches' => function ($qry) {
                  $qry->select('bsched_id', 'name');
                }])
                ->get();
      $response = $bsched;
      BranchSchedule::whereIn('id', $ids)->delete();

      return response()->json($response, 200);
    }
}
