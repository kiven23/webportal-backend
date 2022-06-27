<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Khill\Lavacharts\Lavacharts;
use App\Pending;
use Carbon\Carbon;
use DB;

class PendingChartController extends Controller
{

    public function __construct () {
      $this->middleware(['auth', 'pending_clearance']);
    }

    public function overall () {

    //   	$lava = new Lavacharts; // See note below for Laravel

		// $pendings = \Lava::DataTable();
		// $pendings->addStringColumn('Branches')
		// 		 ->addNumberColumn('Pending');


		// $dt = '%' . Carbon::now()->format('Y-m-d') . '%';
		// $dt2 = Carbon::now()->format('F d, Y');
		// $data = DB::select('SELECT *,
		// 					SUM(por +
		// 					 ci +
		// 					 ch +
		// 					 dep +
		// 					 cla +
		// 					 grpo +
		// 					 si +
		// 					 so +
		// 					 sts +
		// 					 disb +
		// 					 arcm +
		// 					 apcm +
		// 					 pint +
		// 					 rc_cash) as sumPending
		// 					FROM pendings
		// 					WHERE created_at LIKE :id
		// 					GROUP BY user_id
		// 					ORDER BY branch ASC',
		// 					['id' => $dt]);
		// // return $data;

		// foreach ($data as $key => $datum) {
		// 	$pendings->addRow([$datum->branch, $datum->sumPending]);
		// }

		// \Lava::BarChart('Votes', $pendings, [
		// 	'title' => 'Overall Pending as of ' . $dt2,
		// 	'subtitle' => 'Overall Pending as of ' . $dt2,
		// 	'reverseCategories' => 0,
		// 	'orientation' => 'horizontal',
		// 	'theme' => 'material',
		// 	]);

		$data = DB::select('SELECT b.name,
							(
								SUM(por)+
								SUM(ci)+
								SUM(ch)+
								SUM(dep)+
								SUM(cla)+
								SUM(grpo)+
								SUM(si)+
								SUM(so)+
								SUM(sts)+
								SUM(disb)+
								SUM(arcm)+
								SUM(apcm)+
								SUM(pint)+
								SUM(rc_cash)+
								SUM(sc)
							) AS "y",
							(
								(
									SUM(por)+
									SUM(ci)+
									SUM(ch)+
									SUM(dep)+
									SUM(cla)+
									SUM(grpo)+
									SUM(si)+
									SUM(so)+
									SUM(sts)+
									SUM(disb)+
									SUM(arcm)+
									SUM(apcm)+
									SUM(pint)+
									SUM(rc_cash)+
									SUM(sc)
								)
								/
								(
									SELECT
										SUM(por)+
										SUM(ci)+
										SUM(ch)+
										SUM(dep)+
										SUM(cla)+
										SUM(grpo)+
										SUM(si)+
										SUM(so)+
										SUM(sts)+
										SUM(disb)+
										SUM(arcm)+
										SUM(apcm)+
										SUM(pint)+
										SUM(rc_cash)+
										SUM(sc)
									FROM pendings AS p
									INNER JOIN branches AS b ON p.branch_id=b.id
								)
							) * 100 AS "x"
							FROM pendings AS p
							INNER JOIN branches AS b ON p.branch_id=b.id
							GROUP BY b.id
							');
  		$pending = array();
  		foreach ($data as $key => $datum) {
			$pending[] = array(
				'name' => $datum->name,
				'y' => doubleval(str_replace(",","",$datum->y)),
				"percentage" => doubleval(str_replace(",","",$datum->x)),
				"drilldown" => $datum->name,
				);
		}
		$pendingCollect = collect($pending);

		$i = 0;
		$data2 = DB::select('SELECT b.id, b.name,
								SUM(por)+
								SUM(ci)+
								SUM(ch)+
								SUM(dep)+
								SUM(cla)+
								SUM(grpo)+
								SUM(si)+
								SUM(so)+
								SUM(sts)+
								SUM(disb)+
								SUM(arcm)+
								SUM(apcm)+
								SUM(pint)+
								SUM(rc_cash)+
								SUM(sc)
							FROM pendings AS p
							INNER JOIN branches AS b ON p.branch_id=b.id
							GROUP BY b.id
							');
  		$drilldown = array();
  		foreach ($data2 as $key => $datum2) {
			$drilldown[] = array(
				'tooltip' => (object)array(
                    'pointFormat' => ""
                ),
				'id' => $datum2->name,
				'name' => $datum2->name,
				'data' => array()
				);
			$drilldown2 = DB::select('
				SELECT "OR" doc, SUM(por) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "CI" doc, SUM(ci) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "CH" doc, SUM(ch) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "DEP" doc, SUM(dep) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "CLA" doc, SUM(cla) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "GRPO" doc, SUM(grpo) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "SI" doc, SUM(si) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "SO" doc, SUM(so) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "STS" doc, SUM(sts) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "DISB" doc, SUM(disb) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "ARCM" doc, SUM(arcm) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "APCM" doc, SUM(apcm) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "INT" doc, SUM(pint) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "RC CASH" doc, SUM(rc_cash) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
                UNION ALL
                SELECT "SC" doc, SUM(sc) as y
                        FROM pendings AS p
                        INNER JOIN branches AS b ON p.branch_id=b.id
                        WHERE b.id=?
                        GROUP BY b.id
				', [$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id,$datum2->id]);
			     foreach ($drilldown2 as $key => $datum3) {
	         $data2['data'] = array(

	                $datum3->doc,
	                doubleval(str_replace(",","",$datum3->y))

	                );

	            array_push($drilldown[$i]['data'], $data2['data']);
			}
			$i++;
		}
		$drilldownCollect = collect($drilldown);

    	return view('charts.pendings.overall', compact('pendingCollect', 'drilldownCollect'));
    }

    public function perdoctype () {
    	return view('charts.pendings.perdoctype');
    }
}
