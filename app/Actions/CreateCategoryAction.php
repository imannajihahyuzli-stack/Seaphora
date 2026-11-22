<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Support\Str;

class CreateCategoryAction
{
    /** @param array<string, mixed> $data */
    public function handle(array $data): Category
    {
        $data['slug'] = Str::slug($data['name']);

        return Category::create($data);
    }
}
