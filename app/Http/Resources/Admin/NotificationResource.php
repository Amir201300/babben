<?php

namespace App\Http\Resources\Admin;
use App\Http\Resources\UserResource;
use Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title'=>$this->title,
            'desc'=>$this->desc,
            'redirect_id'=>(int)$this->redirect_id,
            'crated_at'=>date('Y-m-d',strtotime($request->crated_at)),
            'user'=> new UserResource($this->user),
            'user_type'=> $this->click_action,
        ];
    }
}
