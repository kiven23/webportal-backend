<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pending extends Model
{
    protected $dates = ['docdate', 'created_at']; // to allow formatting dates in view using blade templating

    public function user () {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
