<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\InstallMentLedger as ledger;
use Carbon\Carbon;
class SapApiController extends Controller
{
    public function index(request $req){
        if($req->sapcode){
            $data = \DB::connection('sqlsrv')->table('oinv')
            ->select(DB::raw('Address as Address'),
            'oinv.NumAtCard as InvoiceNumber',
            'oinv.CardCode as CustomerNumber',
            'oinv.DocNum as DocumentNumber',
            'oinv.DocEntry as DocCode',
            'oinv.DocType as DocumentType',
            'oinv.CardName as CustomerName',
            'oinv.DocStatus as Status',
            'oinv.U_prilist as PriceList',
            'octg.PymntGroup as Installment',
            'oinv.Max1099 as Total',
            'oinv.DocDate as DocumentDate')
            ->join('octg','oinv.GroupNum','=','octg.GroupNum')
            ->orwhere('CardCode', 'like','%'. $req->sapcode .'%')
            ->where('oinv.DocStatus', '!=', 'C' )
            ->where('octg.PymntGroup', '!=', 'CASH')
           ->get();
        } else{
            $data[] = ['Address'=>'' ,
            'InvoiceNumber'=>'',
            'CustomerNumber'=>'',
            'DocumentNumber'=>'',
            'DocCode'=>'',
            'DocumentType'=>'',
            'CustomerName'=>'',
            'Status'=>'',
            'Installment'=>'',
            'Total'=>'',
            'DocumentDate'=>''
];
        }
        return response()->json(@$data);
    }
    public function installment(request $req){
        
        $data = \DB::connection('sqlsrv')->table('inv6')
                 ->select(DB::raw('InstlmntID as InstallmentMonth'),
                                  'InsTotal as InstallmentTotal',
                                  'PaidToDate as Paid',
                                  'DueDate as DueDated',
                                  'Status as DocStat',
                   )->where('DocEntry', $req->DocCode)->get();
        $count = count($data);
        if($count){
            foreach($data as $d){
                if($d->DocStat === 'O'){
                $doc = new Carbon($d->DueDated);
                $ex = explode('-',$doc->diffInDays(now(), false));
                $datas[] = ['OverDueDays' => $doc->diffInDays(now(), false), 
                'DocumentNo'=> $req->DocumentNumber, 
                'Intallment' => $d->InstallmentMonth .' of '.  $count,
                'Date' => $d->DueDated,
                'Total' => $d->InstallmentTotal,
                'check' => $ex[0] ] ;
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
            $data = \DB::connection('sqlsrv')->table('oinv')
            ->select(DB::raw('Address as Address'),
            'oinv.NumAtCard as InvoiceNumber',
            'oinv.CardCode as CustomerNumber',
            'oinv.DocNum as DocumentNumber',
            'oinv.DocEntry as DocCode',
            'oinv.DocType as DocumentType',
            'oinv.CardName as CustomerName',
            'oinv.DocStatus as Status',
            'oinv.DpmAmnt as DownPayment',
            'octg.PymntGroup as Installment',
            'oinv.Max1099 as Total',
            'oinv.DocDate as DocumentDate')
            ->join('octg','oinv.GroupNum','=','octg.GroupNum')
            ->orwhere('CardCode', 'like','%'. $req->sapcode .'%')
            ->where('oinv.DocStatus', '!=', 'C' )
            ->where('octg.PymntGroup', '!=', 'CASH')
           ->get();
        } else{
            $data[] = ['Address'=>'' ,
            'InvoiceNumber'=>'',
            'CustomerNumber'=>'',
            'DocumentNumber'=>'',
            'DocCode'=>'',
            'DocumentType'=>'',
            'CustomerName'=>'',
            'Status'=>'',
            'Installment'=>'',
            'Total'=>'',
            'DocumentDate'=>''
];
        }
    
        return response()->json(@$data);
    }

    public function installment_Bal(request $req){
        
       $data = \DB::connection('sqlsrv')->table('inv6')
                 ->select(DB::raw('InstlmntID as InstallmentMonth'),
                                  'InsTotal as InstallmentTotal',
                                  'PaidToDate as Paid',
                                  'DueDate as DueDated',
                                  'Status as DocStat',
                                  'DocEntry as DocEntry',
                                  )->where('DocEntry', $req->DocCode)->get();


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
           return  \DB::connection('sqlsrv')->table('OASC')->get();
        }

        
    }
 
 
