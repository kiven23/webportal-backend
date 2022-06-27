<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UserEmployment as UEmployment;
use App\AccessChart;
use App\Department;
use App\Division;
use App\Position;
use App\Customer;
use App\UserCustomerMap;
use App\Branch;
use App\User;

use Carbon\Carbon;
use Validator;
use Session;
use Excel;

class UserEmploymentController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'user_employment_clearance']);     

        // for active routing state
        \View::share('is_employment_route', true);
    }

    public function index () {
        $employment_details = UEmployment::select('id',
      	                                          'sss',
                                                  'payroll',
                                                  'user_id',
                                                  'branch_id',
                                                  'position_id',
                                                  'department_id',
                                                  'division_id',
                                                  'accesschart_id',
                                                  'mrf_accesschart_id',
                                                  'po_file_accesschart_id')
                             ->with(['user' => function ($qry) {
                                $qry->select('id', 'first_name', 'last_name');
                             }])
                             ->with(['branch' => function ($qry) {
                                $qry->select('id', 'name');
                             }])
                             ->with(['division' => function ($qry) {
                                $qry->select('id', 'name');
                             }])
                             ->with(['department' => function ($qry) {
                                $qry->select('id', 'division_id', 'name')
                                    ->with(['division' => function ($qry) {
                                        $qry->select('id', 'name');
                                    }]);
                             }])
                             ->with(['position' => function ($qry) {
                                $qry->select('id', 'name');
                             }])
                             ->with(['accesschart' => function ($qry) {
                                $qry->select('id', 'name');
                             }])
                             ->with(['mrf_accesschart' => function ($qry) {
                                $qry->select('id', 'name');
                             }])
                             ->with(['po_file_accesschart' => function ($qry) {
                                $qry->select('id', 'name');
                             }])
                             ->get();
      	return view('users.employment_details.index', compact('employment_details'));
    }

    public function edit ($id) {
      	$employment_detail = UEmployment::find($id);
      	$divisions = Division::orderBy('name', 'asc')->get();
      	$departments = Department::orderBy('name', 'asc')->get();
      	$positions = Position::orderBy('name', 'asc')->get();
        $branches = Branch::orderBy('name', 'asc')->get();

        // 0 = OTLOA, 1 = MRF, 2 = PO FILE
      	$otloa_access_charts = AccessChart::orderBy('name', 'asc')->where('access_for', 0)->get();
      	$mrf_access_charts = AccessChart::orderBy('name', 'asc')->where('access_for', 1)->get();
      	$po_file_access_charts = AccessChart::orderBy('name', 'asc')->where('access_for', 2)->get();

        // get previous user employment id
        $previous = UEmployment::where('id', '<', $id)->max('id');
        // get next user employment id
        $next = UEmployment::where('id', '>', $id)->min('id');

        return view('users.employment_details.edit',
        			 compact('employment_detail',
                            'divisions',
                            'departments',
                            'positions',
                            'branches',
                            'otloa_access_charts',
                            'mrf_access_charts',
                            'po_file_access_charts',
                            'next',
                            'previous'));
    }

    public function update ($id, Request $req) {
        $rules = [
            'sss' => 'required|unique:user_employments,sss,' . $id,
        ];
        $messages = [
            'sss.unique' => 'SSS is a duplicate. Please select another.',
        ];
        $validator = Validator::make($req->all(), $rules, $messages);

        // TIME FROM & TIME TO REQUIRED IF ADMINISTRATION
        if (!$req->branch_select && $req->branch_input == '') {
            $branch_select = explode(',', $req->branch_select[0]);
            $branch_id = $branch_select[0];
            $branch_mn = $branch_select[1];
            if ($branch_mn == 103) {
                if ($req->time_from == '') {
                    $timefrom = 'The Time from input is required.';
                    $validator->after(function ($validator) use ($timefrom) {
                        $validator->getMessageBag()->add('time_from', $timefrom);
                    });
                }

                if ($req->time_to == '') {
                    $timeto = 'The Time to input is required.';
                    $validator->after(function ($validator) use ($timeto) {
                        $validator->getMessageBag()->add('time_to', $timeto);
                    });
                }
            }
        }

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
        
        // DIVISION
        if (!is_numeric($req->division)) {
          $division = new Division;
          $division->name = $req->division;
          $division->save();
          $division = $division->id;
        } else { $division = $req->division; }

        // DEPARTMENT
        if (!is_numeric($req->department)) {
          $department = new Department;
          $department->name = $req->department;
          $department->save();
          $department = $department->id;
        } else { $department = $req->department; }

        // POSITION
        if (!is_numeric($req->position)) {
          $position = new Position;
          $position->name = $req->position;
          $position->save();
          $position = $position->id;
        } else { $position = $req->position; }

        $employmentdetail = UEmployment::find($id);
        $employmentdetail->division_id = $division;
        $employmentdetail->department_id = $department;
        $employmentdetail->position_id = $position;
        // BRANCH
        if ($req->branch_select) {
            $branch_select = explode(',', $req->branch_select[0]);
            $branch_id = $branch_select[0];
            $branch_mn = $branch_select[1];
            $employmentdetail->branch_id = $branch_id;

            // Update users table
            $user = User::find($employmentdetail->user_id);
            $user->branch_id = $branch_id;
            $user->update();
        } else {
            $employmentdetail->branch_id = $branch->id;

            // Update users table
            $user = User::find($employmentdetail->user_id);
            $user->branch_id = $branch->id;
            $user->update();
        }
        $employmentdetail->accesschart_id = $req->otloa_access_chart == 0 ? null : $req->otloa_access_chart;
        $employmentdetail->mrf_accesschart_id = $req->mrf_access_chart == 0 ? null : $req->mrf_access_chart;
        $employmentdetail->po_file_accesschart_id = $req->po_file_access_chart == 0 ? null : $req->po_file_access_chart;
        $employmentdetail->sss = $req->sss;
        $employmentdetail->division_id = $req->division;
        $employmentdetail->payroll = $req->payroll;
        $employmentdetail->time_from = Carbon::parse($req->time_from)->format('H:i:s');
        $employmentdetail->time_to = Carbon::parse($req->time_to)->format('H:i:s');
    	  $employmentdetail->update();

      	$flash_message = [
            'title' => 'Well done!',
            'status' => 'success',
            'message' => 'Employee with ID# ' . $id . ' has been successfully updated.',
        ];
      	Session::flash('update_success', $flash_message);
      	return redirect()->route('employment_details.index');
    }

    public function upload_customer ($id, Request $req) {
      $rules = [
          'customer_file' => 'required',
      ];
      $messages = [
          'customer_file.required' => 'You must select a file to import.',
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
                           ->withErrors($validator);
      }

      $duplicates = [];

      $csv_path = $req->customer_file->getRealPath();
      try {
        Excel::load($csv_path, function($reader) use ($id, $duplicates) {
          foreach ($reader->toArray() as $csv) {
            $cust_name = $csv['customername'];
            $full_name = explode(", ", $cust_name);
            $family_name = $full_name[0];
            $fm_name = explode(" ", $full_name[1]);
            $first_name = $fm_name[0];
            $middle_name = isset($fm_name[count($fm_name) - 1]) ? $fm_name[count($fm_name) - 1] : '';

            $addresses = array(
              $csv['street'],
              $csv['brgy'],
              $csv['city'],
              $csv['province']
            );

            $contact_number = $csv['cellular'] ? "0" . $csv['cellular'] : null;
            $birthday = $csv['birthday'] ? $csv['birthday'] : null;
            $complete_address = implode(" ", $addresses);
            $user_employment = UEmployment::select('user_id', 'branch_id')->where('id', $id)->first();
            
            $existing_customer = Customer::select(
                                  'id',
                                  \DB::raw('CONCAT(first_name, " ", last_name) AS name'),
                                  'contact_number'
                                 )
                                 ->where('contact_number', $contact_number)
                                 ->first();

            if (isset($existing_customer)) {
              array_push($duplicates, $existing_customer);

              // update customer
              $customer_id = $existing_customer->id;
              $customer = Customer::find($customer_id);
              $customer->branch_id = $user_employment->branch_id;
              $customer->update();
            } else {
              if ($contact_number != null) {
                $customer = new Customer;
                $customer->branch_id = $user_employment->branch_id;
                $customer->first_name = $first_name;
                $customer->last_name = $family_name;
                $customer->middle_name = $middle_name;
                $customer->contact_number = $contact_number;
                $customer->address = $complete_address;
                $customer->birth_date = $birthday;
                $customer->save();

                $customer_id = $customer->id;
              }
            }

            $existing_user_customer = UserCustomerMap::where('user_id', $user_employment->user_id)
                                      ->where('customer_id', $customer_id)
                                      ->first();

            if (!isset($existing_user_customer)) {
              $user_customer_map = new UserCustomerMap;
              $user_customer_map->user_id = $user_employment->user_id;
              $user_customer_map->customer_id = $customer_id;
              $user_customer_map->save();
            }
          }
          Session::flash('duplicates', $duplicates);
        });
        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Import Successful.',
        ];
        Session::flash('create_success', $flash_message);
        return redirect()->route('employment_details.index');
      } catch (\Exception $e) {
        $flash_message = [
          'title' => 'Oops!',
          'status' => 'danger',
          'message' => $e->getMessage(),
        ];
        Session::flash('create_fail', $flash_message);
        return redirect()->back();
      }
    }

















    // api
    public function all () {
    	$employments = UEmployment::select('id',
                          'user_id',
                          'position_id',
                          'department_id',
                          'branch_id',
                          'sss',
                          'payroll',
                          'time_from',
                          'time_to',
                          \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ',TIME_FORMAT(time_to, '%h:%i %p')) AS time"),
                          'remarks',
                          'last_date_reported')
             ->with(['user' => function ($qry) {
            	 $qry->select('id', \DB::raw("CONCAT(first_name,' ',last_name) AS full_name"));
             }])
             ->with(['position' => function ($qry) {
            	 $qry->select('id', 'name');
             }])
             ->with(['department' => function ($qry) {
            	 $qry->select('id', 'name');
             }])
             ->with(['branch' => function ($qry) {
            	 $qry->select('id', 'name');
             }])
             ->get();
    	return response()->json($employments, 200);
    }

    public function update_api (Request $req) {
      $validator = Validator::make($req->all(), [
        'sss' => 'required|unique:user_employments,sss,'.$req->id,
        'payroll' => 'required',
        'branch' => 'required',
        'position' => 'required',
        'department' => 'required',
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

      $employment = UEmployment::find($req->id);
      $employment->sss = $req->sss;
      $employment->payroll = $req->payroll;
      $employment->branch_id = $req->branch;
      $employment->position_id = $req->position;
      $employment->department_id = $req->department;
      $employment->time_from = $req->time_from;
      $employment->time_to = $req->time_to;
      $employment->update();

      // update user table: branch_id
      $user = User::where('id', $employment->user_id)->first();
      $user->branch_id = $employment->branch_id;
      $user->update();

      $updatedEmployment = UEmployment::select('id',
                          'user_id',
                          'position_id',
                          'department_id',
                          'branch_id',
                          'sss',
                          'payroll',
                          'time_from',
                          'time_to',
                          \DB::raw("CONCAT(TIME_FORMAT(time_from, '%h:%i %p'),' - ',TIME_FORMAT(time_to, '%h:%i %p')) AS time"),
                          'remarks',
                          'last_date_reported')
       ->with(['user' => function ($qry) {
      	 $qry->select('id', \DB::raw("CONCAT(first_name,' ',last_name) AS full_name"));
       }])
       ->with(['position' => function ($qry) {
      	 $qry->select('id', 'name');
       }])
       ->with(['department' => function ($qry) {
      	 $qry->select('id', 'name');
       }])
       ->with(['branch' => function ($qry) {
      	 $qry->select('id', 'name');
       }])
       ->where('id', $employment->id)
       ->first();

      return response()->json($updatedEmployment, 200);
    }
}
