<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryMap extends Model
{
    public function inventory () {
    	return $this->belongsTo('App\Inventory');
    }
}
