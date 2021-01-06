<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AddressResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FilterProductResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SliderResource;
use App\Models\Category;
use App\Models\FilterProduct;
use App\Models\Product;
use App\Models\Slider;
use App\Models\user_address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, Crypt;

class HomeController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function home(Request $request){
        $user=Auth::user();
        $cats=Category::where('level',1)->take(8)->get();
        $sliders=Slider::get();
        $FilterProduct=FilterProduct::take(4)->get();
        $address=user_address::where('user_id',$user->id)->where('is_default',1)->first();
        $data=['Slider'=>SliderResource::collection($sliders)
        ,'Cats'=>CategoryResource::collection($cats),
        'location'=>new AddressResource($address),
        'FilterProduct'=>FilterProductResource::collection($FilterProduct),
            'cartInfo'=>cartInfo()
        ];
        return $this->apiResponseData($data,'',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function subSlider(Request $request){
        $sliders=Slider::where('type',$request->type)->get();
        return $this->apiResponseData(SliderResource::collection($sliders),'',200);

    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function homeWebSite(Request $request){
        $cats=Category::where('level',1)->take(8)->get();
        $sliders=Slider::get();
        $products=Product::status(1)->OrderBy('id','desc')->take(18)->get();
        $FilterProduct=FilterProduct::take(4)->get();
        $data=['Slider'=>SliderResource::collection($sliders)
            ,'Cats'=>CategoryResource::collection($cats),
            'Products'=>ProductResource::collection($products),
            'FilterProduct'=>FilterProductResource::collection($FilterProduct)
        ];
        $request['type']=5;
        return $this->apiResponseData($data,'',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterProducts(Request $request){

        $FilterProduct=FilterProduct::take(4)->get();
        return $this->apiResponseData(FilterProductResource::collection($FilterProduct),'',200);
    }
}
