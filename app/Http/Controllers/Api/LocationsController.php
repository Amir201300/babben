<?php

namespace App\Http\Controllers\Api;

use App\Models\user_address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, DB;
use App\Http\Resources\AddressResource;

class LocationsController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function my_addresses(){
        $user=Auth::user();
        $address=user_address::where('user_id',$user->id)->get();
        return $this->apiResponseData(AddressResource::collection($address),'',200);

    }
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function add_address(Request $request)
    {
        $user = Auth::user();
        $lang = get_user_lang();
        $validate_user = $this->validate_address($request);
        if (isset($validate_user)) {
            return $validate_user;
        }
        $user = $this->saveData($request, new user_address());
        $msg = $lang == 'ar' ? 'تم اضافة العنوان بنجاح' : 'location added successfully';
        return $this->apiResponseData(new AddressResource($user), $msg);
    }

    /**
     * @param Request $request
     * @param $address_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function edit_address(Request $request,$address_id)
    {
        $user = Auth::user();
        $lang = get_user_lang();
        $address=user_address::where('user_id',$user->id)->where('id',$address_id)->first();
        if(is_null($address)){
            $msg=get_user_lang() =='en' ? 'address not found' : 'العنوان غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        $validate_user = $this->validate_address($request);
        if (isset($validate_user)) {
            return $validate_user;
        }
        $user = $this->saveData($request,$address);
        $msg = $lang == 'ar' ? 'تم التعديل بنجاح' : 'Edited successfully';
        return $this->apiResponseData(new AddressResource($user), $msg);
    }

    /**
     * @param Request $request
     * @param $address_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete_address(Request $request,$address_id)
    {
        $user = Auth::user();
        $lang = get_user_lang();
        $address=user_address::where('user_id',$user->id)->where('id',$address_id)->first();
        if(is_null($address)){
            $msg=get_user_lang() =='en' ? 'address not found' : 'العنوان غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }

        $user = $address->delete();
        $msg = $lang == 'ar' ? 'تم الحذف بنجاح' : 'deleted successfully';
        return $this->apiResponseMessage(1, $msg,200);
    }


    /**
     * @param Request $request
     * @param $address_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function single_address(Request $request,$address_id)
    {
        $user = Auth::user();
        $lang = get_user_lang();
        $address=user_address::where('user_id',$user->id)->where('id',$address_id)->first();
        if(is_null($address)){
            $msg=get_user_lang() =='en' ? 'address not found' : 'العنوان غير موجود';
            return $this->apiResponseMessage(0,$msg,200);
        }
        return $this->apiResponseData(new AddressResource($address),200);
    }


    /***
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function validate_address($request)
    {
        $lang =  Auth::check() ? get_user_lang() : $request->header('lang') ;
        $input = $request->all();
        $validationMessages = [
            'place_type.required' => $lang == 'ar' ?  'من فضلك ادخل نوع المكان' :"place type is required" ,
        ];

        $validator = Validator::make($input, [
            'lat' => 'required',
            'lng' => 'required',
            'place_type' => 'required',
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 2500);
        }
    }

    /**
     * @param $request
     * @param $address
     * @return mixed
     */
    private function saveData($request , $address){
        $user=Auth::user();
        $address->user_id=$user->id;
        $address->lat=$request->lat;
        $address->lng=$request->lng;
        $address->address=$request->address;
        $address->place_name=$request->place_name;
        $address->place_type=$request->place_type;
        $address->note=$request->note;
        $address->city=$request->city;
        $address->is_default=$request->is_default;
        $address->save();
        if($request->is_default ==1){
            DB::table('user_addresses')
                ->where('id','!=',$address->id)->where('user_id',$user->id)->update(['is_default'=>0]);
        }
        return $address;
    }

}
