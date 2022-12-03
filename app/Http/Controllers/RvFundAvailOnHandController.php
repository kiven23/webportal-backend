<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\RevolvingFund;
use App\RvFundCheckVoucherVerification;
use App\RvFundCheckVoucherForTransmittal;
use App\RvFundExpensesForCheckPreparation;

use App\Exec\RevolvingFund\RevolvingFundOnHandHistory;


use App\Http\Resources\AvailRevolvingFundOnHandSummaryResource;

use PDF;

class RvFundAvailOnHandController extends Controller
{
    private $history = null;

    public function __construct(){
      $this->history = new RevolvingFundOnHandHistory();
    }

    public function updateOrCreate(Request $request)
    {
    
 
            $request->validate(
                [
                    'fund' => 'required|numeric|min:1',
                    'cash_advances' => 'required|numeric|min:0',
                    'or'=> 'required|min:8',
                ],
                [
                    'fund.min' => "Revolving Fund must be not equal to zero",
                    'cash_advances.min' => "Cash Advances must be not equal to zero"
                ]
            );


       
 
   
 
        $hasChange = false;
        $data = $request->all();
        if (empty($data["rv_fund_id"])) {
            //$data["cash_advances"] = 0;
            $data["avail_fund_on_hand"] = ($data["fund"] + $data["cash_advances"]);

            if (!$availRVFundOnHand = $this->createAvailOnHandForToday($data) ) {
                return response()->json([
                    'message' => 'Failed in saving data.'
                ], 500);
            }
             
            $hasChange = true;
        } else {
            $rv_fund_id = $data['rv_fund_id'];
            $fund = $data['fund'];
            $cash_advances = $data['cash_advances'];

            $availRVFundOnHand =  RevolvingFund::find($rv_fund_id);
            if ($availRVFundOnHand->fund !== $fund || $availRVFundOnHand->cash_advances !== $cash_advances) {
                $totalExpenses = RvFundCheckVoucherVerification::where('rv_fund_id', $rv_fund_id)->sum('amount') + RvFundCheckVoucherForTransmittal::where('rv_fund_id', $rv_fund_id)->sum('amount') + RvFundExpensesForCheckPreparation::where('rv_fund_id', $rv_fund_id)->sum('amount');
                $data['avail_fund_on_hand'] = ($fund + $cash_advances)  -  $totalExpenses;
                if (!$availRVFundOnHand->update($data)) {
                    return response()->json([
                        'message' => 'Failed in updating data.'
                    ], 500);
                }
         
                $hasChange = true;
            }
        }

        $totalData = [];
        if ($hasChange) {
            $totalData = RevolvingFund::selectRaw("SUM(fund) as rfTotal, SUM(cash_advances) as caTotal, SUM(avail_fund_on_hand) as availRfTotal")->whereRaw('DATE(created_at) = DATE(?)', [date('Y-m-d')])->first();
            //$totalData = $query->first();
        }
        $this->history->createHistory($request);
        return response()->json([
            'totalData' => $totalData,
            'availRVFundOnHand' => $availRVFundOnHand,
            'message' => 'Available Revolving Fund On Hand has successfully updated'
        ], 200);
    }

    public function index()
    {
        $items = collect(AvailRevolvingFundOnHandSummaryResource::collection(Branch::select("id", "name")->get()));

        return [
            'items' => $items,
            'ca_total' => $items->sum('cash_advances'),
            'rf_total' => $items->sum('revolving_fund'),
            'avail_rf_total' => $items->sum('avail_fund_on_hand'),
        ];
    }

    public function updateRevolvingFundsDaily()
    {
        $rvFundBranchQuery = RevolvingFund::whereNotIn('branch_id', function ($query) {
            $query->select('branch_id')->from('revolving_funds')->whereRaw('DATE(created_at) = DATE(?)', [date("Y-m-d")]);
        })->distinct();

        DB::beginTransaction();
        try {
            foreach ($rvFundBranchQuery->pluck('branch_id') as $rvFundBranchId) {
                $oldRF = RevolvingFund::select('id', 'fund', 'cash_advances', 'avail_fund_on_hand')->where('branch_id', $rvFundBranchId)->orderBy("created_at", "desc")->first();
                $createData = $oldRF->toArray();
                //unset($createData['id']);
                $createData['branch_id'] = $rvFundBranchId;
                $newRF = $this->createAvailOnHandForToday($createData);
                $chkVoucherVeriItemsLatest = RvFundCheckVoucherVerification::select('rv_fund_id', 'date_transmitted', 'ck_no', 'status',  'amount')->where('rv_fund_id', $oldRF->id)->orderBy("created_at", "desc")->get();
                foreach ($chkVoucherVeriItemsLatest as $item) {
                    $createData = $item->toArray();
                    $createData['rv_fund_id'] = $newRF->id;
                    RvFundCheckVoucherVerification::create($createData);
                }
                $chkVoucherForTransItemsLatest = RvFundCheckVoucherForTransmittal::select('rv_fund_id', 'check_voucher_date', 'ck_no', 'amount')->where('rv_fund_id', $oldRF->id)->orderBy("created_at", "desc")->get();
                foreach ($chkVoucherForTransItemsLatest as $item) {
                    $createData = $item->toArray();
                    $createData['rv_fund_id'] = $newRF->id;
                    RvFundCheckVoucherForTransmittal::create($createData);
                }
                $expensesFrChkPrepItemsLatest = RvFundExpensesForCheckPreparation::select('rv_fund_id', 'pcv_date', 'particulars', 'amount')->where('rv_fund_id', $oldRF->id)->orderBy("created_at", "desc")->get();
                foreach ($expensesFrChkPrepItemsLatest as $item) {
                    $createData = $item->toArray();
                    $createData['rv_fund_id'] = $newRF->id;
                    RvFundExpensesForCheckPreparation::create($createData);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->errorInfo], 500);
        }

        return response()->json(['message' => 'Records has been successfully inserted'], 200);
    }

    private function createAvailOnHandForToday($data)
    {
        $data["as_of"] = date("Y-m-d");
        return RevolvingFund::create($data);
    }
    public function onHandHistory(request $req){
       //$name = DB::table("branches")->where("name", $req->b)->pluck('name')->first();
        $branchid = DB::table("branches")->where("name", $req->b)->pluck('id')->first();
        return DB::table("onhand_history")->where("branch_id", $branchid)->get();
    }
    public function print()
    {
        $pdf = PDF::loadView("revolving_funds.reports.avail_rf_reports", $this->index());

        return $pdf->download("Available_Revolving_Fund_On_Hand_Reports.pdf")->header('Access-Control-Expose-Headers', 'Content-Disposition');
    }

    public function preview()
    {
        $pdf = PDF::loadView("revolving_funds.reports.avail_rf_reports", $this->index());

        return $pdf->stream();
    }
    
    public function glaccount(){
        $branchID = \Auth::user()->branch_id;
        $getBranch = DB::table("branches")->where("id" , $branchID)->pluck('name')->first();
        $q = DB::connection("fc1474ae7c224d8ff2a96f9bcd1dc9b4")
                           ->select("select FormatCode,AcctName FROM OACT where AcctName LIKE '%$getBranch%' ORDER BY AcctName ASC");
        return response()->json($q);
    }

    // private function getAvailOnHandForToday($rv_fund_id)
    // {
    //     return RevolvingFund::where('id', $rv_fund_id)
    //         ->whereRaw('DATE(created_at) = DATE(?)', [date('Y-m-d')])
    //         ->first();
    // }

    // private function getBranch()
    // {
    //     return Auth::user()->branch;
    // }
}
