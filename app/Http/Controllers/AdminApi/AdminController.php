<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\AdminResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class AdminController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        App::setLocale(get_user_lang());
        $validate_Admin=$this->validate_Admin($request,0);
        if(isset($validate_Admin)){
            return $validate_Admin;
        }
        $Admin = new Admin;
        $Admin->name= $request->name;
        $Admin->phone= $request->phone;
        $Admin->email= $request->email;
        $Admin->password= Hash::make($request->password);
        if($request->image)
            $Admin->image= saveImage('Admin',$request->image);
        $Admin->save();
        return $this->apiResponseData(new AdminResource($Admin),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Admin_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request,$Admin_id){
        App::setLocale(get_user_lang($request->header('lang')));
        $Admin=Admin::find($Admin_id);
        if(is_null($Admin)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $validate_Admin=$this->validate_Admin($request,$Admin_id);
        if(isset($validate_Admin)){
            return $validate_Admin;
        }
        $Admin=Admin::find($Admin_id);
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
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        return $data->getAllDataDashboard(new Admin(),$request,new AdminResource(null));
    }

    /***
     * @param $Admin_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single($Admin_id)
    {
        App::setLocale(get_user_lang());
        $Admin=Admin::where('id',$Admin_id)->first();
        if(is_null($Admin)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new AdminResource($Admin),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Admin_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$Admin_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Admin=Admin::where('id',$Admin_id)->where('id','!=',1)->first();
        if(is_null($Admin)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $Admin->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function validate_Admin($request,$id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'name.required' => 'من فضلك ادخل الاسم' ,
            'name.unique' => 'الاسم موجود ليدنا بالفعل' ,
        ];

        $validator = Validator::make($input, [
            'name' => $id ==0 ? 'required|unique:admin'  :
                'required|unique:admin,name,'.$id,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }

    }
}
