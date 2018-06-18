<?php

namespace App\Http\Resources;

class Cart extends \Illuminate\Http\Resources\Json\JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total' => $this->total(),
            'items' => Item::collection($this->items)
        ];
    }
}
