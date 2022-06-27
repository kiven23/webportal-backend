<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Overtime;
use App\ApprovedLog;
use App\AccessChartUserMap as AccessUser;
use App\UserEmployment as UEmployment;

use Carbon\Carbon;
use Session;
use Validator;
use App\Traits\AccessChartCategorizer;

class OvertimeController extends Controller
{

    use AccessChartCategorizer;

    public function __construct () {
        $this->middleware(['auth', 'overtime_clearance']);

        // for active routing state
        \View::share('is_overtime_filing_route', true);
    }

    public function index () {
      $access_for = 0; // 0 = otloa, 1 = mrf
      $approvers = $this->approver(\Auth::user()->id, $access_for);
      $overtimes = Overtime::where('user_id', \Auth::user()->id)
                  ->where('status', '!=', 5)
                  ->orderBy('id', 'desc')
                  ->with(['officers_approved' => function ($qry) {
                      $qry->orderBy('created_at')->get();
                  }])->get();
    	return view('overtimes.index', compact('overtimes', 'approvers'));
    }

    public function create () {
    	return view('overtimes.create');
    }

    public function store (Request $req) {
        $validator = Validator::make($req->all(), [
                'date_from' => 'required',
            ]);

        // DATE TO EMPTY
        if ($req->onwards != 'on' && $req->date_to == '') {
            $empty = 'The date to input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('date_to', $empty);
            });
        }

        $reasons = [];

        if ($req->tactical_event) {
            $tactical_event = "Due to tactical event at " . $req->tactical_event;
            array_push($reasons, $tactical_event);
        }
        if ($req->assist_department) {
            $assist_department = "To assist other department - " . $req->assist_department;
            array_push($reasons, $assist_department);
        }
        if ($req->travel_to_field) {
            $travel_to_field = "Travel to field - " . $req->travel_to_field;
            array_push($reasons, $travel_to_field);
        }
        if ($req->admin_instruction) {
            $admin_instruction = "Admin instruction to " . $req->admin_instruction;
            array_push($reasons, $admin_instruction);
        }
        if ($req->individual_target == "on") {
            $individual_target = "To attain individual target";
            array_push($reasons, $individual_target);
        }
        if ($req->department_target == "on") {
            $department_target = "To attain department target";
            array_push($reasons, $department_target);
        }
        if ($req->deadline == "on") {
            $deadline = "To finish deadlines";
            array_push($reasons, $deadline);
        }
        if ($req->pending_units == "on") {
            $pending_units = "To deliver pending units";
            array_push($reasons, $pending_units);
        }
        if ($req->after_sales_service == "on") {
            $after_sales_service = "Due to after sales service";
            array_push($reasons, $after_sales_service);
        }
        if ($req->client_concern == "on") {
            $client_concern = "Attend to Client Concern/Customer Service";
            array_push($reasons, $client_concern);
        }

        // Check if reason is empty
        if (empty($reasons)) {
            $empty = 'Please select any of the reasons for justification of your OT.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('reason_empty', $empty);
            });
        }

    	if ($validator->fails()) {
    		$flash_message = [
                'title' => 'Oops!',
                'status' => 'danger',
                'message' => 'Please correct the errors below.',
            ];
    		Session::flash('create_fail', $flash_message);
            if ($req->onwards == 'on') {
                Session::flash('onwards', true);
            } else {
                Session::flash('onwards', false);
            }
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$accesschart = UEmployment::where('user_id', Auth::user()->id)
                        ->with('accessusersmap')->first();

    	$access_level = AccessUser::where('accesschart_id', $accesschart->accesschart_id)->min('access_level');

    	if (!count($accesschart->accessusersmap) > 0) {
    		$flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'You have no Approving Officer. Please contact the administrator.'
    		];
    		Session::flash('create_fail', $flash_message);
    		return redirect()->back()
    						 ->withErrors($validator)
    						 ->withInput();
    	}

    	$overtime = new Overtime;
    	$overtime->user_id = Auth::user()->id;
        $overtime->accesschart_id = $accesschart->accesschart_id;
    	$overtime->waiting_for = $access_level;
    	$overtime->date_from = Carbon::parse($req->date_from);
        if ($req->onwards == 'on') {
            $overtime->date_to = null;
        } else {
            $overtime->date_to = Carbon::parse($req->date_to);
        }   

        $reasons = implode(',',$reasons);
    	$overtime->reason = $reasons;
        if ($req->working_dayoff == "on") {
            $overtime->working_dayoff = 1;
        } else {
            $overtime->working_dayoff = 0;
        }
    	$overtime->status = 1; // 1=pending,2=returned,3=rejected,4=approved,5=rejected_remove
        $overtime->save();

    	$flash_message = [
    		'title' => 'Well Done!',
    		'status' => 'success',
    		'message' => 'Overtime successfully filed.',
    	];
		Session::flash('create_success', $flash_message);
		return redirect()->route('overtimes');
    }

    public function edit ($id) {
        $overtime = Overtime::where('id', $id)->with('officers_approved')->first();
        // ----------------
        // Double Checking
        // ----------------
        if ($overtime->status !== 1 && $overtime->status !== 2) {
            return redirect()->back();
        }

        if ($overtime->user_id != \Auth::user()->id || ($overtime->status !== 1 && $overtime->status !== 2)) {
            return redirect()->back();
        }
        // -----------------------
        // END :: Double Checking
        // -----------------------

        return view('overtimes.edit', compact('overtime'));
    }

    public function update ($id, Request $req) {
        $validator = Validator::make($req->all(), [
                'date_from' => 'required',
            ]);
        
        // DATE TO EMPTY
        if ($req->onwards != 'on' && $req->date_to == '') {
            $empty = 'The date to input is required.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('date_to', $empty);
            });
        }

        $reasons = [];

        if ($req->tactical_event) {
            $tactical_event = "Due to tactical event at " . $req->tactical_event;
            array_push($reasons, $tactical_event);
        }
        if ($req->assist_department) {
            $assist_department = "To assist other department - " . $req->assist_department;
            array_push($reasons, $assist_department);
        }
        if ($req->travel_to_field) {
            $travel_to_field = "Travel to field - " . $req->travel_to_field;
            array_push($reasons, $travel_to_field);
        }
        if ($req->admin_instruction) {
            $admin_instruction = "Admin instruction to " . $req->admin_instruction;
            array_push($reasons, $admin_instruction);
        }
        if ($req->individual_target == "on") {
            $individual_target = "To attain individual target";
            array_push($reasons, $individual_target);
        }
        if ($req->department_target == "on") {
            $department_target = "To attain department target";
            array_push($reasons, $department_target);
        }
        if ($req->deadline == "on") {
            $deadline = "To finish deadlines";
            array_push($reasons, $deadline);
        }
        if ($req->pending_units == "on") {
            $pending_units = "To deliver pending units";
            array_push($reasons, $pending_units);
        }
        if ($req->after_sales_service == "on") {
            $after_sales_service = "Due to after sales service";
            array_push($reasons, $after_sales_service);
        }
        if ($req->client_concern == "on") {
            $client_concern = "Attend to Client Concern/Customer Service";
            array_push($reasons, $client_concern);
        }

        // Check if reason is empty
        if (empty($reasons)) {
            $empty = 'Please select any of the reasons for justification of your OT.';
            $validator->after(function ($validator) use ($empty) {
                $validator->getMessageBag()->add('reason_empty', $empty);
            });
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

        // ----------------
        // Double Checking
        // ----------------
        $overtime = Overtime::where('id', $id)->with('officers_approved')->first();
        
        if ($overtime->status !== 1 && $overtime->status !== 2) {
            return redirect()->back();
        }

        if ($overtime->user_id != \Auth::user()->id || ($overtime->status !== 1 && $overtime->status !== 2)) {
            return redirect()->back();
        }
        // -----------------------
        // END :: Double Checking
        // -----------------------

        $overtime->date_from = Carbon::parse($req->date_from);
        if ($req->onwards == 'on') {
            $overtime->date_to = null;
        } else {
            $overtime->date_to = Carbon::parse($req->date_to);
        }
        $reasons = implode(',',$reasons);
        $overtime->reason = $reasons;
        $overtime->waiting_for = 1;
        if ($req->working_dayoff == "on") {
            $overtime->working_dayoff = 1;
        } else {
            $overtime->working_dayoff = 0;
        }
        if ($overtime->status === 2) {
            $overtime->remarks = null;
            $overtime->status = 1; // 1=pending,2=returned,3=rejected,4=approved,5=rejected_remove
        }
        $overtime->update();

        // -------------------
        // Clear Approval Logs
        // -------------------
        $ot_logs = ApprovedLog::where('overtime_id', $id)->delete();
        // --------------------------
        // END :: Clear Approval Logs
        // --------------------------

        $flash_message = [
        	'title' => 'Well Done!',
        	'status' => 'success',
        	'message' => 'Overtime successfully updated.',
        ];
        Session::flash('update_success', $flash_message);
        return redirect()->route('overtimes');
    }

    public function remove_reject ($id) {
        $overtime = Overtime::find($id);
        $overtime->status = 5;
        $overtime->update();

        return redirect()->route('overtimes');

    }

    public function trash ($id) {
        // ----------------
        // Double Checking
        // ----------------
        $overtime = Overtime::where('id', $id)->with('officers_approved')->first();
        
        if ($overtime->status !== 2) {
            return redirect()->back();
        }

        if ($overtime->user_id != \Auth::user()->id || ($overtime->status != 1 && $overtime->status != 2)) {
            return redirect()->back();
        }
        // -----------------------
        // END :: Double Checking
        // -----------------------

        $pending = Overtime::find($id);
        return view('overtimes.trash', compact('pending'));
    }

    public function delete ($id) {
        // ----------------
        // Double Checking
        // ----------------
        $overtime = Overtime::where('id', $id)->with('officers_approved')->first();
        
        if ($overtime->status !== 2) {
            return redirect()->back();
        }

        if ($overtime->user_id != \Auth::user()->id || ($overtime->status != 1 && $overtime->status != 2)) {
            return redirect()->back();
        }
        // -----------------------
        // END :: Double Checking
        // -----------------------

        $pending = Overtime::find($id);
        $pending->delete();
        return redirect()->route('overtimes');
    }
}
