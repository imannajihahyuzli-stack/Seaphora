<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CategoryForm extends Form
{
    public ?int $categoryId = null;

    public string $name = '';

    public ?string $slug = '';

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($this->categoryId)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($this->categoryId)],
        ];
    }

    public function setCategory(Category $category): void
    {
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
    }
}
