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
    return "http://192.168.1.3:8082";
  }
  public function mssqlcon(){
    return \Auth::user()->dbselection->connection;
  }
  public function createGrpo(request $req){
    if (\Auth::user()->hasRole(['Master GRPO'])) {
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
    return "No Permission";
  }
  public function getLines(request $req){
    $client = new Client();
    $data = $client->request('GET', ($this->ip()).'/api/document/getlines?data='.$req->data)->getBody()->getContents();
     
    return response()->json(json_decode($data));
  }
  public function createGrpo1(request $req){
    if (\Auth::user()->hasRole(['Master GRPO'])) {
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
    return "No Permission";
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
            'A.VatGroup', 'A.PriceAfVAT', 'A.Height1', 'C.FirmName',
            \DB::raw('CASE WHEN A.Quantity = A.OpenQty THEN 1 ELSE 0 END AS Remaining'),
            \DB::raw('(A.Quantity - A.OpenQty) AS createdFor')
        )
          ->join('OITM as B', 'A.ItemCode', '=', 'B.ItemCode')
          ->join('OMRC as C', 'C.FirmCode', '=', 'B.FirmCode')
          ->where('A.DocEntry', $req->data);
          // ->whereIn(\DB::raw('LEFT(A.WhsCode, 4)'), $whs)
          // ->whereRaw('LEFT(A.WhsCode, 4) = ?', [$whs])  
          // ->get();

          if(( is_array($whs))){
            //MAIN WAREHOUSE
            $data = $data->whereIn(\DB::raw('LEFT(A.WhsCode, 4)'), $whs)->where('A.InvntSttus', $req->status);
          }else{
            //BRANCH
            $data = $data->whereRaw('LEFT(A.WhsCode, 4) = ?', [$whs])->where('A.InvntSttus', $req->status);
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
  public function createdFunction(request $req){
      
    $user = \Auth::user()->barcoder;
    $database = explode(' -', \Auth::user()->dbselection->dbname);
    $req['db'] = $database[0];
 
    $data = DB::connection('mysql-qportal')->table('po')->where('DocEntry', $req->data)->get();
         

   
    $client = new Client(['timeout' => 300000]);

  $response = $client->post(($this->ip()).'/api/document/getkey', [
      'headers' => ['Content-Type' => 'application/json'],
      'body' => json_encode($req->all()),
       
  ]);
  $all = ['key'=> json_decode($response->getBody()), 'data'=> $data, 'barcoder'=> $user];
 
  return $all;

  }
  public function viewpos(request $req){
     
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
              'A.VatGroup', 'A.PriceAfVAT', 'A.Height1', 'C.FirmName',
               
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
     $data = \DB::connection($this->mssqlcon())->select("SELECT TOP 40  CardCode,CardName,DocEntry
                FROM OPOR

                WHERE DocStatus = '$req->status'
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
  public function grporeports($data){
    
    try {
      $database = $this->mssqlcon();
      // $expo = explode('-',$data);
      // $po = $expo[0];
      // $qty = (int)$expo[1];
      // $line = (int)$expo[2];
      
      function getDocentry($mapid){
        
        return DB::connection('mysql-qportal')->table('serial')->where('mapid', $mapid)->pluck('docentry')->first();
      }
      // function getSn($map){
      //   return DB::connection('mysql-qportal')->table('mapline')->where('mapline', $map)->get();
      // }
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
        return DB::connection($database)->table('opdn')->where('DocEntry', $docentry)->select('DocNum','DocDate','CardName','Comments','NumAtCard');

      }

      $form = getItems(getDocentry($data), $database);
      $heading = getHeading(getDocentry($data), $database)->get();
      $data = [];
      $sumqty = [];
      foreach($form as $x){
        $data[] = ["prodcat"=> getProdCat($x->ItemCode, $database)[0]->FrgnName,
        "brand"=> getBrand(getProdCat($x->ItemCode, $database)[0]->FirmCode, $database),
        "model"=> $x->Dscription, "po"=> $x->BaseEntry, "qty"=> $x->Quantity, "serial"=> $x->Text];
        $sumqty[] = $x->Quantity;
      }
      return ["head"=> $heading, "item"=> $data, "total"=> $sumqty];
    } catch (\Exception $e) {
      return $e;
    }
 
  }
  public function printreceiving(request $req){
     
    $reports = $this->grporeports($req->data );
 
    $head = $reports['head'];
    $rep = $reports['item'];
    //return view('grpobarcode.receivingreports',compact('head','rep'));
    //return view('grpobarcode.receivingreports',  ["head"=> $head, "rep"=> $rep, "total"=> array_sum($reports['total'])]);
    $pdf = PDF::loadView("grpobarcode.receivingreports", ["head"=> $head, "rep"=> $rep, "total"=> array_sum($reports['total'])])->setPaper('letter', 'portrait');
    return $pdf->download('sample.pdf')->header('Access-Control-Expose-Headers', 'Content-Disposition'); 
  }
  public function checksn(request $req){
    return DB::connection('diapidata')->table('mapline')->where('brand', $req->brand)->where('sn', $req->sn)->get();
  }
}
