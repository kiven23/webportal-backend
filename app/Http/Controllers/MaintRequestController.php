<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Survey;
use App\MaintRequest;
use App\MaintrequestFile AS File;
use App\UserEmployment as UEmployment;
use App\AccessChartUserMap as AccessUser;

use DB;
use Carbon;
use Session;
use Validator;
use App\Traits\MaintRequestTrait;
use App\Traits\AccessChartCategorizer;

class MaintRequestController extends Controller
{

    use AccessChartCategorizer, MaintRequestTrait;

    public function __construct () {
      $this->middleware(['auth', 'maint_request_clearance']);

      // for active routing state
      \View::share('is_maint_request_route', true);
    }

    public function index () {
      $access_for = 1; // 0 = otloa, 1 = mrf, 2 = po file
      $approvers = $this->approver(\Auth::user()->id, $access_for);
      $maint_requests = MaintRequest::latest()
                               ->where('user_id', \Auth::user()->id)
                               ->with(['files' => function ($qry) {
                                 $qry->select('id',
                                 'maint_request_id',
                                 'file_name',
                                 \DB::raw('CONCAT(TRIM(LEADING "public/" FROM path),file_name) AS file_path'));
                               }])
                               ->with(['officers_approved' => function ($qry) {
                                  $qry->orderBy('created_at')->get();
                                }])
                               ->get();
      return view('maint_requests.user_views.index', compact('maint_requests', 'approvers'));
    }

    public function create () {
      $branch = \Auth::user()->branch->name;
      $contact = \Auth::user()->contact ? \Auth::user()->contact : 'None';
      return view('maint_requests.user_views.create', compact('branch', 'contact'));
    }

    public function store (Request $req) {
      $validator = Validator::make($req->all(),[
        'remarks' => 'required'
      ]);
      if ((isset($req['nature-of-concern-sub-child']) && implode($req['nature-of-concern-sub-child']) == "") && !$req['nature-of-concern-sub-child-text']) {
        $message = 'Please select your concern.';
        $validator->after(function ($validator) use ($message) {
          $validator->getMessageBag()->add('concern', $message);
        });
      }
      if (!$req->location || ((isset($req['location-sub']) && implode($req['location-sub']) == "") && !$req['location-sub-text'])) {
        $message = 'Please select a location.';
        $validator->after(function ($validator) use ($message) {
          $validator->getMessageBag()->add('location', $message);
        });
      }

      if ($validator->fails()) {
        return response()->json(['validator' => $validator->errors()], 422);
      }

      if (isset($req['nature-of-concern-sub-child']) && implode($req['nature-of-concern-sub-child']) != "") {
        $nature_concern = $req['nature-of-concern'] . ":" . $req['nature-of-concern-sub'] . ":" . implode($req['nature-of-concern-sub-child'], ",");
      } else if ($req['nature-of-concern-sub-child-text']) {
        $nature_concern = $req['nature-of-concern'] . ":" . $req['nature-of-concern-sub'] . ":" . $req['nature-of-concern-sub-child-text'] . "-textarea";
      }
      if (isset($req['location-sub'])) {
        $location = implode($req['location'], ",") . ":" . implode($req['location-sub'], ",") . "," . $req['location-sub-text'] . "-textarea";
      } else {
        $location = implode($req['location'], ",") . ":" . $req['location-sub-text'] . "-textarea";
      }

      // for approval function
      $accesschart = UEmployment::where('user_id', \Auth::user()->id)
                     ->with('mrf_accessusersmap')->first();

    	$access_level = AccessUser::where('accesschart_id', $accesschart->mrf_accesschart_id)->min('access_level');

    	if (!count($accesschart->mrf_accessusersmap) > 0) {
        $msg = 'You have no Approving Officer. Please contact the administrator.';
    		return response()->json(['approver_err' => $msg], 422);
    	}

      $maint_request = new MaintRequest;
      $maint_request->user_id = \Auth::user()->id;
      $maint_request->branch_id = \Auth::user()->branch->id;
      $maint_request->accesschart_id = $accesschart->mrf_accesschart_id;
    	$maint_request->waiting_for = $access_level;
      $maint_request->nature_concern = $nature_concern;
      $maint_request->location = $location;
      $maint_request->remarks = $req->remarks;
      $maint_request->save();

      if ($req->hasfile('canvas')) {
        foreach ($req->file('canvas') as $file) {
          $name = $file->getClientOriginalName();
          $path = 'public/maint_requests/'.\Auth::user()->branch->id.'/';
          $file->storeAs($path, $name);

          // create new files
          $maint_file = new File;
          $maint_file->file_name = $name;
          $maint_file->path = $path;
          $maint_file->maint_request_id = $maint_request->id;
          $maint_file->save();
        }
      }

      return response()->json(['msg' => 'Request successfully added into our records.'], 200);
    }

    public function edit ($id) {
      $branch = \Auth::user()->branch->name;
      $contact = \Auth::user()->contact ? \Auth::user()->contact : 'None';
      $maint_request = MaintRequest::where('id', $id)
                       ->where('user_id', \Auth::user()->id)
                       ->with(['files' => function ($qry) {
                          $qry->select('id',
                          'maint_request_id',
                          'file_name',
                          \DB::raw('CONCAT(TRIM(LEADING "public/" FROM path),file_name) AS file_path'));
                        }])
                       ->first();
      return view('maint_requests.user_views.edit', compact('branch', 'contact', 'maint_request'));
    }

    public function update (Request $req, $id) {
      $validator = Validator::make($req->all(),[
        'remarks' => 'required'
      ]);
      if ((isset($req['nature-of-concern-sub-child']) && implode($req['nature-of-concern-sub-child']) == "") && !$req['nature-of-concern-sub-child-text']) {
        $message = 'Please select your concern.';
        $validator->after(function ($validator) use ($message) {
          $validator->getMessageBag()->add('concern', $message);
        });
      }
      if (!$req->location || ((isset($req['location-sub']) && implode($req['location-sub']) == "") && !$req['location-sub-text'])) {
        $message = 'Please select a location.';
        $validator->after(function ($validator) use ($message) {
          $validator->getMessageBag()->add('location', $message);
        });
      }

      if ($validator->fails()) {
        return response()->json(['validator' => $validator->errors()], 422);
      }

      if (isset($req['nature-of-concern-sub-child']) && implode($req['nature-of-concern-sub-child']) != "") {
        $nature_concern = $req['nature-of-concern'] . ":" . $req['nature-of-concern-sub'] . ":" . implode($req['nature-of-concern-sub-child'], ",");
      } else if ($req['nature-of-concern-sub-child-text']) {
        $nature_concern = $req['nature-of-concern'] . ":" . $req['nature-of-concern-sub'] . ":" . $req['nature-of-concern-sub-child-text'] . "-textarea";
      }
      if (isset($req['location-sub'])) {
        $location = implode($req['location'], ",") . ":" . implode($req['location-sub'], ",") . "," . $req['location-sub-text'] . "-textarea";
      } else {
        $location = implode($req['location'], ",") . ":" . $req['location-sub-text'] . "-textarea";
      }

      $maint_request = MaintRequest::find($id);
      $maint_request->nature_concern = $nature_concern;
      $maint_request->location = $location;
      $maint_request->remarks = $req->remarks;
      $maint_request->update();

      // Uploaded files holder
      $uploaded_files = [];

      if ($req->hasfile('canvas')) {
        foreach ($req->file('canvas') as $file) {
          $name = $file->getClientOriginalName();
          $path = 'public/maint_requests/'.\Auth::user()->branch->id.'/';
          $file->storeAs($path, $name);

          // create new files
          $maint_file = new File;
          $maint_file->file_name = $name;
          $maint_file->path = $path;
          $maint_file->maint_request_id = $maint_request->id;
          $maint_file->save();

          array_push($uploaded_files, [
            'id' => $maint_file->id,
            'maint_request_id' => $maint_file->maint_request_id,
            'file_name' => $name,
            'file_path' => 'maint_requests/'.\Auth::user()->branch->id.'/'.$name,
          ]);
        }
      }

      $response = [
        'msg' => 'Request has been successfully updated.',
        'uploaded_files' => $uploaded_files
      ];

      return response()->json($response, 200);
    }

    public function completion ($id) {
      $status = MaintRequest::where('id', $id)->pluck('status')->first();

      // abort when status is not yet approved before proceeding
      abort_if($status !== 3, 403);

      $maint_request = MaintRequest::where('id', $id)
                       ->where('user_id', \Auth::user()->id)
                       ->where('status', 3)
                       ->select('id', 'user_id', 'branch_id', 'received_by', 'remarks')
                       ->with(['user' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
                       }])
                       ->with(['received_by_user' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
                       }])
                       ->with(['files' => function ($qry) {
                        $qry->select('id',
                        'maint_request_id',
                        'file_name',
                        \DB::raw('CONCAT(TRIM(LEADING "public/" FROM path),file_name) AS file_path'));
                       }])
                       ->first();
      abort_if(!$maint_request, 403);
      return view('maint_requests.user_views.completion', compact('maint_request'));
    }

    public function completion_proceed ($id, Request $req) {
      $status = MaintRequest::where('id', $id)->pluck('status')->first();

      // abort when status is not yet approved before proceeding
      abort_if($status !== 3, 403);

      $rules = [
        'rate' => 'required',
        'remarks' => 'required',
      ];
      $messages = [
        'rate.required' => 'Please leave a star ratings.',
        'remarks.required' => 'Please leave a remarks.',
      ];
      $validator = Validator::make($req->all(), $rules, $messages);
      
      if ($validator->fails()) {
        $flash_message = [
    			'title' => 'Oops!',
    			'status' => 'danger',
    			'message' => 'Please correct all the errors below.',
    		];
        Session::flash('create_fail', $flash_message);
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
      }

      // change maint_request status to 4 = completed
      MaintRequest::find($id)->update(['status' => 4]);

      $survey = new Survey;
      $survey->rater_id = \Auth::user()->id;
      $survey->maint_request_id = $id;
      $survey->rate = $req->rate;
      $survey->remarks = $req->remarks;
      $survey->save();

      return redirect()->route('maint_requests');
      
    }

    public function file_delete ($id, $file_id) {
      $file = File::where('maint_request_id', $id)->where('id', $file_id)->delete();
      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'File has been successfully deleted.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->back();
    }

    public function trash ($id) {
      $auth_id = \Auth::user()->id;
      $maint_request = MaintRequest::where('id', $id)->first();
      
      abort_if(!$this->can_cancel_approve_delete($id, null), 403);

      if (\Auth::user()->hasPermissionTo('Escalated Delete Maintenance Requests')) {
        $maint_request = MaintRequest::where('id', $id)
                       ->where('status', 1)
                       ->firstOrFail();
      } else {
        $maint_request = MaintRequest::where('id', $id)
                       ->where('user_id', \Auth::user()->id)
                       ->where('status', 0)
                       ->firstOrFail();
      }
      
      return view('maint_requests.trash', compact('maint_request'));
    }

    public function delete ($id) {
      $auth_id = \Auth::user()->id;
      $maint_request = MaintRequest::where('id', $id)->first();

      abort_if(!$this->can_cancel_approve_delete($id, null), 403);

      if (\Auth::user()->hasPermissionTo('Escalated Delete Maintenance Requests')) {
        $redirect = redirect()->route('maint_request.approval.pending');
        MaintRequest::where('id', $id)
                      ->where('status', 1)
                      ->delete();
      } else {
        $redirect = redirect()->route('maint_requests');
        MaintRequest::where('id', $id)
                      ->where('user_id', \Auth::user()->id)
                      ->where('status', 0)
                      ->delete();
      }
      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Maintenance request has been successfully deleted.',
      ];
      Session::flash('delete_success', $flash_message);

      return $redirect;
    }



    // Admin & User
    public function view ($id) {
      $auth_id = \Auth::user()->id; //filter
      $maint_request = MaintRequest::find($id);
      
      abort_if(!$this->is_mrf_approver($id), 403);

      // filter request where auth user exists
      $maint_request = MaintRequest::where('id', $id)
             // filtered relationship
             ->with(['approvers' => function ($qry) use ($auth_id) {
               $qry->select('accesschart_id', 'user_id')
                   ->where('user_id', $auth_id);
             }])
             ->first();

      // check if auth user is approver
      $approvers = explode(",",$maint_request->approvers);
      $is_approver = $approvers[0] == '[]' ? false : true;

      if ($this->is_escalatable($id)) {
        $escalatable = true;
      } else { $escalatable = false; }

      // receive request upon viewing for the first time
      if (!$maint_request->received_by && // if no one already received
          $is_approver && // check approver if existing in request - required for 3rd condition
          $maint_request->approvers[0]->user_id == $auth_id) { // if auth user is the approver
        $maint_request->req_no = \Carbon\Carbon::now()->format('njyHis');
        $maint_request->received_by = \Auth::user()->id;
        $maint_request->date_received = \Carbon\Carbon::now();
        $maint_request->status = 1;
        $maint_request->update();
      }

      $maint_request = MaintRequest::where('id', $id)
                       ->with(['files' => function ($qry) {
                         $qry->select('id',
                         'maint_request_id',
                         'file_name',
                         \DB::raw('CONCAT(TRIM(LEADING "public/" FROM path),file_name) AS file_path'));
                       }])
                       ->with(['survey' => function ($qry) {
                        $qry->select('maint_request_id', 'rate');
                       }])
                       ->with(['received_by_user' => function ($qry) {
                         $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                       }])
                       ->with(['approved_by_user' => function ($qry) {
                         $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                       }])
                       ->first();
      $branch = $maint_request->branch->name;
      $contact = $maint_request->branch->contact ? $maint_request->branch->contact : 'None';

      return view('maint_requests.view', compact('maint_request',
                                                 'branch',
                                                 'contact',
                                                 'is_approver',
                                                 'escalatable'));
    }

    // Admin only
    public function approved_mrfs () {
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
                WHERE mr.status=3
                ORDER BY mr.id ASC');
      return view('maint_requests.admin_views.approved_mrfs.index', compact('maint_requests'));
    }

    public function view_approved ($id) {
      $maint_request = MaintRequest::where('id', $id)
                       ->with(['files' => function ($qry) {
                         $qry->select('id',
                         'maint_request_id',
                         'file_name',
                         \DB::raw('CONCAT(TRIM(LEADING "public/" FROM path),file_name) AS file_path'));
                       }])
                       ->with(['survey' => function ($qry) {
                        $qry->select('maint_request_id', 'rate');
                       }])
                       ->with(['received_by_user' => function ($qry) {
                         $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                       }])
                       ->with(['approved_by_user' => function ($qry) {
                         $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                       }])
                       ->first();
      $branch = $maint_request->branch->name;
      $contact = $maint_request->branch->contact ? $maint_request->branch->contact : 'None';

      return view('maint_requests.admin_views.approved_mrfs.view', compact('maint_request',
                                                 'branch',
                                                 'contact',
                                                 'is_approver',
                                                 'escalatable'));
    }
}
