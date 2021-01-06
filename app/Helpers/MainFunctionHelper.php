<?php

use App\Http\Resources\ProductResource;

/**
 * @return string
 */
function get_baseUrl()
{
    return url('/');
}

/**
 * @return mixed
 */
function get_user_lang()
{
    return Auth::user()->lang;
}

/**
 * @param $type
 * @return string
 */
function getPlaceType($type){
    if($type == 1)
        $name = get_user_lang() =='en'  ? 'store' : 'متجر';
    if($type == 2)
        $name = get_user_lang() =='en'  ? 'office' : 'مكتب';
    if($type == 3)
        $name = get_user_lang() =='en'  ? 'house' : 'بيت';
    if($type == 4)
        $name = get_user_lang() =='en'  ? 'Flat' : 'شقة';
    return $name;

}


function setting(){
    return \App\Models\Setting::first();
}

/**
 * @param $cat
 * @param $type
 * @return mixed
 */
function filterProductsByCat($cat,$type){
    $Products=$cat->Products;
    if($type ==1)
        $Products=$cat->Products->where('is_fire',1);
    if($type ==2)
        $Products=$cat->Products->where('is_offer',1);
    if($type ==3) {
        $Products = $cat->ProductsInCart;
    }
    if($type ==4) {
        $Products = $cat->ProductsInWhist;
    }
    if($type ==5){
        return null;
    }
    return ProductResource::collection($Products->take(8));
}
