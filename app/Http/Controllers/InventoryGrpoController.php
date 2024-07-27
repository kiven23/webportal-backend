<?php

namespace App\Http\Controllers;
 
use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use DB;
use PDF;

class InventoryGrpoController extends Controller
{
  public function ip(){
    return "http://192.168.1.26:8082";
  }
  public function mssqlcon(){
    return \Auth::user()->dbselection->connection;
  }
  public function createGrpo(request $req){
    
    $database = explode(' - ', \Auth::user()->dbselection->dbname);
    $req['ip'] = $database[1];
    $req['db'] = $database[0];
    //$sapuser = \Auth::user()->sapuser;
    // $sappassword = \Auth::user()->sappasword;
    $client = new Client(['timeout' => 300000]);
 
    $response = $client->post(($this->ip()).'/api/grpo', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($req->all()),
         
    ]);
    
    $body = ($response->getBody());
    return $body;
  }
  public function getLines(request $req){
    $client = new Client();
    $data = $client->request('GET', ($this->ip()).'/api/document/getlines?data='.$req->data)->getBody()->getContents();
     
    return response()->json(json_decode($data));
  }
  public function createGrpo1(request $req){
  
    $database = explode(' - ', \Auth::user()->dbselection->dbname);
    $req['ip'] = $database[1];
    $req['db'] = $database[0];
    //$sapuser = \Auth::user()->sapuser;
    // $sappassword = \Auth::user()->sappasword;
    $client = new Client(['timeout' => 300000]);
 
    $response = $client->post(($this->ip()).'/api/document/grpo', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($req->all()),
         
    ]);
    
    $body = ($response->getBody());
    return $body;
  }
  public function search(request $req){
    
      $user = \Auth::user()->barcoder;
      $database = explode(' -', \Auth::user()->dbselection->dbname);
      $req['db'] = $database[0];
      
      $code = DB::table('branches')->where('id', \Auth::user()->branch_id)->pluck('whscode')->first();
      
      $dd = explode(",", $code);
      if( $dd[0] == 'ADMN'){
        $whs = ["ADMN","ADM2","ANON","EASY","THRE","STEA","ELEC","OUTX","ISAB","APPT","CAMA", "MIAW"];
      }else{
        $whs = $dd[0];
      }
      
       $data = \DB::connection($this->mssqlcon())
          ->table('POR1 as A')
          // ->select('A.DocEntry as DocEntry, A.VisOrder as VisOrder', 'C.FirmName')  
          ->select(
            'A.DocEntry', 'A.VisOrder as LineNum', 'A.TargetType', 'A.TrgetEntry', 'A.BaseRef',
            'A.BaseType', 'A.BaseEntry', 'A.BaseLine', 'A.LineStatus', 'A.ItemCode',
            'A.Dscription', 'A.Quantity', 'A.ShipDate', 'A.OpenQty', 'A.Price',
            'A.Currency', 'A.Rate', 'A.DiscPrcnt', 'A.LineTotal', 'A.TotalFrgn',
            'A.OpenSum', 'A.OpenSumFC', 'A.VendorNum', 'A.SerialNum', 'A.WhsCode',
            'A.SlpCode', 'A.Commission', 'A.TreeType', 'A.AcctCode', 'A.TaxStatus',
            'A.GrossBuyPr', 'A.PriceBefDi', 'A.DocDate', 'A.Flags', 'A.OpenCreQty',
            'A.UseBaseUn', 'A.SubCatNum', 'A.BaseCard', 'A.TotalSumSy', 'A.OpenSumSys',
            'A.InvntSttus', 'A.OcrCode', 'A.Project', 'A.CodeBars', 'A.VatPrcnt',
            'A.VatGroup', 'A.PriceAfVAT', 'A.Height1', 'C.FirmName'
        )
          ->join('OITM as B', 'A.ItemCode', '=', 'B.ItemCode')
          ->join('OMRC as C', 'C.FirmCode', '=', 'B.FirmCode')
          ->where('A.DocEntry', $req->data);
          // ->whereIn(\DB::raw('LEFT(A.WhsCode, 4)'), $whs)
          // ->whereRaw('LEFT(A.WhsCode, 4) = ?', [$whs])  
          // ->get();

          if(( is_array($whs))){
            //MAIN WAREHOUSE
            $data = $data->whereIn(\DB::raw('LEFT(A.WhsCode, 4)'), $whs);
          }else{
            //BRANCH
            $data = $data->whereRaw('LEFT(A.WhsCode, 4) = ?', [$whs]);
          }
            $data2 = $data->get();

      
      $client = new Client(['timeout' => 300000]);
 
    $response = $client->post(($this->ip()).'/api/document/getkey', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($req->all()),
         
    ]);
    $all = ['key'=> json_decode($response->getBody()), 'data'=> $data2, 'barcoder'=> $user];
   
    return $all;
  }
  public function viewpos(){
 
      function recheckdata($re,$auth){
        
        $user = \Auth::user()->barcoder;
        $database = explode(' -', \Auth::user()->dbselection->dbname);
        $req['db'] = $database[0];
        
        $code = DB::table('branches')->where('id', \Auth::user()->branch_id)->pluck('whscode')->first();
        
        $dd = explode(",", $code);
        if( $dd[0] == 'ADMN'){
          $whs = ["ADMN","ADM2","ANON","EASY","THRE","STEA","ELEC","OUTX","ISAB","APPT","CAMA","MIAW"];
        }else{
          $whs = $dd[0];
        }
      
         $data = \DB::connection($auth)
            ->table('POR1 as A')
            // ->select('A.*', 'C.FirmName')  
            ->select(
              'A.DocEntry', 'A.VisOrder as LineNum', 'A.TargetType', 'A.TrgetEntry', 'A.BaseRef',
              'A.BaseType', 'A.BaseEntry', 'A.BaseLine', 'A.LineStatus', 'A.ItemCode',
              'A.Dscription', 'A.Quantity', 'A.ShipDate', 'A.OpenQty', 'A.Price',
              'A.Currency', 'A.Rate', 'A.DiscPrcnt', 'A.LineTotal', 'A.TotalFrgn',
              'A.OpenSum', 'A.OpenSumFC', 'A.VendorNum', 'A.SerialNum', 'A.WhsCode',
              'A.SlpCode', 'A.Commission', 'A.TreeType', 'A.AcctCode', 'A.TaxStatus',
              'A.GrossBuyPr', 'A.PriceBefDi', 'A.DocDate', 'A.Flags', 'A.OpenCreQty',
              'A.UseBaseUn', 'A.SubCatNum', 'A.BaseCard', 'A.TotalSumSy', 'A.OpenSumSys',
              'A.InvntSttus', 'A.OcrCode', 'A.Project', 'A.CodeBars', 'A.VatPrcnt',
              'A.VatGroup', 'A.PriceAfVAT', 'A.Height1', 'C.FirmName'
          )
            ->join('OITM as B', 'A.ItemCode', '=', 'B.ItemCode')
            ->join('OMRC as C', 'C.FirmCode', '=', 'B.FirmCode')
            ->where('A.DocEntry', $re);
            if(( is_array($whs))){
              $data = $data->whereIn(\DB::raw('LEFT(A.WhsCode, 4)'), $whs);
            }else{
              $data = $data->whereRaw('LEFT(A.WhsCode, 4) = ?', [$whs]);
            }
            return $data->count();
            
  
      }
     $data = \DB::connection($this->mssqlcon())->select("SELECT  CardCode,CardName,DocEntry
                FROM OPOR
                WHERE DocStatus = 'O'
                ORDER BY DocDate DESC
               
                ");
//      $data = \DB::connection($this->mssqlcon())->select("SELECT  CardCode,CardName,DocEntry
//                 FROM OPOR
//                 WHERE DocStatus = 'O'
                
//                -- WHERE YEAR(DocDate) = YEAR(GETDATE())
//                -- WHERE MONTH(DocDate) = MONTH(GETDATE())
//                 WHERE DocEntry = '91205'
//                 ORDER BY DocDate DESC");
      //RECHECK
    
      foreach($data as $recheck) {
        
       $information = recheckdata($recheck->DocEntry,$this->mssqlcon());
        if($information != 0){
          $filtered[] = $recheck;
        }  
          
        
      }
      return Response()->json($filtered);
  }
  public function progress(Request $req){
    $client = new Client();
    $data = $client->request('GET', ($this->ip()).'/api/progress?data='.$req->data)->getBody()->getContents();
    $p['status'] = json_decode($data);
    return response()->json($p);
  }
  public function print(request $req){
    $value = json_decode(base64_decode($req->id));
    $barcodeGenerator = new BarcodeGeneratorHTML();
    $forbr =  $value;
    $model = $req->model;
    $brand = $req->brand;
    
    // Generate a barcode for a given value (e.g., product code)
    function barcoders($code, $barcodeGenerator){
     return  $barcodeGenerator->getBarcode($code, $barcodeGenerator::TYPE_CODE_128);
    }
     
    foreach($forbr as $br){
      $new[$br] =  ['br'=>barcoders($br, $barcodeGenerator), 'code'=> $br];
    }
      
    foreach($forbr as $br){
      $new2[] =  ['code'=> $br];
    }
     
    // Pass the generated barcode to the view
     return  view('barcodetemplates.invtransfer', compact('new','brand', 'model','new2'));
  }
  public function download(request $req){
    $barcodeGenerator = new BarcodeGeneratorHTML();
    $forbr = $req->data;
    // Generate a barcode for a given value (e.g., product code)
    function barcoders($code, $barcodeGenerator){
     return  $barcodeGenerator->getBarcode($code, $barcodeGenerator::TYPE_CODE_128);
    }
    foreach($forbr as $br){
      $new[$br] =  ['br'=>barcoders($br, $barcodeGenerator), 'code'=> $br];
    }
    $pdf = PDF::loadView("grpobarcode.generator", ["data"=> $new]);
    return $pdf->download('sample.pdf')->header('Access-Control-Expose-Headers', 'Content-Disposition'); 
  }
}
