<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Interfaces\HandleDataInterface;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class UserController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        $users =User::orderBy('id','desc');
        if($request->markter)
            $users=$users->where('markter',1);
        return $data->getAllDataDashboard($users,$request,new UserResource(null));
    }

    /***
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single(Request $request,$user_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $user=User::find($user_id);
        if(is_null($user)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new UserResource($user),__('responseMessage.success'),200);
    }


    /**
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$user_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $user=User::find($user_id);
        if(is_null($user)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        deleteFile('users',$user->image);
        $user->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }

}
