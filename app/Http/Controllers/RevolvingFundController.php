<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\RevolvingFund;
use App\RvFundAvailOnHand;

use App\Http\Resources\RevolvingFundResource;
use App\Http\Resources\RvFundWithExpenseItems;

use App\Http\Controllers\AvailRvFundOnHandHelper\AvailRvFundOnHandHelper;

use PDF;
use DB;
class RevolvingFundController extends Controller
{
    public function index()
    {
      $rvFundsQuery = RevolvingFund::first();
  
        if (!Auth::user()->hasPermissionTo("Show All Revolving Funds")) {
        //$rvFundsQuery->where('branch_id', 2);
        return RevolvingFundResource::collection($rvFundsQuery->where('branch_id', $this->getbranch()->id)->get());
        }
      // return $rvFundsQuery->where('branch_id', 4)->get();
        foreach($rvFundsQuery->get() as $item){
           $dd1[] = DB::table('rv_fund_check_voucher_for_transmittals')->where('rv_fund_id', $item->id)->pluck('rv_fund_id')->first();
           $dd2[] = DB::table('rv_fund_check_voucher_verifications')->where('rv_fund_id', $item->id)->pluck('rv_fund_id')->first();
           $dd3[] = DB::table('rv_fund_expenses_for_check_preparations')->where('rv_fund_id', $item->id)->pluck('rv_fund_id')->first();
           
        }
          
         $result = array_merge( $dd1,  $dd2,  $dd3);
        

        $id = array_filter($result);
        $rvFundsQuery = RevolvingFund::whereIn('id', $id)->get();
        return RevolvingFundResource::collection($rvFundsQuery)  ;
    }
    public function history(Request $req){

        if(\Auth::user()->branch_id == 1){
            $id = DB::table("branches")->where("name", $req->b)->pluck("id")->first();
            $branchID =  $id;
          
        }else{
            $branchID =  \Auth::user()->branch_id;
            
        }
 
        if($req->id == 1){
            if($req->date){
                $explode = explode("-", $req->date);
                $history = DB::table("rv_fund_expenses_for_check_preparations_history")
                ->where("tin", $req->id)
                ->where('branch_id', $branchID)
                ->whereYear("pcv_date",  $explode[0])
                ->whereMonth("pcv_date",  $explode[1])
                ->get();
            }
        }else{  
           $history = DB::table("rv_fund_expenses_for_check_preparations_history")
            ->where("branch_id", $branchID)
            ->get();
        }
      
        return $history;
    }
    public function CKhistory(request $req){
        if(\Auth::user()->branch_id == 1){
            $id = DB::table("branches")->where("name", $req->b)->pluck("id")->first();
            $branchID =  $id;
          
        }else{
            $branchID =  \Auth::user()->branch_id;
            
        }
 
        $history = DB::table("rv_fund_check_voucher_verifications")
                   ->join("revolving_funds" , "rv_fund_check_voucher_verifications.rv_fund_id", "=", "revolving_funds.id")
                   ->join("branches" , "revolving_funds.branch_id", "=", "branches.id")
                   
                   ->select("date_transmitted as DATETRANSMITTED", "ck_no as ck_no",
                   "status as status", "amount AS AMOUNT","fund AS fund","name AS BRANCH", "branch_id as branch_id")
                   ->where("STATUS", "Transmittal")
                   ->where("branch_id", $branchID)
                   ->get();
        return $history ;

    }
    public function toprint(request $req){
        if($req->stat == true){
            DB::table('rv_fund_check_voucher_verifications')->where('id', $req->id)->update(
                ['toprint' => 1]
            );
            return 1;
        }else{
            DB::table('rv_fund_check_voucher_verifications')->where('id', $req->id)->update(
                ['toprint' => NULL]
            );
           return NULL;
        }
    
         
    }
    public function printBIR(request $req){
         
        if($req->iden == 1){
            $id = DB::table("branches")->where("name", $req->b)->pluck("id")->first();
            $revid = DB::table('revolving_funds')->where('branch_id',  $id )->pluck('id')->first();
            $data = DB::table('rv_fund_check_voucher_verifications')->where('rv_fund_id', $revid)->where('status', 'Transmittal')->where('verify', NULL)->where('toprint', 1)->get();
            
            $branch =  $req->b;
            //return view("revolving_funds.reports.transmittal",compact("branch", "data"));
            $pdf = PDF::loadView("revolving_funds.reports.transmittal", compact("branch", "data"));
            return $pdf->stream();
        }
 
        if(\Auth::user()->branch_id == 1){
            $id = DB::table("branches")->where("name", $req->b)->pluck("id")->first();
            $branchID =  $id;
          
        }else{
            $branchID =  \Auth::user()->branch_id;
            
        }
         
        if($req->id == 1){
            if($req->date){
                
                $explode = explode("-", $req->date);
                $history = DB::table("rv_fund_expenses_for_check_preparations_history")
                ->where("tin", $req->id)
                ->where('branch_id', $branchID)
                ->whereYear("pcv_date",  $explode[0])
                ->whereMonth("pcv_date",  $explode[1])
                ->get();
                
             }
        }else{  
           $history = DB::table("rv_fund_expenses_for_check_preparations_history")
            ->where("branch_id", $branchID)
            ->get();
        }
        $asof = $req->date;
       
        //return view("revolving_funds.reports.withtin", compact("history", "asof"));
         $pdf = PDF::loadView("revolving_funds.reports.withtin", compact("history", "asof"));
                return $pdf->stream();
                        
    }
    
    
 

    // public function create(Request $request)
    // {
    //     $data = $request->validate([
    //         'as_of' => 'required|date',
    //         'fund' => 'required|numeric',
    //         'cash_advances' => 'nullable|numeric',
    //     ]);

    //     $data['branch_id'] = $this->getBranch()->id;

    //     $this->setCashAdvancesValue($data);

    //     if (!$rvFund = RevolvingFund::create($data)) {
    //         return response()->json([
    //             'message' => 'Failed in saving data.'
    //         ], 500);
    //     }

    //     return response()->json([
    //         'rv_fund' => new RevolvingFundResource($rvFund),
    //         'message' => 'New revolving fund has successfully added'
    //     ], 200);
    // }

    public function view($id)
    {
     
       $rvFund = RevolvingFund::find($id);
        if (!$rvFund) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        return new RvFundWithExpenseItems($rvFund);
    }

    public function updateCashAdvances(Request $request, $id)
    {
        $rvFund = RevolvingFund::find($id);
        if (!$rvFund) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        if (!$rvFund->update($request->all())) {
            return response()->json([
                'message' => 'Failed in updating fund.'
            ], 500);
        }

        return response()->json([
            'cashAdvances' => $rvFund->cash_advances,
            'message' => 'Cash Advances has been successfully updated'
        ], 200);
    }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'as_of' => 'required|date',
    //         'fund' => 'required|numeric',
    //         'cash_advances' => 'nullable|numeric',
    //     ]);

    //     $rvFund = RevolvingFund::find($id);
    //     if (!$rvFund) {
    //         return response()->json([
    //             'message' => 'Record not found.'
    //         ], 500);
    //     }

    //     $data = $request->except('id');
    //     $this->setCashAdvancesValue($data);

    //     if (!$rvFund->update($data)) {
    //         return response()->json([
    //             'message' => 'Failed in updating data.'
    //         ], 500);
    //     }

    //     return response()->json([
    //         'rv_fund' => new RevolvingFundResource($rvFund),
    //         'message' => 'Revolving fund has been successfully updated'
    //     ], 200);
    // }

    // public function deleteItems(Request $request)
    // {
    //     if (!RevolvingFund::destroy($request->ids)) {
    //         return response()->json([
    //             'message' => 'Failed in deleting data.'
    //         ], 500);
    //     }

    //     return response()->json([
    //         'message' => 'Revolving fund/s has been successfully deleted'
    //     ], 200);
    // }

    public function print($id)
    {
         
        $company_id = \Auth::user()->company_id;
        $whereCompany = DB::table('companies')->where('id', $company_id)->pluck('name')->first();
        $preparedverifiedby = \Auth::user()->first_name ." ".\Auth::user()->last_name;
        $rvFund = RevolvingFund::find($id);
        if (!$rvFund) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        $rvFundResouce = (new RvFundWithExpenseItems($rvFund));
        $data = $rvFundResouce->toArray(app('request'));
        //date_format($rvFund->created_at, "M d, Y")
         
        $data['submitted_date'] =  date("M d, Y");
        $data['tin'] = $rvFund->tin;
        $data['avail_fund_on_hand'] = $rvFund->avail_fund_on_hand;
        $data['preparedverifiedby'] = $preparedverifiedby;
        $data['company'] = $whereCompany;




        $pdf = PDF::loadView("revolving_funds.summary.revolving_fund_summary", $data);

        return $pdf->download("Summary_Of_Revolving_Fund.pdf")->header('Access-Control-Expose-Headers', 'Content-Disposition');
    }

    public function updateAvailRevolvingFundOnHand(Request $request, $id)
    {
        $rvFund = RevolvingFund::find($id);
        if (!$rvFund) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        if (!$rvFund->update($request->all())) {
            return response()->json([
                'message' => 'Failed in updating fund.'
            ], 500);
        }

        return response()->json([
            'message' => 'Available Revolving Fund on Hand has been successfully updated'
        ], 200);
    }

    public function preview($id)
    {
        $rvFund = RevolvingFund::find($id);
        if (!$rvFund) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        $rvFundResouce = (new RvFundWithExpenseItems($rvFund))->additional(['some_id => 1']);
        $data = $rvFundResouce->toArray(app('request'));
        $data['submitted_date'] = date_format($rvFund->created_at, "M d, Y");
        $data['avail_fund_on_hand'] = RvFundAvailOnHand::where('rv_fund_id', $rvFund->id)
            ->whereRaw('DATE(created_at) = DATE(?)', [date('Y-m-d')])
            ->first()->fund_on_hand;
        $pdf = PDF::loadView("revolving_funds.summary.revolving_fund_summary", $data);

        return $pdf->stream();
    }

    private function getBranch()
    {
        return Auth::user()->branch;
    }

    private function setCashAdvancesValue(&$data)
    {
        $data['cash_advances'] = $data['cash_advances'] ?: 0.00;
    }
}
