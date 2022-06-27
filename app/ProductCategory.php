<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    public function items () {
      return $this->hasMany('App\ProductItem');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($product_item) {
            if ($product_item->items()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });
    }
}
