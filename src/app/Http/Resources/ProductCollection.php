<?php

namespace App\Http\Resources;

class ProductCollection extends \Illuminate\Http\Resources\Json\ResourceCollection
{
    public function toArray($request)
    {
        return ['data' => $this->collection];
    }
}
