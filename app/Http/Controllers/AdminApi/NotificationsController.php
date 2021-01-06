<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Api\NotificationMethods;
use App\Http\Resources\ColorResource;
use App\Http\Resources\Admin\NotificationResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Color;
use App\Models\Notification;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class NotificationsController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function send(Request $request){
        $title=$request->title;
        $desc=$request->desc;
        $type=$request->type;
      //  return $type;
        if($type == 1){
            $ids=User::where('user_type',$request->user_type)->pluck('id')->toArray();
            NotificationMethods::senNotificationToMultiUsers($title,$desc,null,$ids,$request->user_type);
        }elseif($type == 2){
            $user=User::find($request->ids[0]);
            NotificationMethods::senNotificationToMultiUsers($title,$desc,null,$request->ids,$user->user_type);
        }elseif($type ==3){
            $user=User::find($request->ids[0]);
            if(is_null($user)){
                $msg=get_user_lang() =='en' ? 'user not found' : 'العضو غير موجود';
                return $this->apiResponseMessage(0,$msg,200);
            }
            NotificationMethods::senNotificationToSingleUser($user,$title,$desc,null,1,1,1);
        }
        $msg=get_user_lang() =='en' ? 'notification send successfully' : 'تم ارسال الاشعار بنجاح';
        return $this->apiResponseMessage(1,$msg,200);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function all(){
        $notfication=Notification::orderBy('id','desc')->where('admin',1)->get();
        return $this->apiResponseData(NotificationResource::collection($notfication),'',200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$id)
    {
        App::setLocale(get_user_lang());
        $Notification=Notification::find($id);
        if(is_null($Notification)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $Notification->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resend(Request $request,$id)
    {
        App::setLocale(get_user_lang());
        $No=Notification::find($id);
        if(is_null($No)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $user=User::find($No->user_id);
        NotificationMethods::senNotificationToSingleUser($user,$No->title,$No->desc,null,
            1,1,1);
        $msg=get_user_lang() =='en' ? 'Notification resend successfully' : 'تم اعادة الارسال بنجاح';
        return $this->apiResponseMessage(1,$msg,200);
    }
}
