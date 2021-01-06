<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ColorResource;
use App\Http\Resources\ImagesResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use DB,Auth;
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $MainId=$this->cat ? $this->cat->cat_id : null;
        $mainCat=Category::where('id',$MainId)->first();
        //return $this->id;
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar ,
            'name_en' => $this->name_en ,
            'desc_ar' =>$this->desc_ar ,
            'desc_en' =>$this->desc_en ,
            'SubCat_id' =>$this->cat_id ,
            'mainCat_id' =>$mainCat ? $mainCat->id  : null,
            'subCat_ar' => $this->cat ? $this->cat->name_ar : null  ,
            'subCat_en' => $this->cat ? $this->cat->name_en : null  ,
            'mainCat_en' => $mainCat ? $mainCat->name_en : null  ,
            'mainCat_ar' => $mainCat ? $mainCat->name_ar : null  ,
            'image' => getImageUrl('Products',$this->icon),
            'rate' => (int)$this->rate,
            'status' => (int)$this->status,
            'is_fire' => (int)$this->is_fire,
            'price_product'=>(double)$this->price,
            'price_after_offer'=>(double)$this->price_after_offer,
            'quantity'=>(int)$this->quantity,
            'is_offer'=>$this->is_offer ?(int) $this->is_offer : 0,
            'offer_amount'=>(int)$this->offer_amount,
            'images'=>ImagesResource::collection($this->images),
        ];
    }
}