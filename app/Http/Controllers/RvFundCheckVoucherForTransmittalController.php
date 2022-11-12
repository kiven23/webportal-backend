<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\RvFundCheckVoucherForTransmittal;
use App\RvFundCheckVoucherVerification;

class RvFundCheckVoucherForTransmittalController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'rv_fund_id' => 'required',
            'check_voucher_date' => 'required|date',
            'ck_no' => 'required|integer',
            'amount' => 'required|numeric'
        ], [
            'ck_no.integer' => 'Check no. must me be a digits',
        ]);

        if (!$item = RvFundCheckVoucherForTransmittal::create($data)) {
            return response()->json([
                'message' => 'Failed in saving data.'
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
            'check_voucher_date' => 'required|date',
            'ck_no' => 'required|integer',
            'amount' => 'required|numeric'
        ], [
            'ck_no.integer' => 'Check no. must me be a digits',
        ]);

        $rvChkVoucherForTrans = RvFundCheckVoucherForTransmittal::find($id);
        if (!$rvChkVoucherForTrans) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        $data = $request->except('id');

        if (!$rvChkVoucherForTrans->update($data)) {
            return response()->json([
                'message' => 'Failed in saving data.'
            ], 500);
        }

        return response()->json([
            'item' => $rvChkVoucherForTrans,
            'total' => $this->getAmtTotal($rvChkVoucherForTrans->rv_fund_id),
            'message' => 'Record has been successfully updated'
        ], 200);
    }

    public function destroy($id)
    {
        $rvChkVoucherForTrans = RvFundCheckVoucherForTransmittal::find($id);
        if (!$rvChkVoucherForTrans) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        if (!$rvChkVoucherForTrans->delete()) {
            return response()->json([
                'message' => 'Failed in deleting data.'
            ], 500);
        }

        return response()->json([
            'total' => $this->getAmtTotal($rvChkVoucherForTrans->rv_fund_id),
            'message' => 'Record status has been successfully deleted'
        ], 200);
    }

    public function transmit(Request $request)
    {
        DB::beginTransaction();
        try {
            $item =  RvFundCheckVoucherForTransmittal::select('id', 'rv_fund_id', 'ck_no', 'amount')->where('id', $request->id)->first();
            $data = $item->toArray();
            $data["date_transmitted"] = date("Y-m-d");
            $data["status"] = "Pending";
            $newItem = RvFundCheckVoucherVerification::create($data);
            $item->delete();
            DB::commit();
            return response()->json([
                'item' => $newItem,
                'message' => 'Records has been successfully replenished'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    private function getAmtTotal($rv_fund_id)
    {
        return RvFundCheckVoucherForTransmittal::where('rv_fund_id', $rv_fund_id)->sum('amount');
    }
}
