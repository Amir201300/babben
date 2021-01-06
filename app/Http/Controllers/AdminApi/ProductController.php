<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\ProductResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Category;
use App\Models\Product;
use App\Reposatries\ProductReposatry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class ProductController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request,ProductReposatry $productReposatry)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $validate_product=$this->validate_product($request);
        if(isset($validate_product)){
            return $validate_product;
        }
        $Product = Product::create($request->except('icon','price_after_offer'));
        if($request->image){
            $Product->icon=saveImage('Products',$request->image);
        }
        $Product->save();
        $Product= $productReposatry->set_amount_of_offer($Product->id);
        return $this->apiResponseData(new ProductResource($Product),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $product_id
     * @param ProductReposatry $productReposatry
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Request $request,$product_id,ProductReposatry $productReposatry){
        App::setLocale(get_user_lang($request->header('lang')));
        $Product=Product::find($product_id);
        if(is_null($Product)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $validate_product=$this->validate_product($request);
        if(isset($validate_product)){
            return $validate_product;
        }
        $Product->update($request->except('icon','price_after_offer'));
        if($request->image){
            deleteFile('Products',$Product->icon);
            $Product->icon=saveImage('Products',$request->image);
        }
        $Product->save();
        $Product= $productReposatry->set_amount_of_offer($Product->id);
        return $this->apiResponseData(new ProductResource($Product),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        $products=Product::whereHas('cat');
        if($request->cat_id)
            $products=$products->where('cat_id',$request->cat_id);
        if($request->status)
            $products=$products->where('status',$request->status);
        return $data->getAllDataDashboard($products,$request,new ProductResource(null));
    }

    /***
     * @param Request $request
     * @param $cat_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single(Request $request,$cat_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Product=Product::find($cat_id);
        if(is_null($Product)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new ProductResource($Product),__('responseMessage.success'),200);
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_product($request)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'name_ar.required' => __('responseValidation.name_ar') ,
            'price.required' => __('responseValidation.price') ,
            'price.regex' => __('responseValidation.priceRegex') ,
            'cat_id.exists' => __('responseValidation.catExists') ,
            'cat_id.required' => __('responseValidation.cat_idRequired') ,
            'offer_amount.required' => __('responseValidation.offerAmountExists') ,
        ];

        $validator = Validator::make($input, [
            'name_ar' => 'required',
            'price' => 'required|required|regex:/^\d+(\.\d{1,2})?$/',
            'cat_id' => 'required|exists:categories,id',
            'offer_amount' => $request->is_offer ==1 ? 'required' : '',
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }

        $cats=Category::where('id',$request->cat_id)->where('level',2)->first();
        if(is_null($cats)){
            $msg=__('responseValidation.catExists') ;
            return $this->apiResponseMessage(0,$msg, 200);
        }
    }
}
