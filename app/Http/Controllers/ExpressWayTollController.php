<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExpressWayDriver;
use App\ExpressWayToll;
use App\ExpressWayUpload;
use DB;
use Carbon\Carbon;
class ExpressWayTollController extends Controller
{
    public function index(Request $req){
         $get = ExpressWayUpload::with(['getruf' => function ($query) {
            $query->whereIn('typerfid', [0, 1]);
          }])
        ->get();
         
        foreach($get as $dd){
            foreach($dd['getruf'] as $s){
                $ss[] = $s->typerfid;
            }
            $sd[] = ['id'=>$dd['id'],'uid' => $dd['uid'],
             'rfid' => array_values(array_unique($ss)),
             'plateno'=> $dd['plateno'],
             'name'=>$dd['name'], 
             'asof'=> $dd['asof'],
             'created_at'=> date($dd->created_at) ];
              
           
        }

       return  $sd;
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
    public function reports(Request $req){
        try{
        $explode = explode(',' , $req->date);
        $datefrom = Carbon::parse($explode[0])->format('Y-m-d');
        $dateto = Carbon::parse($explode[1])->format('Y-m-d');
        
         $query = ExpressWayUpload::with(['getruf' => function ($qry) use ($datefrom, $dateto) {
            $qry->whereBetween('date', [$datefrom, $dateto]);
         }])
            ->with('getDrivers')
            ->get() ;
     
            
            foreach($query as $filters) {
                if(count($filters['getruf'])){
                    $groupbyplate[$filters['plateno']][] = $filters;
                }
               
            }
          
            
            $ds = [];
            $index = 0;
          foreach ($groupbyplate as $key => $plateNumber) {
            $test = [];
            $total = [];
            $i = 0;
            foreach ($plateNumber as $key => $getruf) {
                foreach ($getruf->getruf as $key => $data) {
                    if($data->typerfid == $req->type){
                        $test[$i++]=$data;
                        $total[] = $data->amount;
                    }
                    
                }
            }
            if(count($total)){
                $ds[$index++] = ['data'=> $test, 'grandtotals'=> array_sum($total) , 'type'=> $plateNumber[0]->getruf[0]['typerfid'] , 'drivers'=> $plateNumber[0]['getDrivers'], 'fromto'=> $datefrom.' - '.$dateto ];
            
            }
            
          }
          
        //   return $response;
               
        //     foreach( $query  as $d) {
               
        //         $total = [];
        //         foreach($d['getruf'] as $index=> $getruf){
        //             $total[] =  $getruf['amount'];
        //         }
        //         if(count($d['getruf'])){
 
        //          if( $d['getruf'][$index]['typerfid'] == $req->type){
        //                 $ds[]  = ['data'=> $d['getruf'],
        //                         'grandtotals'=> array_sum($total),
        //                         'drivers'=> $d['getDrivers'], 
        //                         'type'=> $d['getruf'][$index]['typerfid'], 
        //                         'fromto'=> $datefrom.' - '.$dateto ];
        //                 } 
 
        //         }
                
        //     }
        //  return $ds;
            $type = $req->type;
            return view('motorpoolprinting.expressway.expresswaytollreports', compact('ds','type'));

             } catch (\Exception $e) {
               return "NO RECORDS FOUND";
             } 
             
            #return $query;
            // if(isset($query)){
             // }else{
            //     return "Error Please Contact Stevefox_Linux @ISD addessa";
            // }
       
    }
    public function trash(Request $req){
         
        #check map first
        ExpressWayUpload::where('uid', $req->id)->delete();
         
         
             ExpressWayToll::where('uid', $req->id)->delete();
         
        
        
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
