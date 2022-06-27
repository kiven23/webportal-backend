<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{
    public function files () {
      return $this->hasMany('App\File', 'type_id', 'id');
    }

    protected static function boot() {
      parent::boot();

      static::deleting(function($file) {
          if ($file->files()->count() > 0) {
              throw new \Exception("Model have child records");
          }
      });
  }
}
