<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConnectivityTicket extends Model
{
    public function user () {
      return $this->belongsTo('App\User');
    }

    public function updatedBy () {
      return $this->belongsTo('App\User', 'updated_by', 'id');
    }

    public function confirmedBy () {
      return $this->belongsTo('App\User', 'confirmed_by', 'id');
    }
    
    public function branch () {
      return $this->belongsTo('App\Branch');
    }

    public function service_provider () {
      return $this->belongsTo('App\ServiceProvider');
    }

    public function service_type () {
      return $this->belongsTo('App\ServiceType');
    }

    public function service_category () {
      return $this->belongsTo('App\ServiceCategory');
    }

    public function survey () {
      return $this->hasOne('App\Survey');
    }
}
