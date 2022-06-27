<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archived extends Model
{
   public function tct_data(){
    return $this->hasMany('App\GovDataReport', 'unique_id', 'tct_data');
   }
   public function tax_dec_data(){
    return $this->hasMany('App\GovDataReport', 'unique_id', 'tax_dec_data');
   }
   public function deed_of_sale_data(){
    return $this->hasMany('App\GovDataReport', 'unique_id', 'deed_of_sale_data');
   }
   public function real_property_data(){
    return $this->hasMany('App\GovDataReport', 'unique_id', 'real_property_tax_data');
   }
   public function vicinity_map_data(){
    return $this->hasMany('App\GovDataReport', 'unique_id', 'vicinity_map_data');
   }
   public function archived_new(){
      return $this->hasMany('App\Archived_Add', 'reportid', 'reportid');
     }
}
