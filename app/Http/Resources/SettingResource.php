<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DB, Auth;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $lang = Auth::check() ? get_user_lang() : $request->header('lang');

        return [
            'id' => $this->id,
            'use' => $lang == 'ar' ? $this->use_ar : $this->use_en,
            'policy' => $lang == 'ar' ? $this->policy_ar : $this->policy_en,
            'condition' => $lang == 'ar' ? $this->condition_ar : $this->condition_en,
        ];
    }
}