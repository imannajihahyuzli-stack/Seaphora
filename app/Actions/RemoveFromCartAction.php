<?php

namespace App\Actions;

use App\Models\CartItem;

class RemoveFromCartAction
{
    public function handle(CartItem $item): void
    {
        $item->delete();
    }
}
