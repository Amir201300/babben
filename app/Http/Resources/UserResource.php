<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Manage\BaseController;


class UserResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => (int)$this->status,
            'fire_base' => $this->fire_base,
            'jop'=>$this->jop,
            'lang'=>$this->lang,
            'token' => $this->user_token,
        ];
    }
}
