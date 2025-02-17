<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use DB;
use Auth;
class InventorySapBackendController extends Controller
{
    public function ip(){
        return "http://192.168.1.26:8082";
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
       //END
       try {
            if($req->get == 'items'){
                if($req->page || $req){
                    if($req->search){
                        $req->search = DB::connection($this->mssqlcon())->table('OSRN')->where('DistNumber', $req->search)->pluck('ItemCode')->first();
                         
                        $get = $this->SapTablesInventoryTransfer('items')
                        ->where('ItemCode', 'LIKE', '%'.$req->search.'%')
                        ->where('OnHand', '>', 0)
                        ->paginate(1)
                         ;
                        foreach(Warehouse($this,$req->search) as $i){
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
                        return $out;
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
}
