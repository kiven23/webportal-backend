<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public function employees () {
      return $this->hasMany('App\UserEmployment', 'department_id', 'id');
    }

    public function division () {
      return $this->belongsTo('App\Division');
    }
}
