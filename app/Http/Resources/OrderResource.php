<?php

namespace App\Http\Resources;

use App\Models\Store;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderResource extends JsonResource
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
            'total_price' => number_format((float)$this->total_price, 2, '.', ''),
            'shipping_price' => number_format((float)$this->shipping_price, 2, '.', ''),
            'products_price' =>number_format((float)$this->product_price, 2, '.', '') ,
            'discount_price' => number_format((float)$this->discount_price, 2, '.', ''),
            'status' => (int)$this->status,
            'rate' => (int)$this->rate,
            'comment' => $this->comment,
            'notFoundProduct' => getNotFountProduct($this->notFoundProduct),
            'note' =>$this->note,
            'client' => $this->user ? new UserResource($this->user) : null,
            'payment_method' =>(int) $this->payment_method,
            'discountCode' =>$this->discount_code ? new CodeResource($this->discount_code) : null,
            'address' =>$this->address ? new AddressResource($this->address) : null,
            'products' => ProductResource::collection($this->products),
            'crated_at'=>date('Y-m-d',strtotime($this->created_at)),
        ];
    }
}
