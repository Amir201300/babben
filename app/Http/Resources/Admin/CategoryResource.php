<?php

namespace App\Http\Resources\Admin;

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
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'desc_ar' =>$this->desc_ar,
            'desc_en' =>$this->desc_en,
            'level' =>(int)$this->level,
            'cat_id' =>(int)$this->cat_id,
            'status' =>(int)$this->status,
            'parent_name' =>$this->parent ? $this->parent->name_ar : null,
            'image' => getImageUrl('Category',$this->icon),
        ];
    }
}
