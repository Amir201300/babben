<?php

namespace App\Reposatries;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\user_address;
use Auth;

class OrderRepo
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function validate_order($request)
    {
        $lang = get_user_lang();
        $user = Auth::user();
        if ($user->my_cart->count() == 0) {
            $msg = $lang == 'en' ? 'cart is empty' : 'عربة التسوق فارغه';
            return $this->apiResponseMessage(0, $msg, 200);
        }
        $location = user_address::where('id', $request->address_id)->where('user_id', $user->id)->first();
        if (is_null($location)) {
            $msg = $lang == 'en' ? 'address not found' : 'العنوان غير موجود';
            return $this->apiResponseMessage(0, $msg, 200);
        }

        if($request->code_id !=0 AND $request->code_id != $user->code_id){
            $msg = $lang == 'en' ? 'code not found' : 'الكود غير صحيح';
            return $this->apiResponseMessage(0, $msg, 200);
        }
    }

    /**
     * @param $request
     * @return Order
     */
    public function save_order($request){
        $user=Auth::user();
        $discount_value= $request->code_id !=0 ? calDiscount_price(cart_price(),$user->user_discount_value,$user->user_discount_type) : 0;
        $order=new Order();
        $order->status=1;
        $order->client_id=$user->id;
        $order->notFoundProduct=$request->notFoundProduct;
        $order->note=$request->note;
        $order->discount_code_id=$request->code_id ? $request->code_id : null;
        $order->product_price= cart_price();
        $order->discount_price=$discount_value;
        $order->total_price=cart_price() + setting()->shipping_price - $discount_value ;
        $order->address_id=$request->address_id;
        $order->shipping_price=setting()->shipping_price;
        $order->save();
        $this->save_product_to_order($order->id);
        if($request->code_id !=0)
            handleCode(null,2);
        return $order;
    }

    /**
     * @param $order_id
     * @return int
     */
    public function save_product_to_order($order_id){
        $user=Auth::user();
        $carts=Cart::where('client_id',$user->id)->where('is_order',0)->get();
        foreach($carts as $row){
            $row->order_id=$order_id;
            $row->is_order=1;
            $row->save();
            $prod=Product::find($row->product_id);
            $prod->quantity-=$row->quantity;
            $prod->save();
        }
        return 1;
    }

}