<?php

namespace App\Actions;

use App\Models\Category;

class DeleteCategoryAction
{
    /** Returns false when the category still has products. */
    public function handle(Category $category): bool
    {
        if ($category->products()->exists()) {
            return false;
        }

        $category->delete();

        return true;
    }
}
