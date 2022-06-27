<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{

	protected $fillable = ['user_id',
						   'skin',
						   'sidebar_mini',
						   'sidebar_collapse',
						   'fixed'];

    public function user () {
      return $this->belongsTo('App\User');
    }
}
