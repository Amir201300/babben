<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Resources\Admin\CategoryResource;
use App\Interfaces\HandleDataInterface;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Validator,Auth,Artisan,Hash,File,Crypt;

class CategoryController extends Controller
{
    use \App\Traits\ApiResponseTrait;

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $validate_cat=$this->validate_cat($request);
        if(isset($validate_cat)){
            return $validate_cat;
        }
        $category = Category::create($request->except('icon','cat_id'));
        if($request->image){
            $category->icon=saveImage('Category',$request->image);
        }

        $category->cat_id= $request->level == 2 ? $request->cat_id : 0;
        $category->save();
        return $this->apiResponseData(new CategoryResource($category),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $cat_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request,$cat_id){
        App::setLocale(get_user_lang($request->header('lang')));
        $category=Category::find($cat_id);
        if(is_null($category)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        $validate_cat=$this->validate_cat($request);
        if(isset($validate_cat)){
            return $validate_cat;
        }
        $category->update($request->except('icon'));
        if($request->image){
            deleteFile('Category',$category->icon);
            $category->icon=saveImage('Category',$request->image);
        }
        $category->cat_id= $request->level == 2 ? $request->cat_id : 0;
        $category->save();
        return $this->apiResponseData(new CategoryResource($category),__('responseMessage.update'),200);
    }

    /**
     * @param Request $request
     * @param HandleDataInterface $data
     * @return mixed
     */
    public function all(Request $request,HandleDataInterface $data){
        $cats=new Category();
        if($request->cat_id){
            $cats=$cats->where('level',2)->where('cat_id',$request->cat_id);
        }else{
            $cats=$cats->where('level',1);
        }

        return $data->getAllDataDashboard($cats,$request,new CategoryResource(null));
    }

    /***
     * @param Request $request
     * @param $cat_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function single(Request $request,$cat_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $category=Category::find($cat_id);
        if(is_null($category)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        return $this->apiResponseData(new CategoryResource($category),__('responseMessage.success'),200);
    }

    /**
     * @param Request $request
     * @param $cat_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(Request $request,$cat_id)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $category=Category::find($cat_id);
        if(is_null($category)){
            return $this->apiResponseMessage(0,__('responseMessage.notFound'),200);
        }
        deleteFile('Category',$category->icon);
        $category->delete();
        Category::where('cat_id',$cat_id)->delete();
        return $this->apiResponseMessage(1,__('responseMessage.delete'),200);
    }
    /***
     * @param $request
     *  @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    private function validate_cat($request)
    {
        App::setLocale(get_user_lang($request->header('lang')));
        $input = $request->all();
        $validationMessages = [
            'name_ar.required' => __('responseValidation.name_ar') ,
            'name_en.required' => __('responseValidation.name_en') ,
            'cat_id.required' => __('responseValidation.cat_idRequired') ,
        ];

        $validator = Validator::make($input, [
            'name_ar' => 'required',
            'name_en' => 'required',
            'cat_id' => $request->level ==2 ? 'required' : '' ,
        ], $validationMessages);

        if ($validator->fails()) {
            return $this->apiResponseMessage(0,$validator->messages()->first(), 200);
        }

        if($request->level == 2){
            $cat=Category::where('id',$request->cat_id)->where('level',1)->first();
            if(is_null($cat)){
                return $this->apiResponseMessage(0,__('responseValidation.mainCatExists'),200);
            }
        }
    }
}
