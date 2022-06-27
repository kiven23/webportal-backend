<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchSchedule extends Model
{

	protected $fillable = ['time_from', 'time_to'];

    public function branches () {
      return $this->hasMany('App\Branch', 'bsched_id', 'id');
    }
}
