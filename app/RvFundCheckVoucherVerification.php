<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvFundCheckVoucherVerification extends Model
{
    protected $fillable = [
        'rv_fund_id',
        'date_transmitted',
        'ck_no',
        'status',
        'amount',
    ];
}
