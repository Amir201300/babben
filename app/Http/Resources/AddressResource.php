<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Manage\BaseController;


class AddressResource extends JsonResource
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
            'lat' => $this->lat,
            'lng' => $this->lng,
            'address' => $this->address,
            'city' => $this->city,
            'is_default' => (int)$this->is_default,
            'place_type' => $this->place_type,
            'placeTypeName' => getPlaceType($this->place_type),
            'place_name' => $this->place_name,
            'note'=>$this->note,
        ];
    }
}
