<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archived_Add extends Model
{
    public function tct_data2(){
        return $this->hasMany('App\GovDataReport', 'unique_id', 'tct_data');
       }
       public function tax_dec_data2(){
        return $this->hasMany('App\GovDataReport', 'unique_id', 'tax_dec_data');
       }
       public function deed_of_sale_data2(){
        return $this->hasMany('App\GovDataReport', 'unique_id', 'deed_of_sale_data');
       }
       public function real_property_data2(){
        return $this->hasMany('App\GovDataReport', 'unique_id', 'real_property_tax_data');
       }
       public function vicinity_map_data2(){
        return $this->hasMany('App\GovDataReport', 'unique_id', 'vicinity_map_data');
       }
}
