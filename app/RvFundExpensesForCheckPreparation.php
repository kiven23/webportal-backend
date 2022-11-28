<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvFundExpensesForCheckPreparation extends Model
{
    protected $fillable = [
        'rv_fund_id',
        'pcv_date',
        'tin',
        'glaccounts',
        'amount',
        'payee'
    ];
}
