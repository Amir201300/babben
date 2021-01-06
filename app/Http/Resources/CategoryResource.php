<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class CategoryResource extends JsonResource
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
        $subCats=Category::where('cat_id',$this->id)->get();
        $mainCat=Category::find($this->cat_id);
        $Products=filterProductsByCat($this,$this->type);
        return [
            'id' => $this->id,
            'selected' =>$request->cat_id== $this->id ? true : false,
            'name' =>$lang=='en' ? $this->name_en : $this->name_ar,
            'desc' =>$lang=='en' ? $this->desc_en : $this->desc_ar,
            'image' => getImageUrl('Category',$this->icon),
            'mainName'=>$mainCat ? $lang =='en' ? $mainCat->name_en : $mainCat->name_ar : null,
            'subCats' => CategoryResource::collection($subCats),
            'Products' => $Products,
        ];
    }
}
