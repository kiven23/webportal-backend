<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

use App\Overtime;
use App\AccessChart;
use App\ApprovedLog;
use App\AccessChartUserMap as AccessUser;

use Session;
use Validator;
use App\Traits\AccessChartCategorizer;

class OvertimeApprovalController extends Controller
{

    use AccessChartCategorizer;

    public function __construct () {
        $this->middleware(['auth', 'otloa_approval_clearance']);
        
        // for active routing state
        \View::share('is_overtime_approval_route', true);
    }

    public function pending () {
    	$otpendings = DB::select('SELECT
                                    aum.user_id as `user_id`,
                                    aum.accesschart_id,
                                    aum.access_level,
                                    ot.*,CONCAT(u.first_name, " ",u.last_name) as `employee`,
                                    pos.name AS `position`,
                                    dep.name AS `department`
    						FROM overtimes AS ot
    						INNER JOIN access_chart_user_maps AS aum ON ot.accesschart_id=aum.accesschart_id
    						INNER JOIN users AS u ON ot.user_id=u.id
                            INNER JOIN user_employments AS ue ON ue.user_id=u.id
                            INNER JOIN positions AS pos ON pos.id=ue.position_id
                            INNER JOIN departments AS dep ON dep.id=ue.department_id
                            WHERE aum.user_id=:id &&
    						aum.access_level=ot.waiting_for &&
    						ot.status=1
                            ORDER BY ot.id ASC', ['id' => \Auth::user()->id]);

    	return view('overtimes.approvals.pending', compact('otpendings'));
    }

    public function approve ($id) {
        $pending = Overtime::find($id);
        return view('overtimes.approvals.approve', compact('pending'));
    }

    public function proceed_approve ($id, Request $req) {
        $overtime = Overtime::find($id);
        
        if (\Auth::user()->hasPermissionTo('Overlook Overtimes')) { // will bypass all approvers
            $overtime->waiting_for = null;
            $overtime->remarks_by = \Auth::user()->id;
            $overtime->status = 4; // 1=pending,2=returned,3=rejected,4=approved,5=rejected_moved
        } else {
            $max_level = $this->max_level($overtime->accesschart_id);
            if ($overtime->waiting_for < $max_level) {
                $overtime->waiting_for = $overtime->waiting_for + 1;
            } else {
                $overtime->waiting_for = null;
                $overtime->remarks_by = \Auth::user()->id;
                $overtime->status = 4; // 1=pending,2=returned,3=rejected,4=approved,5=rejected_moved
            }
        }
        
        $overtime->update();

        // --------------------
        // Log Approvals
        // --------------------
        $ot_logs = new ApprovedLog;
        $ot_logs->overtime_id = $id;
        $ot_logs->approver = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
        $ot_logs->save();
        // --------------------
        // END :: Log Approvals
        // --------------------

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Overtime successfully approved.',
        ];
        Session::flash('create_success', $flash_message);

        if (\Auth::user()->hasPermissionTo('Overlook Overtimes')) {
            return redirect()->route('approval.overlook');
        } else {
            return redirect()->route('approval.pending');
        }
    }

    public function oreturn ($id) {
    	$pending = Overtime::find($id);
    	return view('overtimes.approvals.return', compact('pending'));
    }

    public function proceed_return ($id, Request $req) {
    	$rules = [
    		'remarks' => 'required|min:5',
    	];

    	$messages = [
    		'remarks.required' => 'You must leave a remark for your reason of returning the filed overtime',
    		'remarks.min' => 'Remarks field must be at least 5 characters long',
    	];
    	$validator = Validator::make($req->all(), $rules, $messages);

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

    	$overtime = Overtime::find($id);
    	$overtime->status = 2; // 1=pending,2=returned,3=rejected,4=approved,5=rejected_moved
    	$overtime->remarks_by = \Auth::user()->id;
    	$overtime->remarks = $req->remarks;
    	$overtime->update();

    	$flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Overtime successfully returned to the employee.',
        ];
    	Session::flash('create_success', $flash_message);

        if (\Auth::user()->hasPermissionTo('Overlook Overtimes')) {
            return redirect()->route('approval.overlook');
        } else {
            return redirect()->route('approval.pending');
        }
    }

    public function reject ($id) {
        $pending = Overtime::find($id);
        return view('overtimes.approvals.reject', compact('pending'));
    }

    public function proceed_reject ($id, Request $req) {
        $rules = [
            'remarks' => 'required|min:5',
        ];

        $messages = [
            'remarks.required' => 'You must leave a remark for your reason of rejecting the filed overtime',
            'remarks.min' => 'Remarks field must be at least 5 characters long',
        ];
        $validator = Validator::make($req->all(), $rules, $messages);

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

        $overtime = Overtime::find($id);
        $overtime->remarks_by = \Auth::user()->id;
        $overtime->remarks = $req->remarks;
        $overtime->status = 3; // 1=pending,2=returned,3=rejected,4=approved,5=rejected_moved
        $overtime->update();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Overtime was rejected.',
        ];
        Session::flash('create_success', $flash_message);

        if (\Auth::user()->hasPermissionTo('Overlook Overtimes')) {
            return redirect()->route('approval.overlook');
        } else {
            return redirect()->route('approval.pending');
        }
    }

    public function overlook () {
        $pendings = Overtime::where('status', 1)
                            ->with('approvers')->get();
        return view('overtimes.overlooks.pending', compact('pendings'));
    }
}
