<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class FilterProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $cats=Category::take(8)->where('level',2)->whereHas('products')->get();
        if(Auth::check()) {
            foreach ($cats as $row) {
                $row['type'] = $this->id;
            }
            $Cats = CategoryResource::collection($cats);
        }
        $lang=Auth::check() ? get_user_lang() : $request->header('lang');
        return [
            'id' => $this->id,
            'image' => getImageUrl('Product_image',$this->image),
            'name' => $lang =='en' ? $this->name_en : $this->name_ar,
            'selected' => $request->id ==$this->id ? true : false,
            'Cats' => isset($Cats) ? $Cats : null,
        ];
    }
}
