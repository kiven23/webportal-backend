<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvFundAvailOnHand extends Model
{
    protected $fillable = [
        'rv_fund_id',
        'fund_on_hand',
    ];
}
