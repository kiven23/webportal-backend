<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public function created_by () {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function company () {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }
}
