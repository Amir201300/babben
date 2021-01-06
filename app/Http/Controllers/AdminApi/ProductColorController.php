<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\ColorResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Color;
use App\Models\Product;
use App\Models\Product_color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class ProductColorController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function addColors(Request $request,$product_id)
    {
        $validate_Color=$this->validate_Color($request,$product_id);
        if(isset($validate_Color)){
            return $validate_Color;
        }
        $productColor=Product_color::where('product_id',$product_id)->where('color_id',$request->color_id)
            ->first();
        $msg=__('responseMessage.update');
        if(is_null($productColor)){
            $productColor=new Product_color;
            $productColor->color_id=$request->color_id;
            $productColor->product_id=$product_id;
            $msg=__('responseMessage.added');
        }
        deleteFile('Color',$productColor->image);
        $productColor->image=saveImage('Color',$request->image);
        $productColor->save();
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function removeColor(Request $request,$product_id){
        App::setLocale(get_user_lang());
        $productColor=Product_color::where('product_id',$product_id)->where('color_id',$request->color_id)
            ->first();
        $msg=__('responseMessage.delete');
        if(is_null($productColor)){
            $msg=get_user_lang() =='en' ? 'color not found' : 'اللون غير موجود';
        }else {
            deleteFile('Color', $productColor->image);
            $productColor->delete();
        }
        return $this->apiResponseMessage(1,$msg,200);

    }

    /**
     * @param $request
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_Color($request,$product_id)
    {
        $input = $request->all();
        $validationMessages = [
            'image.required' =>  get_user_lang() =='en' ? 'image is required' : 'من فضلك ادخل الصورة',
        ];

        $validator = Validator::make($input, [
            'image' => 'required|image' ,
        ], $validationMessages);

        $product=Product::find($product_id);
        if(is_null($product)){
            $msg=get_user_lang() =='en' ? 'product not found' : 'المنتج غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $color=Color::find($request->color_id);
        if(is_null($color)){
            $msg=get_user_lang() =='en' ? 'color not found' : 'اللون غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }
    }
}
