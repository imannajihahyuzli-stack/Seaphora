<?php

namespace App\Actions;

use App\Models\Brand;

class CreateBrandAction
{
    /** @param array<string, mixed> $data */
    public function handle(array $data): Brand
    {
        return Brand::create($data);
    }
}
