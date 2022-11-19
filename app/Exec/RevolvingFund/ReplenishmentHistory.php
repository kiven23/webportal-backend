<?php
namespace App\Exec\RevolvingFund;
use DB;
class ReplenishmentHistory{
    function createHistory($data, $id){
            DB::table('rv_fund_expenses_for_check_preparations_history')->INSERT([
                "mapid"=> $id,
                "rv_fund_id" => $data->rv_fund_id,
                "pcv_date" => $data->pcv_date,
                "amount" => $data->amount,
                "tin" => $data->tin,
                "glaccounts" => $data->glaccounts,
                "status" => 'None'
            ]); 
        return "save history";
    }
    function updateHistory($data){
        foreach($data['items'] as $content){
            DB::table('rv_fund_expenses_for_check_preparations_history')->where('mapid', $content['id'])
            ->update(
                    [
                    "amount"=> $content['amount'],
                    "tin"=> $content['tin'],
                    "glaccounts"=> $content['glaccounts'],
                    "status"=> "Transfer To Expenses For Check Preparation"
                ]
                    );
        }
        return "update history";
    }
}
 
?>