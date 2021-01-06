<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name_ar', 'name_en', 'desc_ar','desc_en','icon' ,'status' , 'quantity' ,'is_offer' ,'offer_amount',
        'price_after_offer', 'status' ,'cat_id','price','is_fire'
    ];
    /**
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeStatus($query,$status)
    {
        return $query->where('status', $status);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cat(){
        return $this->belongsTo(Category::class,'cat_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(){
        return $this->hasMany(ProductImage::class,'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user_favorite()
    {
        return $this->belongsToMany(User::class,'whishlists','product_id','user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cart()
    {
        return $this->belongsToMany(User::class,'carts','product_id','client_id')
            ->where('is_order',0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function CartOrder()
    {
        return $this->belongsToMany(User::class,'carts','product_id','client_id')
            ->where('is_order',1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class,'carts','product_id','order_id');
    }
}
