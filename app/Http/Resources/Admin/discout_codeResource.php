<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class discout_codeResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'status' => $this->status,
            'desc_ar' =>$this->desc_ar,
            'desc_en' =>$this->desc_en,
            'amount' =>$this->amount,
            'amount_type' =>(int)$this->amount_type,
            'code' =>$this->code,
            'expire_data' =>$this->expire_data,
        ];
    }
}
