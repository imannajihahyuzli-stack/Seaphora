<?php

namespace App\Actions;

use App\Models\Product;

class UpdateProductAction
{
    /** @param array<string, mixed> $data */
    public function handle(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }
}
