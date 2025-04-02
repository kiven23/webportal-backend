<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use DB;
use Auth;
use PDF;
class InventorySapBackendController extends Controller
{
    public function ip(){
        return \Auth::user()->backend;
    }
    public function mssqlcon(){
        return \Auth::user()->dbselection->connection;
    }
    public function getdatabase(){
        $db = DB::table('custom_db')->where('entryname', $this->mssqlcon())->select('dbname','server')->get();
        return $db[0];
    }
## -------------------------------------------GOODSISSUE AND GOODSRECEIPT -----------------------------------------------#
    //SAP TABLES BACK-END GOODSISSUE
    public function SapTablesGoodsIssue($t){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
        if($t == 'items'){
            return DB::connection($this->mssqlcon())->table('OITM') 
            ->select('ItemCode','ItemName','FrgnName','OnHand',
            'U_srp','U_RegNC','U_PresentNC','U_Freebies','U_cSizes');
        }elseif($t == 'itembywarehouse'){
            return DB::connection($this->mssqlcon())->table('OITW');
        }elseif($t == 'availablesn'){
            return DB::connection($this->mssqlcon())->table('OSRI');
        }elseif($t == 'gl'){
            return DB::connection($this->mssqlcon())->table('OACT');
        }elseif($t == 'goodsissuelist'){
            return DB::connection($this->mssqlcon())->table('OIGE');
        }else{
            return "ERROR WEW!!";
        }
       }
    }
     //SAP TABLES BACK-END GOODSRECEIPT
     public function SapTablesGoodsReceipt($t){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
        if($t == 'items'){
            return DB::connection($this->mssqlcon())->table('OITM') 
            ->select('ItemCode','ItemName','FrgnName','OnHand',
            'U_srp','U_RegNC','U_PresentNC','U_Freebies','U_cSizes');
        }elseif($t == 'itembywarehouse'){
            return DB::connection($this->mssqlcon())->table('OITW');
        }elseif($t == 'availablesn'){
            return DB::connection($this->mssqlcon())->table('OSRI');
        }elseif($t == 'gl'){
            return DB::connection($this->mssqlcon())->table('OACT');
        }elseif($t == 'goodsreceiptlist'){
            return DB::connection($this->mssqlcon())->table('OIGN');
        }else{
            return "ERROR WEW!!";
        }
       }
    }
    //SAP TABLES BACK-END INVENTORY TRANSFER
    public function SapTablesInventoryTransfer($t){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
            if($t == 'items'){
           
                return DB::connection($this->mssqlcon())->table('OITM') 
                ->select('ItemCode','ItemName','FrgnName','OnHand',
                'U_srp','U_RegNC','U_PresentNC','U_Freebies','U_cSizes');
                }elseif($t == 'itembywarehouse'){
                    return DB::connection($this->mssqlcon())->table('OITW');
                }elseif($t == 'availablesn'){
                    return DB::connection($this->mssqlcon())->table('OSRI');
                }elseif($t == 'gl'){
                    return DB::connection($this->mssqlcon())->table('OACT');
                }elseif($t == 'inventorytransferlist'){
                    return DB::connection($this->mssqlcon())->table('OWTR');
                }elseif($t == 'whslist'){
                    return DB::connection($this->mssqlcon())->table('OWHS');
                }elseif($t == 'udf'){
                    return DB::connection($this->mssqlcon())->table('UFD1');
                }else{
                return "ERROR WEW!!";
            }
        }
    }
    //LIST OF SAP GETTERS INVENTORYTRANSFER
    public function GettersItemsInventoryTransfer(Request $req){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
       //ARRAY DATA MANIPULATION
       function Recustomize($DocEntry, $db){
            return DB::connection($db)->table('WTR1')
            ->select('DocEntry','ItemCode','Dscription as ItemName','Quantity','WhsCode','AcctCode', 'Text')
            ->where('DocEntry', $DocEntry)->get();
       }
       //FUNCTION GET WAREHOUSE
       function Warehouse($functions,$itemCode){
          return  $warehouse = $functions->SapTablesInventoryTransfer('itembywarehouse')
            ->select('ItemCode','WhsCode')                
            ->where('ItemCode',  $itemCode)
            ->where('OnHand', '>', 0)
            ->get();
       }
       function checkserial($itemCode,$whs,$serial, $t){
            return  $t->SapTablesInventoryTransfer('availablesn')
            ->select('IntrSerial')
            ->where('ItemCode', $itemCode)
            ->where('WhsCode', $whs)
            ->where('IntrSerial', $serial)
            ->pluck('IntrSerial')
            ->first();
       }
       //END
       try {
            if($req->get == 'items'){
                if($req->page || $req){
                    if($req->search){
                        $v= $req->search;
                        $req->search = DB::connection($this->mssqlcon())->table('OSRN')->where('DistNumber', $req->search)->pluck('ItemCode')->first();
                         
                       $get = $this->SapTablesInventoryTransfer('items')
                        ->where('ItemCode', 'LIKE', '%'.$req->search.'%')
                        ->where('OnHand', '>', 0)
                        ->paginate(1)
                         ;
                         
                        foreach(Warehouse($this,$req->search) as $i){
                            
                            if(checkserial(@$get[0]->ItemCode,@$i->WhsCode,$v,$this)){
                                $out[] =  ['ItemCode' => @$get[0]->ItemCode,
                                'id'=> @$get[0]->ItemCode.'-'.@$i->WhsCode,
                                'WhsCode'=> @$i->WhsCode,
                                'ItemName' => @$get[0]->ItemName ,
                                'FrgnName' => @$get[0]->FrgnName,
                                'OnHand'	=>   @$get[0]->OnHand,
                                'U_srp'  =>	@$get[0]->U_srp,
                                'U_RegNC' =>	@$get[0]->U_RegNC,
                                'U_PresentNC' =>	@$get[0]->U_PresentNC,
                                'U_Freebies' =>	@$get[0]->U_Freebies,
                                'U_cSizes'	=> @$get[0]->U_cSizes
                            ];
                            }
                            
                        }if($out){
                            return $out;
                        }else{
                            return "";
                        }
                         
                    }else{
                        return "";
                        return $this->SapTablesInventoryTransfer('items') 
                        ->orderby('CreateDate', 'DESC')
                        ->where('OnHand', '>', 0)
                        ->paginate(10);
                    }
                }
            }elseif($req->get == 'itembywarehouse'){
          
                    return $this->SapTablesInventoryTransfer('itembywarehouse')
                    ->select('ItemCode','WhsCode','OnHand','IsCommited','OnOrder')
                     ->where('ItemCode', $req->itemcode)
                    ->where('OnHand', '>', 0)
                    ->paginate(10);
  
            }elseif($req->get == 'availablesn'){
                return $this->SapTablesInventoryTransfer('availablesn')
                    ->select('IntrSerial','ItemCode','WhsCode')
                    ->where('ItemCode', $req->itemcode)
                    ->where('WhsCode', $req->warehouse)
                   // ->where('Status', $req->status)
                    ->get();
            }elseif($req->get == 'gl'){
                return $this->SapTablesInventoryTransfer('gl')
                ->select('AcctCode','AcctName','CurrTotal')
                ->where('FrozenFor', 'N')
                ->get();
            }elseif($req->get == 'inventorytransferlist'){
                return $this->SapTablesInventoryTransfer('inventorytransferlist')
                ->select('DocEntry','DocNum','DocStatus','DocDate','Comments','JrnlMemo','Filler')
                ->orderby('DocDate', 'DESC')
                ->paginate(1);
            }elseif($req->get == 'whslist'){
                return $this->SapTablesInventoryTransfer('whslist')
                ->select('WhsCode')
                ->get();
            }elseif($req->get == 'index'){
                //plucking
                //?get=index&docentry={}
                return Recustomize($req->docentry, $this->mssqlcon());
            }elseif($req->get == 'udf'){
                return $this->SapTablesInventoryTransfer('udf')
                ->where('FieldID', $req->id)
                ->select('FldValue','Descr')
                ->get();
            }else{
                return "ERROR";
            }
       }catch(\Exception $e){
         return $e;
       }
    }else{
        return Response()->json(['error'=>'No Access']);
    }
     }
    //LIST OF SAP GETTERS GOODSISSUE
    public function GettersItemsGoodsIssue(Request $req){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
       //ARRAY DATA MANIPULATION
       function Recustomize($DocEntry, $db){
            return DB::connection($db)->table('IGE1')
            ->select('DocEntry','ItemCode','Dscription as ItemName','Quantity','WhsCode','AcctCode')
            ->where('DocEntry', $DocEntry)->get();
       }
       //END
       try {
            if($req->get == 'items'){
                if($req->page || $req){
                    if($req->search){
                        return $this->SapTablesGoodsIssue('items')
                        ->where('ItemName', 'LIKE', '%'.$req->search.'%')
                        ->where('OnHand', '>', 0)
                        ->paginate(10);
                    }else{
                        return $this->SapTablesGoodsIssue('items') 
                        ->orderby('CreateDate', 'DESC')
                        ->where('OnHand', '>', 0)
                        ->paginate(10);
                    }
                }
            }elseif($req->get == 'itembywarehouse'){
          
                    return $this->SapTablesGoodsIssue('itembywarehouse')
                    ->select('ItemCode','WhsCode','OnHand','IsCommited','OnOrder')
                    ->where('ItemCode', $req->itemcode)
                    ->where('OnHand', '>', 0)
                    ->paginate(10);
    
              
            }elseif($req->get == 'availablesn'){
                return $this->SapTablesGoodsIssue('availablesn')
                    ->select('IntrSerial','ItemCode','WhsCode')
                    ->where('ItemCode', $req->itemcode)
                    ->where('WhsCode', $req->warehouse)
                    ->where('Status', $req->status)
                    ->get();
            }elseif($req->get == 'gl'){
                return $this->SapTablesGoodsIssue('gl')
                ->select('AcctCode','AcctName','CurrTotal')
                ->where('FrozenFor', 'N')
                ->get();
            }elseif($req->get == 'goodsissuelist'){
                return $this->SapTablesGoodsIssue('goodsissuelist')
                ->select('DocEntry','DocNum','DocStatus','DocDate','Comments','JrnlMemo')
                ->orderby('DocDate', 'DESC')
                ->paginate(1);
            }elseif($req->get == 'index'){
                //plucking
                //?get=index&docentry={}
                return Recustomize($req->docentry, $this->mssqlcon());
            }else{
                return "ERROR";
            }
       }catch(\Exception $e){
         return $e;
       }
    }else{
        return Response()->json(['error'=>'No Access']);
    }
     }

      //LIST OF SAP GETTERS GOODSISSUE
    public function GettersItemsGoodsReceipt(Request $req){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
        //ARRAY DATA MANIPULATION
        function Recustomize($DocEntry, $db){
             return DB::connection($db)->table('IGN1')
             ->select('DocEntry','ItemCode','Dscription as ItemName','Quantity','WhsCode','AcctCode')
             ->where('DocEntry', $DocEntry)->get();
        }
        //END
        try {
             if($req->get == 'items'){
                 if($req->page || $req){
                     if($req->search){
                         return $this->SapTablesGoodsReceipt('items')
                         ->where('ItemName', 'LIKE', '%'.$req->search.'%')
                         ->where('OnHand', '>', 0)
                         ->paginate(10);
                     }else{
                         return $this->SapTablesGoodsReceipt('items') 
                         ->orderby('CreateDate', 'DESC')
                         ->where('OnHand', '>', 0)
                         ->paginate(10);
                     }
                 }
             }elseif($req->get == 'itembywarehouse'){
                     return $this->SapTablesGoodsReceipt('itembywarehouse')
                     ->select('ItemCode','WhsCode','OnHand','IsCommited','OnOrder')
                     ->where('ItemCode', $req->itemcode)
                     ->where('OnHand', '>', 0)
                     ->paginate(10);
     
               
             }elseif($req->get == 'availablesn'){
                 return $this->SapTablesGoodsReceipt('availablesn')
                     ->select('IntrSerial','ItemCode','WhsCode')
                     ->where('ItemCode', $req->itemcode)
                     ->where('WhsCode', $req->warehouse)
                     ->where('Status', $req->status)
                     ->get();
             }elseif($req->get == 'gl'){
                 return $this->SapTablesGoodsReceipt('gl')
                 ->select('AcctCode','AcctName','CurrTotal')
                 ->where('FrozenFor', 'N')
                 ->get();
             }elseif($req->get == 'goodsreceiptlist'){
                 return $this->SapTablesGoodsReceipt('goodsreceiptlist')
                 ->select('DocEntry','DocNum','DocStatus','DocDate','Comments','JrnlMemo')
                 ->orderby('DocDate', 'DESC')
                 ->paginate(1);
             }elseif($req->get == 'index'){
                 //plucking
                 //?get=index&docentry={}
                 return Recustomize($req->docentry, $this->mssqlcon());
             }else{
                 return "ERROR";
             }
        }catch(\Exception $e){
          return $e;
        }
    }else{
        return Response()->json(['error'=>'No Access']);
    }
      }
     public function sendGoodsIssue(request $req){
      if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
        try{
            if($req->all()){
            $data = ["db"=> ['dbname' => $this->getdatabase()->dbname,  'dbserver' => $this->getdatabase()->server], "data"=> $req->all()]; 
            $client = new Client(['timeout' => 300000]);
            $response = $client->post(($this->ip()).'/api/inventory/goodsissue', [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($data),  
            ]);
            $body = ($response->getBody());
            return $body;
        } 
        }catch(\Exception $e){
            return $e;
        } 
      }else{
        return Response()->json(['error'=>'No Access']);
      }
     }
     public function sendInventoryTransfer(request $req){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
          try{
              if($req->all()){
              $data = ["db"=> ['dbname' => $this->getdatabase()->dbname,  'dbserver' => $this->getdatabase()->server], "data"=> $req->all()]; 
              $client = new Client(['timeout' => 300000]);
              $response = $client->post(($this->ip()).'/api/inventory/stocktransfer', [
                  'headers' => ['Content-Type' => 'application/json'],
                  'body' => json_encode($data),  
              ]);
              $body = ($response->getBody());
              return $body;
          } 
          }catch(\Exception $e){
              return $e;
          } 
        }else{
          return Response()->json(['error'=>'No Access']);
        }
       }
     public function sendGoodsReceipt(request $req){
        try{
            if($req->all()){
                $data = ["db"=> ['dbname' => $this->getdatabase()->dbname,  'dbserver' => $this->getdatabase()->server], "data"=> $req->all()];
                $client = new Client(['timeout' => 300000]);
                $response = $client->post(($this->ip()).'/api/inventory/goodsreceipt', [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode($data),
                ]);
                $body = ($response->getBody());
                return $body;
            } 
        }catch(\Exception $e){
            return $e;
        }
    }
## ------------------------------------------- END GOODSISSUE AND GOODSRECEIPT -----------------------------------------------#


## -------------------------------------------  BUSINESS PARTNER CONTROLLER -----------------------------------------------#

public function SapTablesBusinessPartner($t,$search){
    if($t == 'bp'){
        //OCRD
        if($search){
            return DB::connection($this->mssqlcon())->table('OCRD')->where('CardName', 'LIKE', '%'.$search.'%');
        }else{
            return DB::connection($this->mssqlcon())->table('OCRD');
        }
         
    }elseif($t == 'series'){
        //NNM1
   
        return DB::connection($this->mssqlcon())->table('NNM1');
    }elseif($t == 'groupcode'){
        //OCRG
        return DB::connection($this->mssqlcon())->table('OCRG');
    }elseif($t == 'bank'){
        //ODSC
        return DB::connection($this->mssqlcon())->table('ODSC');
    }elseif($t == 'salesemployee'){
        //OSLP
        return DB::connection($this->mssqlcon())->table('OSLP');
    }elseif($t == 'paymentterm'){
        //OCTG
        return DB::connection($this->mssqlcon())->table('OCTG');
    }else{
        return "ERROR WEW!!";
    }
}
 
public function GettersBusinessPartner(Request $req){
    if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
            try {
                if($req->item == 'series'){
                return $this->SapTablesBusinessPartner('series','')->where('objectcode', 2)->select('Series','SeriesName')->get();
                }elseif($req->item == 'groupcode'){
                    return $this->SapTablesBusinessPartner('groupcode','')->select('GroupCode','GroupName','GroupType')->get();
                }elseif($req->item == 'bank'){
                    return $this->SapTablesBusinessPartner('bank','')->select('BankCode','BankName','DfltAcct','DfltBranch')->get();
                }elseif($req->item == 'salesemployee'){
                    return $this->SapTablesBusinessPartner('salesemployee','')->select('SlpCode','SlpName','Memo')->get();
                }elseif($req->item == 'bp'){
                    if($req->search){
                        return $this->SapTablesBusinessPartner('bp', $req->search)->paginate(1);
                    }else{
                        return $this->SapTablesBusinessPartner('bp','')->paginate(1);
                    }
                    
                }elseif($req->item == 'paymentterm'){
                    return $this->SapTablesBusinessPartner('paymentterm','')->select('GroupNum','PymntGroup')->get();
                }else{
                    return "ERROR";
                }
        }catch(\Exception $e){
            return $e;
        }
    }


}

public function sendNewBusinessPartner(request $req){
    if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
        try{
            
            if($req->all()){
                $data = ["db"=> ['dbname' => $this->getdatabase()->dbname,  'dbserver' => $this->getdatabase()->server], "data"=> $req->all()];
                $client = new Client(['timeout' => 300000]);
                $response = $client->post(($this->ip()).'/api/businesspartner', [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode($data),
                ]);
                $body = ($response->getBody());
                return $body;
            } 
        }catch(\Exception $e){
            return $e;
        }
  }
}

## ------------------------------------------- END BUSINESS PARTNER CONTROLLER-----------------------------------------------#

## ------------------------------------------- INVENTORY TRANSFER REPORTS GENERATION ----------------------------------------#

public function inventorytransferreports($req){
    try {
      // $database = $this->mssqlcon();
     $database = '7279f466b64f2099266553eba43fef48';
      function getDocentry($DocEntry,$database){
        return DB::connection($database)->table('wtr1')->where('DocEntry',  $DocEntry)->get();
      }
      function getItems($docentry, $database){
        return DB::connection($database)->table('pdn1')->where('docentry', $docentry)->get();
      }
      function getProdCat($itemcode, $database){
        return DB::connection($database)->table('oitm')->where('ItemCode', $itemcode)->select('FrgnName', 'FirmCode')->get();
      }
      function getBrand($firmcode, $database){
        return DB::connection($database)->table('omrc')->where('FirmCode', $firmcode)->pluck('FirmName')->first();
      }
      function getHeading($docentry, $database){
        return DB::connection($database)->table('owtr')->where('DocEntry', $docentry)->select('DocNum','DocDate','CardName','Comments','NumAtCard');

      }
      function getSerial($docnum,$docenty,$itemcode,$database){
         return DB::connection($database)->table('OSRN as T0')
         ->select(
             DB::raw('MIN(T0.DistNumber) as DistNumber'),
         )
         ->join('OITM as T1', 'T1.ItemCode', '=', 'T0.ItemCode')
         ->leftJoin('OSRQ as T2', function($join) {
             $join->on('T2.ItemCode', '=', 'T0.ItemCode')
                  ->on('T2.SysNumber', '=', 'T0.SysNumber')
                  ->where('T2.Quantity', '>', 0);
         })
         ->join('ITL1 as T3', function($join) {
             $join->on('T3.ItemCode', '=', 'T0.ItemCode')
                  ->on('T3.SysNumber', '=', 'T0.SysNumber');
         })
         ->join('OITL as T4', 'T4.LogEntry', '=', 'T3.LogEntry')
         ->leftJoin('OCRD as T5', 'T5.CardCode', '=', 'T4.CardCode')
         ->where('T1.InvntItem', 'Y')
         ->where('T4.ApplyType', 67)
         ->whereBetween('T4.AppDocNum', [$docnum, $docnum])
         ->whereBetween('T4.ApplyEntry', [$docenty, $docenty])
         ->where('T4.ItemCode',  $itemcode)
         ->groupBy('T0.AbsEntry')
         ->get();   
      }

      $form = getDocentry($req->DocEntry,$database);
      $whs = explode('-', $form[0]->WhsCode);
      $getbranch = DB::table('branches')->where('whscode', 'LIKE', '%'.$whs[0].'%')->get() ;
      $heading = getHeading($req->DocEntry, $database)->get();
      $data = [];
      $sumqty = [];
      foreach($form as $x){
        $data[] = ["prodcat"=> getProdCat($x->ItemCode, $database)[0]->FrgnName,
            "brand"=> getBrand(getProdCat($x->ItemCode, $database)[0]->FirmCode, $database),
            "model"=> $x->Dscription,
            "whs"=> $x->WhsCode,
            "qty"=> (float)$x->Quantity, 
            "serial"=> getSerial($req->DocNum,$req->DocEntry,$x->ItemCode,$database)];
        $sumqty[] = $x->Quantity;
      }
      return ["head"=> $heading, "item"=> $data, "total"=> $sumqty, "additional"=> $getbranch];
    } catch (\Exception $e) {
      return $e;
    }
 
  }
 
public function printInventorytransfer(request $req){
   $client = new Client(['timeout' => 300000]);
   function format_json($serials){
            $serial = [];
            foreach($serials as $sn){
               $serial [] = $sn->DistNumber;
            }
            return $serial;
   }
   function format_reports($index, $data,$comment,$branch,$docnum,$date){
        return  ["brand" => $data['brand'],
                "prodcat" => $data['prodcat'],
                "Description" => $data['model'],
                "Warehouse" => $data['whs'],
                "qty" => $data['qty'],
                "serial" => format_json($data['serial']),
                "name" => $branch,
                "no" => $docnum,
                "date" => date('Y/m/d', strtotime($date)) ,
                "comment" => $comment];
   }
   $reports = $this->inventorytransferreports($req);
   $comment = $reports['head'][0]->Comments;
   $date = $reports['head'][0]->DocDate;
   $branch = $reports['additional'][0]->name;
   $docnum = $reports['head'][0]->DocNum;
   $data = [];
   foreach($reports['item'] as $index=> $rep){
      $data[] = format_reports($index,$rep,$comment,$branch,$docnum,$date);
   }
    
   $response = $client->post('http://192.168.200.11:8004/api/reports/crystal?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiYWRtaW4iLCJleHAiOjIwNTc3MjQ3NDd9.0F5ZFHigMNt732EHIFd7azram_PWHIC5RGkkz8wqEz8', [
    'headers' => ['Content-Type' => 'application/json'],
    'body' => json_encode($data),
    ]);

    file_put_contents('InventoryTransfer-Report-'.$date.'.pdf', $response->getBody());
    $response = response()->download('InventoryTransfer-Report-'.$date.'.pdf');
    $response->headers->set('Access-Control-Expose-Headers', 'Content-Disposition');
    return $response;
   ## ------------------------------------------- END INVENTORY TRANSFER REPORTS CREATION --------------------------------------#
    #====================OLD CODE========================
   function checkqty($q){
        if($q > 80){
            return 2;
        }
        if($q < 80){
            return 5;
        }
   }
   $reports = $this->inventorytransferreports($req);
   $head = $reports['head'];
   $rep = $reports['item'] ;
   $additional = $reports['additional'];
   $items = [];
   $temp = [];
    foreach ($rep as $index => $display) {
        $temp[] = $display;
        
        // Once $temp has 2 items, push it to $items and reset $temp
        $t = [];
        if (count($temp) == checkqty( array_sum($reports['total']))) {
            foreach($temp as $c	){
                $t[] = (int)($c['qty']);
            }
            $items[] = ["branch"=>  $additional[0]->name,"items"=> $temp, "head"=> $head, "total"=>   $t ];
            $temp = [];
        }
    }
    // If there are remaining items (odd count), push them as well
    if (!empty($temp)) {
        foreach($temp as $c	){
            $t[] = (int)($c['qty']);
        }
        $items[] =["branch"=>  $additional[0]->name,"items"=> $temp, "head"=> $head, "total"=>   $t ];
    }
  //   return $items;
  return ["data" => $items ];
  return view('grpobarcode.inventory_reports',  ["data" => $items ]);
   $pdf = PDF::loadView('grpobarcode.inventory_reports',  ["data" => $items ]) ->setPaper('letter', 'portrait');
   return $pdf->download('inventory-transfer.pdf')->header('Access-Control-Expose-Headers', 'Content-Disposition'); 
   return ["additional"=> $additional[0],"head"=> $head, "rep"=> $rep, "total"=> array_sum($reports['total'],)];
    //return view('inventory_transfer.inventory_reports', compact(''));
   return view('grpobarcode.inventory_reports',  ["additional"=> $additional[0],"head"=> $head, "rep"=> $rep, "total"=> array_sum($reports['total'],)]);
//    $pdf = PDF::loadView("grpobarcode.inventory_reports", ["additional"=> $additional[0],"head"=> $head, "rep"=> $rep, "total"=> array_sum($reports['total'])]) ->setPaper('letter', 'portrait');
//     return $pdf->download('inventory-transfer.pdf')->header('Access-Control-Expose-Headers', 'Content-Disposition'); 
}



#####==========================PURCHASING AP CREDIT MEMO ==================================#####
    //SAP TABLES BACK-END INVENTORY TRANSFER
    public function sendapcmTransfer(request $req){
        
        
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
            $seriesname = \Auth::user()->branch->seriesname;
          try{
              if($req->all()){
              $data = ["db"=> ['dbname' => $this->getdatabase()->dbname,  'dbserver' => $this->getdatabase()->server], "data"=> $req->all(), "series"=>  \Auth::user()->branch->seriesname]; 
              $client = new Client(['timeout' => 300000]);
               
              $response = $client->post(($this->ip()).'/api/inventory/apcredit/memo', [
                  'headers' => ['Content-Type' => 'application/json'],
                  'body' => json_encode($data),  
              ]);
              $body = ($response->getBody());
              return $body;
          } 
          }catch(\Exception $e){
              return $e;
          } 
        }else{
          return Response()->json(['error'=>'No Access']);
        }
       }
    public function SapTablesAPCM($t){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
            if($t == 'items'){
           
                return DB::connection($this->mssqlcon())->table('OITM') 
                ->select('ItemCode','ItemName','FrgnName','OnHand',
                'U_srp','U_RegNC','U_PresentNC','U_Freebies','U_cSizes');
                }elseif($t == 'itembywarehouse'){
                    return DB::connection($this->mssqlcon())->table('OITW');
                }elseif($t == 'availablesn'){
                    return DB::connection($this->mssqlcon())->table('OSRI');
                }elseif($t == 'gl'){
                    return DB::connection($this->mssqlcon())->table('OACT');
                }elseif($t == 'inventorytransferlist'){
                    return DB::connection($this->mssqlcon())->table('ORPC');
                }elseif($t == 'whslist'){
                    return DB::connection($this->mssqlcon())->table('OWHS');
                }elseif($t == 'udf'){
                    return DB::connection($this->mssqlcon())->table('UFD1');
                }elseif($t == 'ocrd'){
                    return DB::connection($this->mssqlcon())->table('OCRD');
                }elseif($t == 'companies'){
                    $companyID = \Auth::user()->branch->companies;
                    return DB::table('companies')->where('id', $companyID )->pluck('name')->first();
       
                }else{
                return "ERROR WEW!!";
            }
        }
    }
    public function GettersItemsAPCM(Request $req){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
    //ARRAY DATA MANIPULATION
    function Recustomize($DocEntry, $db){
            return DB::connection($db)->table('RPC1')
            ->select('DocEntry','ItemCode','Dscription as ItemName','Quantity','WhsCode','AcctCode', 'Text')
            ->where('DocEntry', $DocEntry)->get();
    }
    //FUNCTION GET WAREHOUSE
    function Warehouse($functions,$itemCode){
        return  $warehouse = $functions->SapTablesAPCM('itembywarehouse')
            ->select('ItemCode','WhsCode')                
            ->where('ItemCode',  $itemCode)
            ->where('OnHand', '>', 0)
            ->get();
    }
    function checkserial($itemCode,$whs,$serial, $t){
            return  $t->SapTablesAPCM('availablesn')
            ->select('IntrSerial')
            ->where('ItemCode', $itemCode)
            ->where('WhsCode', $whs)
            ->where('IntrSerial', $serial)
            ->pluck('IntrSerial')
            ->first();
    }
    //END
    try {
            if($req->get == 'items'){
                if($req->page || $req){
                    if($req->search){
                        $v= $req->search;
                        $req->search = DB::connection($this->mssqlcon())->table('OSRN')->where('DistNumber', $req->search)->pluck('ItemCode')->first();
                        
                    $get = $this->SapTablesAPCM('items')
                        ->where('ItemCode', 'LIKE', '%'.$req->search.'%')
                        ->where('OnHand', '>', 0)
                        ->paginate(1)
                        ;
                        foreach(Warehouse($this,$req->search) as $i){
                            
                            if(checkserial(@$get[0]->ItemCode,@$i->WhsCode,$v,$this)){
                                $out[] =  ['ItemCode' => @$get[0]->ItemCode,
                                'id'=> @$get[0]->ItemCode.'-'.@$i->WhsCode,
                                'WhsCode'=> @$i->WhsCode,
                                'ItemName' => @$get[0]->ItemName ,
                                'FrgnName' => @$get[0]->FrgnName,
                                'OnHand'	=>   @$get[0]->OnHand,
                                'U_srp'  =>	@$get[0]->U_srp,
                                'U_RegNC' =>	@$get[0]->U_RegNC,
                                'U_PresentNC' =>	@$get[0]->U_PresentNC,
                                'U_Freebies' =>	@$get[0]->U_Freebies,
                                'U_cSizes'	=> @$get[0]->U_cSizes
                            ];
                            }
                            
                        }if($out){
                            return $out;
                        }else{
                            return "";
                        }
                        
                    }else{
                        return "";
                        return $this->SapTablesAPCM('items') 
                        ->orderby('CreateDate', 'DESC')
                        ->where('OnHand', '>', 0)
                        ->paginate(10);
                    }
                }
            }elseif($req->get == 'itembywarehouse'){
        
                    return $this->SapTablesAPCM('itembywarehouse')
                    ->select('ItemCode','WhsCode','OnHand','IsCommited','OnOrder')
                    ->where('ItemCode', $req->itemcode)
                    ->where('OnHand', '>', 0)
                    ->paginate(10);

            }elseif($req->get == 'availablesn'){
                return $this->SapTablesAPCM('availablesn')
                    ->select('IntrSerial','ItemCode','WhsCode')
                    ->where('ItemCode', $req->itemcode)
                    ->where('WhsCode', $req->warehouse)
                // ->where('Status', $req->status)
                    ->get();
            }elseif($req->get == 'gl'){
                return $this->SapTablesAPCM('gl')
                ->select('AcctCode','AcctName','CurrTotal')
                ->where('FrozenFor', 'N')
                ->get();
            }elseif($req->get == 'inventorytransferlist'){
                return $this->SapTablesAPCM('inventorytransferlist')
                ->select('DocEntry','DocNum','DocStatus','DocDate','Comments','JrnlMemo','Filler')
                ->orderby('DocDate', 'DESC')
                ->paginate(1);
            }elseif($req->get == 'whslist'){
                return $this->SapTablesAPCM('whslist')
                ->select('WhsCode')
                ->get();
            }elseif($req->get == 'index'){
                //plucking
                //?get=index&docentry={}
                return Recustomize($req->docentry, $this->mssqlcon());
            }elseif($req->get == 'udf'){
                return $this->SapTablesAPCM('udf')
                ->where('FieldID', $req->id)
                ->select('FldValue','Descr')
                ->get();
            }elseif($req->get == 'vendor'){
                return $this->SapTablesAPCM('ocrd')->select("CardName","CardCode")
                ->where('CardType', 'S')->where('validFor', 'y')->where('frozenFor', 'n')
                ->get();
            }elseif($req->get == 'companies'){
                return $this->SapTablesAPCM('companies');
            }else{
                return "ERROR";
            }
    }catch(\Exception $e){
        return $e;
    }
    }else{
        return Response()->json(['error'=>'No Access']);
    }
  }
  
#####==========================PURCHASING AP END ==============================#####


#####==========================PURCHASING AP INVOICE START ==================================#####
    
    //SAP TABLES BACK-END INVENTORY TRANSFER

     
    public function sendapInvoice(request $req){
        
       
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
            $seriesname = \Auth::user()->branch->seriesname;
          try{
              if($req->all()){
              $data = ["db"=> ['dbname' => $this->getdatabase()->dbname,  'dbserver' => $this->getdatabase()->server], "data"=> $req->items, "series"=>  \Auth::user()->branch->seriesname, "comments"=> $req->comments]; 
              $client = new Client(['timeout' => 300000]);
               
              $response = $client->post(($this->ip()).'/api/inventory/apcredit/invoice', [
                  'headers' => ['Content-Type' => 'application/json'],
                  'body' => json_encode($data),  
              ]);
              $body = ($response->getBody());
              return $body;
          } 
          }catch(\Exception $e){
              return $e;
          } 
        }else{
          return Response()->json(['error'=>'No Access']);
        }
       }
    public function SapTablesAPINVOICE($t){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
            if($t == 'items'){
           
                return DB::connection($this->mssqlcon())->table('OITM') 
                ->select('ItemCode','ItemName','FrgnName','OnHand',
                'U_srp','U_RegNC','U_PresentNC','U_Freebies','U_cSizes');
                }elseif($t == 'itembywarehouse'){
                    return DB::connection($this->mssqlcon())->table('OITW');
                }elseif($t == 'availablesn'){
                    return DB::connection($this->mssqlcon())->table('OSRI');
                }elseif($t == 'gl'){
                    return DB::connection($this->mssqlcon())->table('OACT');
                }elseif($t == 'inventorytransferlist'){
                    return DB::connection($this->mssqlcon())->table('OPCH')->where("DocType", "I");
                }elseif($t == 'whslist'){
                    return DB::connection($this->mssqlcon())->table('OWHS');
                }elseif($t == 'udf'){
                    return DB::connection($this->mssqlcon())->table('UFD1');
                }elseif($t == 'ocrd'){
                    return DB::connection($this->mssqlcon())->table('OCRD');
                }elseif($t == 'companies'){
                    $companyID = \Auth::user()->branch->companies;
                    return DB::table('companies')->where('id', $companyID )->pluck('name')->first();
       
                }else{
                return "ERROR WEW!!";
            }
        }
    }
    public function fetchCreatedAPCMExternal(){
   
        function getItem($icode){
          $d = DB::connection('mysql-qportal-test')->table('apcm_items')->where('docnum_id', $icode)->get();
          foreach($d as $item){
            $data[] = ["ItemCode"=>$item->itemcode, "DocNum"=> $item->docnum_id,"Quantity"=> $item->quantity, "WarehouseCode"=> $item->towarehouse, "SerialNumbers"=> json_decode($item->serialnumbers)];
       
          }
          return $data;
        }
        function getSourceVendor($v){
           return DB::table('companies')->where('name', $v)->pluck('sap_name')->first();
        }
        function getCardCode($t,$c){
           return DB::connection($t->mssqlcon())->table('OCRD')->where('CardName', $c)->pluck('CardCode')->first();
        }
        function getSeries($branch,$d, $objectcode){
           $seriesname = DB::table('branches')->where('id', $branch->id)->pluck('seriesname')->first();
           return DB::connection($d->mssqlcon())->table('NNM1')->where('ObjectCode',  $objectcode)->where('SeriesName', $seriesname)->pluck('series')->first();
        }
         $id = \Auth::user()->branch;
        $companies = DB::table('companies')->where('id', $id->companies)->pluck('sap_name')->first();
        $created = DB::connection('mysql-qportal-test')->table('apcm_created')->where('to_vendor', 'like', '%'.$companies.'%')->get();
        
        foreach( $created  as $i){
            
           $data [] = ["source_vendor"=> getSourceVendor($i->source_vendor),
                       "CardCode"=>  getCardCode($this,getSourceVendor($i->source_vendor)),
                       "Series"=> (int)getSeries($id, $this, $i->objectcode),
                       "objectcode"=>$i->objectcode,
                       "to_vendor"=> $i->to_vendor,
                       "docnumber"=> $i->docnum_id,
                       "status"=> $i->status,
                       "lines"=> getItem($i->docnum_id)
   
                       ];
        }
        return $data;
     }
    public function GettersItemsAPINVOICE(Request $req){
        if (\Auth::user()->hasRole(['SapB1FullAccess'])) {
             
    //ARRAY DATA MANIPULATION
    function Recustomize($DocEntry, $db){
            return DB::connection($db)->table('PCH1')
            ->select('DocEntry','ItemCode','Dscription as ItemName','Quantity','WhsCode','AcctCode', 'Text')
            ->where('DocEntry', $DocEntry)->get();
    }
    //FUNCTION GET WAREHOUSE
    function Warehouse($functions,$itemCode){
        return  $warehouse = $functions->SapTablesAPINVOICE('itembywarehouse')
            ->select('ItemCode','WhsCode')                
            ->where('ItemCode',  $itemCode)
            ->where('OnHand', '>', 0)
            ->get();
    }
    function checkserial($itemCode,$whs,$serial, $t){
            return  $t->SapTablesAPINVOICE('availablesn')
            ->select('IntrSerial')
            ->where('ItemCode', $itemCode)
            ->where('WhsCode', $whs)
            ->where('IntrSerial', $serial)
            ->pluck('IntrSerial')
            ->first();
    }
    //END
    try {
            if($req->get == 'items'){
                if($req->page || $req){
                    if($req->search){
                        $v= $req->search;
                        $req->search = DB::connection($this->mssqlcon())->table('OSRN')->where('DistNumber', $req->search)->pluck('ItemCode')->first();
                        
                    $get = $this->SapTablesAPINVOICE('items')
                        ->where('ItemCode', 'LIKE', '%'.$req->search.'%')
                        ->where('OnHand', '>', 0)
                        ->paginate(1)
                        ;
                        foreach(Warehouse($this,$req->search) as $i){
                            
                            if(checkserial(@$get[0]->ItemCode,@$i->WhsCode,$v,$this)){
                                $out[] =  ['ItemCode' => @$get[0]->ItemCode,
                                'id'=> @$get[0]->ItemCode.'-'.@$i->WhsCode,
                                'WhsCode'=> @$i->WhsCode,
                                'ItemName' => @$get[0]->ItemName ,
                                'FrgnName' => @$get[0]->FrgnName,
                                'OnHand'	=>   @$get[0]->OnHand,
                                'U_srp'  =>	@$get[0]->U_srp,
                                'U_RegNC' =>	@$get[0]->U_RegNC,
                                'U_PresentNC' =>	@$get[0]->U_PresentNC,
                                'U_Freebies' =>	@$get[0]->U_Freebies,
                                'U_cSizes'	=> @$get[0]->U_cSizes
                            ];
                            }
                            
                        }if($out){
                            return $out;
                        }else{
                            return "";
                        }
                        
                    }else{
                        return "";
                        return $this->SapTablesAPINVOICE('items') 
                        ->orderby('CreateDate', 'DESC')
                        ->where('OnHand', '>', 0)
                        ->paginate(10);
                    }
                }
            }elseif($req->get == 'itembywarehouse'){
        
                    return $this->SapTablesAPINVOICE('itembywarehouse')
                    ->select('ItemCode','WhsCode','OnHand','IsCommited','OnOrder')
                    ->where('ItemCode', $req->itemcode)
                    ->where('OnHand', '>', 0)
                    ->paginate(10);

            }elseif($req->get == 'availablesn'){
                return $this->SapTablesAPINVOICE('availablesn')
                    ->select('IntrSerial','ItemCode','WhsCode')
                    ->where('ItemCode', $req->itemcode)
                    ->where('WhsCode', $req->warehouse)
                // ->where('Status', $req->status)
                    ->get();
            }elseif($req->get == 'gl'){
                return $this->SapTablesAPINVOICE('gl')
                ->select('AcctCode','AcctName','CurrTotal')
                ->where('FrozenFor', 'N')
                ->get();
            }elseif($req->get == 'inventorytransferlist'){
                return $this->SapTablesAPINVOICE('inventorytransferlist')
                ->select('CardName','DocEntry','DocNum','DocStatus','DocDate','Comments','JrnlMemo','Filler')
                ->orderby('DocDate', 'DESC')
                ->paginate(1);
            }elseif($req->get == 'whslist'){
                return $this->SapTablesAPINVOICE('whslist')
                ->select('WhsCode')
                ->get();
            }elseif($req->get == 'index'){
                //plucking
                //?get=index&docentry={}
                return Recustomize($req->docentry, $this->mssqlcon());
            }elseif($req->get == 'udf'){
                return $this->SapTablesAPINVOICE('udf')
                ->where('FieldID', $req->id)
                ->select('FldValue','Descr')
                ->get();
            }elseif($req->get == 'vendor'){
                return $this->SapTablesAPINVOICE('ocrd')->select("CardName","CardCode")
                ->where('CardType', 'S')->where('validFor', 'y')->where('frozenFor', 'n')
                ->get();
            }elseif($req->get == 'companies'){
                return $this->SapTablesAPINVOICE('companies');
            }elseif($req->get == 'getcreatedapcexternal'){
                return $this->fetchCreatedAPCMExternal();
            }else{
                return "ERROR";
            }
    }catch(\Exception $e){
        return $e;
    }
    }else{
        return Response()->json(['error'=>'No Access']);
    }
  }
   
#####==========================PURCHASING AP INVOICE END ==============================#####
}
