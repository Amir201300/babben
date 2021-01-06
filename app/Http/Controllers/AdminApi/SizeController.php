<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\SizeResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class SizeController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        App::setLocale(get_user_lang());
        $validate_Size=$this->validate_Size($request);
        if(isset($validate_Size)){
            return $validate_Size;
        }
        $Size = new Size;
        $Size->name_ar= $request->name_ar;
        $Size->name_en= $request->name_en;
        $Size->save();
        return $this->apiResponseData(new SizeResource($Size),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Size_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request,$Size_id){
        App::setLocale(get_user_lang($request->header('lang')));
        $Size=Size::find($Size_id);
        if(is_null($Size)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $validate_Size=$this->validate_Size($request);
        if(isset($validate_Size)){
            return $validate_Size;
        }
        $Size=Size::find($Size_id);
        $Size->name_ar= $request->name_ar;
        $Size->name_en= $request->name_en;
        $Size->save();
        return $this->apiResponseData(new SizeResource($Size),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        return $data->getAllDataDashboard(new Size(),$request,new SizeResource(null));
    }

    /***
     * @param Request $request
     * @param $Size_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single(Request $request,$Size_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Size=Size::find($Size_id);
        if(is_null($Size)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new SizeResource($Size),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Size_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$Size_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Size=Size::find($Size_id);
        if(is_null($Size)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $Size->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }
    /***
     * @param $request
     *  @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_Size($request)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'name_ar.required' => __('responseValidation.name_ar') ,
            'name_en.required' => __('responseValidation.name_en') ,
        ];

        $validator = Validator::make($input, [
            'name_ar' => 'required' ,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }

    }
}
