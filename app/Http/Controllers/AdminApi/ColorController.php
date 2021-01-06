<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\ColorResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class ColorController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        App::setLocale(get_user_lang());
        $validate_Color=$this->validate_Color($request);
        if(isset($validate_Color)){
            return $validate_Color;
        }
        $Color = new Color;
        $Color->color_code= $request->color_code;
        $Color->save();
        return $this->apiResponseData(new ColorResource($Color),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Color_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request,$Color_id){
        App::setLocale(get_user_lang($request->header('lang')));
        $Color=Color::find($Color_id);
        if(is_null($Color)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $validate_Color=$this->validate_Color($request);
        if(isset($validate_Color)){
            return $validate_Color;
        }
        $Color=Color::find($Color_id);
        $Color->color_code= $request->color_code;
        $Color->save();
        return $this->apiResponseData(new ColorResource($Color),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        return $data->getAllDataDashboard(new Color(),$request,new ColorResource(null));
    }

    /***
     * @param $Color_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single($Color_id)
    {
        App::setLocale(get_user_lang());
        $Color=Color::find($Color_id);
        if(is_null($Color)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new ColorResource($Color),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Color_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$Color_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Color=Color::find($Color_id);
        if(is_null($Color)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $Color->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }
    /***
     * @param $request
     *  @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_Color($request)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'color_code.required' => __('responseValidation.color') ,
        ];

        $validator = Validator::make($input, [
            'color_code' => 'required' ,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }

    }
}
