<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\File;
use App\User;
use App\Company;
use App\FileType;

use Session;
use Validator;

class PurchasingReportController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'purch_report_clearance']);

      // for active routing state
      \View::share('is_purch_report_route', true);
    }

    public function index () {
      $files = File::select('id',
                            'file',
                            'from',
                            'to',
                            'company_id',
                            'type_id',
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
                 ->with(['type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                ->where('customer_id', null)
                ->where('type_id', '!=', null)
                ->latest()
                ->get();
      return view('reports.purchasing_reports.index', compact('files'));
    }

    public function view () {
      $files = File::select('id',
                            'file',
                            'from',
                            'to',
                            'company_id',
                            'type_id',
                            'remarks',
                            'remarks2')
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
                 ->with(['type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                ->where('customer_id', null)
                ->where('type_id', '!=', null)
                ->where(function ($qry) {
                  $qry->where('to', \Auth::user()->id)
                      ->orWhere('company_id', \Auth::user()->company_id);
                })
                ->latest()
                ->get();
      return view('reports.purchasing_reports.view', compact('files'));
    }

    public function create () {
      $users = User::select('id', 'first_name', 'last_name')->get();
      $companies = Company::select('id', 'name')->get();
      $file_types = FileType::select('id', 'name')->get();
      return view('reports.purchasing_reports.create', compact('users', 'companies', 'file_types'));
    }

    public function store (Request $req) {
      $rules = [
        'files' => 'required|max:10240',
        'remarks' => 'required',
      ];
      $messages = [
        'files.max' => 'The file may not be greater than 10 MB.',
      ];
      $validator = Validator::make($req->all(), $rules, $messages);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('create_fail', $flash_message);
        return redirect()->back()->withInput()->withErrors($validator);
      }

      if (!is_numeric($req->type)) {
        $file_type = new FileType;
        $file_type->name = $req->type;
        $file_type->save();
        $type = $file_type->id;
      } else { $type = $req->type; }

      // Store file into folder
      $file_names = [];
      $req_files = $req->file('files');
      foreach ($req_files as $req_file) {
        $file_name = $req_file->getClientOriginalName();
        if ($req->user) {
          $end_path = 'user/' . $req->user;
        } else {
          $end_path = 'company/' . $req->company;
        }
        $file_path = 'public/files/' . $end_path;
        $req_file->storeAs($file_path, $file_name);

        array_push($file_names, $file_name);
      }

      $file = new File;
      $file->type_id = $type;
      $file->file = implode(",", $file_names);
      $file->from = \Auth::user()->id;
      $file->to = $req->user;
      $file->company_id = $req->company;
      $file->remarks = $req->remarks;
      $file->save();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'New record has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);

      if ($req->savebtn == 0) {
        return redirect()->route('report.purchasing.create');
      } else { return redirect()->route('report.purchasing.index'); }
    }

    public function edit ($id) {
      $file = File::select('id',
                            'file',
                            'from',
                            'to',
                            'company_id',
                            'type_id',
                            'remarks',
                            'remarks2')
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
                 ->with(['type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                ->where('from', \Auth::user()->id)
                ->where('id', $id)
                ->where('customer_id', null)
                ->where('type_id', '!=', null)
                ->firstOrFail();

      $users = User::select('id', 'first_name', 'last_name')->get();
      $companies = Company::select('id', 'name')->get();
      $file_types = FileType::select('id', 'name')->get();
      return view('reports.purchasing_reports.edit', compact('file', 'users', 'companies', 'file_types'));
    }

    public function edit_ajax ($id) {
      $file = File::select('to', 'company_id')
              ->where('from', \Auth::user()->id)
              ->where('type_id', '!=', null)
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

    public function update ($id, Request $req) {
      $file = File::select('id', 'to', 'company_id')
              ->where('from', \Auth::user()->id)
              ->where('type_id', '!=', null)
              ->where('id', $id)->firstOrFail();
      $rules = [
        'file' => 'sometimes|max:10240',
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

      $file->type_id = $req->type;
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
      return redirect()->route('report.purchasing.index');
    }

    public function download ($id, $file_name) {
      $file = File::where('id', $id)->first();
      if ($file->to) {
        $path = 'user/' . $file->to;
      } else {
        $path = 'company/' . $file->company_id;
      }
      return response()->download(storage_path("app/public/files/{$path}/{$file_name}"));
    }

    public function trash ($id) {
      $file = File::select('id', 'file', 'from', 'to', 'company_id', 'remarks', 'type_id')
              ->with(['from_user' => function ($qry) {
                $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
              }])
              ->with(['type' => function ($qry) {
                $qry->select('id', 'name');
              }])
              ->with(['to_user' => function ($qry) {
                $qry->select('id', \DB::raw('CONCAT(first_name, " ", last_name) AS name'));
              }])
              ->with(['to_company' => function ($qry) {
                $qry->select('id', 'name');
              }])
              ->where('id', $id)
              ->first();
      return view('reports.purchasing_reports.trash', compact('file'));
    }

    public function delete ($id) {
      $file = File::find($id);
      $file_name = $file->file;
      $file->delete();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => $file_name . ' has been successfully delete from our records.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->route('report.purchasing.index');
    }

    public function subreport ($id) {
      // for active routing state
      \View::share('is_purch_subreport_route', $id);

      $file_type = FileType::select('name')->where('id', $id)->pluck('name')->first();
      $files = File::select('id',
                            'file',
                            'from',
                            'to',
                            'company_id',
                            'type_id',
                            'remarks',
                            'remarks2')
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
                 ->with(['type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                ->where('customer_id', null)
                ->where('type_id', $id)
                ->latest()
                ->get();
      return view('reports.purchasing_reports.index', compact('files', 'file_type'));
    }

    public function view_subreport ($id) {
      // for active routing state
      \View::share('is_purch_subreport_route', $id);

      $file_type = FileType::select('name')->where('id', $id)->pluck('name')->first();
      $files = File::select('id',
                            'file',
                            'from',
                            'to',
                            'company_id',
                            'type_id',
                            'remarks',
                            'remarks2')
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
                 ->with(['type' => function ($qry) {
                    $qry->select('id', 'name');
                  }])
                ->where('customer_id', null)
                ->where('type_id', $id)
                ->where(function ($qry) {
                  $qry->where('to', \Auth::user()->id)
                      ->orWhere('company_id', \Auth::user()->company_id);
                })
                ->latest()
                ->get();
      return view('reports.purchasing_reports.view', compact('files', 'file_type'));
    }
}
