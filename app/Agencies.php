<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agencies extends Model
{
    public function dl_data(){
         return $this->hasMany('App\GovDataReport', 'unique_id', 'unique_id');
       }
    public function branch(){
         return $this->hasOne('App\Branch', 'id', 'branch');
    }
}
