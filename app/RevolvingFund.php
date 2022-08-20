<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevolvingFund extends Model
{
    protected $fillable = [
        'branch_id',
        'fund',
        'cash_advances',
        'as_of',
    ];
}
