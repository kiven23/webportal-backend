<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComputerwareTicket extends Model
{

    public function logged_by () {
      return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function branch () {
      return $this->belongsTo('App\Branch');
    }

    public function item () {
      return $this->belongsTo('App\ProductItem', 'product_item_id');
    }
}
