<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use App\User;
use App\Branch;
use App\Overtime;
use App\Biometric;
use App\UserEmployment as UEmployment;

use DB;
use Excel;
use Session;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Collection;


// Koolreport
use App\Reports\EmployeeList;
use App\Reports\PieChart\EmployeePerPosition;
use App\Reports\Gauge\Gauge;

class ReportController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'report_clearance']);

        // for active routing state
        \View::share('is_report_route', true);
    }

    // START KOOLREPORTS

    public function employee_list () {
      $report = new EmployeeList;
      $report->run();
      return view('reports.employee_list', compact('report'));
    }

    public function employee_per_position () {
      $report = new EmployeePerPosition;
      $report->run();
      return view('reports.employee_per_position', compact('report'));
    }

      // for print
      public function employee_list_print () {
        $report = new EmployeeList;
        $report->run()->render();
      }
      public function employee_per_position_print () {
        $report = new EmployeePerPosition;
        return $report->run()->render();
      }

    // END KOOLREPORTS

    public function overtime (Request $req) {
        // for active routing state
        \View::share('is_overtime_route', true);

        $branch = explode(',',$req->branch);
        $branch_id = $branch[0];
        if ($branch[0] != "") {
            $branch_mn = $branch[1];
        }

        if ($branch_id != null) {
            $branch_name = Branch::where('id', $branch_id)->pluck('name')->first();
            $branch_sched = Branch::where('id', $branch_id)->with('schedule')->first();
        } else {
            $branch_name = null;
            $branch_sched = null;
        }

        if (isset($req->date_range)) {
            $date_range = explode(' - ',$req->date_range);
            $start = Carbon::parse($date_range[0])->format('Y-m-d');
            $end = Carbon::parse($date_range[1])->addDays(1)->format('Y-m-d');

            $start_display = Carbon::parse($date_range[0])->format('F d, Y');
            $end_display = Carbon::parse($date_range[1])->format('F d, Y');

            $start_date = Carbon::parse($date_range[0])->format('m/d/Y');
            $end_date = Carbon::parse($date_range[1])->format('m/d/Y');
        } else {
            $start = null;
            $end = null;

            $start_display = null;
            $end_display = null;

            $start_date = Carbon::now()->format('m/d/Y');
            $end_date = Carbon::now()->format('m/d/Y');
        }

        $branches = Branch::orderBy('name', 'asc')->get();
        if ($branch[0] != "") {
            $users = DB::select('SELECT
                                u.id AS `user_id`,
                                u.first_name,
                                u.last_name

                                FROM user_employments AS ue

                                INNER JOIN overtimes AS ot ON ue.user_id=ot.user_id
                                INNER JOIN branches AS b ON ue.branch_id=b.id
                                INNER JOIN biometrics AS bio ON ue.sss=bio.sss
                                INNER JOIN users AS u ON ot.user_id=u.id

                                WHERE b.machine_number=:mn &&
                                    DATE(ot.date_from)=DATE(bio.datetime) &&
                                    bio.datetime BETWEEN :start AND :end &&
                                    ot.status=4

                                GROUP BY u.id
                                ORDER BY u.last_name',
                                    [
                                        'mn' => $branch_mn,
                                        'start' => $start,
                                        'end' => $end
                                    ]);

            $useremployments = DB::select('SELECT
                                u.id AS `user_id`,
                                u.first_name,
                                u.last_name,
                                ue.time_from AS `ue_time_from`,
                                ue.time_to AS `ue_time_to`,
                                bs.time_from,
                                bs.time_to,
                                MIN(bio.datetime) AS `bio_datefrom`,
                                MAX(bio.datetime) AS `bio_dateto`,
                                DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s") AS `bio_timefrom`,
                                DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s") AS `bio_timeto`,
                                ot.date_from,
                                TIME(ot.date_to) AS `date_to`,
                                ROUND((TIME_TO_SEC(TIMEDIFF(
                                                        CASE
                                                            WHEN b.machine_number <> 103 THEN bs.time_from
                                                            ELSE ue.time_from
                                                        END
                                                        , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 4) AS `diff`,
                                CASE
                                    WHEN
                                        ROUND((TIME_TO_SEC(TIMEDIFF(
                                                        CASE
                                                            WHEN b.machine_number <> 103 THEN bs.time_from
                                                            ELSE ue.time_from
                                                        END
                                                        , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 1) > 0
                                    THEN
                                    CASE
                                        WHEN
                                            MOD(ROUND((TIME_TO_SEC(TIMEDIFF(
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_from
                                                                        ELSE ue.time_from
                                                                    END
                                                                    , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 4), 1) <= .8333
                                        THEN
                                        CASE
                                            WHEN
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_from
                                                                        ELSE ue.time_from
                                                                    END
                                                                    , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60) = 0
                                            THEN
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_from
                                                                        ELSE ue.time_from
                                                                    END
                                                                    , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60)
                                            ELSE
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_from
                                                                        ELSE ue.time_from
                                                                    END
                                                                    , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60) + .5
                                        END
                                        ELSE
                                            ROUND((TIME_TO_SEC(TIMEDIFF(
                                                            CASE
                                                                WHEN b.machine_number <> 103 THEN bs.time_from
                                                                ELSE ue.time_from
                                                            END
                                                            , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 0)
                                    END

                                    ELSE
                                    CASE
                                        WHEN
                                            ROUND((TIME_TO_SEC(TIMEDIFF(
                                                            CASE
                                                                WHEN b.machine_number <> 103 THEN bs.time_from
                                                                ELSE ue.time_from
                                                            END
                                                            , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 2) <= -.17
                                                            &&
                                            ROUND((TIME_TO_SEC(TIMEDIFF(
                                                            CASE
                                                                WHEN b.machine_number <> 103 THEN bs.time_from
                                                                ELSE ue.time_from
                                                            END
                                                            , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 2) >= -.49
                                        THEN -.5
                                        ELSE
                                            CASE
                                                WHEN
                                                    ROUND((TIME_TO_SEC(TIMEDIFF(
                                                            CASE
                                                                WHEN b.machine_number <> 103 THEN bs.time_from
                                                                ELSE ue.time_from
                                                            END
                                                            , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 2) <= -.50
                                                            &&
                                                    ROUND((TIME_TO_SEC(TIMEDIFF(
                                                            CASE
                                                                WHEN b.machine_number <> 103 THEN bs.time_from
                                                                ELSE ue.time_from
                                                            END
                                                            , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 2) >= -.99
                                                THEN -1.0
                                            ELSE
                                                CASE (((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"),
                                                                CASE
                                                                    WHEN b.machine_number <> 103 THEN bs.time_from
                                                                    ELSE ue.time_from
                                                                END
                                                                )) / 60) / 60) mod 1 >= .5)
                                                    WHEN true THEN
                                                        -FLOOR((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"),
                                                                            CASE
                                                                                WHEN b.machine_number <> 103 THEN bs.time_from
                                                                                ELSE ue.time_from
                                                                            END
                                                                            )) / 60) / 60) - .5
                                                    ELSE
                                                        CASE WHEN
                                                            ROUND((TIME_TO_SEC(TIMEDIFF(
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_from
                                                                        ELSE ue.time_from
                                                                    END
                                                                    , DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 2) = .50
                                                            THEN .5
                                                            ELSE
                                                                -ROUND((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"),
                                                                            CASE
                                                                                WHEN b.machine_number <> 103 THEN bs.time_from
                                                                                ELSE ue.time_from
                                                                            END
                                                                            )) / 60) / 60, 0)
                                                        END
                                                END
                                            END
                                    END
                                END AS `pre_totaltime`,
                                CASE
                                    WHEN
                                        ROUND((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"),
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_to
                                                                        ELSE ue.time_to
                                                                    END
                                                                    )) / 60) / 60, 1) >= .8333
                                    THEN
                                        CASE (((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"),
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_to
                                                                        ELSE ue.time_to
                                                                    END
                                                                    )) / 60) / 60) mod 1 >= .5)
                                            WHEN true THEN
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"),
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_to
                                                                        ELSE ue.time_to
                                                                    END
                                                                    )) / 60) / 60) + .5
                                            ELSE
                                                ROUND((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"),
                                                                    CASE
                                                                        WHEN b.machine_number <> 103 THEN bs.time_to
                                                                        ELSE ue.time_to
                                                                    END
                                                                    )) / 60) / 60, 0)
                                        END
                                    ELSE 0
                                END AS `post_totaltime`,
                                ot.reason

                                FROM user_employments AS ue
                                INNER JOIN overtimes AS ot ON ue.user_id=ot.user_id
                                INNER JOIN branches AS b ON ue.branch_id=b.id
                                LEFT JOIN branch_schedules AS bs ON b.bsched_id=bs.id
                                INNER JOIN biometrics AS bio ON ue.sss=bio.sss
                                INNER JOIN users AS u ON ot.user_id=u.id

                                WHERE b.machine_number=:mn &&
                                    DATE(ot.date_from)=DATE(bio.datetime) &&
                                    bio.datetime BETWEEN :start AND :end &&
                                    ot.status=4

                                GROUP BY u.id,
                                        u.last_name,
                                        u.first_name,
                                        ue.time_from,
                                        ue.time_to,
                                        ot.date_from,
                                        ot.date_to,
                                        ot.reason,
                                        b.machine_number,
                                        bs.time_from,
                                        bs.time_to',
                                    [
                                        'mn' => $branch_mn,
                                        'start' => $start,
                                        'end' => $end
                                    ]);
            // FORMULA FOR 10 MINUTES GRACE PERIOD
            // 50/60 = .8333
        } else {
            $useremployments = null;
        }

        // check the uploaded csv file
        $biometric_mindate = Carbon::parse(Biometric::min('datetime'))->format('F d, Y');
        $biometric_maxdate = Carbon::parse(Biometric::max('datetime'))->format('F d, Y');

        return view('overtimes.reports.index',
                compact('users',
                        'useremployments',
                        'start_date',
                        'end_date',
                        'start_display',
                        'end_display',
                        'branch_name',
                        'branch_sched',
                        'branches',
                        'biometric_mindate',
                        'biometric_maxdate'));
    }

    public function import (Request $req) {
        $rules = [
            'file' => 'required',
        ];
        $messages = [
            'file.required' => 'You must select a file to import.',
        ];
        $validator = Validator::make($req->all(), $rules, $messages);

        if ($validator->fails()) {
            $flash_message = 'Please correct the error below.';
            Session::flash('create_fail', $flash_message);
            return redirect()->back()
                             ->withErrors($validator);
        }

        Biometric::truncate();
        $csv_path = $req->file->getRealPath();
        try {
            Excel::load($csv_path, function($reader) {
                foreach ($reader->toArray() as $csv) {
                    $biometric = new Biometric;
                    $biometric->sss = $csv['no.'];
                    $biometric->status = $csv['status'];
                    $biometric->datetime = Carbon::parse($csv['datetime'])->format('y-m-d H:i:s');
                    $biometric->location = $csv['location_id'];
                    $biometric->save();
                }
            });

            // SYNC MACHINE NUMBERS
            $unsyncs = DB::table('biometrics as bio')->select('bio.sss as sss',
                                                              'b.machine_number as machine_number',
                                                              'bio.id as bio_id',
                                                              'bio.location as location')
                                                     ->join('user_employments as ue', 'ue.sss', '=', 'bio.sss')
                                                     ->join('branches as b', 'ue.branch_id', '=', 'b.id')
                                                     ->where('bio.sss', '!=', null)
                                                     ->orderBy('bio.sss', 'ASC')
                                                     ->orderBy('bio.datetime', 'ASC')
                                                     ->get();
            foreach ($unsyncs as $unsync) {
                $sync = Biometric::where('id', $unsync->bio_id)->where('sss', $unsync->sss)->first();
                $sync->location = $unsync->machine_number;
                $sync->update();
            }

            $flash_message = [
              'title' => 'Well Done!',
              'status' => 'success',
              'message' => 'Import successful.',
            ];
            Session::flash('create_success', $flash_message);
            if (Request::route()->getName() === 'report.overtime') {
              return redirect()->route('report.overtime');
            } else if(Request::route()->getName() === 'report.biometric') {
              return redirect()->route('report.biometric');
            } else if(Request::route()->getName() === 'breport.biometric') {
              return redirect()->route('breport.biometric');
            } else if(Request::route()->getName() === 'report.dtr') {
              return redirect()->route('report.dtr');
            } else if(Request::route()->getName() === 'breport.dtr') {
              return redirect()->route('breport.dtr');
            }
            return redirect()->route('report.biometric');
        } catch (\Exception $e) {
            Session::flash('create_fail', $e->getMessage());
            return redirect()->back();
        }
    }

    public function dtr (Request $req) {
        \View::share('is_dtr_route', true);

        $branch = explode(',',$req->branch);
        $branch_id = $branch[0];
        if ($branch[0] != "") {
            $branch_mn = $branch[1];
        }

        if ($branch_id != null) {
            $branch_name = Branch::where('id', $branch_id)->pluck('name')->first();
            $branch_sched = Branch::where('id', $branch_id)->with('schedule')->first();
        } else {
            $branch_name = null;
            $branch_sched = null;
        }

        if (isset($req->date_range)) {
            $date_range = explode(' - ',$req->date_range);
            $start = Carbon::parse($date_range[0])->format('Y-m-d');
            $end = Carbon::parse($date_range[1])->addDays(1)->format('Y-m-d');

            $start_display = Carbon::parse($date_range[0])->format('F d, Y');
            $end_display = Carbon::parse($date_range[1])->format('F d, Y');

            $start_date = Carbon::parse($date_range[0])->format('m/d/Y');
            $end_date = Carbon::parse($date_range[1])->format('m/d/Y');
        } else {
            $start = null;
            $end = null;

            $start_display = null;
            $end_display = null;

            $start_date = Carbon::now()->format('m/d/Y');
            $end_date = Carbon::now()->format('m/d/Y');
        }

        $datediff = strtotime($end) - strtotime($start);
        $datediff = floor($datediff/(60*60*24));

        $branches = Branch::orderBy('name', 'asc')->get();
        if ($branch[0] != "") {
            $useremployments = UEmployment::where('sss', '!=', null)
                                            ->where('department_id', '!=', null)
                                            ->where('position_id', '!=', null)
                                            ->where('payroll', $req->payroll)->get();
            // return Biometric::where('location', '103')->where('sss', '23725')->get();
            $biometrics = DB::table('biometrics as bio')->select('bio.sss',
                                                          'location',
                                                          DB::raw('GROUP_CONCAT(CASE WHEN bio.status="C/In" THEN CONCAT(TIME(bio.datetime), " ", bio.status) ELSE null END ORDER BY 1) AS time_ins'),
                                                          DB::raw('GROUP_CONCAT(CASE WHEN bio.status="C/Out" THEN CONCAT(TIME(bio.datetime), " ", bio.status) ELSE null END ORDER BY 1) AS time_outs'),
                                                          DB::raw('date(bio.datetime) AS date'))
                                                 ->join('user_employments as ue', 'ue.sss', '=', 'bio.sss')
                                                 ->where('bio.sss', '!=', null)
                                                 ->where('ue.payroll', $req->payroll)
                                                 ->where('bio.location', $branch_mn)
                                                 ->whereBetween('bio.datetime', [$start, $end])
                                                 ->groupBy('bio.sss', 'bio.location', 'date')
                                                 ->get();
            $ssss = $biometrics->pluck('sss')->unique();
            $logs = [];
            $dates = [];
            // push user info & time-in/time-out array
            foreach ($ssss as $key => $sss) {
                array_push($logs, [
                        'sss' => $sss,
                        'first_name' => null,
                        'last_name' => null,
                        'department' => null,
                        'position' => null,
                        'tito' => [],
                    ]);
            }

            // push date range & time logs array
            for ($jaa=0; $jaa < count($logs); $jaa++) {
                for ($i = 0; $i < $datediff; $i++) {
                    $date = date("Y-m-d", strtotime($start . ' + ' . $i . 'day'));
                    array_push($logs[$jaa]['tito'], [
                        'date' => $date,
                        'am_in' => null,
                        'am_out' => null,
                        'pm_in' => null,
                        'pm_out' => null,
                    ]);
                }
            }

            foreach ($logs as $key => $log) {
                foreach ($useremployments as $useremployment) {
                    if ($useremployment->sss === $log['sss']) {
                        $logs[$key]['first_name'] = $useremployment->user->first_name;
                        $logs[$key]['last_name'] = $useremployment->user->last_name;
                        $logs[$key]['department'] = $useremployment->department ? $useremployment->department->name : 'N/A';
                        $logs[$key]['position'] = $useremployment->position->name;
                        for ($jaa=0; $jaa < $datediff; $jaa++) {
                            foreach ($biometrics as $biometric) {
                                if ($biometric->sss === $log['sss']) {
                                    if ($log['tito'][$jaa]['date'] === $biometric->date) {
                                        $time_ins = explode(',', $biometric->time_ins);
                                        $time_outs = explode(',', $biometric->time_outs);

                                        // push all time in logs
                                        $counter = 0;
                                        foreach ($time_ins as $ti => $time_in) {
                                            if ($counter === 0) {
                                                $trimmed_time = str_replace(' C/In', '', $time_in);
                                                // if no time log in the morning
                                                if (strtotime($trimmed_time) > strtotime('12:00:00')) {
                                                    $logs[$key]['tito'][$jaa]['pm_in'] = str_replace(' C/In', '', $time_in);
                                                } else { // else insert to morning time log
                                                    $logs[$key]['tito'][$jaa]['am_in'] = str_replace(' C/In', '', $time_in);
                                                }
                                            }

                                            if ($counter === 1) {
                                                $logs[$key]['tito'][$jaa]['pm_in'] = str_replace(' C/In', '', $time_in);
                                            }

                                            $counter++;
                                        }

                                        // push all time out logs
                                        $counter2 = 0;
                                        foreach ($time_outs as $to => $time_out) {
                                            $trimmed_time = str_replace(' C/Out', '', $time_out);
                                            if ($counter2 === 0) {
                                                // if no time log during lunch
                                                if (strtotime($trimmed_time) > strtotime('16:00:00')) {
                                                    $logs[$key]['tito'][$jaa]['pm_out'] = str_replace(' C/Out', '', $time_out);
                                                } else { // else insert to lunch time log
                                                    $logs[$key]['tito'][$jaa]['am_out'] = str_replace(' C/Out', '', $time_out);
                                                }
                                            }

                                            if (strtotime($trimmed_time) > strtotime('12:00:00')) {
                                                if (end($time_outs)) {
                                                    $logs[$key]['tito'][$jaa]['pm_out'] = str_replace(' C/Out', '', $time_out);
                                                }
                                            }
                                            $counter2++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // return $logs;
            // return '';
            // return $logs[66]['tito'][0]['times'][0];

            // Pagination for Custom Array
            $currentPath = \Request::fullUrl();
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 4;

            $col = new Collection($logs);

            $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage, ['path' => $currentPath] );
            // $logs = new LengthAwarePaginator($currentPageSearchResults, count($logs), $perPage);
        } else {
            $useremployments = null;
        }

        // check the uploaded csv file
        $biometric_mindate = Carbon::parse(Biometric::min('datetime'))->format('F d, Y');
        $biometric_maxdate = Carbon::parse(Biometric::max('datetime'))->format('F d, Y');

        return view('dtrs.reports.index',
                compact('users',
                        'start_date',
                        'end_date',
                        'datediff',
                        'start_display',
                        'end_display',
                        'branch_name',
                        'branch_sched',
                        'branches',
                        'useremployments',
                        'biometrics',
                        'entries',
                        'biometric_mindate',
                        'biometric_maxdate'));
    }

    public function biometric (Request $req) {
        \View::share('is_biometric_route', true);
        $branch = explode(',',$req->branch);
        $branch_id = $branch[0];
        if ($branch[0] != "") {
            $branch_mn = $branch[1];
        }

        if ($branch_id != null) {
            $branch_name = Branch::where('id', $branch_id)->pluck('name')->first();
            $branch_sched = Branch::where('id', $branch_id)->with('schedule')->first();
        } else {
            $branch_name = null;
            $branch_sched = null;
        }

        if (isset($req->date_range)) {
            $date_range = explode(' - ',$req->date_range);
            $start = Carbon::parse($date_range[0])->format('Y-m-d');
            $end = Carbon::parse($date_range[1])->addDays(1)->format('Y-m-d');

            $start_display = Carbon::parse($date_range[0])->format('F d, Y');
            $end_display = Carbon::parse($date_range[1])->format('F d, Y');

            $start_date = Carbon::parse($date_range[0])->format('m/d/Y');
            $end_date = Carbon::parse($date_range[1])->format('m/d/Y');
        } else {
            $start = null;
            $end = null;

            $start_display = null;
            $end_display = null;

            $start_date = Carbon::now()->format('m/d/Y');
            $end_date = Carbon::now()->format('m/d/Y');
        }

        $datediff = strtotime($end) - strtotime($start);
        $datediff = floor($datediff/(60*60*24));

        $branches = Branch::orderBy('name', 'asc')->get();
        if ($branch[0] != "") {
            $useremployments = UEmployment::where('sss', '!=', null)->get();
            // return Biometric::where('location', '103')->where('sss', '23725')->get();
            $biometrics = DB::table('biometrics as bio')->select('bio.sss',
                                                          'bio.location',
                                                          'bio.status',
                                                          'bio.datetime',
                                                          'u.first_name',
                                                          'u.last_name')
                                                 ->join('user_employments as ue', 'ue.sss', '=', 'bio.sss')
                                                 ->join('users as u', 'u.id', '=', 'ue.user_id')
                                                 ->where('bio.sss', '!=', null)
                                                 ->where('bio.location', $branch_mn)
                                                 ->whereBetween('bio.datetime', [$start, $end])
                                                 ->orderBy('bio.sss', 'ASC')
                                                 ->orderBy('bio.datetime', 'ASC')
                                                 ->get();
        } else {
            $useremployments = null;
        }

        // check the uploaded csv file
        $biometric_mindate = Carbon::parse(Biometric::min('datetime'))->format('F d, Y');
        $biometric_maxdate = Carbon::parse(Biometric::max('datetime'))->format('F d, Y');

        return view('biometrics.reports.index',
                compact('users',
                        'start_date',
                        'end_date',
                        'datediff',
                        'start_display',
                        'end_display',
                        'branch_name',
                        'branch_sched',
                        'branches',
                        'useremployments',
                        'biometrics',
                        'biometric_mindate',
                        'biometric_maxdate'));
    }
}
