<?php

namespace App\Actions;

use App\Models\Order;
use DomainException;
use Illuminate\Support\Facades\DB;

class UpdateOrderStatusAction
{
    /** Which status can follow which */
    public const TRANSITIONS = [
        'pending' => ['paid', 'cancelled'],
        'paid' => ['shipped', 'cancelled'],
        'shipped' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function handle(Order $order, string $newStatus): Order
    {
        $allowed = self::TRANSITIONS[$order->status] ?? [];

        if (! in_array($newStatus, $allowed, true)) {
            throw new DomainException("An order cannot go from {$order->status} to {$newStatus}.");
        }

        return DB::transaction(function () use ($order, $newStatus) {
            // A cancelled order puts its products back into stock
            if ($newStatus === 'cancelled') {
                foreach ($order->items()->with('product')->get() as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $newStatus]);

            return $order;
        });
    }
}
