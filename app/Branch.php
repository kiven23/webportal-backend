<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function schedule()
    {
        return $this->belongsTo('App\BranchSchedule', 'bsched_id', 'id');
    }

    public function pendings()
    { // for pending transaction monitoring web app
        // return $this->hasManyThrough('App\Pending','App\User','machinenum','user_id')->with('pendings');
        return $this->hasMany('App\Pending', 'branch_id', 'id')
            ->orderBy('docdate', 'asc');
    }

    public function region()
    { // for pending transaction monitoring web app
        return $this->belongsTo('App\Region');
    }

    public function computerware_tickets()
    {
        return $this->hasMany('App\ComputerwareTicket');
    }

    public function connectivity_tickets()
    {
        return $this->hasMany('App\ConnectivityTicket');
    }

    public function power_interruptions()
    {
        return $this->hasMany('App\PowerInterruption');
    }

    public function revolving_funds()
    {
        return $this->hasMany('App\RevolvingFund');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if ($user->users()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });

        static::deleting(function ($computerware_ticket) {
            if ($computerware_ticket->computerware_tickets()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });

        static::deleting(function ($connectivity_ticket) {
            if ($connectivity_ticket->connectivity_tickets()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });

        static::deleting(function ($power_interruption) {
            if ($power_interruption->power_interruptions()->count() > 0) {
                throw new \Exception("Model have child records");
            }
        });
    }
}
