<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Branch;
use App\InterviewSchedule as ISched;
use App\PowerInterruption;
use App\ConnectivityTicket;

use Session;
use Validator;
use Carbon\Carbon;

class InterviewScheduleController extends Controller
{

    public function __construct() {
        $this->middleware(['auth', 'interview_schedules_clearance']);

        // for active routing state
        \View::share('is_interview_route', true);

        // check branches with connectivity problems
        $this->conn_tickets = ConnectivityTicket::where('status', 1)
                              ->whereRaw('replace(problem, " ", "") like "%noconnection%"')
                              ->select('branch_id')->get();
        $this->conn_tickets_array = [];
        foreach ($this->conn_tickets as $conn_ticket) {
          array_push($this->conn_tickets_array, $conn_ticket->branch_id);
        }

        // check branches with power interruption
        $this->power_interruptions = PowerInterruption::whereDate('datetime_from', '<=', Carbon::today())
                                                      ->whereDate('datetime_to', '>=', Carbon::today())->get();
        $this->power_interruptions_array = [];
        foreach ($this->power_interruptions as $power_interruption) {
          array_push($this->power_interruptions_array, $power_interruption->branch_id);
        }
    }

    public function index () {
        if (\Auth::user()->branch->machine_number === 103) {
          $ischeds = ISched::oldest()->get();
        } else {
          $ischeds = ISched::where('branch_id', \Auth::user()->branch->id)->latest()->get();
        }
        return view('schedules.interviews.index', compact('ischeds'));
    }

    public function create () {
        if (\Auth::user()->branch->machine_number === 103) {
            abort('403');
        } else {
            return view('schedules.interviews.create');
        }

    }

    public function store (Request $req) {
      $rules = [
          'applicant_name' => 'required',
          'contact_number' => 'required',
          'position_applying' => 'required',
          'agreement' => 'required',
          'approval_number' => 'required',
      ];
      $messages = [
          'applicant_name.required' => 'This field is required.',
          'contact_number.required' => 'This field is required.',
          'position_applying.required' => 'This field is required.',
          'agreement.required' => 'You have to agree to the ff. requirements.',
          'approval_number.required' => 'This field is required.',
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

      $requirements = [
        'Application for employment',
        'Resume',
        'Birth Certificate',
        'Exams with passing grade',
        'Background Investigation',
      ];

      $isched = new ISched;
      $isched->branch_id = $req->branch ? $req->branch : \Auth::user()->branch->id;
      $isched->added_by_admin = $req->branch ? 1 : 0;
      $isched->applicant_name = $req->applicant_name;
      $isched->contact_number = $req->contact_number;
      $isched->position_applying = $req->position_applying;
      $isched->requirements_on_hand = implode(',', $requirements);
      $isched->approval_number = $req->approval_number;
      $isched->save();

      $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Interview schedule with ' . $isched->applicant_name . ' has been successfully added into our records.',
      ];
      Session::flash('create_success', $flash_message);

      if ($req->savebtn == 0) {
        if ($req->branch) {
          return redirect()->route('interview_sched.add');
        } else {
          return redirect()->route('interview_sched.create');
        }
      } else {
        return redirect()->route('interview_scheds.index');
      }
    }

    public function edit ($id, Request $req) {
        $isched = ISched::find($id);
        abort_unless($isched->status === 0, '403');
        if ($isched->branch_id !== \Auth::user()->branch->id) {
          if (\Auth::user()->branch->machine_number === 103) {
            abort_unless($isched->added_by_admin, '403');
          } else { abort('403'); }
        }

        return view('schedules.interviews.edit', compact('isched'));
    }

    public function update ($id, Request $req) {
      $isched = ISched::find($id);
      abort_unless($isched->status === 0, '403');
      if ($isched->branch_id !== \Auth::user()->branch->id) {
        if (\Auth::user()->branch->machine_number === 103) {
          abort_unless($isched->added_by_admin, '403');
        } else { abort('403'); }
      }

      $rules = [
          'applicant_name' => 'required',
          'contact_number' => 'required',
          'position_applying' => 'required',
          'approval_number' => 'required|integer',
      ];
      $messages = [
          'applicant_name.required' => 'This field is required.',
          'contact_number.required' => 'This field is required.',
          'position_applying.required' => 'This field is required.',
          'approval_number.required' => 'This field is required.',
          'approval_number.integer' => 'This field must be a number.',
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

      $isched->applicant_name = $req->applicant_name;
      $isched->contact_number = $req->contact_number;
      $isched->position_applying = $req->position_applying;
      $isched->approval_number = $req->approval_number;
      $isched->update();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'One (1) records has been successfully updated.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('interview_scheds.index');
    }

    public function trash ($id) {
      $isched = ISched::find($id);
      abort_unless($isched->status === 0, '403');
      if ($isched->branch_id !== \Auth::user()->branch->id) {
        if (\Auth::user()->branch->machine_number === 103) {
          abort_unless($isched->added_by_admin, '403');
        } else { abort('403'); }
      }
      
      return view('schedules.interviews.trash', compact('isched'));
    }

    public function delete ($id) {
      $isched = ISched::find($id);
      abort_unless($isched->status === 0, '403');
      if ($isched->branch_id !== \Auth::user()->branch->id) {
        if (\Auth::user()->branch->machine_number === 103) {
          abort_unless($isched->added_by_admin, '403');
        } else { abort('403'); }
      }

      $applicant_name = $isched->applicant_name;
      $isched->delete();

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Interview schedule with ' . $applicant_name . ' has been successfully deleted.',
      ];
      Session::flash('delete_success', $flash_message);
      return redirect()->route('interview_scheds.index');
    }

    // Admin Only
    public function add () {
      $branches = Branch::orderBy('name', 'asc')
                  ->where('machine_number', '!=', '103')
                  ->where('machine_number', '!=', '255')
                  ->whereIn('id', $this->conn_tickets_array) // only branches without connections
                  ->orWhereIn('id', $this->power_interruptions_array) // only branches without connections
                  ->get();
      return view('schedules.interviews.admin_views.create', compact('branches'));
    }

    public function complete ($id, Request $req) {
      abort_unless(\Auth::user()->hasPermissionTo('Complete Interview Schedules'), '403');
      $isched = ISched::where('id', $id)
                ->select('id',
                         'branch_id',
                         'applicant_name',
                         'contact_number',
                         'position_applying',
                         'approval_number',
                         'status')
                ->with(['branch' => function ($qry) {
                  $qry->select('id', 'name');
                }])
                ->first();
      
      abort_unless($isched->status === 0, '403');
      return view('schedules.interviews.admin_views.complete', compact('isched'));
    }

    public function complete_proceed ($id, Request $req) {
      $validator = Validator::make($req->all(), [
        'interview_date' => 'required',
      ]);

      if ($validator->fails()) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => 'Please correct all the errors below.',
        ];
        Session::flash('update_success', $flash_message);
        return redirect()->back()->withInput()->withErrors($validator);
      }

      abort_unless(\Auth::user()->hasPermissionTo('Complete Interview Schedules'), '403');

      $isched = ISched::find($id);

      abort_unless($isched->status === 0, '403');

      if ($req->complete_btn == 1) {
        $isched->status = 1;
      } else { $isched->status = 2; }
      $isched->interview_date = $req->interview_date;
      $isched->update();

      $status = $isched->status == 1 ? 'passed' : 'failed';

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'Applicant ' . $isched->applicant_name . ' has been successfully marked as ' . $status . '.',
      ];
      Session::flash('update_success', $flash_message);
      return redirect()->route('interview_scheds.index');
    }
}
