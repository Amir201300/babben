<?php
/**
 * @return float|int
 */
function cart_price(){
    $user = Auth::user();
    $price = 0;
    foreach ($user->my_cart as $row) {
        $price += $row->price * $row->pivot->quantity;
    }
    return $price;
}

/**
 * @param $code
 * @param $check
 * @return array
 */
function handleCode($code,$check){
    $user=Auth::user();
    if($check == 2){
        $user->code_id=null;
        $user->user_discount_value=0;
        $user->user_discount_type=null;
        $user->save();
    }else{
        $user->code_id=$code->id;
        $user->user_discount_value=$code->value;
        $user->user_discount_type=$code->value_type;
        $user->save();
        return ['code_id'=>$code->id , 'discount_value'=>$code->value,'discount_type'=>$code->value_type];
    }
}

/**
 * @param $product_price
 * @param $discount
 * @param $discount_type
 * @return float|int
 */
function calDiscount_price($product_price,$discount,$discount_type)
{
    $discount_amount= $discount;
    if($discount_type==2) {
        $discount_amount = ($product_price * $discount / 100);
    }
    return $discount_amount;
}

function getNotFountProduct($type){
    $name="";
    if($type == 1)
        $name = get_user_lang() =='en'  ? 'call me' : 'كلمني';
    if($type == 2)
        $name = get_user_lang() =='en'  ? 'cancel this product' : 'الغيه من الطلب';
    if($type == 3)
        $name = get_user_lang() =='en'  ? 'get similar' : 'جيب زيه';
    return $name;

}

/**
 * @return array
 */
function cartInfo(){
    $user=Auth::user();
    return [
      'cartPrice'=>cart_price(),
      'cartCount'=>$user->my_cart->count(),
    ];
}