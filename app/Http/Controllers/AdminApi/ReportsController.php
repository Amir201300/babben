<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator,Auth,Artisan,Hash,File,Crypt;

class ReportsController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param $driverId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function Driver(Request $request,$driverId){
        $driver=User::where('user_type',2)->where('id',$driverId)->first();
        if(is_null($driver)){
            $msg=get_user_lang() =='en' ? 'driver not found' : 'السائق غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $orders=Order::where('driver_id',$driverId);
        $orders=$this->getOrderByType($request,$orders);
        $orders=$orders->get();
        $data=['order'=>OrderResource::collection($orders),
            'totalPrice'=>castNumbers($orders->sum('total_price')),
            'shippingPrice'=>castNumbers($orders->sum('shipping_price'))
        ];
        return $this->apiResponseData($data,'success',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function paymentType(Request $request){
        $orders=Order::orderBy('id','desc');
        $orders=$this->getOrderByType($request,$orders);
        $orders=$orders->get();
        $ordersVisa=$orders->where('payment_method',2)->count();
        $ordersCash=$orders->where('payment_method',1)->count();
        $data=['visa'=>$ordersVisa,
            'cash'=>$ordersCash
        ];
        return $this->apiResponseData($data,'success',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function counts(Request $request){
        $productCount=Product::whereMonth('created_at',now())->count();
        $orderCount=Order::whereMonth('created_at',now())->count();
        $UserCount=User::whereMonth('created_at',now())->count();
        $data=['orderCount'=>$orderCount,
            'productCount'=>$productCount,
            'UserCount'=>$UserCount
        ];
        return $this->apiResponseData($data,'success',200);

    }

    /**
     * @param $request
     * @param $orders
     * @return mixed
     */
    public function getOrderByType($request,$orders){
        if($request->type == 1)
            $orders=$orders->whereDay('created_at',now());
        if($request->type == 2)
            $orders=$orders->whereMonth('created_at',now());
        if($request->type == 3)
            $orders=$orders->whereYear('created_at',now());
        if($request->type ==4)
            $orders=$orders->whereDate('created_at',$request->from);
        if($request->type ==5)
            $orders=$orders->whereDate('created_at','>=',$request->from)->whereDate('created_at','<=',$request->to);
        return $orders;
    }

    /**
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|int
     */
    public function quantityOfProduct(Request $request,$product_id){
        $product=Product::find($product_id);
        if(is_null($product)){
            $msg=get_user_lang() =='en' ? 'product not found' : 'المنتج غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $orders=Order::orderBy('id','desc')->whereHas('products',function($q) use ($product_id){
            $q->where('product_id',$product_id);
        });
        $orders=$this->getOrderByType($request,$orders);
        $orders=$orders->get();
        $quantity=0;
        foreach($orders as $row){
            $quantity+=$row->products->where('id',$product_id)->first()->pivot->quantity;
        }
        $data=['sailQuantity'=>$quantity,'realQuantity'=>$quantity +$product->quantity ];
        return $this->apiResponseData($data,'success',200);
    }

}
