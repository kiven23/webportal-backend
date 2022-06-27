<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    public function brand () {
      return $this->belongsTo('App\ProductBrand', 'product_brand_id');
    }

    public function category () {
      return $this->belongsTo('App\ProductCategory', 'product_category_id');
    }

    public function computerware_tickets () {
      return $this->hasMany('App\ComputerwareTicket');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($computerware_tickets) {
            if ($computerware_tickets->computerware_tickets()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });
    }
}
