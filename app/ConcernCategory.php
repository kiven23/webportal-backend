<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConcernCategory extends Model
{
    public function concerns () {
      return $this->hasMany('App\Concern', 'concern_category_id', 'id');
    }

    protected static function boot() {
      parent::boot();

      static::deleting(function($type) {
        if ($type->concerns()->count() > 0) {
          throw new \Exception("Model have child records");
        }
      });
    }
}
