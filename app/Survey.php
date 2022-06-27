<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    public function rater () {
      return $this->belongsTo('App\User', 'rater_id', 'id');
    }

    public function conn_ticket () {
      return $this->belongsTo('App\ConnectivityTicket');
    }
}
