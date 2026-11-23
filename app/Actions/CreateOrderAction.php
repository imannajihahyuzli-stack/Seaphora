<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function handle(User $user, string $shippingAddress): Order
    {
        // A transaction means: if anything fails, nothing is saved
        return DB::transaction(function () use ($user, $shippingAddress) {
            $cart = Cart::with('items.product')
                ->where('user_id', $user->id)
                ->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw new DomainException('Your cart is empty.');
            }

            $total = 0;

            foreach ($cart->items as $item) {
                $product = $item->product;

                if (! $product->is_active || $product->stock < $item->quantity) {
                    throw new DomainException("Not enough stock for {$product->name}.");
                }

                $total += $item->quantity * $product->price;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();

            return $order;
        });
    }
}
