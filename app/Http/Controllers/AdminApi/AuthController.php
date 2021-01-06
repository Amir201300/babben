<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class AuthController extends Controller
{
    use \App\Traits\ApiResponseTrait;


    /**
     * @param Request $request
     * @param AuthInterface $user
     * @return mixed
     */
    public function login(Request $request)
    {
        $lang = $request->header('lang');
        $credentials = [

            'name' => $request['name'],
            'password'=>$request['password'],
        ];
        $credentials_2 = [

            'email' => $request['name'],
            'password'=>$request['password'],
        ];
        if (Auth::guard('Admin')->attempt($credentials) || Auth::guard('Admin')->attempt($credentials_2)) {
            $user=Auth::guard('Admin')->user();
            $user->lang=$lang;
            $user->save();
            $token=$user->createToken('Admin')->accessToken;
            $user['my_token']=$token;
            $msg=$lang=='ar' ? 'تم تسجيل الدخول بنجاح' : 'login success';
            return $this->apiResponseData(new AdminResource($user),$msg,200);
        }
        $msg=$lang=='ar' ? 'البيانات المدخلة غير صحيحة' : 'invalid username or password';
        return $this->apiResponseMessage(0,$msg,200);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function my_info(){
        $user=Auth::user();
        return $this->apiResponseData(new AdminResource($user),'',200);

    }

    /**
     * @param Request $request
     * @param $Admin_id
     * @param AdminController $adminController
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function edit_profile(Request $request,AdminController $adminController){
        App::setLocale(get_user_lang());
        $Admin=Auth::user();
        $validate_Admin=$adminController->validate_Admin($request,$Admin->id);
        if(isset($validate_Admin)){
            return $validate_Admin;
        }
        $Admin->name= $request->name;
        $Admin->phone= $request->phone;
        $Admin->email= $request->email;
        if($request->password)
            $Admin->password= Hash::make($request->password);
        if($request->image) {
            deleteFile('Admin',$Admin->image);
            $Admin->image = saveImage('Admin', $request->image);
        }
        $Admin->save();
        return $this->apiResponseData(new AdminResource($Admin),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });
        $msg=get_user_lang()=='ar' ? 'تم تسجيل الخروج بنجاح' : 'logout successfully';
        return $this->apiResponseMessage(1,$msg,200);
    }
}
