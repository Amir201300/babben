<?php

namespace App\Http\Resources;

use App\Models\Cart;
use Illuminate\Http\Resources\Json\JsonResource;
use DB, Auth;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth('api')->user();
        $lang = Auth::check() ? get_user_lang() : $request->header('lang');
        $is_fav = false;
        $inCart = false;
        if ($user) {
            $is_fav = $this->user_favorite->contains($user->id);
            $inCart = $this->cart->contains($user->id);
            $quantity = Cart::where('client_id',$user->id)
                ->where('is_order',0)->where('product_id',$this->id)->first();
        }

        return [
            'id' => $this->id,
            'name' => $lang == 'ar' ? (string)$this->name_ar : (string)$this->name_en,
            'desc' => $lang == 'ar' ? (string)$this->desc_ar : (string)$this->desc_en,
            'cat' => $this->cat ? $lang == 'en' ? $this->cat->name_en : $this->cat->name_ar : null,
            'image' => getImageUrl('Products', $this->icon),
            'rate' => (int)$this->rate,
            'status' => $this->quantity > 1 ? (int)$this->status : 0,
            'price_product' => number_format((float)$this->price, 2, '.', ''),
            'price_after_offer' => number_format((float)$this->price_after_offer, 2, '.', ''),
            'quantity' => (int)$this->quantity,
            'is_fire' => (int)$this->is_fire,
            'CartQuantity' => $this->pivot ? (int)$this->pivot->quantity :0,
            'is_offer' => $this->is_offer ? (int)$this->is_offer : 0,
            'offer_amount' => (int)$this->offer_amount,
            'is_favorite' => $is_fav,
            'inCart' => $inCart,
            'quantityInCart' => isset($quantity) ? (int)$quantity->quantity : 0,
            'images' => ImagesResource::collection($this->images),
        ];
    }
}