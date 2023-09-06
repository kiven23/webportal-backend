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
use GuzzleHttp\Client;
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
                    'fund' => 'required|numeric|min:0',
                    'cash_advances' => 'required|numeric|min:0',
                    'or'=> 'required|min:8',
                ],
                [
                    
                    'cash_advances.min' => "Cash Advances must be not equal to zero"
                ]
            );


       
         
 
        $hasChange = false;
        $data = $request->all();
        
        if (empty($data["rv_fund_id"])) {
            //$data["cash_advances"] = 0;
            $data["avail_fund_on_hand"] = ($data['fund']  + $data["cash_advances"]);
            if (!$availRVFundOnHand = $this->createAvailOnHandForToday($data) ) {
                return response()->json([
                    'message' => 'Failed in saving data.'
                ], 500);
            }
            $hasChange = true;
        } else {

            $rv_fund_id = $data['rv_fund_id'];

            $oldFund = DB::table("revolving_funds")->where("id", $rv_fund_id)->pluck("fund")->first();
            $oldFundCA = DB::table("revolving_funds")->where("id", $rv_fund_id)->pluck("cash_advances")->first();
            if($data["ex"] == 'PS'){
                $funds = $oldFund? $oldFund + $data["fund"]: 0  ; 
                $cashAd = $oldFundCA? $oldFundCA + $data['cash_advances']: 0;
                $fund = $data["fund"] = $funds;
                $cash_advances = $data['cash_advances'] = $cashAd;
            }else{
                $funds = $oldFund? $oldFund - $data["fund"]: 0  ; 
                $cashAd = $oldFundCA? $oldFundCA -  $data['cash_advances']: 0;
                $fund = $data["fund"] = $funds;
                $cash_advances = $data['cash_advances'] = $cashAd;
            }
            

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
        $items = collect(AvailRevolvingFundOnHandSummaryResource::collection(Branch::select("id", "name")->whereNotIn("id",[49,55,56, 71,69] )->get()));

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
        $solve = DB::table("onhand_history")
                 ->select(DB::raw("CASE WHEN LEFT(ornumber, 2) = 'RC' AND revolvingfund != 0 THEN CONCAT('(', '₱', FORMAT(revolvingfund, 2), ')') ELSE CONCAT('₱', FORMAT(revolvingfund, 2)) END AS revolvingfund"),
                          DB::raw("CASE WHEN LEFT(ornumber, 2) = 'RC' AND cashadvance != 0 THEN CONCAT('(', '₱', FORMAT(cashadvance, 2), ')') ELSE CONCAT('₱', FORMAT(cashadvance, 2)) END AS cashadvance"),
                          DB::raw("CONCAT('₱', FORMAT(balance, 2)) AS balance"),
                          "ornumber",
                          "created_at",
                          DB::raw("LEFT(ornumber, 2) AS explode"),

                         "revolvingfund AS revolvingfund1",
                         "cashadvance AS cashadvance1"
                 )
                 ->where("branch_id", $branchid)
                 ->get()->toArray();

        foreach($solve as $index => $sum){
            $sum1[] = $sum->explode == 'RC'? ($sum->revolvingfund1 + $sum->cashadvance1) * -1 : $sum->revolvingfund1 + $sum->cashadvance1;
            $bal[] = array_sum($sum1);
            $out[] = [
                      "revolvingfund" => $sum->revolvingfund, 
                      "cashadvance" => $sum->cashadvance, 
                      "balance" => '₱'.number_format($bal[$index], 2), 
                      "ornumber" =>  $sum->ornumber, 
                      "created_at" =>  $sum->created_at, 
                      "explode" =>  @$sum->explode, 
                      "revolvingfund1" =>  $sum->revolvingfund1, 
                      "cashadvance1" =>  $sum->cashadvance1 
                  
             ]; 
        }

        
        foreach($out  as $d){
            if($d['explode'] == 'PS'){
             $rvsum[] = $d['revolvingfund1'];
             $casum[] = $d['cashadvance1'];
          
            }
            if($d['explode'] == 'RC'){
             $rvsub[] = $d['revolvingfund1'];
             $casub[] = $d['cashadvance1'];
          
            }
        }
        @$revTotal = array_sum(@$rvsum) - array_sum(@$rvsub) ;
        @$caTotal = array_sum(@$casum) - array_sum(@$casub);
        $balTotal = $revTotal - $caTotal;
        $out[] = ['ornumber' => 'TOTAL',
                      'revolvingfund'=> '₱'.number_format($revTotal, 2 ), 
                      'cashadvance'=> '₱'.number_format($caTotal, 2 ),
                      'balance' => '₱'.number_format($balTotal, 2 )];
        return response()->json($out);
         
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
    public function mssqlcon(){
        return \Auth::user()->dbselection->connection;
    }
    public function glaccount(){


        $full_link = "http://192.168.1.19:7771/glaccount.json";
        $client = new Client;
        $response = $client->request('GET', $full_link);
       return $response_body = json_decode($response->getBody()) ;
            
        $branchID = \Auth::user()->branch_id;
        $getBranch = DB::table("branches")->where("id" , $branchID)->pluck('name')->first();
        $q = DB::connection($this->mssqlcon())
                           ->select("select FormatCode,AcctName FROM OACT where AcctName LIKE '%$getBranch%'  AND AcctName LIKE '%Expense%' ORDER BY AcctName ASC");
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
