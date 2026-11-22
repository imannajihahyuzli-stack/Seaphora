<?php

namespace App\Actions;

use App\Models\Product;

class DeleteProductAction
{
    public function handle(Product $product): string
    {
        // Ordered products can't be deleted (onDelete restrict), so hide them instead.
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);

            return 'deactivated';
        }

        $product->delete();

        return 'deleted';
    }
}
