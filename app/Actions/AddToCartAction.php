<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use DomainException;

class AddToCartAction
{
    public function handle(User $user, Product $product, int $quantity = 1): CartItem
    {
        if (! $product->is_active || $product->stock < 1) {
            throw new DomainException('This product is not available.');
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = min(($item->quantity ?? 0) + $quantity, $product->stock);
        $item->save();

        return $item;
    }
}