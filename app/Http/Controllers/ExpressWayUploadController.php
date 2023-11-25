<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ExpressWayDriver;
use App\ExpressWayToll;
use App\ExpressWayUpload;
use DB;

class ExpressWayUploadController extends Controller
{

    public function upload(Request $req){
        
        $json = $req;
        function compute($data){
            foreach($data as $tt){
                   $total[] = $tt['pay'];
            }
            return array_sum($total);
        }
        $pdfname = $json[1]['additional']['filename'];
        $path = $json[1]['additional']['path'];
        $idkeymap = md5($json);
       
        foreach($json[0]['all']  as $data){
           if(isset($data['info'] )){
             $uid = md5($data['info']['driver'].$data['info']['plateno'].date('Y-m-d H:i:s').$path.$pdfname);
           }
           if(isset($data['row'])){
                $arr = [];
                foreach($data['row'] as $tollway){
                    if (isset($tollway['Posted']) && strtotime($tollway['Posted']) !== false) {
                          $asof[] = $tollway['Posted'];
                          if (preg_match('/\b(\d{1,3}(?:,\d{3})*\.\d{2})\b/', $tollway['Description'], $matches)) {
                            $pay = str_replace(',', '', $matches[1]); 
                          } 
                            $parts = explode(' ', $tollway['Description']);
                            foreach ($parts as $part) {
                                if ($part === "NLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "CAVITEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "CALAX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "SLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "SKYWAY") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "NAIAX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "STAR") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "TPLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                                if ($part === "CLLEX") {
                                    $nlex = $part;
                                    break; 
                                }
                            }
                            if (isset($nlex)) {
                                $toll[] =  $nlex;
                            } 
                            $arr[] = ["uid"=> $uid,
                                      "posted"=> $tollway['Posted'], 
                                      "pay"=> $pay,
                                      "tollway"=> $nlex,
                                      "data"=> $tollway['Description'],
                                    ];
                     } 
                }
           }
           if(isset($data['info'])){
           $das[] = ["uid"=> $uid,
                     "map"=> $idkeymap,
                     "driver"=> $data['info']['driver'],
                     "department"=>$data['info']['department'],
                     "brand"=>$data['info']['brand'],
                     "model"=>$data['info']['model'],
                     "plateno"=>$data['info']['plateno'],
                     "total"=>compute($arr),
                     "expressData"=> $arr];  
           }
        }
 
        sort($asof);
        $lowestDate = $asof[0];
        $highestDate = end($asof);
        $newDate = $lowestDate.' -> '.$highestDate;
       
        $checked = ExpressWayUpload::where('uid', $idkeymap)->pluck('uid')->first();
        if(!$checked){
            $execute = new ExpressWayUpload();
            $execute->uid = $idkeymap;
            $execute->pdfname = $pdfname;
            $execute->path = $path;
            $execute->asof = $newDate;
            $execute->save();
            foreach($das as $all){
             
                $driver = new ExpressWayDriver();
                $driver->map = $all['map'];
                $driver->uid = $all['uid'];
                $driver->driver = $all['driver'];
                $driver->department = $all['department'];
                $driver->brand = $all['brand'];
                $driver->model = $all['model'];
                $driver->total = $all['total'];
                $driver->plate = $all['plateno'];
                $driver->save();
                foreach($all['expressData'] as $express){
                    $toll = new ExpressWayToll();
                    $toll->uid = $express['uid'];
                    $toll->posted = $express['posted'];
                    $toll->pay = $express['pay'];
                    $toll->data = $express['data'];
                    $toll->toll1 = $express['tollway'] == 'NLEX'? $express['pay'] : NULL;
                    $toll->toll2 = $express['tollway'] == 'CAVITEX'? $express['pay'] : NULL;
                    $toll->toll3 = $express['tollway'] == 'CALAX'? $express['pay'] : NULL;
                    $toll->toll4 = $express['tollway'] == 'SLEX'? $express['pay'] : NULL;
                    $toll->toll5 = $express['tollway'] == 'SKYWAY'? $express['pay'] : NULL;
                    $toll->toll6 = $express['tollway'] == 'NAIAX'? $express['pay'] : NULL;
                    $toll->toll7 = $express['tollway'] == 'STAR'? $express['pay'] : NULL;
                    $toll->toll8 = $express['tollway'] == 'TPLEX'? $express['pay'] : NULL;
                    $toll->toll9 = $express['tollway'] == 'CLLEX'? $express['pay'] : NULL;
                    $toll->save();
                }
            }
            return 0;
        }else{
            return 1;
        }
        
        
         
    }
}
