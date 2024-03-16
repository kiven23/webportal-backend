<?php

namespace App\Http\Controllers;
use App\User;
use App\Branch;
use App\Company;
use App\Position;
use App\Department;
use App\FileSetting;
use App\AccessChart;
use App\AccessChartUserMap;
use App\UserEmployment as UEmployment;




use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\InstallMentLedger as ledger;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Auth;
class SapApiController extends Controller
{

    public function mssqlcon(){
        return \Auth::user()->dbselection->connection;
    }
    public function getBrandId($firmcode){
        return DB::connection($this->mssqlcon())->table('OMRC')->where('FirmCode', $firmcode)->pluck('FirmName')->first();
    }
    public function jdt1extract(request $req){
        
        // Define the path to the directory containing the split CSV files
        $directory = '/var/www/stevefoxlinux/jdt2/';
         
        // Loop through CSV files from split_1.csv to split_64.csv
        // for ($i = 1; $i <= 1; $i++) {
            $filename = "split_{$req->row}.csv";
            $path = $directory . $filename;
            
            if (file_exists($path)) {
                $data = array_map('str_getcsv', file($path));

                foreach ($data as $row) {
                    DB::statement("
                    INSERT IGNORE INTO jdt1 (TransId, Duedate, LineMemo, ContraAct, TransType, phase)
                    VALUES (?, ?, ?, ?, ?, ?)
                ", [$row[0], $row[1], $row[2], $row[3], $row[4], $filename]);
                }
            }
             
        // }

        return "Processing complete.";
    }
    public function checkbrand($id){
            //steadfordDB
            // ec24fd9907a1aed316b1a1509351a91b
            if($this->mssqlcon() == 'cf118c5fc6ce30b08894f11f54f1ac0a'){
                $db = 'ec24fd9907a1aed316b1a1509351a91b';
                $omrc =  \DB::connection($db)->table('OMRC')->where('FirmCode', $id)
                ->pluck('FirmName')->first();
              return  \DB::connection($this->mssqlcon())->table('OMRC')->where('FirmName', $omrc)->pluck('FirmCode')->first();
            }else{
              return  $id;
            }
            
            return $finale;
    }
    public function getLatestFirmcode(){
        
    }
    public function render(request $req){
         $latestBrand = \DB::connection($this->mssqlcon())->table('OMRC')->where('FirmName', $req->brand)->pluck('FirmCode')->first();
        
        if($req->search2 == 2){
            
            $data = \DB::connection($this->mssqlcon())->table('oitm')
            ->where('FirmCode',$latestBrand)
            // ->where('ItemName', $req->search2)
            ->orderBy('ItemCode', 'desc')->paginate(10);
        }else{

            $data = \DB::connection($this->mssqlcon())->table('oitm')
            ->where('FirmCode', $req->search)
            ->where('ItemName', "like", '%'.$req->search2.'%')
            ->orderBy('ItemCode', 'desc')->paginate(10); 
        }
         
        $ar =  ["data"=> $data, "brand"=> $req->brand];
        return $ar;
    }
    public function vendor(){
        $data = \DB::connection($this->mssqlcon())->table('ocrd')
                ->select("CardCode","CardName")->where("CardType", 'S')->where('Password', 'S')->get() ;
        return $data;
    }
    public function oitg(){
        $data = \DB::connection($this->mssqlcon())->table('oitg')->take(8)->get() ;
        return $data;
    }
    public function fimcode(){
        $data = \DB::connection($this->mssqlcon())->table('omrc')->get() ;
        return $data;
    }
    public function create(request $req){
         

        $sapuser = \Auth::user()->sapuser;
        // $sappassword = \Auth::user()->sappasword;
        $client = new Client(['timeout' => 300000]);
        $arr = json_encode($req->all['data']);
        $arr1 = json_encode($req->all['prop']);
        $arr2 = json_encode($req->all['database']);
        // $arr3 = $sapuser;
        // $arr4 = $sappassword;
        $response = $client->post('http://192.168.1.26:8000/api/add', [
            'form_params' => [
                $arr,$arr1,$arr2,$sapuser
            ]
        ]);
        
        $body = ($response->getBody());
        return $body;
    }
    public function update(request $req){
    
        $sapuser = \Auth::user()->sapuser;
        $client = new Client(['timeout' => 300000]);
        $arr = json_encode($req->all['data']);
        $arr1 = json_encode($req->all['prop']);
        //$arr1 = json_encode($req->all['prop']);
        $response = $client->post('http://192.168.1.26:8000/api/update', [
            'Connection' => 'keep-alive',
            'form_params' => [
                $arr ,$arr1,$sapuser
            ],
             
        ]);
        
        $body = ($response->getBody());
        return $body;
    }
    public function fields(){
        $oitg = $this->oitg();
        $vendor =  $this->vendor();
        $client = new Client();
        $firmcode = $this->fimcode();
        //$firmcode = $client->request('GET', 'http://192.168.1.26:8000/api/fields?data=firmcode')->getBody()->getContents();
        $warranty1 = $client->request('GET', 'http://192.168.1.26:8000/api/fields?data=warranty1')->getBody()->getContents();
        //$pvendor = $client->request('GET', 'http://192.168.1.19:7771/api/fields?data=pvendor')->getBody()->getContents();
        $oitb = $client->request('GET', 'http://192.168.1.26:8000/api/fields?data=oitb')->getBody()->getContents();
        //$oitg = $client->request('GET', 'http://192.168.1.26:8000/api/fields?data=oitg')->getBody()->getContents();
        $arr['preferredv'] = json_decode($vendor);
        $arr['warrantyt'] = json_decode($warranty1);
        $arr['firmcode'] = json_decode($firmcode);
        $arr['oitb'] = json_decode($oitb);
        $arr['oitg'] = json_decode($oitg);
        return response()->json($arr);
    }
    public function progress(Request $req){
        $client = new Client();
        $data = $client->request('GET', 'http://192.168.1.26:8000/api/progress?data='.$req->uniqueid)->getBody()->getContents();
        $p['status'] = json_decode($data);
        return response()->json($p);
    }

    public function index(request $req){
   
        if($req->sapcode){
            $data = DB::table('oinv')->where('db', $this->mssqlcon())->where('CustomerNumber', 'like','%'. $req->sapcode .'%')->get();
        //     $data = \DB::connection($this->mssqlcon())->table('oinv')
        //     ->select(DB::raw('Address as Address'),
        //     'oinv.NumAtCard as InvoiceNumber',
        //     'oinv.CardCode as CustomerNumber',
        //     'oinv.DocNum as DocumentNumber',
        //     'oinv.DocEntry as DocCode',
        //     'oinv.DocType as DocumentType',
        //     'oinv.CardName as CustomerName',
        //     'oinv.DocStatus as Status',
        //     'oinv.U_prilist as PriceList',
        //     'octg.PymntGroup as Installment',
        //     'oinv.Max1099 as Total',
        //     'oinv.DocDate as DocumentDate')
        //     ->join('octg','oinv.GroupNum','=','octg.GroupNum')
        //     // ->orwhere('CardCode', 'like','%'. $req->sapcode .'%')
        //     ->where('oinv.DocStatus', '!=', 'C' )
        //     ->where('octg.PymntGroup', '!=', 'CASH')
        //    ->get();
        } else{
            $data = DB::table('oinv')->where('db', $this->mssqlcon())->where('CustomerNumber', 'like','%'. \Auth::user()->branch->sapcode .'%')->get();
        //     $data = \DB::connection($this->mssqlcon())->table('oinv')
        //     ->select(DB::raw('Address as Address'),
        //     'oinv.NumAtCard as InvoiceNumber',
        //     'oinv.CardCode as CustomerNumber',
        //     'oinv.DocNum as DocumentNumber',
        //     'oinv.DocEntry as DocCode',
        //     'oinv.DocType as DocumentType',
        //     'oinv.CardName as CustomerName',
        //     'oinv.DocStatus as Status',
        //     'oinv.U_prilist as PriceList',
        //     'octg.PymntGroup as Installment',
        //     'oinv.Max1099 as Total',
        //     'oinv.DocDate as DocumentDate')
        //     ->join('octg','oinv.GroupNum','=','octg.GroupNum')
        //     ->orwhere('CardCode', 'like','%'. \Auth::user()->branch->sapcode .'%')
        //     ->where('oinv.DocStatus', '!=', 'C' )
        //     ->where('octg.PymntGroup', '!=', 'CASH')
        //    ->get();
        }
        return response()->json(@$data);
    }
    public function installment(request $req){
        
        // $GETOR =  \DB::connection($this->mssqlcon())->table('inv6 as b')
        //             ->distinct()
        //             ->select('b.DocEntry', DB::raw('x.DocLine as InstlmntID'), 'y.CounterRef')
        //             ->join('RCT2 as x', function ($join) {
        //                 $join->on('x.DocEntry', '=', 'b.DocEntry')
        //                     ->where('x.InvType', '=', '13');
        //             })
        //             ->join('ORCT as y', 'y.DocNum', '=', 'x.DocNum')
        //             ->where('b.DocEntry', '=', $req->DocCode)
        //             ->orderBy('InstlmntID')
        //             ->take(12)->get();
        $dpp = $req->Total - $req->DownPayment;
        if($req->Installment == 'INST6'){
            $out = $dpp / 6;
            $ins = 6;
        }else if($req->Installment == 'INST9'){
            $out = $dpp / 9;
            $ins = 9;
        }else if($req->Installment == 'INST12'){
            $out = $dpp / 12;
            $ins = 12;
        }else if($req->Installment == 'INST15'){
            $out = $dpp / 15;
            $ins = 15;
        }else if($req->Installment == 'INST8'){
            $out = $dpp / 8;
            $ins = 8;
        }else if($req->Installment == 'FLMI12'){
            $out = $dpp / 11;
            $ins = 12;
        } 
        else if($req->Installment == 'FDP12'){
            $out = $dpp / 12;
            $ins = 12;
        } else if($req->Installment == 'INST5'){
            $out = $dpp / 5;
            $ins = 5;
        } 
        
        $GETOR =  \DB::table('oinv6')->where('DocEntry', $req->DocCode)->take($ins)->get();

        $basement = $req->CustomerNumber;
        // $data = \DB::connection($this->mssqlcon())->table('inv6')
        //                 ->select(DB::raw('InstlmntID as InstallmentMonth'),
        //                                 'InsTotal as InstallmentTotal',
        //                                 'PaidToDate as Paid',
        //                                 'DueDate as DueDated',
        //                                 'Status as DocStat',
        //             )->where('DocEntry', $req->DocCode)->get();
         
         $data = DB::table('inv6')->where('DocEntry', $req->DocCode)->where('InstallmentTotal', $out)->get();

        // $ornumber = "SELECT Duedate,LineMemo  FROM jdt1  WHERE ContraAct = '$basement' AND TransType = '24' ORDER BY TransId DESC";
        // $jdt1 = \DB::connection($this->mssqlcon())->select($ornumber);

        // $jdt1 = DB::table('jdt1')
        // ->select('Duedate', 'LineMemo')
        // ->where('TransType', $basement)
        // ->orderBy('TransId', 'asc')
        // ->get();
        $OR[] = $GETOR;
        function getOR($or, $index2){
           foreach($or[0] as $index => $data){
                if($index == $index2){
                    $X = 'OR#'. $data->CounterRef;
                    return  $X;
                }
           }
        }
    
        $count = count($data);
        if($count){
                foreach($data as $d){
                    if( $d->DocStat === 'O' ||  $d->DocStat == 'C' ){
                    $doc = new Carbon($d->DueDated);
                    $ex = explode('-',$doc->diffInDays(now(), false));
                    $datas[] = ['OverDueDays' => $doc->diffInDays(now(), false), 
                    'DocumentNo'=> $req->DocumentNumber, 
                    'Intallment' => $d->InstallmentMonth .' of '.  $count,
                    'Date' => $d->DueDated,
                    'Total' => $d->InstallmentTotal,
                    'check' => $ex[0]];
                    }
         
         }
        } 
        //GET PRICELIST
        if($req->PriceList == '0% SPL' ||  $req->PriceList == '0% REG'){
            $prilist = 0.05;
          }else{
            $prilist = 0.07;
          }
        $sum = [];
        $TotalToPay = [];
        foreach($datas as $index => $d){
            array_push($TotalToPay, $d['Total']);
                if($d['check'] != ""){
                    $ddd = $index + 1;
                    if($prilist == 0.07){
                        if( $d['OverDueDays'] >= 1 ){
                            $interest = $d['Total'] * $ddd * $prilist;
                            array_push($sum, (int)$interest);
                                }else{
                            $interest = 0;
                        }
                    }else{
                        if( $d['OverDueDays'] >= 7 ){
                            $interest = $d['Total'] * $ddd * $prilist;
                            array_push($sum, (int)$interest);
                                }else{
                            $interest = 0;
                        }
                    }

                }else{
                $interest = 0;
            }
            $final[] = ['OverDueDays'=> $d['OverDueDays'],
            'DocumentNo' => $d['DocumentNo'],
            'Intallment' => $d['Intallment'],
            'Date' => $d['Date'],
            'Total' => $d['Total'],
            'check' => $d['check'],
            'Interest' => $interest,
            'OR'=> getOR($OR, $index)
            ];
        }
        $TotalInt = ['OverDueDays'=> 'TOTAL',
                    'DocumentNo'=>'',
                    'Intallment'=>'',
                    'Date'=>'',
                    'Total'=> array_sum($TotalToPay),
                    'check' => '',
                    'Interest'=> array_sum($sum),
            ];
        array_push($final,$TotalInt);
        return response()->json($final);
    }
    public function installment_ledger(request $req){
        
        if($req->sapcode){
            $data = DB::table('oinv')->where('db', $this->mssqlcon())->where('CustomerNumber', 'like','%'. $req->sapcode .'%')->orderBy('DocumentDate', 'desc')->get();
        //     $data = \DB::connection($this->mssqlcon())->table('oinv')
        //     ->select(DB::raw('Address as Address'),
        //     'oinv.NumAtCard as InvoiceNumber',
        //     'oinv.CardCode as CustomerNumber',
        //     'oinv.DocNum as DocumentNumber',
        //     'oinv.DocEntry as DocCode',
        //     'oinv.DocType as DocumentType',
        //     'oinv.CardName as CustomerName',
        //     'oinv.DocStatus as Status',
        //     'oinv.DpmAmnt as DownPayment',
        //     'octg.PymntGroup as Installment',
        //     'oinv.Max1099 as Total',
        //     'oinv.DocDate as DocumentDate')
        //     ->join('octg','oinv.GroupNum','=','octg.GroupNum')
        //     ->orwhere('CardCode', 'like','%'. $req->sapcode .'%')
        //     ->where('oinv.DocStatus', '!=', 'C' )
        //     ->where('octg.PymntGroup', '!=', 'CASH')
        //    ->get();

      
        } else{
            $data = DB::table('oinv')->where('db', $this->mssqlcon())->where('CustomerNumber', 'like','%'.  \Auth::user()->branch->sapcode .'%')->get();
        //     $data = \DB::connection($this->mssqlcon())->table('oinv')
        //     ->select(DB::raw('Address as Address'),
        //     'oinv.NumAtCard as InvoiceNumber',
        //     'oinv.CardCode as CustomerNumber',
        //     'oinv.DocNum as DocumentNumber',
        //     'oinv.DocEntry as DocCode',
        //     'oinv.DocType as DocumentType',
        //     'oinv.CardName as CustomerName',
        //     'oinv.DocStatus as Status',
        //     'oinv.DpmAmnt as DownPayment',
        //     'octg.PymntGroup as Installment',
        //     'oinv.Max1099 as Total',
        //     'oinv.DocDate as DocumentDate')
        //     ->join('octg','oinv.GroupNum','=','octg.GroupNum')
        //     ->orwhere('CardCode', 'like','%'.  \Auth::user()->branch->sapcode .'%')
        //     ->where('oinv.DocStatus', '!=', 'C' )
        //     ->where('octg.PymntGroup', '!=', 'CASH')
        //    ->get();
     }
      
         $d = ['data'=> $data, 'grade'=>  $this->compute_grade($req->sapcode)];
        return response()->json(@$d);
    }

    public function installment_Bal(request $req){
        $dpp = $req->Total - $req->DownPayment;
        if($req->Installment == 'INST6'){
            $outs = $dpp / 6;
            $inss = 6;
        }else if($req->Installment == 'INST9'){
            $outs = $dpp / 9;
            $inss = 9;
        }else if($req->Installment == 'INST12'){
            $outs = $dpp / 12;
            $inss = 12;
        }else if($req->Installment == 'INST15'){
            $outs = $dpp / 15;
            $inss = 15;
        }else if($req->Installment == 'INST8'){
            $outs = $dpp / 8;
            $inss = 8;
        }else if($req->Installment == 'FLMI12'){
            $outs = $dpp / 11;
            $inss = 12;
        } 
        else if($req->Installment == 'FDP12'){
            $outs = $dpp / 12;
            $inss = 12;
        } else if($req->Installment == 'INST5'){
            $outs = $dpp / 5;
            $inss = 5;
        } 
        

    //    $install = preg_replace("/[^0-9]/", "", $req->Installment);
    //   $di = $req->Total / $install;
       
       $data = DB::table('inv6')->where('DocEntry', $req->DocCode)->where('InstallmentTotal', $outs)->get();
    //    $data = \DB::connection($this->mssqlcon())->table('inv6')
    //              ->select(DB::raw('InstlmntID as InstallmentMonth'),
    //                               'InsTotal as InstallmentTotal',
    //                               'PaidToDate as Paid',
    //                               'DueDate as DueDated',
    //                               'Status as DocStat',
    //                               'DocEntry as DocEntry',
    //                               )->where('DocEntry', $req->DocCode)->get();


		$total = [$req->Total - $req->DownPayment];
        foreach($data as $t){
                $to = $total[0] - $t->Paid;
                $total = [];
                array_push($total, $to);
                $out[] = ['InstallmentMonth'=> $t->InstallmentMonth,
                            'Paid'=> $t->Paid,
                            'DueDated'=> $t->DueDated,
                            'InstallmentTotal'=> $t->InstallmentTotal,
                            'DocStat'=> $t->DocStat,
                            'DocEntry'=> $t->DocEntry,
                            'SapEndingBalance'=> $to];
		}
        $count = count($out);
        if($count){
            $check = DB::table('install_ment_ledgers')->where('DocEntry', $req->DocCode)->first();
            if($check){
                foreach($out as $d){
                    DB::table('install_ment_ledgers')
                        ->where('DocEntry', $req->DocCode)->where('InstlmntID', $d['InstallmentMonth'])
                        ->update([
                            'DocEntry' => $d['DocEntry'],
                            'InstlmntID'=> $d['InstallmentMonth'],
                            'DueDate'=> $d['DueDated'],
                            'Status'=> $d['DocStat'],
                            'InsTotalSy'=> $d['InstallmentTotal'],
                            'PaidSys'=> $d['Paid'],
                            'SapEndingBalance'=>  $d['SapEndingBalance']
										    ]);			 
                    }
            }else{
                foreach($out as $d){
                    DB::table('install_ment_ledgers')
                        ->insert([
                            'DocEntry' => $d['DocEntry'],
                            'InstlmntID'=> $d['InstallmentMonth'],
                            'DueDate'=> $d['DueDated'],
                            'Status'=> $d['DocStat'],
                            'InsTotalSy'=> $d['InstallmentTotal'],
                            'PaidSys'=> $d['Paid'],
                            'SapEndingBalance'=> $d['SapEndingBalance']
                        ]);
                
                }
						}
					}
           $kunin_ulet_yongjson = DB::table('install_ment_ledgers')
                                    ->select(DB::raw('InstlmntID as InstallmentMonth'),
                                    'InsTotalSy as InstallmentTotal',
                                    'PaidSys as Paid',
                                    'DueDate as DueDated',
                                    'Status as DocStat',
                                    'SapEndingBalance as SapEndingBalance',
                                    'ManualEndingBalance as ManualEndingBalance',
                                    'id as id'
                                    )->where('DocEntry', $req->DocCode)->get();
            foreach($kunin_ulet_yongjson as $d){
                $doc = new Carbon($d->DueDated);
                $ex = explode('-',$doc->diffInDays(now(), false));
                $datas[] = ['OverDueDays' => $doc->diffInDays(now(), false), 
                            'DocumentNo' => $req->DocumentNumber, 
                            'Intallment' => $d->InstallmentMonth .' of '.  $count,
                            'ins' => $d->InstallmentMonth,
                            'Date' => $d->DueDated,
                            'Payment' => $d->Paid,
                            'SapEndingBalance' => $d->SapEndingBalance,
                            'DocStat'=> $d->DocStat,
                            'ManualEndingBalance'=>$d->ManualEndingBalance,
                            'id'=> $d->id,
                            'Total' => $d->InstallmentTotal,'check' => $ex[0]] ;
            }    
            return response()->json($datas);
				} 
        public function updatemanual(request $req){
            $update  = ledger::find($req->id);
            $update->ManualEndingBalance = $req->value;
            $update->update();
            return 'ok';
        }	
        public function getBranchSegment(){
           return  \DB::connection($this->mssqlcon())->table('OASC')->get();
        }
        public function getBranchSeries(){
            return  \DB::connection($this->mssqlcon())->table('@Progtbl')->get();
         }
        public function changecolor(){
            $arr = [
                'color'=> true
            ];
            return response()->json($arr);
        }
        public function compute_grade($req = ''){

            function getbranch($resp){
                $sql = \DB::connection("sqlsrv2")->select(DB::raw("SELECT * FROM InstallmentReceivableDetailed_LY where CardCode LIKE '%$resp%' UNION SELECT * FROM IRDetailed_ETO where CardCode LIKE '%$resp%'"));
                    // ->where()->get();
                return $sql;
                // $sql = \DB::connection('sqlsrv2')->table('InstallmentReceivableDetailed_LY')
                // ->where('CardCode',  'LIKE' ,'%'.$resp.'%')->get();
                // return $sql;
            }
            function compute($resp){
 
                foreach(@$resp as $calculate){
                    @$irtotal[] = (int)$calculate->IRBal;
                    @$currenttotal[] = (int)$calculate->CurrentAmt;
                 }
                 if(@$irtotal || @$currenttotal){
                    $ir =  array_sum(@$irtotal);
                    $cur = array_sum(@$currenttotal);
                    return response()->json(round($cur / $ir * 100));
                 }else{
                    return response()->json(0);
                 }
            }
             //FUNCTION
             if(\Auth::user()->branch->id != 1 ){
                    $branch = \Auth::user()->branch->sapcode;
                    $query = getbranch($branch);
                        return compute($query);
                }else{
                    if($req){
                        $query = getbranch($req);
                         return compute($query);
                    }else{
                        return 0;
                    }  
                }
        
        }
        public function Rsync_branchsegment(){

            function remaping($series){
                $q  = DB::table('branches')->where('name', 'like', '%'.$series.'%')->pluck('name','id')->first();
                return $q;
            }
            $q = DB::connection('sqlsrv')->select("SELECT distinct SeriesName from nnm1");
   
            foreach($q as $d){
                $arr [] = remaping(substr($d->SeriesName, 0, 4)) .'---> '. $d->SeriesName;
            }
            return $arr;
 
            function zinc($s){
                $qw = explode(' ', $s);
                return $qw[0];
            }
            foreach( $nnm1  as $q){
 
            DB::table('branches')->where('name', 'like', '%'.$q['SeriesName'].'%')
              ->update(['seriesname'=> $q['SeriesName']]);
         
            }
            return 'ok';
        }
     public function callback(request $request){ 
       return "ok";
     }
     public function executionpromax(request $ids){ 
      
      function sync($databases){
        $data = \DB::connection($databases)->table('oinv')
        ->select(DB::raw('Address as Address'),
                  'oinv.NumAtCard as InvoiceNumber',
                  'oinv.CardCode as CustomerNumber',
                  'oinv.DocNum as DocumentNumber',
                  'oinv.DocEntry as DocCode',
                  'oinv.DocType as DocumentType',
                  'oinv.CardName as CustomerName',
                  'oinv.DocStatus as Status',
                  'oinv.U_prilist as PriceList',
                  'oinv.DpmAmnt as DownPayment',
                  'octg.PymntGroup as Installment',
                  'oinv.Max1099 as Total',
                  'oinv.DocDate as DocumentDate')
                  ->join('octg','oinv.GroupNum','=','octg.GroupNum')
                  ->where('oinv.DocStatus', '!=', 'C' )
                  ->where('octg.PymntGroup', '!=', 'CASH')
                  ->whereDate('oinv.DocDate', '>=', '2023-09-01')
                  ->get();   
                  
        foreach($data as $oinv){
            DB::table('oinv')->insert([
                'Address'=> $oinv->Address,
                'InvoiceNumber'=> $oinv->InvoiceNumber,
                'CustomerNumber'=> $oinv->CustomerNumber,
                'DocumentNumber'=> $oinv->DocumentNumber,
                'DocCode'=> $oinv->DocCode,
                'DocumentType'=> $oinv->DocumentType,
                'CustomerName'=> $oinv->CustomerName,
                'Status'=> $oinv->Status,
                'PriceList'=> $oinv->PriceList,
                'DownPayment'=> $oinv->DownPayment,
                'Installment'=> $oinv->Installment,
                'Total'=> $oinv->Total,
                'DocumentDate'=> $oinv->DocumentDate,
                'db'=> $databases,
            ]);
        }
       return "ok"; 

      }
      //return sync("cf118c5fc6ce30b08894f11f54f1ac0a");
      function oinv64($code, $db, $id, $lastid, $maxValue){
       $data = \DB::connection($db)->table('inv6 as b')
                ->distinct()
                ->select('b.DocEntry', DB::raw('x.DocLine as InstlmntID'), 'y.CounterRef')
                ->join('RCT2 as x', function ($join) {
                    $join->on('x.DocEntry', '=', 'b.DocEntry')
                        ->where('x.InvType', '=', '13');
                })
                ->join('ORCT as y', 'y.DocNum', '=', 'x.DocNum')
                ->where('b.DocEntry', '=', $code)
                ->orderBy('InstlmntID')
                ->take(12)->get(); 

        $oin =  \DB::connection($db)->table('inv6')
                ->select(DB::raw('InstlmntID as InstallmentMonth'),
                                'InsTotal as InstallmentTotal',
                                'PaidToDate as Paid',
                                'DueDate as DueDated',
                                'Status as DocStat',
            )->where('DocEntry', $code)->get();
        foreach($data as $da){
            DB::table('oinv6')->insert([
                    'DocEntry'=> $da->DocEntry,
                    'InstlmntID'=> $da->InstlmntID,
                    'CounterRef'=> $da->CounterRef
            ]);
        }  
        foreach($oin as $da){
            DB::table('inv6')->insert([
                    'InstallmentMonth'=> $da->InstallmentMonth,
                    'InstallmentTotal'=> $da->InstallmentTotal,
                    'Paid'=> $da->Paid,
                    'DueDated'=> $da->DueDated,
                    'DocStat'=> $da->DocStat,
                    'DocEntry'=> $code
            ]);
        
        } 
         $count = DB::table('syncprogress')->where('id', $id)->pluck('progress')->first();
        // Calculate the progress percentage
 
        $progressPercentage = ( $count / $maxValue) * 100;
        DB::table('syncprogress')->where('id', $id)->update([
            'progress'=> $count - 1,
            'current'=> $code,
            'lastid'=> $lastid,
            'status'=> $progressPercentage
 
        ]);
        return "ok";
      }
     
     
       $startingId = 1;
       $code = DB::table('oinv')
            ->select('DocCode', 'db', 'id')
            ->where('db', $ids->database)
            ->where('id', '>=', $startingId) // Filter records with ID greater than or equal to $startingId
            ->get();
       
      //$code = DB::table('oinv')->select('DocCode', 'db', 'id')->where('db', $ids->database)->get();
    
    
      $id = DB::table('syncprogress')->insertGetId([
        'progress'=> count($code)
      ]);
      
      //REMAPING
      foreach($code as $datas){
        oinv64($datas->DocCode, $datas->db, $id, $datas->id, count($code));
      }
       
    
     return "";
    //   $db = DB::table('custom_db')->select('entryname')->get();
    //   foreach($db as $database){
        // sync($database->entryname);
        //FIRST SYNC
       // sync("cf118c5fc6ce30b08894f11f54f1ac0a");
    //   }

       
     }

     public function createuser(){
  
        function getdb($db){
            $a = DB::table('branches')
           ->where('branches.id', '=', $db)
           ->join('custom_db', 'branches.companies', '=', 'custom_db.company_id')
           ->pluck('custom_db.entryname')
           ->first();
           return DB::table('database_selections')->where('connection', $a)->pluck('id')->first();
 
         }
          
         $account =  
             [
                
                 [
                     "Name"=> "Alaminos",
                     "Username"=> "ccsportal-alaminos@webportal.ac",
                     "Password"=> "alaminosPass@456",
                     "branch_id"=> "3"
                 ] 
              
             
         ];
 
         $roless = ["roles" => [51,67,71,74,75,76,77,79]];
          
         foreach($account as $req){
             $first_name = $req['Name'];
             $last_name = 'Branch';
             $password = $req['Password'];
             $branch_id = $req['branch_id'];
             $email = $req['Username'];
             $user = new User;
             $user->first_name = 'CCS-'.$first_name;
             $user->last_name = $last_name;
             $user->branch_id = $branch_id;
             $user->email = $email;
             $user->password = bcrypt($password);
             $user->sqldb = getdb($branch_id);
             $user->save();
              // Save for User Employment
             $employment = new UEmployment;
             $employment->user_id = $user->id;
             $employment->branch_id = $user->branch_id;
             $employment->position_id = 1;
             $employment->department_id = 1;
             $employment->save();
             $roles = $roless['roles'];
             if (isset($roles)) {
             $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
             }
             $dd[] = ['email'=> $first_name ];
         }
         return $dd; 
          
   
         
     }
        
    }
 
 
