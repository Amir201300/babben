<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductOrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\user_address;
use App\Reposatries\HandleDataReposatry;
use App\Reposatries\ProductReposatry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, Crypt;
use App\Http\Resources\AddressResource;

class ProductController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param ProductReposatry $ProductReposatry
     * @param $product_id
     * @return mixed|void
     * @throws \Exception
     */
    public function favorite(ProductReposatry $ProductReposatry,$product_id){
        return $ProductReposatry->add_to_favorite($product_id);
    }

    /**
     * @param $product_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function singleProduct($product_id){
        $product=Product::Status(1)->where('id',$product_id)->first();
        if(is_null($product)){
            $msg=get_user_lang() =='en' ? 'product not found' : 'المنتج غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $relatedProducts=Product::Status(1)->where('cat_id',$product->cat_id)
            ->where('id','!=',$product_id)->take(8)->get();
        $data=['product'=>new ProductResource($product),
            'relatedProducts'=>ProductResource::collection($relatedProducts)
        ];
        return $this->apiResponseData($data,'',200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ProductByCat(Request $request){
        $cats=Category::where('level',1)->get();
        $data=[
            'Cats'=>CategoryResource::collection($cats),
            'cartInfo'=>cartInfo()
        ];
        return $this->apiResponseData($data,'',200);
    }

    /**
     * @param Request $request
     * @param HandleDataReposatry $dataReposatry
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed
     */
    public function ProductBySubCat(Request $request,HandleDataReposatry $dataReposatry){
        $products=Product::where('cat_id',$request->cat_id)->status(1);
        return $dataReposatry->getAllData($products,$request,new ProductResource(null));
    }

    /**
     * @param Request $request
     * @param HandleDataReposatry $dataReposatry
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed
     */
    public function filterProduct(Request $request,HandleDataReposatry $dataReposatry){
        $products=Product::status(1);
        if($request->type ==1)
            $products=$products->orderBy('id','desc');
        if($request->type ==2)
            $products=$products->whereHas('orders')->withCount('orders')->orderBy('orders_count','desc');
        return $dataReposatry->getAllData($products,$request,new ProductResource(null));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function orderProduct(Request $request){
        if(!$request->name){
            $msg=get_user_lang() =='en' ? 'name of product is required' : 'من فضلك ادخل اسم المنتج';
            return $this->apiResponseMessage(1,$msg,200);
        }
        $user=Auth::user();
        $orderProduct=new ProductOrder;
        $orderProduct->name=$request->name;
        $orderProduct->desc=$request->desc;
        $orderProduct->user_id=$user->id;
        if($request->image)
            $orderProduct->image=saveImage('ProductOrder',$request->image);
        $orderProduct->save();
        $msg=get_user_lang() =='en' ? 'product saved successfully' : 'تم حفظ المنتج وعند توفره سوف نعلمكم';
        return $this->apiResponseData(new ProductOrderResource($orderProduct),$msg,200);
    }
}
