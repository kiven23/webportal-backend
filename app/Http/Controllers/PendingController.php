<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use App\Pending;
use App\Branch;
use App\Region;
use App\User;
use App\PowerInterruption;
use App\ConnectivityTicket;
use Validator;
use Toastr;
use DB;
use Carbon\Carbon;
use Excel;
use Session;

// For Chart
use Khill\Lavacharts\Lavacharts;

class PendingController extends Controller
{

    public function __construct() {
        // Middleware
        $this->middleware(['auth', 'pending_clearance']);

        // for active routing state
        \View::share('is_pending_route', true);

        // Pending schedule
        $this->dtNow = '%' . Carbon::now()->format('Y-m-d') . '%';
        $this->dtPrev = '%' . Carbon::now()->addDay(-1)->format('Y-m-d') . '%';
        $this->time = [
            'now' => Carbon::now()->format('H:i:s'),
            'start' => Carbon::parse('14:00:00')->format('H:i:s'),
            'end' => Carbon::parse('23:00:00')->format('H:i:s'),
        ];

        if ($this->time['now'] > $this->time['start'] && $this->time['now'] < $this->time['end']) {
            $this->inBetween = 1;
        } else { $this->inBetween = 0; }

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

    public function index (Request $request) {
      	if (Auth::user()->branch->machine_number === 103) {
            $dt = Carbon::now()->setTimezone('Asia/Singapore')->format('Y-m-d');
            $date = '%' . $dt .'%';

            $regions = Region::with(['branches.pendings' => function($query) use ($date) {
                $query->where('created_at', 'LIKE', $date);
            }])
            ->with(['branches' => function ($qry) {
                $qry->where('name', '!=', 'TEST BRANCH');
            }])->get();

            $grandTotal = DB::select('SELECT * FROM pendings WHERE created_at LIKE :id', ['id' => $date]);
            $gt = collect($grandTotal);

            $grace_period = $this->time['start'];

            $conn_tickets_array = $this->conn_tickets_array;
            $power_interruptions_array = $this->power_interruptions_array;
            return view('pendings.index', compact('regions',
                                                  'gt',
                                                  'grace_period',
                                                  'conn_tickets_array',
                                                  'power_interruptions_array'));
      	} else {
            $dtNow = $this->dtNow;
            $dtPrev = $this->dtPrev;
            $time = $this->time;
            $inBetween = $this->inBetween;
            $grace_period = $this->time['start'];

            // $user = User::find(Auth::user()->id);
            $prevPendings = Pending::where('branch_id', '=', Auth::user()->branch->id)
                                ->where('created_at', 'LIKE', $dtPrev)
                                ->orderBy('docdate', 'asc')
                                ->get();
            $pendings = Pending::where('branch_id', '=', Auth::user()->branch->id)
                                ->where('created_at', 'LIKE', $dtNow)
                                ->orderBy('docdate', 'asc')
                                ->get();
            if ($inBetween == 1) {
                // return 'inbetween = 1';
                return view('pendings.index2', compact('pendings','prevPendings','inBetween'));
            } else {
                // return 'inbetween = 2';
                return view('pendings.index3', compact('prevPendings', 'grace_period'));
            }
      	}
    }

    public function ci () {
        if (Auth::user()->branch->machine_number === 103) {
            $dt = Carbon::now()->setTimezone('Asia/Singapore')->format('Y-m-d');
            $date = '%' . $dt .'%';

            $regions = Region::with(['branches.pendings' => function($query) use ($date) {
                $query->where('created_at', 'LIKE', $date);
            }])
            ->with(['branches' => function ($qry) {
                $qry->where('name', '!=', 'TEST BRANCH');
            }])->get();
            $grandTotal = DB::select('SELECT * FROM pendings WHERE created_at LIKE :id', ['id' => $date]);
            $gt = collect($grandTotal);

            $grace_period = $this->time['start'];

            $conn_tickets_array = $this->conn_tickets_array;
            $power_interruptions_array = $this->power_interruptions_array;
            return view('pendings.ci', compact('regions',
                                               'gt',
                                               'grace_period',
                                               'conn_tickets_array',
                                               'power_interruptions_array'));
        } else {
            abort('401');
        }
    }

    public function show (Request $request) {
        if (Auth::user()->branch->machine_number === 103) {
            $filterdate = '%' . $request->filterdate . '%';
            $filterdate2 = Carbon::parse($request->filterdate)->format('F d, Y');

            $regions = Region::with(['branches.pendings' => function($query) use ($filterdate) {
                $query->where('created_at', 'LIKE', $filterdate);
            }])
            ->with(['branches' => function ($qry) {
                $qry->where('name', '!=', 'TEST BRANCH');
            }])->get();
            $grandTotal = DB::select('SELECT
                                      SUM(por) as por,
                                      SUM(ci) as ci,
                                      SUM(ch) as ch,
                                      SUM(dep) as dep,
                                      SUM(cla) as cla,
                                      SUM(grpo) as grpo,
                                      SUM(si) as si,
                                      SUM(so) as so,
                                      SUM(sts) as sts,
                                      SUM(disb) as disb,
                                      SUM(arcm) as arcm,
                                      SUM(apcm) as apcm,
                                      SUM(pint) as pint,
                                      SUM(rc_cash) as rc_cash,
                                      SUM(sc) as sc
                                      FROM pendings
                                      WHERE created_at LIKE :id', ['id' => $filterdate]);
            $gt = collect($grandTotal);
            
            $grace_period = $this->time['start'];
            $conn_tickets_array = $this->conn_tickets_array;
            $power_interruptions_array = $this->power_interruptions_array;
            return view('pendings.index', compact('regions',
                                                  'gt',
                                                  'filterdate2',
                                                  'grace_period',
                                                  'conn_tickets_array',
                                                  'power_interruptions_array'));
        } else {
            abort('401');
        }
    }

    public function show_ci (Request $request) {
        if (Auth::user()->branch->machine_number === 103) {
            $filterdate = '%' . $request->filterdate . '%';
            $filterdate2 = Carbon::parse($request->filterdate)->format('F d, Y');

            $regions = Region::with(['branches.pendings' => function($query) use ($filterdate) {
                $query->where('created_at', 'LIKE', $filterdate);
            }])
            ->with(['branches' => function ($qry) {
                $qry->where('name', '!=', 'TEST BRANCH');
            }])->get();
            $grandTotal = DB::select('SELECT
                                      SUM(por) as por,
                                      SUM(ci) as ci,
                                      SUM(ch) as ch,
                                      SUM(dep) as dep,
                                      SUM(cla) as cla,
                                      SUM(grpo) as grpo,
                                      SUM(si) as si,
                                      SUM(so) as so,
                                      SUM(sts) as sts,
                                      SUM(disb) as disb,
                                      SUM(arcm) as arcm,
                                      SUM(apcm) as apcm,
                                      SUM(pint) as pint,
                                      SUM(rc_cash) as rc_cash,
                                      SUM(sc) as sc
                                      FROM pendings
                                      WHERE created_at LIKE :id', ['id' => $filterdate]);
            $gt = collect($grandTotal);

            $grace_period = $this->time['start'];
            $conn_tickets_array = $this->conn_tickets_array;
            $power_interruptions_array = $this->power_interruptions_array;
            return view('pendings.ci', compact('regions',
                                               'gt',
                                               'filterdate2',
                                               'grace_period',
                                               'conn_tickets_array',
                                               'power_interruptions_array'));

        } else {
            abort('401');
        }
    }

    public function create () {
        if (Auth::user()->branch->machine_number <> 103 && $this->inBetween === 1) {
            return view('pendings.create');
        } else {
            return abort('401');
        }
    }

    public function createPrev () {
        $time = $this->time;
        if (Auth::user()->branch->machine_number <> 103 && $this->inBetween === 0) {
            return view('pendings.createPrev');
        } else {
            return abort('401');
        }
    }

    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
          'docdate' => 'required',
      		'ls_or' => 'required|integer',
      		'or' => 'required|integer|max:1000',
      		'ls_ci' => 'required|integer',
      		'ci' => 'required|integer|max:1000',
      		'ls_ch' => 'required|integer',
      		'ch' => 'required|integer|max:1000',
      		'dep' => 'required|integer|max:1000',
      		'cla' => 'required|integer|max:1000',
      		'grpo' => 'required|integer|max:1000',
      		'si' => 'required|integer|max:1000',
      		'so' => 'required|integer|max:1000',
      		'sts' => 'required|integer|max:1000',
      		'disb' => 'required|integer|max:1000',
      		'arcm' => 'required|integer|max:1000',
      		'apcm' => 'required|integer|max:1000',
      		'int' => 'required|integer|max:1000',
            'rc_cash' => 'required|integer|max:1000',
      		'sc' => 'required|integer|max:1000',
      		'reason' => 'required',
    		]);

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

        if ($request->branch) {
            $branch = Branch::where('id', $request->branch)->with('region')->first();
            $branch_id = $branch->id;
            $branch_name = $branch->name;
            $region_name = $branch->region->name;
        } else {
            $user = User::find(Auth::user()->id);
            $branch_id = $user->branch->id;
            $branch_name = $user->branch->name;
            $region_name = $user->branch->region->name;
        }

        $pending = New Pending;
        $pending->branch_id = $branch_id;
        $pending->branch = $branch_name;
        $pending->region = $region_name;
        $pending->docdate = $request->docdate;
        $pending->ls_or = $request->ls_or;
        $pending->por = $request->or;
        $pending->ls_ci = $request->ls_ci;
        $pending->ci = $request->ci;
        $pending->ls_ch = $request->ls_ch;
        $pending->ch = $request->ch;
        $pending->dep = $request->dep;
        $pending->cla = $request->cla;
        $pending->grpo = $request->grpo;
        $pending->si = $request->si;
        $pending->so = $request->so;
        $pending->sts = $request->sts;
        $pending->disb = $request->disb;
        $pending->arcm = $request->arcm;
        $pending->apcm = $request->apcm;
        $pending->pint = $request->int;
        $pending->rc_cash = $request->rc_cash;
        $pending->sc = $request->sc;
        $pending->reason = $request->reason;
        if ($request->isCreatePrev == 1) { // if you adding pending on dated yesterday
            $pending->created_at = Carbon::now()->addDay(-1); // date yesterday
        }
        $pending->save();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'New pendings are added into your records.',
        ];
        Session::flash('create_success', $flash_message);

        if ($request->isReadd == 1) {
            if ($request->branch) {
                return redirect()->route('pending.index_as', ['id' => $request->branch]);
            } else {
                return redirect()->route('pendings');
            }
        } else {
            if ($request->branch) {
                return redirect()->route('pending.index_as', ['id' => $request->branch]);
            } else {
                return redirect()->route('pendings');
            }
        }
    }

    public function store_breakdown ($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'docdate' => 'required',
            'ls_or' => 'required|integer',
            'or' => 'required|integer|max:1000',
            'ls_ci' => 'required|integer',
            'ci' => 'required|integer|max:1000',
            'ls_ch' => 'required|integer',
            'ch' => 'required|integer|max:1000',
            'dep' => 'required|integer|max:1000',
            'cla' => 'required|integer|max:1000',
            'grpo' => 'required|integer|max:1000',
            'si' => 'required|integer|max:1000',
            'so' => 'required|integer|max:1000',
            'sts' => 'required|integer|max:1000',
            'disb' => 'required|integer|max:1000',
            'arcm' => 'required|integer|max:1000',
            'apcm' => 'required|integer|max:1000',
            'int' => 'required|integer|max:1000',
            'rc_cash' => 'required|integer|max:1000',
            'sc' => 'required|integer|max:1000',
            'reason' => 'required',
        ]);

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

        $dtNow = $this->dtNow;
        $dtPrev = $this->dtPrev;
        $time = $this->time;
        $inBetween = $this->inBetween;

        if ($request->branch) {
            $branch = Branch::where('id', $request->branch)->with('region')->first();
            $branch_id = $branch->id;
            $branch_name = $branch->name;
            $region_name = $branch->region->name;
        } else {
            $user = User::find(Auth::user()->id);
            $branch_id = $user->branch->id;
            $branch_name = $user->branch->name;
            $region_name = $user->branch->region->name;
        }

        $pending = New Pending;
        $pending->branch_id = $branch_id;
        $pending->branch = $branch_name;
        $pending->region = $region_name;
        $pending->docdate = $request->docdate;
        $pending->ls_or = $request->ls_or;
        $pending->por = $request->or;
        $pending->ls_ci = $request->ls_ci;
        $pending->ci = $request->ci;
        $pending->ls_ch = $request->ls_ch;
        $pending->ch = $request->ch;
        $pending->dep = $request->dep;
        $pending->cla = $request->cla;
        $pending->grpo = $request->grpo;
        $pending->si = $request->si;
        $pending->so = $request->so;
        $pending->sts = $request->sts;
        $pending->disb = $request->disb;
        $pending->arcm = $request->arcm;
        $pending->apcm = $request->apcm;
        $pending->pint = $request->int;
        $pending->rc_cash = $request->rc_cash;
        $pending->sc = $request->sc;
        $pending->reason = $request->reason;
        if ($inBetween == 0) { // if you're adding pending dated yesterday
            $pending->created_at = Carbon::now()->addDay(-1); // date yesterday
        }
        $pending->save();

        $flash_message = [
            'title' => 'Well Done!',
            'status' => 'success',
            'message' => 'New pending has been added into our records.',
        ];
        Session::flash('create_success', $flash_message);
        return redirect()->route('pending.branch.breakdown', ['id' => $branch_id, 'date' => $request->created_at]);
    }

    public function edit ($id) {
        $pending = Pending::find($id);
        return view('pendings.edit', compact('pending'));
    }

    public function update ($id, Request $request) {
   
        $validator = Validator::make($request->all(), [
          'docdate' => 'required',
          'ls_or' => 'required|integer',
          'or' => 'required|integer|max:1000',
          'ls_ci' => 'required|integer',
          'ci' => 'required|integer|max:1000',
          'ls_ch' => 'required|integer',
          'ch' => 'required|integer|max:1000',
          'dep' => 'required|integer|max:1000',
          'cla' => 'required|integer|max:1000',
          'grpo' => 'required|integer|max:1000',
          'si' => 'required|integer|max:1000',
          'so' => 'required|integer|max:1000',
          'sts' => 'required|integer|max:1000',
          'disb' => 'required|integer|max:1000',
          'arcm' => 'required|integer|max:1000',
          'apcm' => 'required|integer|max:1000',
          'int' => 'required|integer|max:1000',
          'rc_cash' => 'required|integer|max:1000',
          'sc' => 'required|integer|max:1000',
          'reason' => 'required',
        ]);

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

        $pending = Pending::find($id);
        $pending->docdate = $request->docdate;
        $pending->ls_or = $request->ls_or;
        $pending->por = $request->or;
        $pending->ls_ci = $request->ls_ci;
        $pending->ci = $request->ci;
        $pending->ls_ch = $request->ls_ch;
        $pending->ch = $request->ch;
        $pending->dep = $request->dep;
        $pending->cla = $request->cla;
        $pending->grpo = $request->grpo;
        $pending->si = $request->si;
        $pending->so = $request->so;
        $pending->sts = $request->sts;
        $pending->disb = $request->disb;
        $pending->arcm = $request->arcm;
        $pending->apcm = $request->apcm;
        $pending->pint = $request->int;
        $pending->rc_cash = $request->rc_cash;
        $pending->sc = $request->sc;
        $pending->reason = $request->reason;
        $pending->update();

        $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'One (1) record has been successfully updated.',
        ];
        Session::flash('update_success', $flash_message);
        $branch_id = Pending::where('id', $id)->pluck('branch_id')->first();
        if (Auth::user()->branch->id != $branch_id) { // IF ADMIN EDITED THE PENDING
            return redirect()->route('pending.index_as', ['id' => $branch_id]);
        } else {
            return redirect()->route('pendings');
        }
    }

    public function delete ($id, $pending_id) {
        $pending = Pending::find($pending_id);
        $pending->delete();
        $flash_message = [
          'title' => 'Well Done!',
          'status' => 'success',
          'message' => 'Pending has been successfully deleted.',
        ];
        Session::flash('delete_success', $flash_message);
        if (Auth::user()->branch->id != $id) { // IF ADMIN DELETED THE PENDING
            return redirect()->route('pending.index_as', ['id' => $id]);
        } else {
            return redirect()->route('pendings');
        }
    }

    public function readd ($id) {
        $pending = Pending::find($id);
        return view('pendings.readd', compact('pending'));
    }

    public function breakdown ($id, $date) {
        $branch_id = $id;
        $branch_name = Branch::where('id', $branch_id)->pluck('name')->first();

        $submitted_date = '%' . $date . '%';
        $display_date = Carbon::parse($date)->format('F d, Y');
        $breakdown_pendings = Pending::orderBy('docdate', 'asc')->where('branch_id', $id)->where('created_at', 'LIKE', $submitted_date)->get();
        return view('pendings.breakdown', compact('breakdown_pendings',
                                                  'date',
                                                  'display_date',
                                                  'branch_id',
                                                  'branch_name'));
    }

    public function readd_breakdown ($id, $pending_id) {
        $pending = Pending::find($pending_id);
        $branch_id = $id;
        $branch_name = Branch::where('id', $branch_id)->pluck('name')->first();
        $date = Carbon::parse($pending->created_at)->format('Y-m-d');
        return view('pendings.readd_breakdown', compact('pending', 'date', 'branch_id', 'branch_name'));
    }

    // -------
    // CHARTS
    // -------
    public function chart ($id) {
        $branch_id = $id;
        $branch_name = Branch::where('id', $branch_id)->pluck('name')->first();
        $filterdate1 = '';
        $data = DB::select('SELECT
                            SUM(por) AS `OR`,
                            SUM(ci) AS `CI`,
                            SUM(ch) AS `CH`,
                            SUM(dep) AS `DEP`,
                            SUM(cla) AS `CLA`,
                            SUM(grpo) AS `GRPO`,
                            SUM(si) AS `SI`,
                            SUM(so) AS `SO`,
                            SUM(sts) AS `STS`,
                            SUM(disb) AS `DISB`,
                            SUM(arcm) AS `ARCM`,
                            SUM(apcm) AS `APCM`,
                            SUM(pint) AS `INT`,
                            SUM(rc_cash) AS `RC`,
                            SUM(sc) AS `SC`,

                            (
                                SUM(por)+
                                SUM(ci)+
                                SUM(ch)+
                                SUM(dep)+
                                SUM(cla)+
                                SUM(grpo)+
                                SUM(si)+
                                SUM(so)+
                                SUM(sts)+
                                SUM(disb)+
                                SUM(arcm)+
                                SUM(apcm)+
                                SUM(pint)+
                                SUM(rc_cash)+
                                SUM(sc)
                            ) AS `GT`

                            FROM pendings
                            WHERE branch_id = :id',
                            ['id' => $branch_id]);

            $pending = array();
            foreach ($data as $key => $datum) {
            $pending[] = array(
                    'name' => 'OR',
                    'y' => doubleval(str_replace(",","",$datum->OR)),
                    'percentage' => $datum->OR == 0 ? 0 : ($datum->OR / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'CI',
                    'y' => doubleval(str_replace(",","",$datum->CI)),
                    'percentage' => $datum->CI == 0 ? 0 : ($datum->CI / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'CH',
                    'y' => doubleval(str_replace(",","",$datum->CH)),
                    'percentage' => $datum->CH == 0 ? 0 : ($datum->CH / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'DEP',
                    'y' => doubleval(str_replace(",","",$datum->DEP)),
                    'percentage' => $datum->DEP == 0 ? 0 : ($datum->DEP / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'CLA',
                    'y' => doubleval(str_replace(",","",$datum->CLA)),
                    'percentage' => $datum->CLA == 0 ? 0 : ($datum->CLA / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'GRPO',
                    'y' => doubleval(str_replace(",","",$datum->GRPO)),
                    'percentage' => $datum->GRPO == 0 ? 0 : ($datum->GRPO / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'SI',
                    'y' => doubleval(str_replace(",","",$datum->SI)),
                    'percentage' => $datum->SI == 0 ? 0 : ($datum->SI / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'SO',
                    'y' => doubleval(str_replace(",","",$datum->SO)),
                    'percentage' => $datum->SO == 0 ? 0 : ($datum->SO / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'STS',
                    'y' => doubleval(str_replace(",","",$datum->STS)),
                    'percentage' => $datum->STS == 0 ? 0 : ($datum->STS / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'DISB',
                    'y' => doubleval(str_replace(",","",$datum->DISB)),
                    'percentage' => $datum->DISB == 0 ? 0 : ($datum->DISB / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'ARCM',
                    'y' => doubleval(str_replace(",","",$datum->ARCM)),
                    'percentage' => $datum->ARCM == 0 ? 0 : ($datum->ARCM / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'APCM',
                    'y' => doubleval(str_replace(",","",$datum->APCM)),
                    'percentage' => $datum->APCM == 0 ? 0 : ($datum->APCM / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'INT',
                    'y' => doubleval(str_replace(",","",$datum->INT)),
                    'percentage' => $datum->INT == 0 ? 0 : ($datum->INT / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'RC',
                    'y' => doubleval(str_replace(",","",$datum->RC)),
                    'percentage' => $datum->RC == 0 ? 0 : ($datum->RC / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'SC',
                    'y' => doubleval(str_replace(",","",$datum->SC)),
                    'percentage' => $datum->SC == 0 ? 0 : ($datum->SC / $datum->GT) * 100,
                );
        }
        $pendingCollect = collect($pending);

        $filterdate2 = ['date' => 'Overall'];
        $filterdate2 = collect($filterdate2);

        return view('pendings.charts.index', compact('pendingCollect',
                                                     'filterdate2',
                                                     'filterdate1',
                                                     'branch_id',
                                                     'branch_name'));
    }

    public function filtered_chart ($id, Request $req) {
        $branch_id = $id;
        $branch_name = Branch::where('id', $branch_id)->pluck('name')->first();

        $filterdate = '%' . $req->filterdate . '%';
        $filterdate1 = $req->filterdate;
        $filterdate2 = ['date' => Carbon::parse($req->filterdate)->format('F d, Y')];
        $filterdate2 = collect($filterdate2);

        $data = DB::select('SELECT
                            SUM(por) AS `OR`,
                            SUM(ci) AS `CI`,
                            SUM(ch) AS `CH`,
                            SUM(dep) AS `DEP`,
                            SUM(cla) AS `CLA`,
                            SUM(grpo) AS `GRPO`,
                            SUM(si) AS `SI`,
                            SUM(so) AS `SO`,
                            SUM(sts) AS `STS`,
                            SUM(disb) AS `DISB`,
                            SUM(arcm) AS `ARCM`,
                            SUM(apcm) AS `APCM`,
                            SUM(pint) AS `INT`,
                            SUM(rc_cash) AS `RC`,
                            SUM(sc) AS `SC`,

                            (
                                SUM(por)+
                                SUM(ci)+
                                SUM(ch)+
                                SUM(dep)+
                                SUM(cla)+
                                SUM(grpo)+
                                SUM(si)+
                                SUM(so)+
                                SUM(sts)+
                                SUM(disb)+
                                SUM(arcm)+
                                SUM(apcm)+
                                SUM(pint)+
                                SUM(rc_cash)+
                                SUM(sc)
                            ) AS `GT`

                            FROM pendings
                            WHERE branch_id = :id AND created_at LIKE :fdate',
                            ['id' => $branch_id, 'fdate' => $filterdate]);

            $pending = array();
            foreach ($data as $key => $datum) {
            $pending[] = array(
                    'name' => 'OR',
                    'y' => doubleval(str_replace(",","",$datum->OR)),
                    'percentage' => $datum->OR == 0 ? 0 : ($datum->OR / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'CI',
                    'y' => doubleval(str_replace(",","",$datum->CI)),
                    'percentage' => $datum->CI == 0 ? 0 : ($datum->CI / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'CH',
                    'y' => doubleval(str_replace(",","",$datum->CH)),
                    'percentage' => $datum->CH == 0 ? 0 : ($datum->CH / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'DEP',
                    'y' => doubleval(str_replace(",","",$datum->DEP)),
                    'percentage' => $datum->DEP == 0 ? 0 : ($datum->DEP / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'CLA',
                    'y' => doubleval(str_replace(",","",$datum->CLA)),
                    'percentage' => $datum->CLA == 0 ? 0 : ($datum->CLA / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'GRPO',
                    'y' => doubleval(str_replace(",","",$datum->GRPO)),
                    'percentage' => $datum->GRPO == 0 ? 0 : ($datum->GRPO / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'SI',
                    'y' => doubleval(str_replace(",","",$datum->SI)),
                    'percentage' => $datum->SI == 0 ? 0 : ($datum->SI / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'SO',
                    'y' => doubleval(str_replace(",","",$datum->SO)),
                    'percentage' => $datum->SO == 0 ? 0 : ($datum->SO / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'STS',
                    'y' => doubleval(str_replace(",","",$datum->STS)),
                    'percentage' => $datum->STS == 0 ? 0 : ($datum->STS / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'DISB',
                    'y' => doubleval(str_replace(",","",$datum->DISB)),
                    'percentage' => $datum->DISB == 0 ? 0 : ($datum->DISB / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'ARCM',
                    'y' => doubleval(str_replace(",","",$datum->ARCM)),
                    'percentage' => $datum->ARCM == 0 ? 0 : ($datum->ARCM / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'APCM',
                    'y' => doubleval(str_replace(",","",$datum->APCM)),
                    'percentage' => $datum->APCM == 0 ? 0 : ($datum->APCM / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'INT',
                    'y' => doubleval(str_replace(",","",$datum->INT)),
                    'percentage' => $datum->INT == 0 ? 0 : ($datum->INT / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'RC',
                    'y' => doubleval(str_replace(",","",$datum->RC)),
                    'percentage' => $datum->RC == 0 ? 0 : ($datum->RC / $datum->GT) * 100,
                );
            $pending[] = array(
                    'name' => 'SC',
                    'y' => doubleval(str_replace(",","",$datum->SC)),
                    'percentage' => $datum->SC == 0 ? 0 : ($datum->SC / $datum->GT) * 100,
                );
        }
        $pendingCollect = collect($pending);
        return view('pendings.charts.index', compact('pendingCollect', 'filterdate2', 'filterdate1', 'branch_id', 'branch_name'));
    }

    public function index_as ($id) {
        $dtNow = $this->dtNow;
        $dtPrev = $this->dtPrev;
        $time = $this->time;
        $inBetween = $this->inBetween;
        $grace_period = $this->time['start'];

        $prevPendings = Pending::where('branch_id', '=', $id)
                            ->where('created_at', 'LIKE', $dtPrev)
                            ->orderBy('docdate', 'asc')
                            ->get();
        $pendings = Pending::where('branch_id', '=', $id)
                            ->where('created_at', 'LIKE', $dtNow)
                            ->orderBy('docdate', 'asc')
                            ->get();

        $branch_name = Branch::where('id', $id)->pluck('name')->first();

        if ($this->inBetween == 1) {
            return view('pendings.as_branch.index', compact('pendings','prevPendings','inBetween', 'branch_name', 'id'));
        } else {
            return view('pendings.as_branch.index_previous', compact('prevPendings', 'grace_period', 'branch_name', 'id'));
        }
    }

    public function add_as () {
        $branches = Branch::orderBy('name', 'asc')
                    ->where('machine_number', '!=', '103')
                    ->where('machine_number', '!=', '255')
                    ->whereIn('id', $this->conn_tickets_array) // only branches without connections
                    ->orWhereIn('id', $this->power_interruptions_array) // only branches without connections
                    ->get();
        if ($this->inBetween == 1) {
            return view('pendings.as_branch.create', compact('branches'));
        } else {
            return view('pendings.as_branch.create_previous', compact('branches'));
        }
    }

    public function edit_as ($id, $pending_id) {
        $pending = Pending::find($pending_id);
        $branch_id = $id;
        $branch_name = Branch::where('id', $id)->pluck('name')->first();
        return view('pendings.as_branch.edit', compact('pending', 'branch_id', 'branch_name'));
    }

    public function readd_as ($id) {
        $pending = Pending::find($id);
        $branch_id = $pending->branch_id;
        $branch_name = $pending->branch;
        if ($this->inBetween == 1) {
            return view('pendings.as_branch.readd', compact('pending', 'branch_id', 'branch_name'));
        } else {
            return view('pendings.as_branch.readd_previous', compact('pending', 'branch_id', 'branch_name'));
        }
    }

    public function add_all ($id, $date) {
      if ($date) {
        $pending_date = '%' . $date . '%';
      } else { $pending_date = $this->dtPrev; }

      $pendings = Pending::where('branch_id', '=', $id)
                            ->where('created_at', 'LIKE', $pending_date)
                            ->orderBy('docdate', 'asc')
                            ->get();



      $inBetween = $this->inBetween;
      if ($inBetween == 0) { // if you're adding pending dated yesterday
        $pending_date = Carbon::now()->addDay(-1); // date yesterday
      } else { $pending_date = Carbon::now(); }

      $branch = Branch::where('id', $id)->with('region')->first();
      $branch_id = $branch->id;
      $branch_name = $branch->name;
      $region_name = $branch->region->name;

      foreach ($pendings as $pend) {
        $pending = New Pending;
        $pending->branch_id = $branch_id;
        $pending->branch = $branch_name;
        $pending->region = $region_name;
        $pending->docdate = $pend->docdate;
        $pending->ls_or = $pend->ls_or;
        $pending->por = $pend->por;
        $pending->ls_ci = $pend->ls_ci;
        $pending->ci = $pend->ci;
        $pending->ls_ch = $pend->ls_ch;
        $pending->ch = $pend->ch;
        $pending->dep = $pend->dep;
        $pending->cla = $pend->cla;
        $pending->grpo = $pend->grpo;
        $pending->si = $pend->si;
        $pending->so = $pend->so;
        $pending->sts = $pend->sts;
        $pending->disb = $pend->disb;
        $pending->arcm = $pend->arcm;
        $pending->apcm = $pend->apcm;
        $pending->pint = $pend->pint;
        $pending->rc_cash = $pend->rc_cash;
        $pending->sc = $pend->sc;
        $pending->reason = $pend->reason;
        $pending->created_at = $pending_date;
        $pending->save();
      }

      $flash_message = [
        'title' => 'Well Done!',
        'status' => 'success',
        'message' => 'New pendings has been added to current pending transaction.',
      ];
      Session::flash('create_success', $flash_message);

      if (Auth::user()->branch_id != $branch_id) {
        return redirect()->route('pending.index_as', ['id' => $id]);
      } else { return redirect()->route('pendings'); }
      
    }




















    // api
    public function all (Request $req) {
      $date = $req->date;
      $date_filter = $req->date . ' %';
      $inBetween = $this->inBetween;

      if (Auth::user()->branch->machine_number !== 103) {
        $branch = true;
      } else { $branch = false; }

    	$pendings = Branch::with(['pendings' => function ($qry) use ($date_filter) {
                    $qry->select(
                          'id',
                          'branch_id',
                          'docdate',
                          \DB::raw('DATE_FORMAT(docdate, "%d") AS disp_docdate'),
                          'ls_or',
                          'por',
                    	  'ls_ci',
                          'ci',
                    	  'ls_ch',
                          'ch',
                          'cla',
                          'dep',
                          'grpo',
                          'si',
                          'so',
                          'sts',
                          'disb',
                          'arcm',
                          'apcm',
                          'pint',
                          'rc_cash',
                          'sc',
                          'reason'
                        )
                        ->where('created_at', 'LIKE', $date_filter);
                  }])
                  ->select('id', 'name')
                  ->orderBy('name', 'asc')
                  ->when($branch, function ($a) { // when user is from branch
                    $a->where('id', Auth::user()->branch->id);
                  })
                  ->get();
      $pendings->map(function ($pending) use ($date, $inBetween) {
        $pending['date'] = Carbon::parse($date)->format("M d, Y");
        $pending['schedule'] = $inBetween;
        return $pending;
      });

      $power_interruptions_array = $this->power_interruptions_array;
    	return response()->json($pendings, 200);
    }

    public function store_api (Request $req) {
      
      $validator = Validator::make($req->all(), [
        'docdate' => 'required',
        'ls_or' => 'required',
        'ls_ci' => 'required',
        'ls_ch' => 'required',
        'ci' => 'required',
        'por' => 'required',
        'ci' => 'required',
        'ch' => 'required',
        'cla' => 'required',
        'dep' => 'required',
        'grpo' => 'required',
        'si' => 'required',
        'so' => 'required',
        'sts' => 'required',
        'disb' => 'required',
        'arcm' => 'required',
        'apcm' => 'required',
        'pint' => 'required',
        'rc_cash' => 'required',
        'sc' => 'required',
        'reason' => 'required',
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

      $branch = Branch::where('id', $req->branch)->with('region')->first();
      $branch_id = $branch->id;
      $branch_name = $branch->name;
      $region_name = $branch->region->name;

      $ls_or = $req->ls_or;
      $ls_ci = $req->ls_ci;
      $ls_ch = $req->ls_ch;

      $docdate = Carbon::parse($req->docdate)->format('Y-m-d H:i:s');
      $created_at = $req->date . '%';

      $inDB = Pending::where('branch_id', $req->branch)
              ->where('docdate', $docdate)
              ->where('created_at', 'like', $created_at)
              ->first();

      if ($inDB) {
        $test = 1;
        $pending = Pending::find($inDB->id);
      } else { $pending = new Pending; $test = 0; }

      $pending->branch_id = $branch_id;
      $pending->branch = $branch_name;
      $pending->region = $region_name;
      $pending->docdate = $req->docdate;
      $pending->ls_or = $ls_or;
      $pending->por = $req->por;
      $pending->ls_ci = $ls_ci;
      $pending->ci = $req->ci;
      $pending->ls_ch = $ls_ch;
      $pending->ch = $req->ch;
      $pending->dep = $req->dep;
      $pending->cla = $req->cla;
      $pending->grpo = $req->grpo;
      $pending->si = $req->si;
      $pending->so = $req->so;
      $pending->sts = $req->sts;
      $pending->disb = $req->disb;
      $pending->arcm = $req->arcm;
      $pending->apcm = $req->apcm;
      $pending->pint = $req->pint;
      $pending->rc_cash = $req->rc_cash;
      $pending->sc = $req->sc;
      $pending->reason = $req->reason;
      $pending->created_at = $req->date;
      if ($inDB) {
        $pending->update();
      } else { $pending->save(); }

      $pending = Pending::where('id', $pending->id)
                 ->first();
      if ($inDB) {
        $pending->setAttribute('inDB', 1);
      } else {
        $pending->setAttribute('inDB', 0);
      }

    	return response()->json($pending, 200);
    }

    public function addall (Request $req) {
     
        foreach ($req->pendings as $pendingTran) {
            $branch = Branch::where('id', $req->id)->with('region')->first();
            $branch_id = $branch->id;
            $branch_name = $branch->name;
            $region_name = $branch->region->name;
    
            $ls_or = $pendingTran['ls_or'];
            $ls_ci = $pendingTran['ls_ci'];
            $ls_ch = $pendingTran['ls_ch'];
    
            if ($this->inBetween == 1) {
              $created_date = Carbon::now(); // add today
            } else { $created_date = Carbon::now()->addDay(-1); } // add to yesterday's date
    
            $pending = new Pending;
            $pending->branch_id = $branch_id;
            $pending->branch = $branch_name;
            $pending->region = $region_name;
            $pending->docdate = $pendingTran['docdate'];
            $pending->ls_or = $ls_or;
            $pending->por = $pendingTran['por'];
            $pending->ls_ci = $ls_ci;
            $pending->ci = $pendingTran['ci'];
            $pending->ls_ch = $ls_ch;
            $pending->ch = $pendingTran['ch'];
            $pending->dep = $pendingTran['dep'];
            $pending->cla = $pendingTran['cla'];
            $pending->grpo = $pendingTran['grpo'];
            $pending->si = $pendingTran['si'];
            $pending->so = $pendingTran['so'];
            $pending->sts = $pendingTran['sts'];
            $pending->disb = $pendingTran['disb'];
            $pending->arcm = $pendingTran['arcm'];
            $pending->apcm = $pendingTran['apcm'];
            $pending->pint = $pendingTran['pint'];
            $pending->rc_cash = $pendingTran['rc_cash'];
            $pending->sc = $pendingTran['sc'];
            $pending->reason = $pendingTran['reason'];
            $pending->created_at = $created_date;
            $pending->save();
          }
    
            return response()->json($req->pendings, 200);
    }

    public function update_api (Request $req) {
      if ($req->value == null) {
        $payload = [
          'message' => 'Update failed!',
          'value' => $req->value, // return previous value
        ];
        $pending = Pending::find($req->id);
        return response()->json($payload, 422);
      }

      $pending = Pending::find($req->id);
      if ($req->key == 'docdate') { $pending->docdate = $req->value; }
      if ($req->key == 'por') { $pending->por = $req->value; }
      if ($req->key == 'ci') { $pending->ci = $req->value; }
      if ($req->key == 'ch') { $pending->ch = $req->value; }
      if ($req->key == 'cla') { $pending->cla = $req->value; }
      if ($req->key == 'dep') { $pending->dep = $req->value; }
      if ($req->key == 'grpo') { $pending->grpo = $req->value; }
      if ($req->key == 'si') { $pending->si = $req->value; }
      if ($req->key == 'so') { $pending->so = $req->value; }
      if ($req->key == 'sts') { $pending->por = $req->value; }
      if ($req->key == 'disb') { $pending->disb = $req->value; }
      if ($req->key == 'arcm') { $pending->arcm = $req->value; }
      if ($req->key == 'apcm') { $pending->apcm = $req->value; }
      if ($req->key == 'pint') { $pending->pint = $req->value; }
      if ($req->key == 'rc_cash') { $pending->rc_cash = $req->value; }
      if ($req->key == 'sc') { $pending->sc = $req->value; }
    
      if ($req->key == 'ls_or') { $pending->ls_or = $req->value; }
      if ($req->key == 'ls_ch') { $pending->ls_ch = $req->value; }
      if ($req->key == 'ls_ci') { $pending->ls_ci = $req->value; }
      $pending->update();

      return response()->json($pending, 200);
    }

    public function delete_api (Request $req) {
      $pending = Pending::where('id', $req->id)
                 ->first();
      $response = $pending;
      Pending::where('id', $req->id)->delete();
      return response()->json($response, 200);
    }
}