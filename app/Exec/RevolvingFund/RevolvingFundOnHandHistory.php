<?php
namespace App\Exec\RevolvingFund;
use DB;
class RevolvingFundOnHandHistory{
    function balance($fund, $cash){
        return $fund + $cash;
    }
    function createHistory($data){
            DB::table('onhand_history')->INSERT([
                "branch_id"=> $data->branch_id,
                "revolvingfund" => $data->fund,
                "cashadvance" => $data->cash_advances,
                "balance" => $this->balance($data->fund, $data->cash_advances),
                "incoming" => $data->incoming,
                "outgoing" => $data->outgoing,
                "created_at" => date("Y-m-d h:i:s"),
                "updated_at"=>  date("Y-m-d h:i:s")
            ]); 
        return "save history";
    }
    // function updateHistory($data){
    //     foreach($data['items'] as $content){
    //         DB::table('rv_fund_expenses_for_check_preparations_history')->where('mapid', $content['id'])
    //         ->update(
    //                 [
    //                 "amount"=> $content['amount'],
    //                 "tin"=> $content['tin'],
    //                 "glaccounts"=> $content['glaccounts'],
    //                 "status"=> "Transfer To Expenses For Check Preparation"
    //                  ]
    //                 );
    //     }
    //     return "update history";
    // }
}
 
?>