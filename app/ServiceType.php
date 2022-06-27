<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    public function connectivity_tickets () {
        return $this->hasMany('App\ConnectivityTicket');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($connectivity_ticket) {
            if ($connectivity_ticket->connectivity_tickets()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });

    }
}
