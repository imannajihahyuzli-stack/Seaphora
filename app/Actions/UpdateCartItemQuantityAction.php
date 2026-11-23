<?php

namespace App\Actions;

use App\Models\CartItem;

class UpdateCartItemQuantityAction
{
    public function handle(CartItem $item, int $quantity): ?CartItem
    {
        if ($quantity < 1) {
            $item->delete();

            return null;
        }

        $item->update([
            'quantity' => min($quantity, $item->product->stock),
        ]);

        return $item;
    }
}