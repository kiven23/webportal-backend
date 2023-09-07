<?php
namespace App\Exec\RevolvingFund;
use DB;
use Auth;
class ReplenishmentHistory{
    function createHistory($data, $id){
            
            DB::table('rv_fund_expenses_for_check_preparations_history')->INSERT([
                "mapid"=> $id,
                "rv_fund_id" => $data->rv_fund_id,
                "pcv_date" => $data->pcv_date,
                "amount" => $data->amount,
                "tin" => $data->tin,
                "glaccounts" => $data->glaccounts,
                "payee" => $data->payee,
                "status" => 'None',
                "branch_id"=> \Auth::user()->branch_id
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
                    "payee"=> $content['payee'],
                    "status"=> "Transfer To Expenses For Check Preparation"
                     ]
                    );
        }
        return "update history";
    }
    function updateHistoryByID($data){
        
            DB::table('rv_fund_expenses_for_check_preparations_history')->where('mapid', $data['id'])
            ->update(
                        [
                        "amount"=> $data['amount'],
                        "tin"=> $data['tin'],
                        "glaccounts"=> $data['glaccounts'],
                        "payee"=> $data['payee'],
                        ]
                    );
       
        return "update history";
    }
}
 
?>