<?php

namespace App\Http\Controllers\AvailRvFundOnHandHelper;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\RevolvingFund;

class AvailRvFundOnHandHelper
{
    public static function createIfNotExisted($branch_id = 0)
    {
        $rvFundBranchQuery = RevolvingFund::select('branch_id')->whereNotIn('id', function ($query) {
            $query->select('id')->from('revolving_funds')->whereRaw('DATE(revolving_funds.created_at) = DATE(?)', [date("Y-m-d")]);
        })->distinct();

        if (!Auth::user()->hasPermissionTo("Show All Revolving Funds")) {
            $rvFundBranchQuery->where('branch_id', $branch_id);
        }

        DB::beginTransaction();
        try {
            foreach ($rvFundBranchQuery->pluck('branch_id') as $rvFundBranchId) {
                $createData = RevolvingFund::select('fund', 'cash_advances', 'avail_fund_on_hand')->where('branch_id', $rvFundBranchId)->orderBy("created_at", "desc")->first()->toArray();
                $createData['branch_id'] = $rvFundBranchId;
                self::createAvailOnHandForToday($createData);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->errorInfo];
        }

        return ['success' => true, 'message' => 'Records has been successfully inserted'];
    }

    public static function createAvailOnHandForToday($data)
    {
        $data["as_of"] = date("Y-m-d");
        return RevolvingFund::create($data);
    }
}
