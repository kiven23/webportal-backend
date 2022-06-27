<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\AccessChartUserMap AS AccessUser;
use App\ApprovedLog;
use App\MaintRequest;

use DB;
use Session;
use Validator;
use App\Traits\MaintRequestTrait;
use App\Traits\AccessChartCategorizer;

class MaintRequestApprovalController extends Controller
{

    use AccessChartCategorizer, MaintRequestTrait;

    public function __construct () {
        $this->middleware(['auth', 'mrf_approval_clearance']);
        
        // for active routing state
        \View::share('is_mrf_approval_route', true);
    }

    public function pending () {
      $maint_requests = DB::select('SELECT
                                    mr.id,
                                    b.name AS `branch`,
                                    CONCAT(u.first_name, " ", u.last_name) AS `submitted_by`,
                                    aum.accesschart_id,
                                    aum.access_level,
                                    mr.created_at AS `date_submitted`,
                                    mr.status,
                                    (SELECT GROUP_CONCAT(CONCAT(TRIM(LEADING "public/" FROM mrf.path),mrf.file_name)) FROM maintrequest_files AS mrf WHERE mr.id=mrf.maint_request_id) AS files
    						FROM maint_requests AS mr
    						INNER JOIN access_chart_user_maps AS aum ON mr.accesschart_id=aum.accesschart_id
    						INNER JOIN users AS u ON mr.user_id=u.id
                INNER JOIN user_employments AS ue ON ue.user_id=u.id
                INNER JOIN branches AS b ON ue.branch_id=b.id
                WHERE aum.user_id=:id &&
                      aum.access_level=mr.waiting_for &&
                      mr.status<3
                ORDER BY mr.id ASC', ['id' => \Auth::user()->id]);
      return view('maint_requests.admin_views.lists', compact('maint_requests'));
    }

    public function cancel ($id) {
      $auth_id = \Auth::user()->id;
      $maint_request = MaintRequest::where('id', $id)->first();
      $status = $maint_request->status;
      $method = 2; // 2 = cancelled, 3 = approved, 4 = completed

      abort_if(!$this->can_cancel_approve_delete($id, $method), 403);

      if ($status === 2) {
        MaintRequest::where('id', $id)->update(['status' => 1]);
      } else {
        MaintRequest::where('id', $id)->update(['status' => 2]);
      }
      
      return redirect()->back();
    }

    public function approve ($id) {
      $auth_id = \Auth::user()->id;
      $maint_request = MaintRequest::find($id);
      $method = 2; // 2 = cancelled, 3 = approved, 4 = completed

      abort_if(!$this->can_cancel_approve_delete($id, $method), 403);

      return view('maint_requests.admin_views.approve', compact('maint_request'));
    }

    public function proceed_approve (Request $req, $id) {
      $auth_id = \Auth::user()->id;
      $auth_name = \Auth::user()->first_name . " " . \Auth::user()->last_name;
      $maint_request = MaintRequest::where('id', $id)->first();
      $status = $maint_request->status;
      $method = 2; // 2 = cancelled, 3 = approved, 4 = completed

      abort_if(!$this->can_cancel_approve_delete($id, $method), 403);

      $validator = Validator::make($req->all(), [
          'remarks' => 'required'
        ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('update_fail', $flash_message);
        return redirect()->back()
                         ->withInput()
                         ->withErrors($validator);
      }

      MaintRequest::where('id', $id)->update([
        'instruction' => $req->remarks,
        'ins_date' => \Carbon\Carbon::now(),
        'approved_by' => \Auth::user()->id,
        'status' => 3
      ]);

      // check auth user if existing in approvedlog
      // before inserting
      $is_escalator = ApprovedLog::where('maint_request_id', $id)->where('approver', $auth_name)->first();
      if (!$is_escalator) {
        $auth_user = \Auth::user()->first_name . " " . \Auth::user()->last_name;
        $approved_log = new ApprovedLog;
        $approved_log->maint_request_id = $id;
        $approved_log->approver = $auth_user;
        $approved_log->save();
      }

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Request has been approved.',
      ];
      Session::flash('update_success', $flash_message);

      if (\Auth::user()->hasPermissionTo('Overlook Maintenance Requests')) {
        $route = 'maint_request.approval.overlook';
      } else {
        $route = 'maint_request.approval.pending';
      }
      return redirect()->route($route);
    }

    public function escalate ($id) {
      $auth_id = \Auth::user()->id;
      $maint_request = MaintRequest::find($id);

      abort_if(!$this->is_escalatable($id), 403);

      return view('maint_requests.admin_views.escalate', compact('maint_request'));
    }

    public function proceed_escalate ($id) {
      $auth_id = \Auth::user()->id;
      $maint_request = MaintRequest::find($id);

      abort_if(!$this->is_escalatable($id), 403);

      // abort when status is already completed or cancelled before proceeding
      $status = $maint_request->status;
      abort_if($status == 2 || $status == 4, 403);

      $next_approver = $maint_request->waiting_for + 1;
      MaintRequest::where('id', $id)->update(['waiting_for' => $next_approver]);

      $auth_user = \Auth::user()->first_name . " " . \Auth::user()->last_name;
      $approved_log = new ApprovedLog;
      $approved_log->maint_request_id = $id;
      $approved_log->approver = $auth_user;
      $approved_log->save();

      $flash_message = [
        'title' => 'Weel Done!',
        'status' => 'success',
        'message' => 'Request has been successfully escalated.',
      ];
      Session::flash('update_success', $flash_message);

      return redirect()->route('maint_request.approval.pending');
    }

    public function overlook () {
      $maint_requests = DB::select('SELECT
                                    mr.id,
                                    b.name AS `branch`,
                                    CONCAT(u.first_name, " ", u.last_name) AS `submitted_by`,
                                    mr.created_at AS `date_submitted`,
                                    mr.status,
                                    (SELECT GROUP_CONCAT(CONCAT(TRIM(LEADING "public/" FROM mrf.path),mrf.file_name)) FROM maintrequest_files AS mrf WHERE mr.id=mrf.maint_request_id) AS files
    						FROM maint_requests AS mr
    						INNER JOIN users AS u ON mr.user_id=u.id
                INNER JOIN user_employments AS ue ON ue.user_id=u.id
                INNER JOIN branches AS b ON ue.branch_id=b.id
                WHERE mr.status >= 1 && mr.status <= 2
                ORDER BY mr.id ASC');
      return view('maint_requests.overlooks.pending', compact('maint_requests'));
    }
}
