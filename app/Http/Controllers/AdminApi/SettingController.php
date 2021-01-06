<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Admin\SettingResource;
use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Store;
use App\Reposatries\ProductReposatry;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class SettingController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param $product_id
     * @param ProductReposatry $productReposatry
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateSetting(Request $request){
        $setting=Setting::first();
        App::setLocale(get_user_lang());

        // $validate_setting=$this->validate_setting($request);
        // if(isset($validate_setting)){
        //     return $validate_setting;
        // }
        if($request->name_ar)
            $setting->name_ar=$request->name_ar;
        if($request->name_en)
            $setting->name_en=$request->name_en;
        if($request->desc_ar)
            $setting->desc_ar=$request->desc_ar;
        if($request->desc_en)
            $setting->desc_en=$request->desc_en;
        if($request->facebook)
            $setting->facebook=$request->facebook;
        if($request->snab)
            $setting->snab=$request->snab;
        if($request->twitter)
            $setting->twitter=$request->twitter;
        if($request->instagram)
            $setting->instagram=$request->instagram;
        if($request->shipping_price)
            $setting->shipping_price=$request->shipping_price;
        if($request->tax)
            $setting->tax=$request->tax;
        $setting->save();
        $store=Store::first();
        if($request->lat)
            $store->lat=$request->lat;
        if($request->lng)
            $store->lng=$request->lng;
        if($request->address)
            $store->address=$request->address;
        $store->save();
        return $this->apiResponseData(new SettingResource($setting),__('responseMessage.update'),200);
    }

    /***
     * @param Request $request
     * @param $cat_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getSetting(Request $request)
    {
        App::setLocale('ar');
        $setting=Setting::first();
        if(is_null($setting)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new SettingResource($setting),__('responseMessage.success'),200);
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_setting($request)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'shipping_price.required' =>"من فضلك ادخل سعر كيلو التوصيل",
            'lat.required' =>"من فضلك ادخل خط العرض",
            'lng.required' =>"من فضلك ادخل خط الطول",
        ];

        $validator = Validator::make($input, [
            'shipping_price' => 'required',
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function search(Request $request){
        $products=Product::where(function($q) use ($request){
            $q->where('name_ar','LIKE','%' . $request->name.'%')->orWhere('name_en','LIKE','%' . $request->name . '%');
        })->get();
        $clients=User::where('name','LIKE','%' . $request->name . '%')->get();
        $data=['products'=>ProductResource::collection($products),'clients'=>UserResource::collection($clients)];
        return $this->apiResponseData($data,'success',200);
    }
}
