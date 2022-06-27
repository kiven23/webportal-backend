<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use App\Branch;
use App\Overtime;
use App\Biometric;
use App\UserEmployment as UEmployment;

class BranchReportController extends Controller
{

    public function __construct () {
        $this->middleware(['auth', 'report_clearance']);
    }

    public function overtime (Request $req) {
        // for active routing state
        \View::share('is_overtime_route', true);
        
        $branch = Auth::user()->employment->branch->name;
      	$branch_id = Auth::user()->employment->branch->id;
      	$branch_num = Auth::user()->employment->branch->machine_number;

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
                                        'mn' => $branch_num,
                                        'start' => $start,
                                        'end' => $end
                                    ]);

            $useremployments = DB::select('SELECT
                                u.id AS `user_id`,
                                u.first_name,
                                u.last_name,
                                MIN(bio.datetime) AS `bio_datefrom`,
                                MAX(bio.datetime) AS `bio_dateto`,
                                DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s") AS `bio_timefrom`,
                                DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s") AS `bio_timeto`,
                                bs.time_from,
                                bs.time_to,
                                ot.date_from,
                                TIME(ot.date_to) AS `date_to`,
                                CASE
                                    WHEN
                                        ROUND((TIME_TO_SEC(TIMEDIFF(bs.time_from, DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 1) >= .8333
                                    THEN
                                        CASE
                                            WHEN
                                                (CASE (ROUND((TIME_TO_SEC(TIMEDIFF(bs.time_from, DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 1) mod 1 >= .5)
                                                    WHEN true THEN
                                                        FLOOR((TIME_TO_SEC(TIMEDIFF(bs.time_from, DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60) + .5
                                                    ELSE
                                                        FLOOR((TIME_TO_SEC(TIMEDIFF(bs.time_from, DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60)
                                                END) != .5
                                            THEN
                                                CASE (ROUND((TIME_TO_SEC(TIMEDIFF(bs.time_from, DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60, 1) mod 1 >= .5)
                                                WHEN true THEN
                                                    FLOOR((TIME_TO_SEC(TIMEDIFF(bs.time_from, DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60) + .5
                                                ELSE
                                                    FLOOR((TIME_TO_SEC(TIMEDIFF(bs.time_from, DATE_FORMAT(MIN(bio.datetime),"%H:%i:%s"))) / 60) / 60)
                                                END
                                            ELSE 1
                                        END
                                END AS `pre_totaltime`,
                                CASE
                                    WHEN
                                        (CASE (ROUND((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"), bs.time_to)) / 60) / 60, 1) mod 1 >= .5)
                                            WHEN true THEN
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"), bs.time_to)) / 60) / 60) + .5
                                            ELSE
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"), bs.time_to)) / 60) / 60)
                                        END) != .5
                                    THEN
                                        CASE (((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"), bs.time_to)) / 60) / 60) mod 1 >= .5)
                                            WHEN true THEN
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"), bs.time_to)) / 60) / 60) + .5
                                            ELSE
                                                FLOOR((TIME_TO_SEC(TIMEDIFF(DATE_FORMAT(MAX(bio.datetime),"%H:%i:%s"), bs.time_to)) / 60) / 60)
                                        END
                                    ELSE 1
                                END AS `post_totaltime`,
                                ot.reason

                                FROM user_employments AS ue

                                INNER JOIN overtimes AS ot ON ue.user_id=ot.user_id
                                INNER JOIN branches AS b ON ue.branch_id=b.id
                                INNER JOIN branch_schedules AS bs ON b.bsched_id=bs.id
                                INNER JOIN biometrics AS bio ON ue.sss=bio.sss
                                INNER JOIN users AS u ON ot.user_id=u.id

                                WHERE b.machine_number=:mn &&
                                    DATE(ot.date_from)=DATE(bio.datetime) &&
                                    bio.datetime BETWEEN :start AND :end &&
                                    ot.status=4

                                GROUP BY u.id,
                                        u.last_name,
                                        u.first_name,
                                        ot.date_from,
                                        ot.date_to,
                                        ot.reason,
                                        bs.time_from,
                                        bs.time_to',
                                    [
                                        'mn' => $branch_num,
                                        'start' => $start,
                                        'end' => $end
                                    ]);
            // FORMULA FOR 10 MINUTES GRACE PERIOD
            // 50/60 = .8333

        // check the uploaded csv file
        $biometric_mindate = Carbon::parse(Biometric::min('datetime'))->format('F d, Y');
        $biometric_maxdate = Carbon::parse(Biometric::max('datetime'))->format('F d, Y');

    	  return view('overtimes.branch_reports.index',
			         compact('users',
                             'useremployments',
                             'start_date',
                             'end_date',
          					 'start_display',
          					 'end_display',
                             'branch',
		                     'branch_sched',
                             'biometric_mindate',
                             'biometric_maxdate'));
    }

    public function dtr (Request $req) {
        \View::share('is_dtr_route', true);
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

        $branch = Branch::where('id', \Auth::user()->branch_id)->first();

        $useremployments = UEmployment::where('sss', '!=', null)
                                        ->where('branch_id', $branch->id)
                                        ->where('department_id', '!=', null)
                                        ->where('position_id', '!=', null)
                                        ->where('payroll', $req->payroll)->get();
        $biometrics = DB::table('biometrics as bio')->select('bio.sss',
                                                      'location',
                                                      DB::raw('GROUP_CONCAT(CASE WHEN bio.status="C/In" THEN CONCAT(TIME(bio.datetime), " ", bio.status) ELSE null END ORDER BY 1) AS time_ins'),
                                                      DB::raw('GROUP_CONCAT(CASE WHEN bio.status="C/Out" THEN CONCAT(TIME(bio.datetime), " ", bio.status) ELSE null END ORDER BY 1) AS time_outs'),
                                                      DB::raw('date(bio.datetime) AS date'))
                                             ->join('user_employments as ue', 'ue.sss', '=', 'bio.sss')
                                             ->where('bio.sss', '!=', null)
                                             ->where('ue.payroll', $req->payroll)
                                             ->where('bio.location', $branch->machine_number)
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
                                                // $logs[$key]['tito'][$jaa]['pm_out'] = str_replace(' C/Out', '', $time_out);
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

        // check the uploaded csv file
        $biometric_mindate = Carbon::parse(Biometric::min('datetime'))->format('F d, Y');
        $biometric_maxdate = Carbon::parse(Biometric::max('datetime'))->format('F d, Y');

        return view('dtrs.branch_reports.index',
                compact('start_date',
                        'end_date',
                        'datediff',
                        'start_display',
                        'end_display',
                        'branch',
                        'useremployments',
                        'biometrics',
                        'entries',
                        'biometric_mindate',
                        'biometric_maxdate'));
    }

    public function biometric (Request $req) {
        \View::share('is_biometric_route', true);
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

        $branch = Branch::where('id', \Auth::user()->branch_id)->first();

        $biometrics = DB::table('biometrics as bio')->select('bio.sss',
                                                          'bio.location',
                                                          'bio.status',
                                                          'bio.datetime',
                                                          'u.first_name',
                                                          'u.last_name')
                                                 ->join('user_employments as ue', 'ue.sss', '=', 'bio.sss')
                                                 ->join('users as u', 'u.id', '=', 'ue.user_id')
                                                 ->where('bio.sss', '!=', null)
                                                 ->where('bio.location', $branch->machine_number)
                                                 ->whereBetween('bio.datetime', [$start, $end])
                                                 ->orderBy('bio.sss', 'ASC')
                                                 ->orderBy('bio.datetime', 'ASC')
                                                 ->get();

        // check the uploaded csv file
        $biometric_mindate = Carbon::parse(Biometric::min('datetime'))->format('F d, Y');
        $biometric_maxdate = Carbon::parse(Biometric::max('datetime'))->format('F d, Y');

        return view('biometrics.branch_reports.index',
                compact('start_date',
                        'end_date',
                        'datediff',
                        'start_display',
                        'end_display',
                        'branch',
                        'biometrics',
                        'biometric_mindate',
                        'biometric_maxdate'));
    }
}
