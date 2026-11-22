<?php

namespace App\Actions;

use App\Models\Brand;

class UpdateBrandAction
{
    /** @param array<string, mixed> $data */
    public function handle(Brand $brand, array $data): Brand
    {
        $brand->update($data);

        return $brand;
    }
}
