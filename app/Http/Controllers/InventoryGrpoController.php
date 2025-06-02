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
    #SERVER3 http://192.168.1.3:8082
    #SERVER2 http://192.168.1.26:8082
    #SERVER1
    return \Auth::user()->backend;
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
            'A.DocEntry', 'A.LineNum', 'A.TargetType', 'A.TrgetEntry', 'A.BaseRef',
            'A.BaseType', 'A.BaseEntry', 'A.BaseLine', 'A.LineStatus', 'A.ItemCode',
            'A.Dscription', 'A.Quantity', 'A.ShipDate', 'A.OpenQty', 'A.Price',
            'A.Currency', 'A.Rate', 'A.DiscPrcnt', 'A.LineTotal', 'A.TotalFrgn',
            'A.OpenSum', 'A.OpenSumFC', 'A.VendorNum', 'A.SerialNum', 'A.WhsCode',
            'A.SlpCode', 'A.Commission', 'A.TreeType', 'A.AcctCode', 'A.TaxStatus',
            'A.GrossBuyPr', 'A.PriceBefDi', 'A.DocDate', 'A.Flags', 'A.OpenCreQty',
            'A.UseBaseUn', 'A.SubCatNum', 'A.BaseCard', 'A.TotalSumSy', 'A.OpenSumSys',
            'A.InvntSttus', 'A.OcrCode', 'A.Project', 'A.CodeBars', 'A.VatPrcnt',
            'A.VatGroup', 'A.PriceAfVAT', 'A.Height1', 'C.FirmName','F.Comments',
            \DB::raw('CASE WHEN A.Quantity = A.OpenQty THEN 1 ELSE 0 END AS Remaining'),
            \DB::raw('(A.Quantity - A.OpenQty) AS createdFor')
        )
          ->join('OITM as B', 'A.ItemCode', '=', 'B.ItemCode')
          ->join('OMRC as C', 'C.FirmCode', '=', 'B.FirmCode')
          ->join('OPOR as F', 'A.DocEntry', '=', 'F.DocEntry')
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
    $all = ['key'=> json_decode($response->getBody()), 'data'=> $data2, 'barcoder'=> $user, 'comment'=> ''];
   
    return $all;
  }
  public function createdFunction(request $req){
    
    $user = \Auth::user()->barcoder;
    $database = explode(' -', \Auth::user()->dbselection->dbname);
    $req['db'] = $database[0];
    if(\Auth::user()->id == 2608){
      $data = DB::connection('mysql-qportal-test')->table('po')->where('DocEntry', $req->data)->get();
    }else{
      try{
        if($req->check == 0){
          if(\Auth::user()->id == 2606){
            $data = DB::connection('mysql-qportal-test')->table('po')->where('DocEntry', $req->data)->get();
          }else{
            $data = DB::connection('mysql-qportal')->table('po')->where('DocEntry', $req->data)->get();
          }
   
        }else{
          if(\Auth::user()->id == 2606){
            $data = DB::connection('mysql-qportal-test')->table('po')->where('DocEntry', $req->data)->get();
          }else{
            $data = DB::connection('mysql-qportal')->table('po')->where('DocEntry', $req->data)->where('status', 0)->get();
          }
          
        }
      }catch(\Exception $e){
        if(\Auth::user()->id == 2606){
          $data = DB::connection('mysql-qportal-test')->table('po')->where('DocEntry', $req->data)->get();
        }else{
          $data = DB::connection('mysql-qportal')->table('po')->where('DocEntry', $req->data)->get();
        }
    
      }
       
      
    }
    
         

   
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
    return "";
    return DB::connection('mysql-qportal')->table('serial')->where('mapid', $req->data)->first();
    // $client = new Client();
    // $data = $client->request('GET', ($this->ip()).'/api/progress?data='.$req->data)->getBody()->getContents();
    // $p['status'] = json_decode($data);
    // return response()->json($p);
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
  // public function grporeports($data){
    
  //   try {
  //     $database = $this->mssqlcon();
  //     // $expo = explode('-',$data);
  //     // $po = $expo[0];
  //     // $qty = (int)$expo[1];
  //     // $line = (int)$expo[2];
      
  //     function getDocentry($mapid){
  //       if(\Auth::user()->id == 2608){
  //         return DB::connection('mysql-qportal-test')->table('serial')->where('mapid', $mapid)->pluck('docentry')->first();
  //       }else{
  //         return DB::connection('mysql-qportal')->table('serial')->where('mapid', $mapid)->pluck('docentry')->first();
  //       }
        
  //     }
  //     // function getSn($map){
  //     //   return DB::connection('mysql-qportal')->table('mapline')->where('mapline', $map)->get();
  //     // }
  //     function getItems($docentry, $database){
  //       return DB::connection($database)->table('pdn1')->where('docentry', $docentry)->get();
  //     }
  //     function getProdCat($itemcode, $database){
  //       return DB::connection($database)->table('oitm')->where('ItemCode', $itemcode)->select('FrgnName', 'FirmCode')->get();
  //     }
  //     function getBrand($firmcode, $database){
  //       return DB::connection($database)->table('omrc')->where('FirmCode', $firmcode)->pluck('FirmName')->first();
  //     }
  //     function getHeading($docentry, $database){
  //       return DB::connection($database)->table('opdn')->where('DocEntry', $docentry)->select('DocNum','DocDate','CardName','Comments','NumAtCard');

  //     }

  //     $form = getItems(getDocentry($data), $database);
  //     $heading = getHeading(getDocentry($data), $database)->get();
  //     $data = [];
  //     $sumqty = [];
  //     foreach($form as $x){
  //       $data[] = ["prodcat"=> getProdCat($x->ItemCode, $database)[0]->FrgnName,
  //       "brand"=> getBrand(getProdCat($x->ItemCode, $database)[0]->FirmCode, $database),
  //       "model"=> $x->Dscription, "po"=> $x->BaseEntry, "qty"=> $x->Quantity, "serial"=> $x->Text];
  //       $sumqty[] = $x->Quantity;
  //     }
  //     return ["head"=> $heading, "item"=> $data, "total"=> $sumqty];
  //   } catch (\Exception $e) {
  //     return $e;
  //   }
 
  // }
  public function grporeportsgen($req, $table,$head){
     
    try {
 
     $database = $this->mssqlcon();
     #$database = '7279f466b64f2099266553eba43fef48';
      function getDocentry($DocEntry,$database,$tbl){
        return DB::connection($database)->table($tbl)->where('DocEntry',  $DocEntry)->get();
         
        
      }
      function Docentry($mapid){
        if(\Auth::user()->id == 2608){
          return DB::connection('mysql-qportal-test')->table('serial')->where('mapid', $mapid)->pluck('docentry')->first();
        }else{
          if(\Auth::user()->id == 2606){
             return DB::connection('mysql-qportal-test')->table('serial')->where('mapid', $mapid)->pluck('docentry')->first();
          }else{
            return DB::connection('mysql-qportal')->table('serial')->where('mapid', $mapid)->pluck('docentry')->first();
          }
        }
        
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
      function getHeading($docentry, $database,$head){
        return DB::connection($database)->table($head)->where('DocEntry', $docentry)->select('DocNum','DocDate','CardName','Comments','NumAtCard');

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
         // ->where('T4.ApplyType', 67)
         ->whereBetween('T4.AppDocNum', [$docnum, $docnum])
         ->whereBetween('T4.ApplyEntry', [$docenty, $docenty])
         ->where('T4.ItemCode',  $itemcode)
         ->groupBy('T0.AbsEntry')
         ->get();   
      }
 
      $docentry = Docentry($req);
      
      $form = getDocentry($docentry,$database, $table);
   
      $whs = explode('-', $form[0]->WhsCode);
       
      $getbranch = DB::table('branches')->where('whscode', 'LIKE', '%'.$whs[0].'%')->get() ;
      
      $heading = getHeading($docentry, $database,$head)->get();
      $data = [];
      $sumqty = [];
       
      foreach($form as $x){
        
        $input = $x->Text;
        $cleaned = trim((string)$input);
        $parts = array_filter(explode(" ", $cleaned));
        $parts = array_values($parts);
        
        // Step 4: Check if index 0 exists
        if (isset($parts[0])) {
            $d = "✅ Value: " . $parts[0];
        } else {
            $d = "❌ Walang laman si index 0";
        }
        $data[] = ["prodcat"=> getProdCat($x->ItemCode, $database)[0]->FrgnName,
            "brand"=> getBrand(getProdCat($x->ItemCode, $database)[0]->FirmCode, $database),
            "model"=> $x->Dscription,
            "whs"=> $x->WhsCode,
            "qty"=> (float)$x->Quantity, 
            "serial"=> $parts  ];
            //getSerial($docentry,$docentry,$x->ItemCode,$database)]
        $sumqty[] = $x->Quantity;
      }
     
      return ["head"=> $heading, "item"=> $data, "total"=> $sumqty, "additional"=> $getbranch];
    } catch (\Exception $e) {
      return $e;
    }
 
  }
 
  public function printreceiving(request $req){
    
  
    $client = new Client(['timeout' => 300000]);
   function format_json($serials){
            $serial = [];
            foreach($serials as $sn){
               $serial [] = $sn;
            }
            return $serial;
   }
   function format_reports($index, $data,$comment,$branch,$docnum,$date,$NumAtCard){
        return  ["brand" => $data['brand'],
                "prodcat" => $data['prodcat'],
                "Description" => $data['model'],
                "Warehouse" => $data['whs'],
                "qty" => $data['qty'],
                "serial" => format_json($data['serial']),
                "name" => $branch,
                "no" => $docnum,
                "date" => date('Y/m/d', strtotime($date)) ,
                "comment" => $comment,
                "reportname"=> "Goods Receipt PO",
                "ref"=> $NumAtCard];
   }
  
 
   $reports = $this->grporeportsgen($req->data, 'pdn1','opdn');
   $comment = @$reports['head'][0]->Comments;
   $date = @$reports['head'][0]->DocDate;
   $branch = @$reports['additional'][0]->name;
   $docnum = @$reports['head'][0]->DocNum;
   $NumAtCard = $reports['head'][0]->NumAtCard;
   $data = [];
    
   foreach($reports['item'] as $index=> $rep){
      $data[] = format_reports($index,$rep,$comment,$branch,$docnum,$date, $NumAtCard);
   }
    
   $response = $client->post('http://192.168.200.11:8004/api/reports/crystal/grpo?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjoiYWRtaW4iLCJleHAiOjIwNTc3MjQ3NDd9.0F5ZFHigMNt732EHIFd7azram_PWHIC5RGkkz8wqEz8', [
    'headers' => ['Content-Type' => 'application/json'],
    'body' => json_encode($data),
    ]);

    file_put_contents('InventoryTransfer-Report-'.$date.'.pdf', $response->getBody());
    $response = response()->download('InventoryTransfer-Report-'.$date.'.pdf');
    $response->headers->set('Access-Control-Expose-Headers', 'Content-Disposition');
    return $response;
    $reports = $this->grporeportsgen($req->data, 'pdn1','opdn' );
 
    $head = $reports['head'];
    $rep = $reports['item'];
    //return view('grpobarcode.receivingreports',compact('head','rep'));
    //return view('grpobarcode.receivingreports',  ["head"=> $head, "rep"=> $rep, "total"=> array_sum($reports['total'])]);
    $pdf = PDF::loadView("grpobarcode.receivingreports", ["head"=> $head, "rep"=> $rep, "total"=> array_sum($reports['total'])])->setPaper('letter', 'portrait');
    return $pdf->download('sample.pdf')->header('Access-Control-Expose-Headers', 'Content-Disposition'); 
  }
  public function checksn(request $req){
    return 0;
    return DB::connection($this->mssqlcon())->table('OSRI')->select("IntrSerial as sn")->where('ItemName', $req->model)->where('IntrSerial',  $req->sn)->get();
  }
  public function getlogs(request $req){
    
      return DB::connection('diapidata')->table('grpologs')->where('mapid', $req->data)->pluck('logs')->first();
     
  }
  public function getUncreatedSn(){
    $code = DB::table('branches')->where('id', \Auth::user()->branch_id)->pluck('whscode')->first();
    $dd = explode(",", $code);
        if( $dd[0] == 'ADMN'){
          $whs = ["ADMN","ADM2","ANON","EASY","THRE","STEA","ELEC","OUTX","ISAB","APPT","CAMA","MIAW"];
        }else{
          $whs = $dd;
        }
        return DB::connection('mysql-qportal')
        ->table('po')
        ->where('status', 0)
        ->where(function($q) use ($whs) {
            foreach ($whs as $base) {
                // this covers everything that *contains* that base code
                $q->orWhere('WhsCode', 'like', "%{$base}%");
            }
        })
        ->select('docentry')
        ->get();
    // $companies = \Auth::user()->branch->companies;
    // $branch = DB::table('branches')->where('')
    //return DB::connection('mysql-qportal')->table('po')->where('status', 0)->whereIn('WhsCode', 'like', '%'.$whs.'%')->select('docentry')->get();
  }
}
