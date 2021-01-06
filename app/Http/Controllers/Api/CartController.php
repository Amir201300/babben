<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stores;
use App\Reposatries\HandleDataReposatry;
use App\Reposatries\CartRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, DB;

class CartController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param CartRepo $CartRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function addToCart (Request $request, CartRepo $CartRepo){
        $validate_product=$CartRepo->validate_product($request->product_id,$request->quantity);
        if(isset($validate_product)){
            return $validate_product;
        }
        $CartRepo->add_to_cart($request->product_id,$request->quantity);
        $msg=get_user_lang() == 'en' ? 'product added successfully' : 'تم اضافة المنتج بنجاح';
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function myCart(){
        $user=Auth::user();
        $data=['products'=>ProductResource::collection($user->my_cart),
            'total_price'=>cart_price(),
        ];
        return $this->apiResponseData($data,'success',200);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteMyCart(){
        $user=Auth::user();
        Cart::where('client_id',$user->id)->where('is_order',0)->delete();
        $msg=get_user_lang() =='en' ? 'cart deleted successfully' : 'تم حذف عربة التسوق بنجاح';
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteFromCart(Request $request){
        $user=Auth::user();
        $cart=Cart::where('client_id',$user->id)->where('is_order',0)->where('product_id',$request->product_id)->first();
        if(is_null($cart)){
            $msg=get_user_lang() == 'en' ? 'product not found' : 'المنتج غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $cart->delete();
        $msg=get_user_lang() =='en' ? 'product deleted successfully' : 'تم حذف المنتج بنجاح';
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @param Request $request
     * @param CartRepo $orderRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateCart(Request $request, CartRepo $orderRepo){
        $user=Auth::user();
        foreach($request->products as $row) {
            $validate_product = $orderRepo->validate_product($row['product_id'],$row['quantity']);
            if (isset($validate_product)) {
                return $validate_product;
            }
        }
        Cart::where('client_id',$user->id)->where('is_order',0)->delete();
        foreach($request->products as $row){
            $orderRepo->add_to_cart($row['product_id'],$row['quantity']);
        }
        $msg=get_user_lang() =='en' ? 'cart updated successfully' : 'تم تعديل عربة التسوق بنجاح';
        return $this->apiResponseMessage(1,$msg,200);

    }

}
