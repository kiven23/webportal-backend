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
        return "http://192.168.1.26:8001";
    }
    public function mssqlcon(){
        return \Auth::user()->dbselection->connection;
    }
    //SAP TABLES BACK-END
    public function SapTables($t){
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
    //LIST OF SAP GETTERS
    public function GettersItems(Request $req){
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
                        return $this->SapTables('items')
                        ->where('ItemName', 'LIKE', '%'.$req->search.'%')
                        ->where('OnHand', '>', 0)
                        ->paginate(10);
                    }else{
                        return $this->SapTables('items') 
                        ->orderby('CreateDate', 'DESC')
                        ->where('OnHand', '>', 0)
                        ->paginate(10);
                    }
                }
            }elseif($req->get == 'itembywarehouse'){
                // if($req->search !== 'undefined'){
                //     return $this->SapTables('itembywarehouse')
                //     ->select('ItemCode','WhsCode','OnHand','IsCommited','OnOrder')
                //     ->where('WhsCode', 'LIKE', '%'.$req->search.'%')
                //     ->where('OnHand', '>', 0)
                //     ->paginate(10);
    
                // }else{
            
                    return $this->SapTables('itembywarehouse')
                    ->select('ItemCode','WhsCode','OnHand','IsCommited','OnOrder')
                    ->where('ItemCode', $req->itemcode)
                    ->where('OnHand', '>', 0)
                    ->paginate(10);
    
              
            }elseif($req->get == 'availablesn'){
                return $this->SapTables('availablesn')
                    ->select('IntrSerial','ItemCode','WhsCode')
                    ->where('ItemCode', $req->itemcode)
                    ->where('WhsCode', $req->warehouse)
                    ->where('Status', $req->status)
                    ->get();
            }elseif($req->get == 'gl'){
                return $this->SapTables('gl')
                ->select('AcctCode','AcctName','CurrTotal')
                ->get();
            }elseif($req->get == 'goodsissuelist'){
                return $this->SapTables('goodsissuelist')
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
     }
    
     public function send(request $req){
            
            $client = new Client(['timeout' => 300000]);
            
         
          
            $response = $client->post(($this->ip()).'/api/inventory/goodsissue', [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($req->all()),
                 
            ]);
            $body = ($response->getBody());
            return $body;
            
     }
  
}
