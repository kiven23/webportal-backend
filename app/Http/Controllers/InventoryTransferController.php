<?php

namespace App\Http\Controllers;

use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class InventoryTransferController extends Controller
{
    public function mssqlcon(){
        return \Auth::user()->dbselection->connection;
      }
    public function activator(){
      if(\Auth::user()->invtymap == 1){
        return ['primary'=> 'OWTR', 'secondary'=> 'WTR1'];
      }
      if(\Auth::user()->invtymap == 2){
        return ['primary'=> 'OINV', 'secondary'=> 'INV1'];
      }
      if(\Auth::user()->invtymap == 3){
        return ['primary'=> 'OPDN', 'secondary'=> 'PDN1'];
      }
     
    }
    public function setactivator(request $req){
     
      DB::table('users')->where('id', \Auth::user()->id)->update([
        'invtymap' => $req->routeid
      ]);
      return $req->routeid;
    }
    public function owtr(request $req){
        

      if($req->search){
        $search = \DB::connection($this->mssqlcon())
        ->table($this->activator()['primary'])
        ->select('DocEntry','DocNum','DocType','Comments', 'JrnlMemo', 'U_Name')
        ->where('DocNum', $req->search)->paginate(10) ;
      }else{
        $search = \DB::connection($this->mssqlcon())
        ->table($this->activator()['primary'])
        ->select('DocEntry','DocNum','DocType','Comments', 'JrnlMemo', 'U_Name')->paginate(10) ;
      }
      return $search;
    }
    public function wtr1(request $req){
      //GET ITEM BRAND
      function getBrand($itemcode,$db){
        $getFirmcode = \DB::connection($db)->table('oitm')->where('ItemCode', $itemcode)->pluck('FirmCode')->first();
        return  \DB::connection($db)->table('omrc')->where('FirmCode', $getFirmcode)->pluck('FirmName')->first();
      }
      
       $items = \DB::connection($this->mssqlcon())->table($this->activator()['secondary'])
       ->select('DocEntry','LineNum','ItemCode','Dscription','Quantity','ShipDate','WhsCode', 'DocDate')->where('DocEntry', $req->item)->get();
       $MYARRAY = [];
       foreach($items as $item){
        $MYARRAY[] = ['ItemCode'=> $item->ItemCode, 'Brand' => getBrand($item->ItemCode, $this->mssqlcon()) ,
                      'DocEntry'=> $item->DocEntry,
                      'LineNum'=>  $item->LineNum,
                      'Dscription'=>  $item->Dscription,
                      'Quantity'=>  $item->Quantity,
                      'ShipDate'=>  $item->ShipDate,
                      'WhsCode'=>  $item->WhsCode,
                      'DocDate'=>  $item->DocDate ];

       }
       return $MYARRAY;
    }
    public function osri(request $req){
   
         
      $var = [];
      for ($x = 0; $x <= 25; $x++) {
            $data = DB::connection($this->mssqlcon())->table('osri')  
                    ->where('ItemCode', $req->itemcode)
                    ->where('BaseLinNum', $x)
                    ->where('ItemName', $req->model)
                    ->where('Quantity', $req->qty)
                    //->where('InDate', $req->indate)
                    ->get();
          if(count($data)){
            array_push($var, $data);
          }
      }
      
      $barcodeGenerator = new BarcodeGeneratorHTML();
      $forbr = $var[0];
      $model = $req->model;
      $brand = $req->brand;
      // Generate a barcode for a given value (e.g., product code)
      function barcoders($code, $barcodeGenerator){
      return  $barcodeGenerator->getBarcode($code, $barcodeGenerator::TYPE_CODE_128);
      }
      foreach($forbr as $br){
        $new[$br->IntrSerial] =  ['br'=>barcoders($br->IntrSerial, $barcodeGenerator), 'code'=> $br->IntrSerial];
      }
     
      foreach($forbr as $br){
        $new2[] =  ['code'=> $br->IntrSerial];
      }
      return  view('barcodetemplates.invtransfer', compact('new','brand', 'model','new2'));
    }
}
