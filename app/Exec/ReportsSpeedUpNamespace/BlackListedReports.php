<?php
namespace App\Exec\ReportsSpeedUpNamespace;
use DB;
class BlackListedReports {
    public function sync() {
           $DB = DB::table("custom_db")->where('server', '192.168.1.14')->get();
           \DB::table("black_listeds")->truncate(); 
          // $DB = ["e5aaa4e1c2af9db56c79bfa45c7f934b"];
         foreach($DB as $db){
            #SYNC
             $id[] =  $this->sapquery($db->entryname);
         }
         return response()->json($id);
    }
    public function sapquery($db) {
          
        try {
           
        #===========# START
        if($db){
            $table = \DB::connection($db)
            ->select( \DB::raw("
                    declare @x1 as datetime
                    set @x1 = '2009-01-01'
                    declare @x2 as datetime
                    set @x2 = '".date('Y-m-d')."'
        
            --Write off
            select  DISTINCT
            'Write-off' as [Cat],
            CASE WHEN e.Name = 'Urdaneta' THEN 'URDA-ALEXANDER'
            WHEN  e.Name = 'Nancayasan' THEN 'URDA-NANCAYASAN'
            WHEN  e.Name = 'Sta. Cruz' THEN 'SANTA CRUZ'
             ELSE e.Name END AS Branch,
            
             D.CardName as [CustomerName],
            
            ISNULL(f.Street,'') + ', ' + ISNULL(f.Block,'') + ', ' + ISNULL(f.City,'') + ', ' + ISNULL(g.Name,'') as [Address],
            
             d.Balance as [Balance]
             
            from OJDT a 
            inner join JDT1 b on a.TransId = b.TransId
            INNER JOIN OACT c ON b.Account = c.AcctCode 
            INNER JOIN OCRD d on d.cardcode =  b.contraact
            inner join OASC e on c.Segment_2 = e.Code
            INNER JOIN CRD1 f on f.CardCode = d.CardCode
            LEFT JOIN OCST g on g.Code = f.State
            
            WHERE --a.number = 200389788  d.Balance
             c.Segment_0 IN ('11230') AND 
             a.RefDate BETWEEN @x1 and @x2		
            
            
            UNION ALL --LEGAL
            
            SELECT
            'Legal' as [Cat],
            br.U_Branch1,
            h.CardName,
            ISNULL(h1.Street,'') + ', ' + ISNULL(h1.Block,'') + ', ' + ISNULL(h1.City,'') + ', ' + ISNULL(g.Name,'') as [Address],
            C.DocTotal - C.PaidToDate as [Balance]
            
            FROM JDT1 a 
            inner join OJDT b on b.TransId = a.TransId and isnull(b.transcode,'') in (N'LEGL',N'ADJ') 
            inner join dbo.OINV c on (c.Cardcode = a.ShortName or c.Cardcode = a.U_BPCode) and c.NumAtCard = a.U_InvoiceNum
            inner join OACT f on f.AcctCode = a.ContraAct --f1
            left join OCRD h on h.CardCode = c.CardCode and h.CardType = 'C'
            INNER JOIN CRD1 h1 on h1.CardCode = h.CardCode
            LEFT JOIN OCST g on g.Code = h1.State
            left outer join NNM1 T2 on T2.Series = b.Series
            left join [@Progtbl] br on T2.SeriesName = br.U_Series1
            
            WHERE 
            
            f.Segment_0 = N'11212' -- Legal Accounts
            and isnull(b.transcode,'') in (N'LEGL',N'ADJ') 
            and isnull(c.U_ClosedTtableype,'') not in (N'CANC',N'WRNG')
            and a.transtype = N'30' and a.RefDate BETWEEN @x1 and @x2			
                
            
            
            UNION ALL --RICA
            
            SELECT DISTINCT
            'RICA' as [Cat],
            MAX(br.U_Branch1),
            MAX(h.CardName),
            ISNULL(MAX(h1.Street),'') + ', ' + ISNULL(MAX(h1.Block),'') + ', ' + ISNULL(MAX(h1.City),'') + ', ' + ISNULL(MAX(g1.Name),'') as [Address],
            MAX(C.DocTotal) - MAX(C.PaidToDate) as [Balance]
            
            from JDT1 a 
            inner join OJDT b on b.TransId = a.TransId
            inner join OINV c on c.transid = a.transid  
            inner join OCTG c1 on c1.Groupnum = c.Groupnum and c1.pymntgroup<>'CreditCard' -- dtt 2012-05-18 Payment Terms
            inner join OACT f on f.AcctCode = a.Account 
            inner join OACT f1 on f1.AcctCode = a.ContraAct
            left join OCRD h on h.CardCode = c.CardCode and h.CardType = 'C'
            INNER JOIN CRD1 h1 on h1.CardCode = h.CardCode
            LEFT JOIN OCST g1 on g1.Code = h1.State
            left outer join NNM1 i on i.Series = c.Series	
            left join [@Progtbl] br on LEFT(i.SeriesName,4) = br.Name COLLATE DATABASE_DEFAULT
            
            /**************** to capture installation fee invoice ***************/
            left outer join (select q0.Transid, max(q2.Segment_0) as ContraAct
                             from jdt1 q0
                             inner join ocrd q1 on q1.cardcode = q0.shortname
                             inner join oact q2 on q2.acctcode = q0.contraAct
                             where q2.Segment_0 = '21560'
                             tablegroup by q0.TransId) P3 on p3.transid = a.transid 
            /**************** e n d *******************************************/		
            
            -- Get Total Recon Amount
            left outer join (select d1.ReconNum, d2.TransId, d2.TransRowId, MAX(d1.ReconDate) as ReconDate, 
                        Sum(d2.ReconSum) as ReconSum
                        from OITR d1
                        inner join ITR1 d2 on d2.ReconNum = d1.ReconNum 
                        where d1.ReconDate <= getdate()  /*@todate*/
                        group by d1.ReconNum, d2.TransId, d2.TransRowId) d on d.TransId = a.TransId and d.TransRowId = a.Line_ID  
                        
            -- Get Last payment date
            left outer join (select d2.TransId, isnull(MAX(d3.u_CancDate),MAX(d1.ReconDate)) as ReconDate, MAX(d3.u_CancDate) as CancDate
                        from OITR d1
                        inner join ITR1 d2 on d2.ReconNum = d1.ReconNum 
                        left outer join OINV d3 on d3.TransId = d2.TransId and d3.U_ClosedType = 'CANC' 
                                                   and d3.DocDate <> d3.U_CancDate and d3.U_CancDate is not null
                        where table
            --((isnull(c.u_closedType,'-') in ('RECO','REPO','-',' ')) 
            ((isnull(c.u_closedType,'-') in ('REPO')) 
            /****************************************************************************************************/
            /*   dtt 2012-05-18 Include CANCELLED Invoice base on below criteria                                */
             --or (isnull(c.u_closedType,'-') = 'CANC' and month(c.docdate) <> month(c.u_cancDate) and year(c.DocDate) <= year(c.U_CancDate) 
             --and ((left(c1.pymntGroup,3) in ('CHE','INS','FDP','FLM') and (right(i.seriesname,4) = 'CHRG' and c.groupnum not in (-1,17)))
             --     or (left(c1.pymntGroup,3) = 'CAS' and f1.Segment_0 = '71110')))
            )             
            /****************************************************************************************************/
            
            and c1.pymntgroup<>'DebitCard' 
            
            
            and ((right(i.seriesname,4) = 'CHRG' and c1.pymntgroup not in('CASH','V-Cash')) or f1.Segment_0 = '71110' or (p3.ContraAct is not null and c1.pymntgroup='CHECK')) 
            and (dd.ReconDate >= @x1 and dd.ReconDate <= @x2)  --and p3.ContraAct is null 
            --and left(i.SeriesName,4) = 'ROSS'
            group by a.TransId
            having (max(isnull(c.DocTotal,0)) + max(isnull(c.DpmAmnt,0)) = sum(isnull(d.ReconSum,0)) + max(isnull(c.DpmAmnt,0)))   "));
        
        
                }else{
                    $table = ['Branch' => 'No Data Please Generate'];
                    return $table;
                }
                if($table){
                    foreach($table as $data){
                        DB::table("black_listeds")->insert([
                            "branch" => $data->Branch,
                            "customername" => $data->CustomerName,
                            "address" => $data->Address,
                            "balance" => $data->Balance,
                            "datefrom" => "2009-01-01",
                            "dateto" => date('Y-m-d'),
                            "db" => $db 
                        ]);
    
                    }
                }
            
                 $out['status'] = "sync-".$db;
        } catch (\Exception $e) {
                 $out['errors'] = $e;
        }
        
        return  $out;

        #===========# END
    }
}

