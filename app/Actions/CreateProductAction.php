<?php

namespace App\Actions;

use App\Models\Product;

class CreateProductAction
{
    /** @param array<string, mixed> $data */
    public function handle(array $data): Product
    {
        return Product::create($data);
    }
}
