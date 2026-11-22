<?php

namespace App\Livewire\Forms;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Livewire\Form;

class ProductForm extends Form
{
    public $brand_id = '';

    public $category_id = '';

    public $name = '';

    public $description = '';

    public $price = '';

    public $stock = 0;

    public $is_active = true;

    // Reuse the rules from the Form Request so they are defined in one place
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
