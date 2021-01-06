<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\OrderResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class OrderController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        $Orders=new Order();
        if($request->status)
            $Orders=$Orders->where('status',$request->status);
        return $data->getAllDataDashboard($Orders,$request,new OrderResource(null));
    }

    /***
     * @param Request $request
     * @param $cat_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single(Request $request,$cat_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Order=Order::find($cat_id);
        if(is_null($Order)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new OrderResource($Order),__('responseMessage.success'),200);
    }


    /**
     * @param Request $request
     * @param $cat_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$cat_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Order=Order::find($cat_id);
        if(is_null($Order)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $Order->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }


}
