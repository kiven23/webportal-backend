<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvFundCheckVoucherForTransmittal extends Model
{
    protected $fillable = [
        'rv_fund_id',
        'check_voucher_date',
        'ck_no',
        'amount',
    ];
}
