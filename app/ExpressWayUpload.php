<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpressWayUpload extends Model
{
    public function getDrivers () {
        return $this->hasMany('App\ExpressWayDriver', 'map', 'uid');
      }
}
