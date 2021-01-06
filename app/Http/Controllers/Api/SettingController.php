<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Reposatries\ProductReposatry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Auth, Artisan, Hash, File, Crypt;

class SettingController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings(){
        $setting=Setting::first();
        return $this->apiResponseData(new SettingResource($setting),'',200);
    }

}
