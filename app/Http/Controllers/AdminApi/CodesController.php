<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\discout_codeResource;
use App\Interfaces\HandleDataInterface;
use App\Models\discout_code;
use App\Models\user_discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class CodesController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        App::setLocale(get_user_lang());
        $validate_discout_code=$this->validate_discout_code($request,0);
        if(isset($validate_discout_code)){
            return $validate_discout_code;
        }
        $discout_code = new discout_code;
        $discout_code->code= $request->code;
        $discout_code->name_en= $request->name_en;
        $discout_code->name_ar= $request->name_ar;
        $discout_code->desc_ar= $request->desc_ar;
        $discout_code->desc_en= $request->desc_en;
        $discout_code->amount= $request->amount;
        $discout_code->amount_type= $request->amount_type;
        $discout_code->expire_data= $request->expire_data;
        $discout_code->save();
        return $this->apiResponseData(new discout_codeResource($discout_code),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $discout_code_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request,$discout_code_id){
        App::setLocale(get_user_lang($request->header('lang')));
        $discout_code=discout_code::find($discout_code_id);
        if(is_null($discout_code)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $validate_discout_code=$this->validate_discout_code($request,$discout_code_id);
        if(isset($validate_discout_code)){
            return $validate_discout_code;
        }
        $discout_code=discout_code::find($discout_code_id);
        $discout_code->code= $request->code;
        $discout_code->name_en= $request->name_en;
        $discout_code->name_ar= $request->name_ar;
        $discout_code->desc_ar= $request->desc_ar;
        $discout_code->desc_en= $request->desc_en;
        $discout_code->amount= $request->amount;
        $discout_code->status= $request->status;
        $discout_code->amount_type= $request->amount_type;
        $discout_code->expire_data= $request->expire_data;
        $discout_code->save();
        return $this->apiResponseData(new discout_codeResource($discout_code),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @param $discout_code_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateStatus(Request $request,$discout_code_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $discout_code = discout_code::find($discout_code_id);
        if (is_null($discout_code)) {
            return $this->apiResponseMessage(0, __('responseMessage.notFound'), 200);
        }
        $discout_code->status=$request->status;
        $discout_code->save();
        return $this->apiResponseData(new discout_codeResource($discout_code),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        return $data->getAllDataDashboard(new discout_code(),$request,new discout_codeResource(null));
    }

    /***
     * @param $discout_code_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single($discout_code_id)
    {
        App::setLocale(get_user_lang());
        $discout_code=discout_code::find($discout_code_id);
        if(is_null($discout_code)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new discout_codeResource($discout_code),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $discout_code_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$discout_code_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $discout_code=discout_code::find($discout_code_id);
        if(is_null($discout_code)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $discout_code->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_discout_code($request,$id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'code.required' => "من فضلك ادخل كود الخصم" ,
            'amount.required' => "من فضلك ادخل قيمة الخصم" ,
            'code.unique' => "كود الخصم موجود لدينا بالفعل" ,
        ];

        $validator = Validator::make($input, [
            'code' => $id ==0 ? 'required|unique:discout_codes'  :
                'required|unique:discout_codes,code,'.$id,
            'amount'=>'required'
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }
    }

    /***
     * @param Request $request
     * @param $code_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function addUserToCode(Request $request,$code_id){
        $code=discout_code::find($code_id);
        if(is_null($code)){
            return $this->apiResponseMessage(0,'الكود غير موجود',200);
        }
        $userCode=user_discount::where('user_id',$request->user_id)->where('code_id',$code_id)->first();
        if(is_null($userCode)){
            $userCode=new user_discount;
            $userCode->user_id=$request->user_id;
            $userCode->code_id=$code_id;
            $userCode->save();
        }
        return $this->apiResponseMessage(0,'تم اضافة الكود بنجاح',200);

    }
}
