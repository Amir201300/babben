<?php

namespace App\Reposatries;

use App\Http\Resources\UserResource;
use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Validator,Auth,Artisan,Hash,File,Crypt;

class UserReposatry implements UserInterface {
    use \App\Traits\ApiResponseTrait;

    /***
     * @param $request
     * @param $user_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function validate_user($request, $user_id)
    {
        $lang =  Auth::check() ? get_user_lang() : $request->header('lang') ;
        $input = $request->all();
        $validationMessages = [
            'name.required' => $lang == 'ar' ?  'من فضلك ادخل الاسم' :"name is required" ,
           // 'email.required' => $lang == 'ar' ? 'من فضلك ادخل البريد الالكتروني' :"email is required"  ,
            'email.unique' => $lang == 'ar' ? 'هذا البريد الالكتروني موجود لدينا بالفعل' :"email is already teken" ,
            'email.regex'=>$lang=='ar'? 'من فضلك ادخل بريد الكتروني صالح' : 'The email must be a valid email address',
            'phone.required' => $lang == 'ar' ? 'من فضلك ادخل  رقم الهاتف' :"phone is required"  ,
            'phone.unique' => $lang == 'ar' ? 'رقم الهاتف موجود لدينا بالفعل' :"phone is already teken" ,
            'phone.min' => $lang == 'ar' ?  'رقم الهاتف يجب ان لا يقل عن 7 ارقام' :"The phone must be at least 7 numbers" ,
            'phone.numeric' => $lang == 'ar' ?  'رقم الهاتف يجب ان يكون رقما' :"The phone must be a number" ,
        ];

        $validator = Validator::make($input, [
            'name' => 'required',
            'phone' => $user_id ==0 ? 'required|unique:users|min:7|numeric' : 'required|unique:users,phone,'.$user_id.'|min:7|numeric',
            'email' => $user_id ==0 ? 'unique:users|regex:/(.+)@(.+)\.(.+)/i' : 'required|unique:users,email,'.$user_id.'|regex:/(.+)@(.+)\.(.+)/i',
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 2500);
        }
    }


    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     */
    public function login($request)
    {
        $lang = $request->header('lang');
        $user=User::where('phone',$request->phone)->first();
        if(is_null($user)){
            $user=new User;
            $user->phone=$request->phone;
            $user->lang=$lang;
        }
        $user->active_code=1234;
        if($request->fire_base)
            $user->fire_base=$request->fire_base;
        $user->save();
        $token = $user->createToken('TutsForWeb')->accessToken;
        $user['user_token']=$token;
        $msg=$lang=='ar' ? 'تم ارسال كود الي رقم هاتفك':'A code has been sent to your phone number' ;
        return $this->apiResponseData(new UserResource($user),$msg,200);
    }

    /***
     * @param $request
     * @param $user
     * @return mixed
     */
    public function edit_profile($request, $user)
    {
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->jop = $request->jop;
        $user->save();
        return $user;
    }
}
