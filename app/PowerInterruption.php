<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PowerInterruption extends Model
{
    public function user () {
      return $this->belongsTo('App\User');
    }

    public function branch () {
      return $this->belongsTo('App\Branch');
    }
}
