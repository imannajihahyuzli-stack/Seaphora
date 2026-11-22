<?php

namespace App\Livewire\Products;

use App\Actions\DeleteProductAction;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $brand = '';

    #[Url]
    public string $category = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedBrand(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, DeleteProductAction $action): void
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        $result = $action->handle($product);

        session()->flash('status', "Product {$result}.");
    }

    public function render()
    {
        $products = Product::with(['brand', 'category'])
            ->when(! auth()->user()->isStaff(), fn ($q) => $q->where('is_active', true))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->brand, fn ($q) => $q->where('brand_id', $this->brand))
            ->when($this->category, fn ($q) => $q->where('category_id', $this->category))
            ->latest()
            ->paginate(10);

        return view('livewire.products.index', [
            'products' => $products,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
