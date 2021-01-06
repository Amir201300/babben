<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
class SupplierOrderResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'activity' => $this->activity,
            'address' => $this->address,
        ];
    }
}
