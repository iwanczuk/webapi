<?php

namespace App\Http\Resources;

class Item extends \Illuminate\Http\Resources\Json\JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'price' => $this->product->price
        ];
    }
}
