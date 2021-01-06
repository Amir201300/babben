<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lang= Auth::check() ? get_user_lang() : $request->header('lang');
        return [
            'id' => $this->id,
            'title' =>$lang=='en' ? $this->title_en : $this->title_ar,
            'image' => getImageUrl('Slider',$this->image),
        ];
    }
}
