<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevolvingFund extends Model
{
    protected $fillable = [
        'branch_id',
        'fund',
        'cash_advances',
        'avail_fund_on_hand',
        'as_of',
    ];

    public function branch()
    {
        return $this->belongsTo('App\Branch');
    }

    public function checkVoucherVerifications()
    {
        return $this->hasMany('App\RvFundCheckVoucherVerification', 'rv_fund_id');
    }

    public function checkVoucherForTransmittals()
    {
        return $this->hasMany('App\RvFundCheckVoucherForTransmittal', 'rv_fund_id');
    }

    public function expensesForCheckPreparations()
    {
        return $this->hasMany('App\RvFundExpensesForCheckPreparation', 'rv_fund_id');
    }
}
