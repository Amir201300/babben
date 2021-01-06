<?php

namespace App\Http\Controllers\AdminApi;

use App\Interfaces\HandleDataInterface;
use App\Models\Product;
use App\Models\Product_image;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class ProductImagesController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function addImage(Request $request,$product_id)
    {
        $validate_Image=$this->validate_Image($request,$product_id);
        if(isset($validate_Image)){
            return $validate_Image;
        }
        $msg=__('responseMessage.success');
        $productImage=new ProductImage();
        $productImage->image=saveImage('Product_image',$request->image);
        $productImage->product_id=$product_id;
        $productImage->save();
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function removeImage(Request $request,$product_id){
        App::setLocale(get_user_lang());
        $productImage=ProductImage::find($product_id);
        $msg=__('responseMessage.delete');
        if(is_null($productImage)){
            $msg=get_user_lang() =='en' ? 'Image not found' : 'الصورة غير موجود';
        }else {
            deleteFile('Product_image', $productImage->image);
            $productImage->delete();
        }
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @param $request
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_Image($request,$product_id)
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
        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }
    }
}
