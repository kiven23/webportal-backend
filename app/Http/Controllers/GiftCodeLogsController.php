<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
class GiftCodeLogsController extends Controller
{
    public function index(request $req){
         
    $data = DB::table('gift_code_logs')->where('code', 'LIKE', '%'.$req->search.'%')
        ->get()->toArray();
        function check($d){
            $month = Carbon::now()->month;
            
            $bd = date_format(date_create($d), "m");
            if($month == $bd){
                $s = 'VALID';
            }else{
                $s = 'EXPIRED';
            }
            return $s;
        }
 
        foreach($data as $index => $stat){
            $data[$index]->status = check($stat->birthday);
        }   
      
        $entries = ['count'=>  count($data), 'entries'=> $data];
        return response()->json($entries);
    }
}
