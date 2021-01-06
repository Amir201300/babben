<?php

namespace App\Reposatries;

use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator,Auth,Artisan,Hash,File,Crypt;

class CartRepo  {
    use \App\Traits\ApiResponseTrait;

    public function add_to_cart($product_id,$quantity){
        $user=Auth::user();
        $cart=Cart::where('client_id',$user->id)->where('product_id',$product_id)->where('is_order',0)->first();
        if(is_null($cart)){
            $cart=new Cart;
            $cart->product_id=$product_id;
            $cart->client_id=$user->id;
        }
        $cart->quantity=$quantity ? $quantity : 1;
        $cart->is_order=0;
        $cart->save();
    }

    /**
     * @param $product_id
     * @param $quantity
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function validate_product($product_id,$quantity){
        $product=Product::where('id',$product_id)->where('status',1)->first();
        if(is_null($product)){
            $msg=get_user_lang() == 'en' ? 'product number '.$product_id.' not found'
                : 'المنتج رقم '.$product_id.' غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        if($quantity > $product->quantity){
            $msg=get_user_lang() == 'en' ? 'quantity for '.$product->name_en.' not found'
                :'غير موجودة '. $product->name_ar.'الكمية المطلوبة ل';
            return $this->apiResponseMessage(0,$msg,200);

        }
    }

}
