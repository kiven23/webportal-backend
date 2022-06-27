<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public function user () {
    	return $this->belongsTo('App\User');
    }

    public function branch () {
    	return $this->belongsTo('App\Branch');
    }

    public function inventory_maps () {
    	return $this->hasMany('App\InventoryMap');
    }
}
