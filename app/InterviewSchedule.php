<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterviewSchedule extends Model
{
    public function branch () {
      return $this->belongsTo('App\Branch');
    }
}
