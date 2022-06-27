<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerDigitizedReq extends Model
{
    public function dl_data(){
        return $this->hasMany('App\DocCcsAttachment', 'doc_id', 'doc_id');
      }
    public function branch(){
        return $this->hasOne('App\Branch', 'id', 'branch');
      }
}
