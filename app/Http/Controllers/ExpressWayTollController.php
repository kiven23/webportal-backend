<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExpressWayDriver;
use App\ExpressWayToll;
use App\ExpressWayUpload;
use DB;
class ExpressWayTollController extends Controller
{
    public function index(Request $req){
        $get = ExpressWayUpload::with(['getDrivers' => function($q){
            $q->with('getTollways');
        }])->get();
        return  $get;
    }
    public function view(Request $req){
        $get = ExpressWayDriver::where('map', $req->map)->with('getTollways')->get();
    
        return view('motorpoolprinting.expressway.expresswaytoll', compact('get'));
 
    }
}
