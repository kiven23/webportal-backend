<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    public function departments () {
      return $this->hasMany('App\Department');
    }

    public function usersemployment () {
      return $this->hasMany('App\UserEmployment', 'division_id', 'id');
    }
}
