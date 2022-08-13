<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class GiftCodeLogsController extends Controller
{
    public function index(request $req){
         
    $data = DB::table('gift_code_logs')->where('code', 'LIKE', '%'.$req->search.'%')
        ->get()->toArray();
        function check($d){
            $month = date_format(date_create('1957-01-21 00:00:00.000'), "m");
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
