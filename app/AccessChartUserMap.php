<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessChartUserMap extends Model
{
    public function user () {
      return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function accesschart () {
      return $this->belongsTo('App\AccessChart', 'accesschart_id', 'id');
    }

    // public function overtimes () {
    // 	return $this->hasMany('App\Overtime', 'waiting_for', 'access_level');
    // }
}
