<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\File;
use App\Seen;
use App\Company;
use App\FileSetting;
use App\UserEmployment AS UEmployment;
use App\AccessChartUserMap AS AccessUser;

use DB;
use Mail;
use Carbon;
use Session;
use Validator;
use App\Traits\AccessChartCategorizer;

class PurchaseOrderFileController extends Controller
{

    use AccessChartCategorizer;

    public function __construct () {
        $this->middleware(['auth', 'po_file_clearance']);

        // for active routing state
        \View::share('is_po_file_route', true);
    }

    public function index () {
        $access_for = 2; // 0 = otloa, 1 = mrf, 2 = po file
        $approvers = $this->approver(\Auth::user()->id, $access_for);
        $files = File::select('id',
                                     'file',
                                     'po_number',
                                     'from',
                                     'to',
                                     'company_id',
                                     'waiting_for',
                                     'status',
                                     'remarks_by',
                                     'remarks',
                                     'remarks2',
                                     'created_at')
                ->with(['from_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_company' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['officers_approved' => function ($qry) {
                   $qry->orderBy('created_at')->get();
                 }])
                ->where('customer_id', null)
                ->where('type_id', null)
                ->latest()
                ->get();
        return view('purchase_orders.files.index', compact('files', 'approvers'));
    }

    public function view () {
        $files = File::select('id',
                                     'file',
                                     'from',
                                     'to',
                                     'company_id',
                                     'remarks',
                                     'status',
                                     'created_at')
                ->with(['from_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_company' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['last_officer_approved' => function ($qry) {
                  $qry->select('po_file_id', \DB::raw('updated_at AS stamp'));
                }])
                ->with(['seen_by_users' => function ($qry) {
                  $qry->select('id', 'po_file_id', 'user_id')
                      ->with(['user' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
                      }]);
                }])
                ->where('status', 2)
                ->where(function ($qry) {
                  $qry->where('to', \Auth::user()->id)
                      ->orWhere('company_id', \Auth::user()->company_id);
                })
                ->get();
        // return Carbon\Carbon::parse($files[0]->created_at)->format('mdy-hi');
        $file_settings = FileSetting::where('user_id', \Auth::user()->id)->first();
        return view('purchase_orders.files.view', compact('files', 'file_settings'));
    }

    public function view_approved () {
      $files = File::select('id',
                            'file',
                            'from',
                            'to',
                            'company_id',
                            'remarks',
                            'status',
                            'created_at')
                ->with(['from_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_company' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->with(['last_officer_approved' => function ($qry) {
                  $qry->select('po_file_id', \DB::raw('updated_at AS stamp'));
                }])
                ->with(['seen_by_users' => function ($qry) {
                  $qry->select('id', 'po_file_id', 'user_id')
                      ->with(['user' => function ($qry) {
                        $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
                      }]);
                }])
                ->where('status', 2)
                ->get();
      return view('purchase_orders.files.view_approved', compact('files'));
    }

    public function create () {
      $companies = Company::orderBy('name', 'asc')->get();
      $users = User::select('id', 'first_name', 'last_name')->where('id', '!=', 1)->orderBy('first_name', 'asc')->get();
      return view('purchase_orders.files.create', compact('companies', 'users'));
    }

    public function store (Request $req) {
        $rules = [
          'file' => 'required|max:10240',
          'po_number' => 'required',
          'remarks' => 'required',
        ];
        $messages = [
          'file.max' => 'The file may not be greater than 10 MB.',
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

        // Store file into folder
        $req_file = $req->file('file');
        $file_name = $req_file->getClientOriginalName();
        if ($req->user) {
          $end_path = 'user/' . $req->user;
        } else {
          $end_path = 'company/' . $req->company;
        }
        $file_path = 'public/files/' . $end_path;
        $req_file->storeAs($file_path, $file_name);

        // for approval function
        $accesschart = UEmployment::where('user_id', \Auth::user()->id)
                                    ->with('po_file_accessusersmap')->first();

        $access_level = AccessUser::where('accesschart_id', $accesschart->po_file_accesschart_id)->min('access_level');

        if (!count($accesschart->po_file_accessusersmap) > 0) {
          $msg = 'You have no Approving Officer. Please contact the administrator.';
          $flash_message = [
            'title' => 'Oops!',
            'status' => 'danger',
            'message' => $msg,
          ];
          Session::flash('create_fail', $flash_message);
          return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $file = new File;
        $file->po_accesschart_id = $accesschart->po_file_accesschart_id;
        $file->po_number = $req->po_number;
        $file->file = $file_name;
        $file->from = \Auth::user()->id;
        $file->to = $req->user;
        $file->company_id = $req->company;
        $file->waiting_for = $access_level;
        $file->remarks = $req->remarks;
        $file->save();

        $flash_message = [
          'title' => 'Well done!',
          'status' => 'success',
          'message' => 'Purchase Order File has been successfully stored in our records.',
        ];
        Session::flash('create_success', $flash_message);

        if ($req->savebtn == 0) {
          return redirect()->back();
        }

        return redirect()->route('purchase_orders.files.index');
    }

    public function edit ($id) {
        $file = File::select('id', 'file', 'po_number', 'from', 'to', 'company_id', 'remarks')
                ->where('from', \Auth::user()->id)
                ->where('status', 0)
                ->where('id', $id)->firstOrFail();
        $companies = Company::select('id', 'name')->orderBy('name', 'asc')->get();
        $users = User::select('id', 'first_name', 'last_name')->where('id', '!=', 1)->orderBy('first_name', 'asc')->get();
        return view('purchase_orders.files.edit', compact('file', 'companies', 'users'));
    }

    public function edit_ajax ($id) {
        $file = File::select('to', 'company_id')
                ->where('from', \Auth::user()->id)
                ->where('status', 0)
                ->where('id', $id)->firstOrFail();
        if ($file->to) {
          $response = [
            'to' => 'user',
            'id' => $file->to,
          ];
        } else {
          $response = [
            'to' => 'company',
            'id' => $file->company_id,
          ];
        }

        return response()->json($response, 200);
    }

    public function update (Request $req, $id) {
        $file = File::where('id', $id)
                ->where('from', \Auth::user()->id)
                ->where('status', 0)
                ->firstOrFail();
        $rules = [
          'file' => 'sometimes|max:10240',
          'po_number' => 'required',
          'remarks' => 'required',
        ];
        $messages = [
          'file.max' => 'The file may not be greater than 10 MB.',
        ];
        $validator = Validator::make($req->all(), $rules, $messages);
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

        if ($req->file) {
            // Store file into folder
            $req_file = $req->file('file');
            $file_name = $req_file->getClientOriginalName();
            if ($req->user) {
              $end_path = 'user/' . $req->user;
            } else {
              $end_path = 'company/' . $req->company;
            }
            $file_path = 'uploads/files/' . $end_path;
            $req_file->storeAs($file_path, $file_name);

            // insert file
            $file->file = $file_name;
        }

        $file->po_number = $req->po_number;
        $file->from = \Auth::user()->id;
        $file->to = $req->user;
        $file->company_id = $req->company;
        $file->remarks = $req->remarks;
        $file->update();

        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'One (1) record has been successfully updated.',
        ];
        Session::flash('update_success', $flash_message);
        return redirect()->route('purchase_orders.files.index');
    }

    public function trash ($id) {
        $file = File::select('id', 'file', 'po_number', 'from', 'to', 'company_id', 'remarks')
                ->with(['from_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_user' => function ($qry) {
                  $qry->select('id', \DB::raw('CONCAT(first_name," ",last_name) AS name'));
                }])
                ->with(['to_company' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->where('from', \Auth::user()->id)
                ->where('status', 0)
                ->where('id', $id)
                ->firstOrFail();
        return view('purchase_orders.files.trash', compact('file'));
    }

    public function delete ($id) {
        $file = File::where('id', $id)
                ->where('from', \Auth::user()->id)
                ->where('status', 0)
                ->firstOrFail();
        $file->delete();
        $flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'One (1) record has been successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);
        return redirect()->route('purchase_orders.files.index');
    }

    public function download ($id) {
        $file = File::where('id', $id)->first();
        if ($file->to) {
          $path = 'user/' . $file->to;
        } else {
          $path = 'company/' . $file->company_id;
        }
        $file = $file->file;
        return response()->download(storage_path("app/public/files/{$path}/{$file}"));
    }

    public function seen ($id) {
      $ce = Seen::where('po_file_id', $id)
            ->where('user_id', \Auth::user()->id)
            ->first();
      if ($ce) {
        return response()->json('existing');
      } else {
        $seen = new Seen;
        $seen->user_id = \Auth::user()->id;
        $seen->po_file_id = $id;
        $seen->save();

        return response()->json('not existing');
      }
    }

    // AJAX STORE
    public function store_ajax (Request $req) {
        $rules = [
          'file' => 'required|max:10240',
          'remarks' => 'required',
        ];
        $messages = [
          'file.max' => 'The file may not be greater than 10 MB.',
        ];
        $validator = Validator::make($req->all(), $rules, $messages);

        if ($validator->fails()) {
          return response()->json(['validator' => $validator->errors()], 422);
        }

        // Store file into folder
        $req_file = $req->file('file');
        $file_name = $req_file->getClientOriginalName();
        if ($req->user) {
          $end_path = 'user/' . $req->user;
        } else {
          $end_path = 'company/' . $req->company;
        }
        $file_path = 'uploads/files/' . $end_path;
        $req_file->storeAs($file_path, $file_name);

        // for approval function
        $accesschart = UEmployment::where('user_id', \Auth::user()->id)
        ->with('po_file_accessusersmap')->first();

        $access_level = AccessUser::where('accesschart_id', $accesschart->mrf_accesschart_id)->min('access_level');

        if (!count($accesschart->po_file_accessusersmap) > 0) {
        $msg = 'You have no Approving Officer. Please contact the administrator.';
        return response()->json(['approver_err' => $msg], 422);
        }

        $file = new File;
        $file->po_accesschart_id = $accesschart->po_file_accesschart_id;
        $file->file = $file_name;
        $file->from = \Auth::user()->id;
        $file->to = $req->user;
        $file->company_id = $req->company;
        $file->waiting_for = $access_level;
        $file->remarks = $req->remarks;
        $file->save();

        $response = \DB::table('files')
                    ->select('files.id',
                             'files.file',
                             \DB::raw('(SELECT CONCAT(first_name," ",last_name) FROM users WHERE id=files.from) AS `from`'),
                             \DB::raw('(SELECT CONCAT(first_name," ",last_name) FROM users WHERE id=files.to) AS `to`'),
                             \DB::raw('(SELECT name FROM companies WHERE id=files.company_id) AS `company`'),
                             'files.remarks')
                    ->where('id', $file->id)
                    ->first();

        return response()->json(['response' => $response], 200);
    }
}