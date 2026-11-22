<?php

namespace App\Livewire\Products;

use App\Actions\CreateProductAction;
use App\Actions\UpdateProductAction;
use App\Livewire\Forms\ProductForm;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public ProductForm $form;

    public ?Product $product = null;

    public function mount(?Product $product = null): void
    {
        // On the create route, Laravel may pass an empty Product, so check ->exists
        if ($product?->exists) {
            $this->authorize('update', $product);
            $this->product = $product;
            $this->form->setProduct($product);
        } else {
            $this->authorize('create', Product::class);
        }
    }

    public function save(CreateProductAction $create, UpdateProductAction $update): void
    {
        $data = $this->form->validate();

        if ($this->product) {
            $this->authorize('update', $this->product);
            $update->handle($this->product, $data);
        } else {
            $this->authorize('create', Product::class);
            $create->handle($data);
        }

        session()->flash('status', 'Product saved.');

        $this->redirectRoute('products.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.products.form', [
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
