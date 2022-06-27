<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public function camera () {
        return $this->belongsTo('App\Camera');
    }

    public function remarks_user () {
    	return $this->belongsTo('App\User', 'remarks_by', 'id');
    }

    public function from_user () {
    	return $this->belongsTo('App\User', 'from', 'id');
    }

    public function to_user () {
    	return $this->belongsTo('App\User', 'to', 'id');
    }

    public function to_company () {
    	return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function officers_approved () {
    	return $this->hasMany('App\ApprovedLog', 'po_file_id', 'id');
    }

    public function last_officer_approved () {
    	return $this->hasOne('App\ApprovedLog', 'po_file_id', 'id')->latest();
    }

    public function seen_by_users () {
        return $this->hasMany('App\Seen', 'po_file_id', 'id')->oldest();
    }

    public function report_seen_by_users () {
        return $this->hasMany('App\Seen', 'report_file_id', 'id')->oldest();
    }

    public function type () {
        return $this->belongsTo('App\FileType', 'type_id', 'id');
    }
}
