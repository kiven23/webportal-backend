<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaintRequest extends Model
{

    protected $fillable = ['status'];

    public function user () {
      return $this->belongsTo('App\User');
    }

    public function received_by_user () {
      return $this->belongsTo('App\User', 'received_by', 'id');
    }

    public function approved_by_user () {
      return $this->belongsTo('App\User', 'approved_by', 'id');
    }

    public function branch () {
      return $this->belongsTo('App\Branch');
    }

    public function files () {
      return $this->hasMany('App\MaintrequestFile');
    }

    public function survey () {
      return $this->hasOne('App\Survey');
    }

    public function officers_approved () {
    	return $this->hasMany('App\ApprovedLog', 'maint_request_id', 'id');
    }

    public function approvers () {
      return $this->hasMany('App\AccessChartUserMap', 'accesschart_id', 'accesschart_id');
    }
}
