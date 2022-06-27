<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
  public function branch () {
    return $this->belongsTo('App\Branch');
  }

  public function type () {
    return $this->belongsTo('App\ConcernType', 'concern_type_id', 'id');
  }

  public function category () {
    return $this->belongsTo('App\ConcernCategory', 'concern_category_id', 'id');
  }
}
