<?php

namespace App\Actions;

use App\Models\Brand;

class DeleteBrandAction
{
    /** Returns false when the brand still has products. */
    public function handle(Brand $brand): bool
    {
        if ($brand->products()->exists()) {
            return false;
        }

        $brand->delete();

        return true;
    }
}
