<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

use App\File;
use App\AccessChart;
use App\ApprovedLog;
use App\PurchaseOrder;
use App\AccessChartUserMap as AccessUser;

use Mail;
use Session;
use Validator;
use App\Traits\FileTrait;
use App\Traits\EmailTrait;
use App\Traits\AccessChartCategorizer;

class PurchaseOrderFileApprovalController extends Controller
{

    use AccessChartCategorizer, FileTrait, EmailTrait;

    public function __construct () {
        $this->middleware(['auth', 'po_file_approval_clearance']);
        
        // for active routing state
        \View::share('is_po_file_approval_route', true);
    }

    public function pending () {
    	$files = DB::select('SELECT
                          f.id,
                          CONCAT(u.first_name, " ", u.last_name) AS `from_user`,
                          (SELECT GROUP_CONCAT(CONCAT(users.first_name," ",users.last_name)) FROM users WHERE id=f.to) AS `to_user`,
                          (SELECT GROUP_CONCAT(users.id) FROM users WHERE id=f.to) AS `to_user_id`,
                          (SELECT GROUP_CONCAT(companies.name) FROM companies WHERE id=f.company_id) AS `to_company`,
                          (SELECT GROUP_CONCAT(companies.id) FROM companies WHERE id=f.company_id) AS `to_company_id`,
                          aum.accesschart_id,
                          aum.access_level,
                          f.*
    						FROM files AS f
    						INNER JOIN access_chart_user_maps AS aum ON f.po_accesschart_id=aum.accesschart_id
    						INNER JOIN users AS u ON f.from=u.id
                INNER JOIN user_employments AS ue ON ue.user_id=u.id
                WHERE aum.user_id=:id &&
                      aum.access_level=f.waiting_for &&
                      f.status=0 && f.po_number IS NOT NULL
                ORDER BY f.id ASC', ['id' => \Auth::user()->id]);

      $access_for = 2; // 0 = otloa, 1 = mrf, 2 = po file
      $approvers = $files ? $this->approver($files[0]->from, $access_for) : null;

    	return view('purchase_orders.files.approvals.pending', compact('files', 'approvers'));
    }

    public function approve ($id) {
        abort_if(!$this->is_po_file_approver($id)[0], 403);

        // for previous link in breadcrumbs & back button
        $previous_link = $this->is_po_file_approver($id)[1] ? 'po.file.approval.pending' : 'po.file.approval.overlook';

        $file = File::with(['from_user' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                      }])
                      ->with(['to_user' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                      }])
                      ->with(['to_company' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->where('id', $id)
                      ->where('customer_id', null)
                      ->where('po_number', '!=', null)
                      ->first();
        return view('purchase_orders.files.approvals.approve', compact('file', 'previous_link'));
    }

    public function proceed_approve ($id, Request $req) {
        abort_if(!$this->is_po_file_approver($id)[0], 403);

        $file = File::with(['from_user' => function ($qry) {
                        $qry->select('id', 'extn_email1', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                      }])
                      ->with(['to_user' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                      }])
                      ->with(['to_company' => function ($qry) {
                        $qry->select('id', 'name');
                      }])
                      ->where('id', $id)
                      ->where('customer_id', null)
                      ->where('type_id', null)
                      ->first();
        
        if (\Auth::user()->hasPermissionTo('Overlook Purchase Order Files')) { // will bypass all approvers
            $file->waiting_for = null;
            $file->remarks_by = \Auth::user()->id;
            $file->status = 2; // 0=pending,1=rejected,2=approved

            $this->po_file_send_to_email($file->id);
        } else {
            $max_level = $this->max_level($file->po_accesschart_id);
            if ($file->waiting_for < $max_level) {
                $file->waiting_for = $file->waiting_for + 1;
            } else {
                $file->waiting_for = null;
                $file->remarks_by = \Auth::user()->id;
                $file->status = 2; // 0=pending,1=rejected,2=approved

                $this->po_file_send_to_email($file->id);
            }
        }
        
        $file->update();

        // --------------------
        // Log Approvals
        // --------------------
        $file_logs = new ApprovedLog;
        $file_logs->po_file_id = $id;
        $file_logs->approver = \Auth::user()->first_name . ' ' . \Auth::user()->last_name;
        $file_logs->save(); 
        // --------------------
        // END :: Log Approvals
        // --------------------

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Purchase order file successfully approved.',
        ];
        Session::flash('create_success', $flash_message);

        if (\Auth::user()->hasPermissionTo('Overlook Purchase Order Files')) {
            return redirect()->route('po.file.approval.overlook');
        } else {
            return redirect()->route('po.file.approval.pending');
        }
    }

    public function reject ($id) {
      abort_if(!$this->is_po_file_approver($id)[0], 403);

      // for previous link in breadcrumbs & back button
      $previous_link = $this->is_po_file_approver($id)[1] ? 'po.file.approval.pending' : 'po.file.approval.overlook';

      $file = File::with(['from_user' => function ($qry) {
                      $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                    }])
                    ->with(['to_user' => function ($qry) {
                      $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                    }])
                    ->with(['to_company' => function ($qry) {
                      $qry->select('id', 'name');
                    }])
                    ->where('id', $id)
                    ->where('customer_id', null)
                    ->where('type_id', null)
                    ->first();
        return view('purchase_orders.files.approvals.reject', compact('file', 'previous_link'));
    }

    public function proceed_reject ($id, Request $req) {
        abort_if(!$this->is_po_file_approver($id)[0], 403);

        $rules = [
            'remarks2' => 'required|min:5',
        ];

        $messages = [
            'remarks2.required' => 'You must leave a remark for your reason of rejecting the item.',
            'remarks2.min' => 'Remarks field must be at least 5 characters long',
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

        $file = File::find($id);
        $file->remarks_by = \Auth::user()->id;
        $file->remarks2 = $req->remarks2;
        $file->status = 1; // 0=pending,1=reject,2=approve
        $file->waiting_for = null; // set waiting_for to null
        $file->update();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Purchase Order file was rejected.',
        ];
        Session::flash('create_success', $flash_message);

        if (\Auth::user()->hasPermissionTo('Overlook Purchase Order Files')) {
            return redirect()->route('po.file.approval.overlook');
        } else {
            return redirect()->route('po.file.approval.pending');
        }
    }

    public function overlook () {
      $files = DB::select('SELECT
                          f.id,
                          CONCAT(u.first_name, " ", u.last_name) AS `from_user`,
                          (SELECT GROUP_CONCAT(CONCAT(users.first_name," ",users.last_name)) FROM users WHERE id=f.to) AS `to_user`,
                          (SELECT GROUP_CONCAT(users.id) FROM users WHERE id=f.to) AS `to_user_id`,
                          (SELECT GROUP_CONCAT(companies.name) FROM companies WHERE id=f.company_id) AS `to_company`,
                          (SELECT GROUP_CONCAT(companies.id) FROM companies WHERE id=f.company_id) AS `to_company_id`,
                          f.*
    						FROM files AS f
    						INNER JOIN users AS u ON f.from=u.id
                WHERE f.status=0 && f.po_number IS NOT NULL
                ORDER BY f.id ASC');

      $access_for = 2; // 0 = otloa, 1 = mrf, 2 = po file
      $approvers = $files ? $this->approver($files[0]->from, $access_for) : null;

    	return view('purchase_orders.files.overlooks.pending', compact('files', 'approvers'));
    }
}
