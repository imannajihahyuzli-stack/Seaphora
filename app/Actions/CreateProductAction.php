<?php

namespace App\Actions;

use App\Models\Product;

class CreateProductAction
{
    public function handle(array $data): Product
    {
        return Product::create($data);
    }
}
