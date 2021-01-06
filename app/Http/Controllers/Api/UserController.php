<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Interfaces\UserInterface;
use App\MOdels\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, Crypt;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Manage\EmailsController;

class UserController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return mixed
     */
    public function login(Request $request, UserInterface $user)
    {
        return $user->login($request);
    }

    /***
     * @param Request $request
     * @param UserInterface $userFunction
     * @return \Illuminate\Http\JsonResponse|mixed
     */

    public function edit_profile(Request $request, UserInterface $userFunction)
    {
        $user = Auth::user();
        $lang = get_user_lang();
        $validate_user = $userFunction->validate_user($request, $user->id);
        if (isset($validate_user)) {
            return $validate_user;
        }
        $user = $userFunction->edit_profile($request, $user);
        $msg = $lang == 'ar' ? 'تم التعديل بنجاح' : 'Edited successfully';
        return $this->apiResponseData(new UserResource($user), $msg);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function my_info(Request $request)
    {
        $lang = get_user_lang();
        $user = Auth::user();
        $msg = $lang == 'ar' ? 'تمت العملية بنجاح' : 'success';
        return $this->apiResponseData(new UserResource($user), $msg);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function check_active_code(Request $request)
    {
        $user = Auth::user();
        $lang = $user->lang;
        if ($request->code != $user->active_code) {
            $msg = $lang == 'en' ? 'code not correct' : 'الكود غير صحيح';
            return $this->apiResponseMessage(0, $msg, 200);
        }
        $user->active_code = null;
        $user->status = 1;
        $user->save();
        $msg = $lang == 'en' ? 'code correct' : 'الكود صحيح';
        return $this->apiResponseMessage(1, $msg, 200);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout()
    {
        $user = Auth::user();
        $lang = get_user_lang();
        $user->fire_base = null;
        $user->save();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        $msg = $lang == 'ar' ? 'تم تسجيل الخروج بنجاح' : 'logout successfully';
        return $this->apiResponseMessage(1, $msg, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function change_lang(Request $request)
    {
        $user = Auth::user();
        $user->lang = $request->lang;
        $user->save();
        $msg = $request->lang == 'ar' ? 'تم تغيير اللغه بنجاح' : 'language updated successfully';
        return $this->apiResponseMessage(1, $msg, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function my_favorite(){
        $user=Auth::user();
        return $this->apiResponseData(ProductResource::collection($user->my_wishlist),'',200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function orderMarket(Request $request){
        $lang=get_user_lang();
        $user=Auth::user();
        $input = $request->all();
        $validationMessages = [
            'code.required' => $lang == 'ar' ?  'من فضلك ادخل الكود' :"code is required" ,
            'code.unique' => $lang == 'ar' ?  'الكود موجود لدينا' :"code is already exists" ,
        ];

        $validator = Validator::make($input, [
            'code' => 'required|unique:discounts' ,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 2500);
        }
        $discount=new Discount();
        $discount->code=$request->code;
        $discount->status=0;
        $discount->user_id=$user->id;
        $discount->save();
        $user=Auth::user();
        $user->markter=1;
        $user->save();
        $msg=$lang=='en' ? 'your order saved successfully' : 'تم تقديم طلك';
        return $this->apiResponseMessage(1,$msg,200);
    }
}
