<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovedLog extends Model
{
    public function user () {
    	return $this->belongsTo('App\User', 'approver_id', 'id');
    }
}
