<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Support\Str;

class UpdateCategoryAction
{
    /** @param array<string, mixed> $data */
    public function handle(Category $category, array $data): Category
    {
        $data['slug'] = Str::slug($data['name']);
        $category->update($data);

        return $category;
    }
}
