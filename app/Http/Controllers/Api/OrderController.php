<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\MOdels\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\user_discount;
use App\Reposatries\HandleDataReposatry;
use App\Reposatries\CartRepo;
use App\Reposatries\OrderRepo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, DB;

class OrderController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    public function makeOrder(Request $request,OrderRepo $order){
        $validate_order=$order->validate_order($request);
        if(isset($validate_order)){
            return $validate_order;
        }
        $order=$order->save_order($request);
        $msg=get_user_lang() =='en' ? 'order saved successfully' : 'تم حفظ الطلب بنجاح';
        return $this->apiResponseData(new OrderResource($order),$msg,200);
    }

    /**
     * @param HandleDataReposatry $dataReposatry
     * @param Request $request
     * @return array|mixed
     */
    public function myOrders(HandleDataReposatry $dataReposatry,Request $request){
        $user=Auth::user();
        $orders=Order::where('client_id',$user->id);
        return $dataReposatry->getAllData($orders,$request,new OrderResource(null));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function singleOrder(Request $request){
        $user=Auth::user();
        $order=Order::where('client_id',$user->id)->where('id',$request->order_id)->first();
        if(is_null($order)){
            $msg=get_user_lang() =='en' ? 'order not found' : 'الطلب غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $msg=get_user_lang() =='en' ? 'success' : 'تمت العملية بنجاح';
        return $this->apiResponseData(new OrderResource($order),$msg,200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
        public function checkDiscountCode (Request $request){
        $user=Auth::user();
        $lang=get_user_lang();
        $code=Discount::where('code',$request->code)
            ->where('status',1)->whereDate('expire_data','>=',now())->first();
        if(is_null($code)){
            $msg=$lang=='en' ? 'code not fount' : 'كود الخصم غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $is_use = user_discount::where('code_id',$code->id)->where('user_id',$user->id)
            ->first();
        if(!is_null($is_use)){
            $msg=$lang=='en' ?'code already used' : 'تم استخدام الكود'  ;
            return $this->apiResponseMessage(0,$msg,200);
        }
        $data= handleCode($code,1);
        $is_use=new user_discount();
        $is_use->user_id=$user->id;
        $is_use->code_id=$code->id;
        $is_use->save();
        $msg=$lang=='en' ? 'code apply successfully' : 'تم الخصم بنجاح';
        return $this->apiResponseData($data,$msg,200);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function removeDiscountCode(){
        $user=Auth::user();
        $code=Discount::where('id',$user->code_id)->first();
        if(is_null($code)){
            $msg=get_user_lang()=='en' ? 'code not fount' : 'كود الخصم غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        user_discount::where('code_id',$code->id)->where('user_id',$user->id)
            ->delete();
        handleCode(null,2);
        $msg=get_user_lang()=='en' ? 'code remove successfully' : 'تم حذف الكود بنجاح';
        return $this->apiResponseMessage(1,$msg,200);
    }

}
