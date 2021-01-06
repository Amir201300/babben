<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
class ProductOrderResource extends JsonResource
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
            'image' => getImageUrl('ProductOrder',$this->image),
            'name' => $this->name,
            'desc' => $this->desc,
            'user' => new UserResource($this->user),
        ];
    }
}
