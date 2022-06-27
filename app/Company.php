<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function users () {
        return $this->hasMany('App\User');
    }

    public function files () {
        return $this->hasMany('App\File');
    }

    public function announcements () {
        return $this->hasMany('App\Announcement');
    }

    protected static function boot() {
        parent::boot();
  
        static::deleting(function($company) {
            if ($company->users()->count() > 0) {
                throw new \Exception("Model have child records");
            }

            if ($company->files()->count() > 0) {
                throw new \Exception("Model have child records");
            }

            if ($company->announcements()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });
  
    }
}
