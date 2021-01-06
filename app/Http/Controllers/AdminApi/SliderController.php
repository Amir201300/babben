<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\SliderResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class SliderController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        App::setLocale(get_user_lang());
        $validate_Slider=$this->validate_Slider($request);
        if(isset($validate_Slider)){
            return $validate_Slider;
        }
        foreach($request->image as $row) {
            $Slider = new Slider;
            $Slider->image = saveImage('Slider', $row);
            $Slider->type = $request->type;
            $Slider->save();
        }
        return $this->apiResponseMessage(1,__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Slider_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request,$Slider_id){
        App::setLocale(get_user_lang($request->header('lang')));
        $Slider=Slider::find($Slider_id);
        if(is_null($Slider)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $Slider=Slider::find($Slider_id);
        $Slider->type= $request->type;
        if($request->image) {
            deleteFile('Slider',$Slider->image);
            $Slider->image = saveImage('Slider', $this->image);
        }
        $Slider->save();
        return $this->apiResponseData(new SliderResource($Slider),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        return $data->getAllDataDashboard(new Slider(),$request,new SliderResource(null));
    }

    /***
     * @param Request $request
     * @param $Slider_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single(Request $request,$Slider_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Slider=Slider::find($Slider_id);
        if(is_null($Slider)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new SliderResource($Slider),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $Slider_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$Slider_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $Slider=Slider::find($Slider_id);
        if(is_null($Slider)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        deleteFile('Slider',$Slider->image);
        $Slider->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }
    /***
     * @param $request
     *  @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_Slider($request)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'image.*.required' => 'من فضلك ادخل الصور' ,
        ];

        $validator = Validator::make($input, [
            'image.*' => 'required|image' ,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }

    }
}
