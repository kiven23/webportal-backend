<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\RvFundCheckVoucherVerification;
use App\Exec\RevolvingFund\checkAvailableBudget;
use DB;
class RvFundCheckVoucherVerificationController extends Controller
{
    private $check = null;

    public function __construct(){
      $this->check = new checkAvailableBudget();
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'rv_fund_id' => 'required',
            'date_transmitted' => 'required|date',
            'ck_no' => 'required|integer',
            'amount' => 'required|numeric'
        ], [
            'ck_no.integer' => 'Check no. must me be a digits',
        ]);
        $budget = DB::table('revolving_funds')
                ->where('branch_id', $this->check->getBranchID())
                ->pluck('avail_fund_on_hand')
                ->first();

        $data["status"] = "Pending";
        if($this->check->checkAmount($budget, $request->amount) == 1){
            if (!$item = RvFundCheckVoucherVerification::create($data)) {
                return response()->json([
                    'message' => 'Failed in saving data.'
                ], 500);
            }
        }else{
            return response()->json([
                'message' => 'Failed in saving data Due to Negative Balance, Please Contact Addessa Admin.'
            ], 500);
        }

        return response()->json([
            'item' => $item,
            'total' => $this->getAmtTotal($item->rv_fund_id),
            'message' => 'Record has been successfully added'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date_transmitted' => 'required|date',
            'ck_no' => 'required|integer',
            'amount' => 'required|numeric'
        ], [
            'ck_no.integer' => 'Check no. must me be a digits',
        ]);

        $rvChkVoucherVerification = RvFundCheckVoucherVerification::find($id);
        if (!$rvChkVoucherVerification) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        $data = $request->except('id');

        if (!$rvChkVoucherVerification->update($data)) {
            return response()->json([
                'message' => 'Failed in saving data.'
            ], 500);
        }

        return response()->json([
            'item' => $rvChkVoucherVerification,
            'total' => $this->getAmtTotal($rvChkVoucherVerification->rv_fund_id),
            'message' => 'Record has been successfully updated'
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {

        $rvChkVoucherVerification = RvFundCheckVoucherVerification::find($id);
        if (!$rvChkVoucherVerification) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        $data = $request->except('id');

        if (!$rvChkVoucherVerification->update($data)) {
            return response()->json([
                'message' => 'Failed in saving data.'
            ], 500);
        }

        return response()->json([
            'item' => $rvChkVoucherVerification,
            'message' => 'Record status has been successfully updated'
        ], 200);
    }

    public function destroy($id)
    {
        $rvChkVoucherVerification = RvFundCheckVoucherVerification::find($id);
        if (!$rvChkVoucherVerification) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }
       $verify = RvFundCheckVoucherVerification::find($id);
       $verify->verify = 1;
       $verify->update();
        // if (!$rvChkVoucherVerification->delete()) {
        //     return response()->json([
        //         'message' => 'Failed in deleting data.'
        //     ], 500);
        // }

        return response()->json([
            'total' => $this->getAmtTotal($rvChkVoucherVerification->rv_fund_id),
            'message' => 'Record status has been successfully deleted'
        ], 200);
    }

    private function getAmtTotal($rv_fund_id)
    {
        return RvFundCheckVoucherVerification::where('rv_fund_id', $rv_fund_id)->sum('amount');
    }
}
