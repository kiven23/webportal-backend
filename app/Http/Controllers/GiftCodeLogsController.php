<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class GiftCodeLogsController extends Controller
{
    public function index(request $req){
        $data = DB::table('gift_code_logs')->where('code', 'LIKE', '%'.$req->search.'%')->get();
        $entries = ['count'=>  count($data), 'entries'=> $data];
        return response()->json($entries);
    }
}
