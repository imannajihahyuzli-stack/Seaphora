<?php

namespace App\Livewire\Forms;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Livewire\Form;

class ProductForm extends Form
{
    public int|string $brand_id = '';

    public int|string $category_id = '';

    public string $name = '';

    public ?string $description = '';

    public string|float $price = '';

    public int|string $stock = 0;

    public bool $is_active = true;

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return (new StoreProductRequest)->rules();
    }

    public function setProduct(Product $product): void
    {
        $this->brand_id = $product->brand_id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->is_active = $product->is_active;
    }
}
