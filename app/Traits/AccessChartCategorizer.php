<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

use App\Overtime;
use App\UserEmployment AS UEmployment;
use App\AccessChartUserMap AS AccessUser;

use DB;

trait AccessChartCategorizer {

	public static function approver ($user_id, $access_for) {
		// for dynamic access_for
		if ($access_for === 0) {
			$access_for = 'accesschart';
		} elseif ($access_for === 1) {
			$access_for = 'mrf_accesschart';
		} else {
			$access_for = 'po_file_accesschart';
		}
		$uemployments = UEmployment::where('user_id', $user_id)
				->with([$access_for => function ($qry) {
					$qry->with(['accessusersmap' => function ($qry) {
						$qry->orderBy('access_level', 'asc')
							->with('user')->get();
					}])->get();
				}])->get();

		$max_level = DB::table('user_employments AS ue')
												->select(DB::raw('MAX(aum.access_level) AS level'))
												->join('access_chart_user_maps AS aum', 'aum.accesschart_id', '=', 'ue.'.$access_for.'_id')
												->where('ue.user_id', $user_id)
												->get();

		$approvers = [];

		for ($a=1; $a <= $max_level[0]->level; $a++) {
			array_push($approvers, ['level' => $a, 'user' => []]);
		}

		$j = 0;
		foreach ($approvers as $approver) {
			foreach ($uemployments as $uemployment) {
				foreach ($uemployment->$access_for->accessusersmap as $accessusersmap) {
					if ($accessusersmap->access_level === $approver['level']) {
						array_push($approvers[$j]['user'],
									$accessusersmap->user->first_name . ' ' . $accessusersmap->user->last_name);
					}
				}
				$j++;
			}
		}

		return $approvers;

	}

	public function max_level ($accesschart_id) {
		return AccessUser::where('accesschart_id', $accesschart_id)->max('access_level');
	}

}

?>
