<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Dompdf\Dompdf;
use Dompdf\Options;
class SapRportsController extends Controller
{
    public function incoming_crb_generate(request $req){
        
 
        function remapBranch($b){
                return  DB::table('branches')->where('name', $b)->pluck('seriesname')->first();
        }
        $getB = remapBranch($req->branch);
        $getD = $req->date;
        $table = \DB::connection('sqlsrv')
        ->select(DB::raw("/* SELECT FROM [dbo].[ORCT] z1 */ 
        DECLARE @Det AS VARCHAR(50)  
        SET @Det = '$getD'   
        /* SELECT FROM [dbo].[OUSR] z2 */  
        DECLARE @Branch AS VARCHAR(50)  
        /* WHERE z2.U_ORCT IS NOT NULL */  
        SET @Branch = '$getB'       
        SELECT	a.docnum as [DocNum],    
                a.docdate as [Date],    
                isnull(a.cardname,a.U_recvFrom) as [Name],      
                a.comments as [Particulars],    
                a.counterref as [OR#],    
                case when a.DocType = 'C'   and a.CashSum = 0.00 
                    then a.CheckSum   
                    else CASE WHEN a.CashSum = 0.00 THEN A.NoDocSum ELSE A.CashSum END
                end as [Amount],
                case when a.DocType = 'C' then  (a.CashSum + a.TrsfrSum - a.NoDocSum) else null end as [PaymtAcct],    
                case when (a.DocType = 'C' and  a.NoDocSum <> 0) then a.NoDocSum else null end as [Interest], 
                case when a.DocType = 'A' then a.NoDocSum else null end as [otherBranch_Acct], 
                case when a.DocType = 'V' then a.NoDocSum else null end as [Supplier], 
                case when a.TrsfrSum = 0 then null else a.TrsfrSum end as [Rebate]    
                
                FROM ORCT A   
                INNER JOIN NNM1 B ON A.Series = B.Series   
                INNER JOIN OACT C ON A.CashAcct = C.AcctCode   
                LEFT JOIN OACT D ON A.TrsfrAcct = D.AcctCode      
                
                where a.canceled = 'N' 
                    and a.docdate = @Det 
                    and b.seriesname = @Branch   
                    and a.Comments not in ('CASH SALES')  
                    AND (a.TrsfrSum = 0   AND C.segment_0 IN('11130','11150') OR  a.TrsfrSum <> 0   
                    AND D.segment_0 IN('41310') 
                    AND C.segment_0 IN('11130','11150') )  
                    AND (a.TrsfrSum = 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null   
                    AND W0.U_ORCT = @Branch group by w0.U_ORCT) OR a.TrsfrSum <> 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null 
                    AND W0.U_ORCT = @Branch group by w0.U_ORCT)  AND D.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null AND W0.U_ORCT = @Branch group by w0.U_ORCT) ) 
                    
                ORDER BY A.counterref ASC"));
        $amount = 0;
        $PaymtAcct = 0;
        $Interest = 0;
        $otherBranch_Acct = 0;
        $Rebate = 0;
        foreach($table as $query){
           $amount += $query->Amount;
           $PaymtAcct += $query->PaymtAcct;
           $Interest += $query->Interest;
           $otherBranch_Acct += $query->otherBranch_Acct;
           $Rebate += $query->Rebate;
        }
        $total = ['Particulars'=> 'TOTAL',
                  'Amount'=> $amount,
                  'PaymtAcct'=> $PaymtAcct,
                  'Interest'=> $Interest,
                  'otherBranch_Acct'=> $otherBranch_Acct,
                  'Rebate'=> $Rebate];
        array_push($table ,  $total);
        return $table;
    }
    public function preview(request $req){
    function remapBranch($b){
            return  DB::table('branches')->where('name', $b)->pluck('seriesname')->first();
    }
    $branch = $req->branch;
    $getB = remapBranch($req->branch);
    $getD = $req->date;
    $table = \DB::connection('sqlsrv')
    ->select(DB::raw("/* SELECT FROM [dbo].[ORCT] z1 */ 
    DECLARE @Det AS VARCHAR(50)  
    SET @Det = '$getD'   
    /* SELECT FROM [dbo].[OUSR] z2 */  
    DECLARE @Branch AS VARCHAR(50)  
    /* WHERE z2.U_ORCT IS NOT NULL */  
    SET @Branch = '$getB'       
    SELECT	a.docnum as [DocNum],    
            a.docdate as [Date],    
            isnull(a.cardname,a.U_recvFrom) as [Name],      
            a.comments as [Particulars],    
            a.counterref as [OR],    
            case when a.DocType = 'C'   and a.CashSum = 0.00 
                then a.CheckSum   
                else CASE WHEN a.CashSum = 0.00 THEN A.NoDocSum ELSE A.CashSum END
            end as [Amount],
            case when a.DocType = 'C' then  (a.CashSum + a.TrsfrSum - a.NoDocSum) else null end as [PaymtAcct],    
            case when (a.DocType = 'C' and  a.NoDocSum <> 0) then a.NoDocSum else null end as [Interest], 
            case when a.DocType = 'A' then a.NoDocSum else null end as [otherBranch_Acct], 
            case when a.DocType = 'V' then a.NoDocSum else null end as [Supplier], 
            case when a.TrsfrSum = 0 then null else a.TrsfrSum end as [Rebate]    
            
            FROM ORCT A   
            INNER JOIN NNM1 B ON A.Series = B.Series   
            INNER JOIN OACT C ON A.CashAcct = C.AcctCode   
            LEFT JOIN OACT D ON A.TrsfrAcct = D.AcctCode      
            
            where a.canceled = 'N' 
                and a.docdate = @Det 
                and b.seriesname = @Branch   
                and a.Comments not in ('CASH SALES')  
                AND (a.TrsfrSum = 0   AND C.segment_0 IN('11130','11150') OR  a.TrsfrSum <> 0   
                AND D.segment_0 IN('41310') 
                AND C.segment_0 IN('11130','11150') )  
                AND (a.TrsfrSum = 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null   
                AND W0.U_ORCT = @Branch group by w0.U_ORCT) OR a.TrsfrSum <> 0 AND C.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null 
                AND W0.U_ORCT = @Branch group by w0.U_ORCT)  AND D.Segment_2 = (select distinct  (select s1.phone2 from oudg s1 where s1.code = max(w0.dfltsGroup))  as [Segment] from ousr w0 where w0.U_ORCT is not null AND W0.U_ORCT = @Branch group by w0.U_ORCT) ) 
                
            ORDER BY A.counterref ASC"));
    $amount = 0;
    $PaymtAcct = 0;
    $Interest = 0;
    $otherBranch_Acct = 0;
    $Rebate = 0;
    foreach($table as $query){
       $amount += $query->Amount;
       $PaymtAcct += $query->PaymtAcct;
       $Interest += $query->Interest;
       $otherBranch_Acct += $query->otherBranch_Acct;
       $Rebate += $query->Rebate;
    }
    $total = ['Particulars'=> 'TOTAL',
              'Amount'=> $amount,
              'PaymtAcct'=> $PaymtAcct,
              'Interest'=> $Interest,
              'otherBranch_Acct'=> $otherBranch_Acct,
              'Rebate'=> $Rebate];
        array_push($table ,  $total);
        return view('sap_reportsprint.incoming.incoming_qpld', compact('table','getD', 'branch' ));
    }

}
