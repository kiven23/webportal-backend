<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AvailRevolvingFundOnHandSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $rv_fund = $this->revolving_funds()->first();
        return [
            'id' => $this->id,
            'rv_fund_id' => $rv_fund ? $rv_fund->id : null,
            'branch' => $this->name,
            'revolving_fund' =>  $rv_fund ? $rv_fund->fund : 0,
            'cash_advances' => $rv_fund ? $rv_fund->cash_advances : 0,
            'avail_fund_on_hand' => $rv_fund ? $rv_fund->avail_fund_on_hand : 0,
        ];
    }
}
