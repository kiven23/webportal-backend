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
            ->orwhere('CardCode', 'like','%'. \Auth::user()->branch->sapcode .'%')
            ->where('oinv.DocStatus', '!=', 'C' )
            ->where('octg.PymntGroup', '!=', 'CASH')
           ->get();
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
            ->orwhere('CardCode', 'like','%'.  \Auth::user()->branch->sapcode .'%')
            ->where('oinv.DocStatus', '!=', 'C' )
            ->where('octg.PymntGroup', '!=', 'CASH')
           ->get();
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
        public function compute_grade(request $req){
           $today = Carbon::now()->format('m.d.Y');
           $branch = \Auth::user()->branch->sap_segment;
           $get = \DB::connection('sqlsrv')->unprepared("
           declare @StatementDate smalldatetime
           declare @LastDayNextMonth smalldatetime
           declare @FirstDayNextMonth smalldatetime
           declare @FirstDayOfTheMonth smalldatetime 
           declare @Segment varchar(3)
           declare @CardCode nvarchar(50)
           
           
           set @StatementDate = '$today'
           set @Segment = '$branch'
           --set @CardCode = 'C01-0005083'
           
           set @LastDayNextMonth = DATEADD(D, -1, DATEADD(m, DATEDIFF(m, 0, @StatementDate) + 2, 0))
           Set @FirstDayNextMonth = DATEADD(dd,-(DAY(DATEADD(mm,0,@LastDayNextMonth))-1),DATEADD(mm,0,@LastDayNextMonth))
           Set @FirstDayOfTheMonth = DATEADD(dd,-(DAY(DATEADD(mm,0,@StatementDate))-1),DATEADD(mm,0,@StatementDate))
           
           
           If Object_Id('TempDB..#TempInstallmentReceivableByPosting') Is Not NULL
           Begin
               Drop Table #TempInstallmentReceivableByPosting
           End
           
           If Object_Id('TempDB..#TempAgingInterest') Is Not NULL
           Begin
               Drop Table #TempAgingInterest
           End
           
           If Object_Id('TempDB..#TempInstallmentsDocument') Is Not NULL
           Begin
                         Drop Table #TempInstallmentsDocument
           End
           
           If Object_Id('TempDB..#TempAppliedTransactions') Is Not NULL
           Begin
                         Drop Table #TempAppliedTransactions
           End
           
           If Object_Id('TempDB..#TempSegments') Is Not NULL
           Begin
               Drop Table #TempSegments
           End
                    
          SELECT * INTO #TempAgingInterest FROM(
            select e0.TransId, 
                        e0.Line_ID, 
                        e0.DueDate, 
                        e1.CardCode,
                        CASE when e0.DueDate > @StatementDate then sum(e0.debit) - sum(isnull(e2.ReconSum,0))  else 0 end  as CurrentBal,
                        CASE when max(isnull(e0.Debit,0)) <> max(isnull(e2.ReconSum,0)) and max(e0.DueDate) <= @StatementDate then sum(e0.debit) - sum(isnull(e2.ReconSum,0)) else 0 end as OverDueAmt, 
                        (select  top 1 insTotal from inv6 where objType = '13' and DocEntry = max(e1.docentry)) as MI, MAX(cast(e1.U_prilist as nvarchar(50))) as PriceList
                        
                        
                        
                 from JDT1 e0 
                 inner join OINV e1 on e1.transid = e0.transid
                 inner join OACT e3 on e3.AcctCode = e0.Account 
                 LEFT OUTER JOIN (select z2.TransId, z2.TransRowId, sum(z2.ReconSum) as ReconSum, max(z1.recondate) as LastReconDate
                                  from dbo.OITR z1 
                                  inner join dbo.ITR1 z2 on z2.ReconNum = z1.ReconNum 
                                  where z1.ReconDate <= @StatementDate and z1.CancelAbs = 0
                                  group by z2.TransId , z2.TransRowId
                                  )e2 ON  e2.[TransId] = e0.[TransId] AND  e2.[TransRowId] = e0.[Line_ID]
                 where e0.RefDate <= @StatementDate and e3.Segment_0 = N'11210' and e3.Segment_2 = @Segment
                 --and e1.CardCode = @CardCode
                 group by e0.transid, e0.line_id, e0.DueDate, e1.CardCode
            
            )Int01
            
            
            
            
            SELECT * INTO #TempInstallmentsDocument FROM(
            
            SELECT	mm0.TransId, 
                    max(mm0.mi) as Mi,  
                    CASE  when ( max(mm0.DueDate) > @StatementDate)
                                       then
                                        case	when  sum(isnull(mm0.OverDueAmt,0)) = 0 then '01 CURRENT AND NEW ACCOUNTS'
                                                when ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) > 0 ) AND ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) <=1) )) then '02 ONE(1) MONTH'
                                                when ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) > 1 ) AND ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) <=2) )) then '03 TWO(2) MONTHS'
                                                when ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) > 2 ) AND ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) <=3) )) then '04 THREE(3) MONTHS'
                                                when ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) > 3 ) AND ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) <=4) )) then '05 FOUR(4) MONTHS'
                                                when ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) > 4 ) AND ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) <=5) )) then '06 FIVE(5) MONTHS'
                                                when ((((sum(isnull(mm0.OverDueAmt,0)))/(max(mm0.MI))) > 5 )) then '07 SIX(6) MONTHS'
                                         end
                                       else 
                                         case	when DATEDIFF(month, max(mm0.DueDate) , @StatementDate) = 0 then '09 LAPCON ONE(1) MONTH'
                                                when DATEDIFF(month, max(mm0.DueDate) , @StatementDate) = 1 then '10 LAPCON TWO(2) MONTHS'
                                                when DATEDIFF(month, max(mm0.DueDate) , @StatementDate) = 2 then '11 LAPCON THREE(3) MONTHS'
                                                when DATEDIFF(month, max(mm0.DueDate) , @StatementDate) = 3 then '12 LAPCON FOUR(4) MONTHS'
                                                when DATEDIFF(month, max(mm0.DueDate) , @StatementDate) = 4 then '13 LAPCON FIVE(5) MONTHS'
                                                when DATEDIFF(month, max(mm0.DueDate) , @StatementDate) = 5 then '14 LAPCON SIX(6) MONTHS'
                                                when DATEDIFF(month, max(mm0.DueDate) , @StatementDate) >= 6 then '15 OVER AND ABOVE SIX(6) MONTHS'
                                            
                                         end		 
                     END  as Aging,
                     
                     (CASE when left(max(mm0.PriceList),2) = '0%' then 
                            sum(isnull(mm0.AccTotal,0)) * 0.05
                        else 
                            sum(isnull(mm0.AccTotal,0)) * 0.07
                        end) as Interest
                             
            FROM(
                select  x0.TransId, 
                        x0.Line_ID, x0.DueDate, x0.CardCode, 
                        x0.CurrentBal as CurrentBal, x0.OverDueAmt as OverDueAmt, 
                        x0.MI as MI, x0.PriceList,
                        (select sum(x1.OverDueAmt) from #TempAgingInterest x1 where x1.TransId = x0.TransId and x1.Line_ID <= x0.Line_ID) as AccTotal
            
                from #TempAgingInterest x0
                )mm0
                 
            GROUP BY mm0.TransId 
            )EXTable0
            
            
            SELECT * INTO #TempInstallmentReceivableByPosting FROM(
            select	t2.CardCode, 
                    max(t2.CardName) as CardName, 
                    t0.TransId, 
                    t0.Line_ID,
                    t0.TransType,
                    t0.Ref2, 
                    t0.RefDate,
                    t0.DueDate,		
                    SUM(t0.Debit - t0.Credit) as Amt,
                    SUM(isnull(t4.ReconSum,0)) as ReconSum,
                    CASE when SUM(t0.Debit - t0.Credit) < 0 
                              then SUM(t0.Debit - t0.Credit) + SUM(isnull(t4.ReconSum,0))
                              else SUM(t0.Debit - t0.Credit) - SUM(isnull(t4.ReconSum,0)) end as IRBal,
            
                    CASE when T0.DueDate > @StatementDate and MAX(T5.Aging) is Not null  then sum(T0.debit) - sum(isnull(T4.ReconSum,0))  else 0 end  as CurrentAmt,
                    CASE when max(isnull(T0.Debit,0)) <> max(isnull(T4.ReconSum,0)) and max(T0.DueDate) <= @StatementDate and MAX(T5.Aging) is Not null  then sum(T0.debit) - sum(isnull(T4.ReconSum,0)) else 0 end as OverDueAmt,
                    (select  top 1 insTotal from inv6 where objType = '13' and DocEntry = max(xx0.docentry)) as MI,  	
                    
                    CASE when T0.DueDate >= @FirstDayNextMonth and T0.DueDate <= @LastDayNextMonth and MAX(T5.Aging) is Not null  then sum(T0.debit) - sum(isnull(T4.ReconSum,0))  else 0 end  as DueNextMonth,
                    MAX(T5.Interest) as Interest,
                    
                    isnull(max(T5.Aging),'08 UNRECONCILED TRANSACTION') as Aging,
                        
                    max(t3.Segment_2) as Segment,
                      (select s0.name from dbo.oasc s0 where  s0.code = max(t3.segment_2)) as Branch,	
                    CASE when t0.TransType = N'13' 
                         then ROW_NUMBER() OVER(PARTITION BY t0.transid ORDER BY t0.line_id desc)
                         else 0
                    end  AS 'Ledger',
                    max(t4.LastReconDate) as LastReconDate, 
                    max(xx0.u_CancDate) as CancDate
                    
                 
            from dbo.JDT1 t0
            left outer join dbo.OINV xx0 on xx0.TransId = t0.TransId
            inner join dbo.OJDT t1 on t1.TransId = t0.TransId and isnull(t1.transcode,'') not in (N'BEG',N'ADJ')  
            inner join dbo.OCRD t2 on t2.CardCode = t0.ShortName and t2.CardType = 'C'
            inner join dbo.OACT t3 on t3.AcctCode = t0.Account
            left outer join dbo.NNM1 t11 on t11.Series = t1.Series
            
            LEFT OUTER JOIN (select z2.TransId, z2.TransRowId, sum(z2.ReconSum) as ReconSum, max(z1.recondate) as LastReconDate
                             from dbo.OITR z1 
                             inner join dbo.ITR1 z2 on z2.ReconNum = z1.ReconNum 
                             where z1.ReconDate <= @StatementDate and z1.CancelAbs = 0
                             group by z2.TransId , z2.TransRowId
                             )T4 ON  T4.[TransId] = T1.[TransId] AND  T4.[TransRowId] = T0.[Line_ID]
            
            LEFT OUTER JOIN #TempInstallmentsDocument T5 ON T5.TransId = T0.TransId
            
            
            where t3.Segment_0 = N'11210' and t3.Segment_2  = @Segment 
            and isnull(t1.transcode,'') not in (N'BEG',N'ADJ')
            and t0.RefDate <= @StatementDate
            --and t0.ShortName = @CardCode
            
            group by t0.TransId, t0.Line_ID, t2.CardCode, t0.TransType, t0.Ref2,t0.RefDate,t0.DueDate
            having 	CASE when SUM(t0.Debit - t0.Credit) < 0 
                         then SUM(t0.Debit - t0.Credit) + SUM(isnull(t4.ReconSum,0))
                         else SUM(t0.Debit - t0.Credit) - SUM(isnull(t4.ReconSum,0)) end <> 0
            
            
            UNION ALL
            
            select	t2.CardCode, 
                    max(t2.CardName) as CardName, 
                    t0.transid, 
                    t0.line_id,
                    t0.TransType,
                    t0.Ref2, 
                    t0.RefDate,
                    t0.DueDate,		
                    SUM(t0.Debit - t0.Credit) as Amt,
                    SUM(isnull(t4.ReconSum,0)) as ReconSum,
                    CASE when SUM(t0.Debit - t0.Credit) < 0 
                              then SUM(t0.Debit - t0.Credit) + SUM(isnull(t4.ReconSum,0))
                              else SUM(t0.Debit - t0.Credit) - SUM(isnull(t4.ReconSum,0)) end as IRBal,
            
                    CASE when T0.DueDate > @StatementDate and MAX(T5.Aging) is Not null then sum(T0.debit) - sum(isnull(T4.ReconSum,0))  else 0 end  as CurrentAmt,
                    CASE when max(isnull(T0.Debit,0)) <> max(isnull(T4.ReconSum,0)) and max(T0.DueDate) <= @StatementDate and MAX(T5.Aging) is Not null  then sum(T0.debit) - sum(isnull(T4.ReconSum,0)) else 0 end as OverDueAmt,
                    (select  top 1 insTotal from inv6 where objType = '13' and DocEntry = max(xx0.docentry)) as MI,  	
                    
                    CASE when T0.DueDate >= @FirstDayNextMonth and T0.DueDate <= @LastDayNextMonth and MAX(T5.Aging) is Not null  then sum(T0.debit) - sum(isnull(T4.ReconSum,0))  else 0 end  as DueNextMonth,
                    MAX(T5.Interest) as Interest,
                    
                    isnull(max(T5.Aging),'08 UNRECONCILED TRANSACTION') as Aging,
                    
                    
                    max(t3.Segment_2) as Segment,
                      (select s0.name from dbo.oasc s0 where  s0.code = max(t3.segment_2)) as Branch,	
                    CASE when t0.TransType = N'13' 
                         then ROW_NUMBER() OVER(PARTITION BY t0.transid ORDER BY t0.line_id desc)
                         else 0
                    end  AS 'Ledger',
                    max(t4.LastReconDate) as LastReconDate, 
                    max(xx0.u_CancDate) as CancDate
                    
                 
            from dbo.JDT1 t0
            inner join dbo.OINV xx0 on xx0.Cardcode = t0.ShortName and xx0.NumAtCard = T0.Ref2
            inner join dbo.OJDT t1 on t1.TransId = t0.TransId and isnull(t1.transcode,'') in (N'BEG',N'ADJ')  
            inner join dbo.OCRD t2 on t2.CardCode = t0.ShortName and t2.CardType = 'C'
            inner join dbo.OACT t3 on t3.AcctCode = t0.Account
            left outer join dbo.NNM1 t11 on t11.Series = t1.Series
            
            LEFT OUTER JOIN (select z2.TransId, z2.TransRowId, sum(z2.ReconSum) as ReconSum, max(z1.recondate) as LastReconDate
                             from dbo.OITR z1 
                             inner join dbo.ITR1 z2 on z2.ReconNum = z1.ReconNum 
                             where z1.ReconDate <= @StatementDate and z1.CancelAbs = 0
                             group by z2.TransId , z2.TransRowId
                             )T4 ON  T4.[TransId] = T1.[TransId] AND  T4.[TransRowId] = T0.[Line_ID]
            
            LEFT OUTER JOIN #TempInstallmentsDocument T5 ON T5.TransId = xx0.TransId
            
            where t3.Segment_0 = N'11210' and t3.Segment_2 = @Segment and isnull(t1.transcode,'') in (N'BEG',N'ADJ') and
            t0.transtype = N'30' and t0.RefDate <= @StatementDate
            --and t0.ShortName = @CardCode
            
            
            group by t0.TransId, t0.Line_ID, t2.CardCode, t0.TransType, t0.Ref2,t0.RefDate,t0.DueDate
            having 	CASE when SUM(t0.Debit - t0.Credit) < 0 
                         then SUM(t0.Debit - t0.Credit) + SUM(isnull(t4.ReconSum,0))
                         else SUM(t0.Debit - t0.Credit) - SUM(isnull(t4.ReconSum,0)) end <> 0 
            
            )EXTable1
            
            
            SELECT * INTO #TempAppliedTransactions FROM(
            select	T0.TransId, 
                        max(T2.NumAtCard) as Ref2,
                        MAX(x.PymntGroup) as PymntGroup,  
                        MAX(T2.CardCode) as CardCode,
                        max(T2.CardName) as CardName,
                        max(T0.DueDate) as DueDate, 
                        sum(isnull(T3.ReconSum,0)) as ReconSum,
                        max(isnull(T5.DownPayment,0)) as DownPayment,
                        (max(isnull(T5.DownPayment,0)) + sum(isnull(T3.ReconSum,0))) as Amt,
                        max(ISNULL(T2.DocTotal,0)) as DocTotal,
                        max(T6.ReconSum) as Interest
                    
                          
                from JDT1 T0
                inner join OJDT T1 on T1.TransId = T0.TransId
                inner join OINV T2 on T2.TransId = T0.TransId and T2.GroupNum <> N'30' and t2.GroupNum <> N'57'
                inner join OACT tt0 on tt0.AcctCode = T0.Account
                inner join OACT tt1 on tt1.AcctCode = T0.ContraAct 
                inner join OCTG x on x.GroupNum = t2.GroupNum
                --Capture Recon Amt
                left outer join (select d1.ReconNum, d2.TransId, d2.TransRowId, MAX(d1.ReconDate) as ReconDate, 
                        Sum(d2.ReconSum) as ReconSum
                        from OITR d1
                        inner join ITR1 d2 on d2.ReconNum = d1.ReconNum 
                        where d1.ReconDate <= @StatementDate and d1.CancelAbs = 0
                        group by d1.ReconNum, d2.TransId, d2.TransRowId
                        )T3 on T3.TransId = T0.TransId and T3.TransRowId = T0.Line_ID 
                
                --Capture Required DP
                LEFT OUTER JOIN (select s0.TransId, max(isnull(s0.Debit,0)) as DownPayment, (s0.ContraAct) as CardCode
                                 from JDT1 s0
                                 inner join OJDT s1 on s1.TransId = s0.TransId 
                                 inner join OACT s2 on s2.AcctCode = s0.Account
                                 where s2.Segment_0 = N'21510'
                                 group by s0.TransId, s0.ContraAct
                                 )T5 on T5.TransId = T0.TransId AND T5.CardCode = T2.CardCode
                                 
                --Capture Interest				 
                LEFT OUTER JOIN(select sum(xx0.ReconSum) as ReconSum, xx0.DocTransId 
                                from(
                                    SELECT  sum(T0.ReconSum) as ReconSum, T01.DocTransId, T0.TransId
                                    FROM  [dbo].[ITR1] T0  
                                    inner join ORCT T00 on T00.TransId = t0.TransId
                                    inner join RCT2 T01 on T01.DocNum = T00.DocNum AND T01.InvoiceId = 0
                                    inner join OITR T02 on T02.ReconNum = T0.ReconNum
                                    inner join OACT T03 on T03.AcctCode = T0.Account
                                    inner join (select T2.ReconNum, sum(T2.ReconSum) as ReconSum, t2.SrcObjTyp 
                                                from [dbo].[JDT1] T1     
                                                INNER  JOIN [dbo].[ITR1] T2  ON  T2.TransId = T1.TransId
                                                INNER  JOIN [dbo].[OACT] T3  ON T3.[Acctcode] = T1.[Account] 
                                                where t2.SrcObjTyp = N'30' and T3.segment_0 = N'41220' 
                                                group by T2.reconnum,t2.SrcObjTyp
                                                )T4 on t4.ReconNum = t0.ReconNum 
                                    WHERE  T0.SrcObjTyp = N'24' /**/ and abs(t0.ReconSum) <= t00.NoDocSum  
                                    --and T00.CardCode = @CardCode
                                    and T03.Segment_2 = @Segment
                                    and T02.ReconDate >= @FirstDayOfTheMonth and T02.ReconDate <= @StatementDate
                                    group by T01.DocTransId, T0.TransId
                                    )xx0
                                group by xx0.DocTransId
                                )T6 on T6.DocTransId = T2.TransId				 
            
                where tt0.Segment_2 = @Segment 
                --and T2.CardCode = @CardCode
                and T0.RefDate <= @StatementDate
                group by T0.TransId
            )EXTable2
             ");
          $select = \DB::connection('sqlsrv')->select(DB::raw("
          --select * from #TempAppliedTransactions 
          --select * from #TempInstallmentReceivableByPosting 
          
          select 	   
                      sum(isnull(f0.IRBal,0)) as IRBal,
                      sum(isnull(f0.CurrentAmt,0)) as CurrentAmt
                      
                      
                      
                      
           from #TempInstallmentReceivableByPosting f0
           left outer join (select f1.docentry,  f1.transid, max(isnull(f1.u_closedtype,'-')) as u_ClosedType, 
                            max(f1.groupnum) as groupnum, max(f3.segment_0) as Segment_0,
                            max(f1.doctype) as Doctype, max(isnull(f1.U_Coll,'-No Collector-')) as CollectorCode,
                            MAX(f13.U_Collector) as CollectorName,
                            MAX(f13.U_EmpID) as CollectorID,
                            (select top 1 CRD1.U_acode from CRD1 where CRD1.CardCode = max(f11.CardCode)) as BP_Area,
                                                                            (select top 1 CRD1.city as Brgy from CRD1 where CRD1.CardCode = max(f11.CardCode)) as Town,     
                            (select top 1 CRD1.Block as Brgy from CRD1 where CRD1.CardCode = max(f11.CardCode)) as Brgy,
                                                                            MAX(f14.Name) as Price_list_name
                            from oinv f1
                            inner join inv1 f2 on f2.docentry = f1.docentry
                            inner join oact f3 on f3.acctcode = f2.acctcode
                            inner join ocrd f11 on f11.cardcode = f1.cardcode
                            inner join nnm1 f12 on f12.series = f1.series
                            left outer join [@COLL] f13 on f13.Name = f1.U_Coll 
                                                                             left outer join [@PRICE_LIST] f14 on f14.Code = f1.U_prilist
                            group by f1.docentry, f1.transid) f4 on f4.transid = f0.transid 
           --left outer join (select g0.segment, sum(isnull(g0.irbal,0)) as BrTotal 
              --			  from #temp g0 group by g0.segment) f5 on f5.segment in (select DISTINCT hb.segment from #Segments hb) --= f0.segment
          
           /**************** to capture installation fee invoice ***************/
          left outer join (select q0.Transid, max(q2.Segment_0) as ContraAct
                           from jdt1 q0
                           inner join ocrd q1 on q1.cardcode = q0.shortname
                           inner join oact q2 on q2.acctcode = q0.contraAct
                           where q2.Segment_0 = '21560'
                           group by q0.TransId) P3 on p3.transid = f0.transid 
          /**************** e n d *******************************************/	
          
          left outer join #TempAppliedTransactions P4 on P4.TransId = f0.TransId
          
          where f0.IRBal <> 0 
          group by f0.TransID"));
        // COMPUTE BRANCH GRADE AS OF MONTH
        foreach(@$select as $calculate){
           @$irtotal[] = (int)$calculate->IRBal;
           @$currenttotal[] = (int)$calculate->CurrentAmt;
        }
        $ir =  array_sum(@$irtotal);
        $cur = array_sum(@$currenttotal);
            $arr = [
               'grade' => round($cur / $ir * 100)
            ];
        return response()->json($arr);
        }
        public function Rsync_branchsegment(){
            $segmentCode = \DB::connection('sqlsrv')->table('OASC')->get();
            foreach($segmentCode as $d){
                $res[] =  DB::table('branches')->where('name', 'like' , '%'.$d['Name'].'%')->update([
                      'sap_segment' => $d['Code']
                  ]); 
             }
            return $res;
        }
        
    }
 
 
