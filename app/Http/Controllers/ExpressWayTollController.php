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
        $get = ExpressWayUpload::all();
        return  $get;
    }
    public function new(Request $req){
        //$req->date;
        $uid = md5($req);
        $driver =  ExpressWayDriver::where('id', $req->driver)->first();
        
        $new = new ExpressWayUpload;
        $new->uid = $uid;
        $new->plateno = $driver->plate;
        $new->name = $driver->driver;
        $new->asof = $req->date;
         $new->save();
        
        foreach($req->ruf as $index){
            $toll = new ExpressWayToll;
            $toll->uid = $uid;
            $toll->date = $req->date;
            $toll->destination = $index['destination'];
            $toll->purpose = $index['purpose'];
            $toll->typerfid = $index['rfidtype'] == 'AutoSweep'? 0:1 ;
            $toll->typeewy = $index['exptype'];
            $toll->entrys = $index['entry'];
            $toll->exits = $index['exit'];
            $toll->amount = $index['amount'];
             $toll->save();
       
        }
        return "ok";
    }
    public function view(Request $req){
       try{
        $query = ExpressWayUpload::where('id', $req->uid)->with(['getruf'=> function($qry) use ($req){
            $qry->where('typerfid', $req->type);
          }])
          ->with('getDrivers')->first();
      

        foreach($query->getruf as $d) {
        $sum[] =  $d->amount;
        }
       
        $query['rfidtype'] = $req->type == 0? 'AUTOSWEET': 'EASYTRIP';
        $query['grandtotal'] =  array_sum($sum);
        if(isset($query)){
            return view('motorpoolprinting.expressway.expresswaytoll', compact('query'));
        }else{
            return "Error Please Contact Stevefox_Linux @ISD addessa";
        }
    } catch (\Exception $e) {
        return "No Record Created. Thank you..!";
    }
  
    }
    public function trash(Request $req){
        #check map first
        ExpressWayUpload::where('uid', $req->id)->delete();
        $check = ExpressWayDriver::where('map', $req->id)->get();
        foreach($check as $uid){
             ExpressWayToll::where('uid', $uid->uid)->delete();
        }
        ExpressWayDriver::where('map', $req->id)->delete();
        
        return "delete";

    }

    public function create(){

    }
    public function queries(request $req){
      return  $query = ExpressWayUpload::where('id', 12)->with(['getruf'=> function($qry){
        $qry->where('typerfid', 0);
      }])
      ->with('getDrivers')->get();
    }
}
