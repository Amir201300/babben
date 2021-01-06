<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductOrderResource;
use App\Http\Resources\SupplierOrderResource;
use App\Models\SupplierOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, Crypt;
use App\Http\Resources\AddressResource;

class SupplierController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function orderSupplier(Request $request){
        $lang=$request->header('lang');
        $input = $request->all();
        $validationMessages = [
            'name.required' => $lang == 'ar' ?  'من فضلك ادخل الاسم' :"name is required" ,
            'email.unique' => $lang == 'ar' ? 'هذا البريد الالكتروني موجود لدينا بالفعل' :"email is already teken" ,
            'email.regex'=>$lang=='ar'? 'من فضلك ادخل بريد الكتروني صالح' : 'The email must be a valid email address',
            'phone.required' => $lang == 'ar' ? 'من فضلك ادخل  رقم الهاتف' :"phone is required"  ,
            'phone.unique' => $lang == 'ar' ? 'رقم الهاتف موجود لدينا بالفعل' :"phone is already teken" ,
            'phone.min' => $lang == 'ar' ?  'رقم الهاتف يجب ان لا يقل عن 7 ارقام' :"The phone must be at least 7 numbers" ,
            'phone.numeric' => $lang == 'ar' ?  'رقم الهاتف يجب ان يكون رقما' :"The phone must be a number" ,
        ];

        $validator = Validator::make($input, [
            'name' => 'required',
            'phone' => 'required|min:7|numeric' ,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 2500);
        }
        $orderSupplier=new SupplierOrder();
        $orderSupplier->name=$request->name;
        $orderSupplier->phone=$request->phone;
        $orderSupplier->address=$request->address;
        $orderSupplier->activity=$request->activity;
        $orderSupplier->status=0;
        $orderSupplier->save();
        $msg=$lang=='en' ? 'data saved successfully' : 'تم حفظ بياناتك بنجاح';
        return $this->apiResponseData(new SupplierOrderResource($orderSupplier),$msg,200);
    }
}
