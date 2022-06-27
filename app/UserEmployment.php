<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEmployment extends Model
{
    public function user () {
      return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function division () {
      return $this->belongsTo('App\Division', 'division_id', 'id');
    }

    public function department () {
      return $this->belongsTo('App\Department', 'department_id', 'id');
    }

    public function position () {
      return $this->belongsTo('App\Position', 'position_id', 'id');
    }

    public function branch () {
      return $this->belongsTo('App\Branch', 'branch_id', 'id');
    }

    public function accesschart () {
        return $this->belongsTo('App\AccessChart', 'accesschart_id', 'id');
    }

    public function mrf_accesschart () {
        return $this->belongsTo('App\AccessChart', 'mrf_accesschart_id', 'id');
    }

    public function po_file_accesschart () {
        return $this->belongsTo('App\AccessChart', 'po_file_accesschart_id', 'id');
    }

    public function accessusersmap () {
        return $this->hasMany('App\AccessChartUserMap', 'accesschart_id', 'accesschart_id');
    }

    public function mrf_accessusersmap () {
        return $this->hasMany('App\AccessChartUserMap', 'accesschart_id', 'mrf_accesschart_id');
    }

    public function po_file_accessusersmap () {
        return $this->hasMany('App\AccessChartUserMap', 'accesschart_id', 'po_file_accesschart_id');
    }

    public function overtimes () {
        return $this->hasMany('App\Overtime', 'user_id', 'user_id');
    }

    public function biometrics () {
        return $this->hasMany('App\Biometric', 'sss', 'sss');
    }
}
