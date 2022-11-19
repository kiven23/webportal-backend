<?php
namespace App\Exec\RevolvingFund;
use Auth;
use DB;
class checkAvailableBudget{
     function checkAmount($avail_fund_on_hand, $amount){
        if($avail_fund_on_hand > $amount){
            $check = 1;
        }else{
            $check = 0;
        }
        return $check;
    }
     function getBranchID(){
      return  $branch = \Auth::user()->branch->id;
    }
}
 
?>