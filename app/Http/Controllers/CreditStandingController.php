<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class CreditStandingController extends Controller
{

    public function mssqlcon(){
        return \Auth::user()->dbselection->connection;
    }
    public function index(){ 
         $table = ['Branch' => 'No Data Please Generate'];
                    return response()->json($table);
       }
    public function generate(request $req){

        $branch =  \Auth::user()->branch->sapcode;
  
        if($req){
            //REGULAR CUSTOMER
            $date1 = $req->regular[0];
            $date2 = $req->regular[1];
            //LEGAL CUSTOMER
            $date3 = $req->legal[0];
            $date4 = $req->legal[1];
            $createTempTables = \DB::connection($this->mssqlcon())
                    ->unprepared(
                            DB::raw("
                            DECLARE @x1 as datetime
                            DECLARE @x2 as datetime
                            DECLARE @x3 as datetime
                            DECLARE @x4 as datetime
                            set @x1 = '" .$date1 . "'
                            set @x2 = '" .$date2 . "'
                            set @x3 = '" .$date3 . "'
                            set @x4 = '" .$date4 . "'
                            CREATE TABLE #CATEGORY(
                                Cardcode VARCHAR(100)
                                ,Cardname VARCHAR(100)
                                ,Bday VARCHAR(25)
                                ,Category VARCHAR(25)
                                ,CardDay VARCHAR(100)
                                ,Branch VARCHAR(30)
                            )
                            
                            INSERT INTO #CATEGORY
                            SELECT DISTINCT A.CardCode, A.CardName, CAST(ISNULL(A.U_Bday,'') AS DATE) as [Bday], 'No History' as [Cat]
                            , A.CardName + CAST(CAST(ISNULL(A.U_Bday,'') AS DATE) AS VARCHAR(20)) as [CardDay], D.GroupName as [Branch]
                            FROM OCRD A 
                            LEFT JOIN OINV B ON A.CardCode = B.CardCode
                            LEFT JOIN JDT1 C ON C.ContraAct = A.CardCode
                            LEFT JOIN OCRG D ON D.GroupCode = A.GroupCode
                            WHERE A.CardType = 'C' AND B.CardCode IS NULL AND C.ContraAct IS NULL
                            AND A.validFor = 'Y'
                            
                            
                            SELECT DISTINCT A.CardCode, A.CardName, A.PaidToDate, A.DocTotal, A.DocTotal - A.PaidToDate as [Balance]
                            , CAST(ROW_NUMBER() OVER(PARTITION BY A.CardName ORDER BY A.DocTotal - A.PaidToDate ASC) AS INT) AS [Row]
                            , ISNULL(B.U_Bday,'') as [Bday]
                            , B.CardName + CAST(CAST(ISNULL(B.U_Bday,'') AS DATE) AS VARCHAR(20)) as [CardDay]
                            INTO #Cat2
                            FROM OINV A 
                            INNER JOIN OCRD B ON A.CardCode = B.CardCode AND B.validFor = 'Y'
                            WHERE A.DocDate BETWEEN @x1 AND @x2 AND A.U_ClosedType NOT IN ('CANC', 'WRNG') ORDER BY 2
                            
                            
                            SELECT B.CardDay, SUM(B.Balance) as [Balance], MAX(B.Row) AS [ROW] INTO #Cat 
                            FROM #Cat2 B 
                            GROUP BY B.CardDay
                            ORDER BY 1
                            
                            INSERT INTO #CATEGORY
                            SELECT A.CardCode, A.CardName, CAST(ISNULL(A.Bday,'') AS DATE)
                            , (SELECT CASE WHEN B.ROW = 1 AND B.Balance <> 0 THEN 'New' ELSE 'Repeat' END FROM #Cat B WHERE B.CardDay = A.CardDay)
                            , A.CardDay, '' as [Branch]
                            FROM #Cat2 A 
                            ORDER BY 2
                            
"));

 
$table = \DB::connection($this->mssqlcon())->select(DB::raw("
                                                    DECLARE @x1 as datetime
                                                    DECLARE @x2 as datetime
                                                    DECLARE @x3 as datetime
                                                    DECLARE @x4 as datetime

                                                    set @x1 = '" .$date1 . "'
                                                    set @x2 = '" .$date2 . "'
                                                    set @x3 = '" .$date3 . "'
                                                    set @x4 = '" .$date4 . "' SELECT 
                                                    MAX(cat.Category) COLLATE DATABASE_DEFAULT as [Category] 
                                                    , a.transid 
                                                    , c.DocNum
                                                    , MAX(br.Branch) COLLATE DATABASE_DEFAULT as [Branch]
                                                    , MAX(c.CardCode) COLLATE DATABASE_DEFAULT as [Cardcode]
                                                    , MAX(c.CardName) COLLATE DATABASE_DEFAULT as [Cardname]
                                                    , MAX(cat.Bday) COLLATE DATABASE_DEFAULT as [Bday]
                                                    , COUNT(DISTINCT Recon.[Sales Discount_TransId]) AS [Rebate]
                                                    , COUNT(DISTINCT n.[Interest_TransId]) AS [Interest]
                                                    , MAX(c.DocDate) as [Posting Date]
                                                    , MAX(c.DocDueDate) as [Due Date]
                                                    , MAX(dd.ReconDate) as [Last Payment Date]
                                                    , MAX(d.PymntGroup) as [Terms]
                                                    , MAX(c.u_closedType) as [Closed Type]
                                                    , MAX(C.PaidToDate) as [Paid Amount], MAX(C.DocTotal) as [DocTotal]
                                                    , MAX(C.DocTotal) - MAX(C.PaidToDate) as [Balance]
                                                    , CASE WHEN MAX(C.PaidToDate) = MAX(C.DocTotal) AND 
                                                    MAX(dd.ReconDate) <= MAX(c.DocDueDate) AND COUNT(DISTINCT Recon.[Sales Discount_TransId]) >= 6 THEN 'EXCELLENT'
                                                    WHEN MAX(C.PaidToDate) = MAX(C.DocTotal) AND 
                                                    MAX(dd.ReconDate) <= MAX(c.DocDueDate) AND 
                                                    COUNT(DISTINCT Recon.[Sales Discount_TransId]) >= 3 AND COUNT(DISTINCT Recon.[Sales Discount_TransId]) <= 5 THEN 'VERY GOOD'
                                                    WHEN MAX(C.PaidToDate) = MAX(C.DocTotal) AND 
                                                    MAX(dd.ReconDate) <= MAX(c.DocDueDate) AND 
                                                    COUNT(DISTINCT Recon.[Sales Discount_TransId]) >= 1 AND COUNT(DISTINCT n.[Interest_TransId]) >= 1 THEN 'GOOD'
                                                    WHEN MAX(C.PaidToDate) = MAX(C.DocTotal) AND 
                                                    MAX(dd.ReconDate) > MAX(c.DocDueDate) 
                                                    AND DATEDIFF(MM, MAX(c.DocDueDate), MAX(dd.ReconDate)) BETWEEN 1 AND 3 THEN 'SATISFACTORY'
                                                    WHEN MAX(C.PaidToDate) = MAX(C.DocTotal) AND 
                                                    MAX(dd.ReconDate) > MAX(c.DocDueDate) 
                                                    AND DATEDIFF(MM, MAX(c.DocDueDate), MAX(dd.ReconDate)) BETWEEN 4 AND 6 THEN 'POOR'
                                                    WHEN MAX(C.PaidToDate) = MAX(C.DocTotal) AND 
                                                    MAX(dd.ReconDate) > MAX(c.DocDueDate) 
                                                    AND DATEDIFF(MM, MAX(c.DocDueDate), MAX(dd.ReconDate)) >= 7 THEN 'DELINQUENT'
                                                    --WHEN MAX(l.[ReconNum]) IS Not NULL THEN 'LEGAL ACCOUNT'
                                                    --WHEN MAX(legal.Legal) = 11212 THEN 'LEGAL ACCOUNT'
                                                    ELSE ''
                                                    End AS [Criteria]
                                                    
                                                    FROM JDT1 a 
                                                    inner join OJDT b on b.TransId = a.TransId
                                                    inner join OINV c on c.transid = a.transid and c.GroupNum <> N'30'
                                                    inner join OCTG d on d.Groupnum = c.Groupnum  --c1
                                                    inner join OACT e on e.AcctCode = a.Account --f
                                                    inner join OACT f on f.AcctCode = a.ContraAct --f1
                                                    left outer join NNM1 g on g.Series = c.Series
                                                    left join OCRD h on h.CardCode = c.CardCode 
                                                    left outer join(select distinct (select a.name from OUBR a where a.Code = s0.Branch) as Branch,
                                                                    CAST(s0.U_OINV as nvarchar(10)) as SeriesName
                                                                
                                                                    from OUSR s0
                                                                    inner join dbo.[@QUOTA] b on b.U_Series = s0.U_WHSE
                                                                
                                                                    where s0.U_OINV is not null
                                                                    )br on LEFT(g.SeriesName,4) = LEFT(br.SeriesName,4) 


                                                    left outer join ( select DISTINCT J003.DocNum, J001.DocNum as [Dc], J001.Transid
                                                                FROM [ORCT] J001
                                                                LEFT OUTER JOIN [RCT2] J002 ON J002.DocNum = J001.DocNum AND J002.InvType = 13
                                                                LEFT OUTER JOIN [OINV] J003 ON J003.DocEntry = J002.DocEntry 
                                                        ) Inc on Inc.DocNum = c.DocNum
                                                        

                                                    left outer join (
                                                    SELECT t01.ReconNum, t01.Transid
                                                        FROM  [dbo].[ITR1] t01
                                                        INNER JOIN [dbo].[OITR] t02 ON t01.ReconNum = t02.ReconNum
                                                            WHERE  t01.SrcObjTyp = N'24' AND t02.CancelAbs = 0
                                                    ) x on  x.Transid = Inc.Transid


                                                    left outer join (select a.Transid, max(c.Segment_0) as ContraAct
                                                                    from jdt1 a
                                                                    inner join ocrd b on b.cardcode = a.shortname
                                                                    inner join oact c on c.acctcode = a.contraAct
                                                                    group by a.TransId) K1 on K1.transid = a.transid 

                                                        
                                                    left outer join (select d1.ReconNum, d2.TransId, d2.TransRowId, MAX(d1.ReconDate) as ReconDate, 
                                                                Sum(d2.ReconSum) as ReconSum, m.ReconNum as [Sales Discount_Recon], m.TransId as [Sales Discount_TransId]
                                                                from OITR d1
                                                                inner join ITR1 d2 on d2.ReconNum = d1.ReconNum 

                                                            
                                                                left outer join (select x.ReconNum, x1.TransId
                                                                                from ITR1 x
                                                                                inner join JDT1 x1 on x1.TransId = x.TransId
                                                                                inner join OACT x2 on x2.AcctCode = x1.Account
                                                                                where x.SrcObjTyp in (/*N'30',*/N'24') and x2.Segment_0 = '41310' 
                                                                                ) m on m.ReconNum = d1.ReconNum
                                                                                
                                                                where d1.ReconDate <= getdate() AND d1.CancelAbs = 0
                                                                group by d1.ReconNum, d2.TransId, d2.TransRowId, m.ReconNum, m.TransId
                                                                ) Recon on Recon.TransId = a.TransId and Recon.TransRowId = a.Line_ID
                                                    

                                                    left outer join (select d2.TransId, isnull(MAX(d3.u_CancDate),MAX(d1.ReconDate)) as ReconDate, MAX(d3.u_CancDate) as CancDate
                                                                from OITR d1
                                                                inner join ITR1 d2 on d2.ReconNum = d1.ReconNum 
                                                                left outer join OINV d3 on d3.TransId = d2.TransId and d3.U_ClosedType = 'CANC' 
                                                                                        and d3.DocDate <> d3.U_CancDate and d3.U_CancDate is not null
                                                                where d1.ReconDate <= getdate() and d1.CancelAbs = 0 and d1.iscard <> 'A'
                                                                group by d2.TransId) dd on dd.TransId = a.TransId    


                                                    left outer join (select T2.ReconNum, T1.TransId as [Interest_TransId], sum(T2.ReconSum) as ReconSum
                                                                from [dbo].[JDT1] T1     
                                                                INNER  JOIN [dbo].[ITR1] T2  ON  T2.TransId = T1.TransId 
                                                                INNER  JOIN [dbo].[OACT] T3  ON T3.[Acctcode] = T1.[Account] 
                                                                where t2.SrcObjTyp = N'30' and T3.segment_0 = '41220' 
                                                                group by T2.reconnum, T1.TransId) n on n.ReconNum = x.ReconNum
                                                                

                                                    left outer join (select x.ReconNum as [ReconNum], sum(x1.Debit) as Debit from ITR1 x 
                                                                    inner join JDT1 x1 on x1.TransId = x.TransId
                                                                    inner join OACT x2 on x2.AcctCode = x1.Account
                                                                    where x.SrcObjTyp in (N'30') and x2.Segment_0 = '11230'
                                                                    group by x.ReconNum) l on l.ReconNum = Recon.ReconNum
                                                                            

                                                    left outer join (select x.Cardcode, x.Cardname, x.Category, x.Bday
                                                                    from #CATEGORY x
                                                                    ) cat on cat.Cardcode = c.CardCode collate database_default
                                                                    
                                                    --WHERE C.DocNum = 122029559

                                                    WHERE --C.DocNum IN (122029559, 162016734, 12055048) AND
                                                    (isnull(c.u_closedType,'-') in ('RECO','-',' ')) 
                                                    AND (right(g.seriesname,4) = 'CHRG' and c.groupnum not in (-1,17)) 
                                                    AND c.DocDate BETWEEN @x1 AND @x2
                                                    AND C.Cardcode LIKE '%$branch%'
                                                    GROUP BY a.transid, c.DocNum

                                                    UNION /*LEGAL ACCOUNT*/
                                                    SELECT 
                                                    MAX(cat.Category)  as [Category] 
                                                    , a.transid, c.DocNum
                                                    , MAX(br.Branch) as [Branch]
                                                    , MAX(c.CardCode) as [Cardcode]
                                                    , MAX(c.CardName) as [Cardname]
                                                    , MAX(cat.Bday) as [Bday]
                                                    , COUNT(DISTINCT Recon.[Sales Discount_TransId]) AS [Rebate]
                                                    , COUNT(DISTINCT n.[Interest_TransId]) AS [Interest]
                                                    , MAX(c.DocDate) as [Posting Date]
                                                    , MAX(c.DocDueDate) as [Due Date]
                                                    , MAX(dd.ReconDate) as [Last Payment Date]
                                                    , MAX(d.PymntGroup) as [Terms]
                                                    , MAX(c.u_closedType) as [Closed Type]
                                                    , MAX(C.PaidToDate) as [Paid Amount], MAX(C.DocTotal) as [DocTotal]
                                                    , MAX(C.DocTotal) - MAX(C.PaidToDate) as [Balance]
                                                    , 'LEGAL ACCOUNT' AS [Criteria]

                                                    FROM JDT1 a 
                                                    inner join OJDT b on b.TransId = a.TransId and isnull(b.transcode,'') in (N'LEGL',N'ADJ') 
                                                    --inner join OINV c on c.transid = a.transid and c.GroupNum <> N'30' 
                                                    inner join dbo.OINV c on (c.Cardcode = a.ShortName or c.Cardcode = a.U_BPCode) and c.NumAtCard = a.U_InvoiceNum
                                                    inner join OCTG d on d.Groupnum = c.Groupnum  --c1
                                                    inner join OACT e on e.AcctCode = a.Account --f
                                                    inner join OACT f on f.AcctCode = a.ContraAct --f1
                                                    left outer join NNM1 g on g.Series = c.Series
                                                    left join OCRD h on h.CardCode = c.CardCode and h.CardType = 'C'
                                                    left outer join NNM1 T2 on T2.Series = b.Series
                                                    left outer join(select distinct (select name from OUBR where Code = a.Branch) as Branch,
                                                                        CAST(a.U_OJDT as nvarchar(50)) as Series
                                                                        
                                                                        from OUSR a
                                                                        inner join dbo.[@QUOTA] b on b.U_Series = a.U_WHSE
                                                                    
                                                                        where b.U_Company is not null
                                                                        )br on br.Series = t2.SeriesName


                                                    left outer join ( select DISTINCT J003.DocNum, J001.DocNum as [Dc], J001.Transid
                                                                FROM [ORCT] J001
                                                                LEFT OUTER JOIN [RCT2] J002 ON J002.DocNum = J001.DocNum AND J002.InvType = 13
                                                                LEFT OUTER JOIN [OINV] J003 ON J003.DocEntry = J002.DocEntry 
                                                        ) Inc on Inc.DocNum = c.DocNum
                                                        

                                                    left outer join (
                                                    SELECT t01.ReconNum, t01.Transid
                                                        FROM  [dbo].[ITR1] t01
                                                        INNER JOIN [dbo].[OITR] t02 ON t01.ReconNum = t02.ReconNum
                                                            WHERE  t01.SrcObjTyp = N'24' AND t02.CancelAbs = 0
                                                    ) x on  x.Transid = Inc.Transid


                                                    left outer join (select a.Transid, max(c.Segment_0) as ContraAct
                                                                    from jdt1 a
                                                                    inner join ocrd b on b.cardcode = a.shortname
                                                                    inner join oact c on c.acctcode = a.contraAct
                                                                    group by a.TransId) K1 on K1.transid = a.transid 

                                                    
                                                    left outer join (select d1.ReconNum, d2.TransId, d2.TransRowId, MAX(d1.ReconDate) as ReconDate, 
                                                                Sum(d2.ReconSum) as ReconSum, m.ReconNum as [Sales Discount_Recon], m.TransId as [Sales Discount_TransId]
                                                                from OITR d1
                                                                inner join ITR1 d2 on d2.ReconNum = d1.ReconNum 

                                                    
                                                                left outer join (select x.ReconNum, x1.TransId
                                                                                from ITR1 x
                                                                                inner join JDT1 x1 on x1.TransId = x.TransId
                                                                                inner join OACT x2 on x2.AcctCode = x1.Account
                                                                                where x.SrcObjTyp in (/*N'30',*/N'24') and x2.Segment_0 = '41310' 
                                                                                ) m on m.ReconNum = d1.ReconNum
                                                                                
                                                                where d1.ReconDate <= getdate() AND d1.CancelAbs = 0
                                                                group by d1.ReconNum, d2.TransId, d2.TransRowId, m.ReconNum, m.TransId
                                                                ) Recon on Recon.TransId = a.TransId and Recon.TransRowId = a.Line_ID
                                                    

                                                    left outer join (select d2.TransId, isnull(MAX(d3.u_CancDate),MAX(d1.ReconDate)) as ReconDate, MAX(d3.u_CancDate) as CancDate
                                                                from OITR d1
                                                                inner join ITR1 d2 on d2.ReconNum = d1.ReconNum 
                                                                left outer join OINV d3 on d3.TransId = d2.TransId and d3.U_ClosedType = 'CANC' 
                                                                                        and d3.DocDate <> d3.U_CancDate and d3.U_CancDate is not null
                                                                where d1.ReconDate <= getdate() and d1.CancelAbs = 0 and d1.iscard <> 'A'
                                                                group by d2.TransId) dd on dd.TransId = a.TransId    

                                                    left outer join (select T2.ReconNum, T1.TransId as [Interest_TransId], sum(T2.ReconSum) as ReconSum
                                                                from [dbo].[JDT1] T1     
                                                                INNER  JOIN [dbo].[ITR1] T2  ON  T2.TransId = T1.TransId 
                                                                INNER  JOIN [dbo].[OACT] T3  ON T3.[Acctcode] = T1.[Account] 
                                                                where t2.SrcObjTyp = N'30' and T3.segment_0 = '41220' 
                                                                group by T2.reconnum, T1.TransId) n on n.ReconNum = x.ReconNum
                                                                

                                                    left outer join (select x.ReconNum as [ReconNum], sum(x1.Debit) as Debit from ITR1 x 
                                                                    inner join JDT1 x1 on x1.TransId = x.TransId
                                                                    inner join OACT x2 on x2.AcctCode = x1.Account
                                                                    where x.SrcObjTyp in (N'30') and x2.Segment_0 = '11230'
                                                                    group by x.ReconNum) l on l.ReconNum = Recon.ReconNum
                                                                    
                                                                    

                                                    left outer join (select x.Cardcode, x.Cardname, x.Category, x.Bday
                                                                    from #CATEGORY x
                                                                    ) cat on cat.Cardcode = c.CardCode collate database_default
                                                                                    
                                                    WHERE 

                                                    f.Segment_0 = N'11212' -- Legal Accounts
                                                    and isnull(b.transcode,'') in (N'LEGL',N'ADJ') 
                                                    and isnull(c.U_ClosedType,'') not in (N'CANC',N'WRNG')
                                                    and a.transtype = N'30' and a.RefDate BETWEEN @x3 and @x4
                                                    AND C.Cardcode LIKE '%$branch%'
                                                    GROUP BY a.transid, c.DocNum


                                                    UNION /*No History*/

                                                    SELECT
                                                    'No History' as [Category] 
                                                    , 0, 0
                                                    , A.Branch as [Branch]
                                                    , A.Cardcode as [Cardcode]
                                                    , A.Cardname as [Cardname]
                                                    , A.BDAY as [Bday]
                                                    , 0 AS [Rebate]
                                                    , 0 AS [Interest]
                                                    , '' as [Posting Date]
                                                    , '' as [Due Date]
                                                    , '' as [Last Payment Date]
                                                    , '' as [Terms]
                                                    , '' as [Closed Type]
                                                    , 0, 0, 0
                                                    , '' AS [Criteria]
                                                    FROM #CATEGORY A 
                                                    WHERE A.Category = 'No History' AND A.Cardcode LIKE '%$branch%'"));
                    }else{
                        $table = ['Branch' => 'No Data Please Generate'];
                    }
                    return response()->json($table);
    }
}
