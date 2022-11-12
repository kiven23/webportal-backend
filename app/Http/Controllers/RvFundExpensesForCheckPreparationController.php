<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\RvFundExpensesForCheckPreparation;
use App\RvFundCheckVoucherForTransmittal;

class RvFundExpensesForCheckPreparationController extends Controller
{
    public function create(Request $request)
    {
         
        $data = $request->validate([
            'rv_fund_id' => 'required',
            'pcv_date' => 'required|date',
            'particulars' => 'required',
            'amount' => 'required|numeric',
            'glaccounts'=> 'required'
        ]);

        if (!$item = RvFundExpensesForCheckPreparation::create($data)) {
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
            'pcv_date' => 'required|date',
            'particulars' => 'required',
            'amount' => 'required|numeric'
        ]);

        $rvExpensesForChkPreparation = RvFundExpensesForCheckPreparation::find($id);
        if (!$rvExpensesForChkPreparation) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        $data = $request->except('id');

        if (!$rvExpensesForChkPreparation->update($data)) {
            return response()->json([
                'message' => 'Failed in saving data.'
            ], 500);
        }

        return response()->json([
            'item' => $rvExpensesForChkPreparation,
            'total' => $this->getAmtTotal($rvExpensesForChkPreparation->rv_fund_id),
            'message' => 'Record has been successfully updated'
        ], 200);
    }

    public function destroy($id)
    {
        $rvExpensesForChkPreparation = RvFundExpensesForCheckPreparation::find($id);
        if (!$rvExpensesForChkPreparation) {
            return response()->json([
                'message' => 'Record not found.'
            ], 500);
        }

        if (!$rvExpensesForChkPreparation->delete()) {
            return response()->json([
                'message' => 'Failed in deleting data.'
            ], 500);
        }

        return response()->json([
            'total' => $this->getAmtTotal($rvExpensesForChkPreparation->rv_fund_id),
            'message' => 'Record status has been successfully deleted'
        ], 200);
    }

    public function replenish(Request $request)
    {
        $request->validate([
            'check_voucher_date' => 'required|date',
            'ck_no' => 'required|integer',
            'amount' => 'required|numeric'
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('items');
            $newItem =  RvFundCheckVoucherForTransmittal::create($data);
            foreach ($request->items as $item) {
                RvFundExpensesForCheckPreparation::destroy($item['id']);
            }
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
        return RvFundExpensesForCheckPreparation::where('rv_fund_id', $rv_fund_id)->sum('amount');
    }
}
