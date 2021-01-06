<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Interfaces\UserInterface;
use App\Models\Category;
use App\Reposatries\HandleDataReposatry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator,Auth,Artisan,Hash,File,Crypt;

class CategoryController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /**
     * @param Request $request
     * @param HandleDataReposatry $dataReposatry
     * @return array|mixed
     */
    public function mainCat(Request $request,HandleDataReposatry $dataReposatry){
        $cats=Category::where('level',1)->where('cat_id',null);
        $request['type']=5;
        return $dataReposatry->getAllData($cats,$request,new CategoryResource(null));
    }

    /**
     * @param $cat_id
     * @param HandleDataReposatry $dataReposatry
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function subCat($cat_id,HandleDataReposatry $dataReposatry,Request $request){
        $cats=Category::where('level',2)->where('cat_id',$cat_id);
        return $dataReposatry->getAllData($cats,$request,new CategoryResource(null));
    }
}
