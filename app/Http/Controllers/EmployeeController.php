<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UserEmployment as UEmployment;

use Session;

class EmployeeController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'employee_clearance']);

        // for active routing state
        \View::share('is_employee_route', true);
    }

    public function index () {
        if (\Auth::user()->hasPermissionTo('Show Employees')) {
            $employees = UEmployment::select('id', 'user_id', 'position_id', 'branch_id')
                                             ->with(['user' => function ($qry) {
                                                $qry->select('id', 'first_name', 'last_name');
                                             }])
                                             ->with(['position' => function ($qry) {
                                                $qry->select('id', 'name');
                                             }])
                                             ->with(['branch' => function ($qry) {
                                                $qry->select('id', 'name');
                                             }])
                                             ->where('branch_id', '!=', null)
                                             ->get();
        } else if (\Auth::user()->hasPermissionTo('Edit Employees')) {
            $employees = UEmployment::select('id', 'user_id', 'position_id', 'branch_id')
                                             ->with(['user' => function ($qry) {
                                                $qry->select('id', 'first_name', 'last_name');
                                             }])
                                             ->with(['position' => function ($qry) {
                                                $qry->select('id', 'name');
                                             }])
                                             ->with(['branch' => function ($qry) {
                                                $qry->select('id', 'name');
                                             }])
                                             ->where('branch_id', \Auth::user()->branch->id)
                                             ->get();
        }
        return view('employees.index', compact('employees'));
    }

    public function edit ($id) {
        $employee = UEmployment::find($id);
        return view('employees.edit', compact('employee'));
    }

    public function update (Request $req, $id) {
        $employee = UEmployment::find($id);
        $employee->remarks = $req->remarks;
        $employee->last_date_reported = $req->last_date_reported;
        $employee->update();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'Employee ' . $employee->user->first_name . ' ' . $employee->user->last_name . ' has been successfully updated.',
        ];
        Session::flash('update_success', $flash_message);

        return redirect()->route('employees.index');
    }
}
