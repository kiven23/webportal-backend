<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DB;
class RevolvingFundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      
        return [
            'id' => $this->id,
            'branch' => $this->branch->name,
            'fund' => $this->fund,
            'cash_advances' => $this->cash_advances,
            'as_of' => $this->as_of,
            'submitted_date' => date_format($this->created_at, "M d, Y h:i a"),
        ];
    }
}
