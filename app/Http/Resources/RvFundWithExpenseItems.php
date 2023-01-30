<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
 
class RvFundWithExpenseItems extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // ->whereRaw('DATE(created_at) = DATE(?)', [date('Y-m-d')]
        $checkVoucherVerifications = $this->checkVoucherVerifications();
        $checkVoucherForTransmittals = $this->checkVoucherForTransmittals();
        $expensesForCheckPreparations = $this->expensesForCheckPreparations();
    
        return [
            'id' => $this->id,
            'branch' => $this->branch->name,
            'fund' => $this->fund,
            'cash_advances' => $this->cash_advances,
            'as_of' => $this->as_of,
            'submitted_date' => date_format($this->created_at, "M d, Y"),
            'check_voucher_verifications' => $checkVoucherVerifications->where('verify', NULL)->get(),
            'check_voucher_verifications_total' => $checkVoucherVerifications->sum('amount'),
            'check_voucher_for_transmittals' => $checkVoucherForTransmittals->get(),
            'check_voucher_for_transmittals_total' => $checkVoucherForTransmittals->sum('amount'),
            'expenses_for_check_preparations' => $expensesForCheckPreparations->get(),
            'expenses_for_check_preparations_total' => $expensesForCheckPreparations->sum('amount'),
        ];
    }
}
