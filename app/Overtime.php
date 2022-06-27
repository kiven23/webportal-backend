<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    public function user () {
    	return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function remarks_user () {
    	return $this->belongsTo('App\User', 'remarks_by', 'id');
    }

    public function officers_approved () {
    	return $this->hasMany('App\ApprovedLog', 'overtime_id', 'id');
    }

    public function approvers () {
        return $this->hasMany('App\AccessChartUserMap', 'accesschart_id', 'accesschart_id');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($overtime) { // before delete() method call this
             $overtime->officers_approved()->delete();
        });
    }
}
