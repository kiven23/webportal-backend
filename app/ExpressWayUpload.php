<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpressWayUpload extends Model
{

    public function getDrivers () {
        return $this->hasOne('App\ExpressWayDriver', 'plate', 'plateno');
    }

    public function getruf () {
        return $this->hasMany('App\ExpressWayToll', 'uid', 'uid');
    }
     
}
