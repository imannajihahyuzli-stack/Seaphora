<?php

namespace App\Livewire\Forms;

use App\Models\Brand;
use Illuminate\Validation\Rule;
use Livewire\Form;

class BrandForm extends Form
{
    public ?int $brandId = null;

    public string $name = '';

    public ?string $description = '';

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($this->brandId)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function setBrand(Brand $brand): void
    {
        $this->brandId = $brand->id;
        $this->name = $brand->name;
        $this->description = $brand->description;
    }
}
