<?php

namespace App\Http\Resources\Admin;

use App\Models\Store;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $store=Store::first();
        return [
            'id' => $this->id,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'tax' => $this->tax,
            'instagram' => $this->instagram,
            'snab' => $this->snab,
            'icon' => getImageUrl('Setting',$this->icon),
            'shipping_price' => (double)$this->shipping_price,
            'name_ar' =>  $this->name_ar,
            'name_en' =>  $this->name_en,
            'desc_ar' => $this->desc_ar,
            'desc_en' => $this->desc_ar,
            'lat' => $store->lat,
            'lng' => $store->lng,
            'address' => $store->address,
        ];
    }
}
