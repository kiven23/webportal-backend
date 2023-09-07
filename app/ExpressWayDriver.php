<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpressWayDriver extends Model
{
    public function getTollways () {
        return $this->hasMany('App\ExpressWayToll', 'uid', 'uid');
      }

}
