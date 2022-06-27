<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessChart extends Model
{
    public function users_map () {
      return $this->belongsToMany('App\User', 'accesschart_user_maps', 'accesschart_id', 'user_id');
    }

    public function accessusersmap () {
      return $this->hasMany('App\AccessChartUserMap', 'accesschart_id', 'id');
    }
}
