<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Products()
    {
        return $this->hasMany(Product::class, 'cat_id')->where('status', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductsInCart()
    {
        $user=Auth::user();
        return $this->hasMany(Product::class, 'cat_id')->where('status', 1)
            ->whereHas('CartOrder',function($q)use($user){
                $q->where('client_id',$user->id);
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductsInWhist(){
        $user=Auth::user();
        return $this->hasMany(Product::class, 'cat_id')->where('status', 1)
            ->whereHas('user_favorite',function($q)use($user){
                $q->where('user_id',$user->id);
            });
    }
}
