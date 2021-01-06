<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\ColorResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Color;
use App\Models\Product;
use App\Models\Product_color;
use App\Models\Product_size;
use App\Models\Size;
use App\Reposatries\ProductReposatry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class ProductSizeController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function addSize(Request $request,$product_id,ProductReposatry $ProductReposatry)
    {
        $validate_Size=$this->validate_Size($request,$product_id);
        if(isset($validate_Size)){
            return $validate_Size;
        }
        $product=Product::find($product_id);
        $productSize=Product_size::where('product_id',$product_id)->where('size_id',$request->size_id)
            ->first();
        $msg=__('responseMessage.update');
        if(is_null($productSize)){
            $productSize=new Product_size;
            $productSize->size_id=$request->size_id;
            $productSize->product_id=$product_id;
            $msg=__('responseMessage.added');
        }
        $productSize->price=$request->price;
        $productSize->price_after_offer=$request->price - $ProductReposatry->get_discount($request->price,$product->offer_amount);
        $productSize->save();
        return $this->apiResponseMessage(1,$msg,200);
    }


    /**
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function removeSize(Request $request,$product_id){
        App::setLocale(get_user_lang());
        $productSize=Product_size::where('product_id',$product_id)->where('size_id',$request->size_id)
            ->first();
        $msg=__('responseMessage.delete');
        if(is_null($productSize)){
            $msg=get_user_lang() =='en' ? 'size not found' : 'الحجم غير موجود';
        }else {
            $productSize->delete();
        }
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @param $request
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_Size($request,$product_id)
    {
        $input = $request->all();
        $validationMessages = [
            'price.required' =>  get_user_lang() =='en' ? 'price is required' : 'من فضلك ادخل السعر',
        ];

        $validator = Validator::make($input, [
            'price' => 'required' ,
        ], $validationMessages);

        $product=Product::find($product_id);
        if(is_null($product)){
            $msg=get_user_lang() =='en' ? 'product not found' : 'المنتج غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $size=Size::find($request->size_id);
        if(is_null($size)){
            $msg=get_user_lang() =='en' ? 'size not found' : 'الحجم غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }
    }
}
