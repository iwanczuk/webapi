<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public function items()
    {
        return $this->hasMany('App\Item', 'cart_id', 'id');
    }

    public function total()
    {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->product->price;
        }

        return $total;
    }
}
