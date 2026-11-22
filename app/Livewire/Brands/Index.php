<?php

namespace App\Livewire\Brands;

use App\Actions\CreateBrandAction;
use App\Actions\DeleteBrandAction;
use App\Actions\UpdateBrandAction;
use App\Livewire\Forms\BrandForm;
use App\Models\Brand;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public BrandForm $form;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorize('viewAny', Brand::class);
    }

    public function create(): void
    {
        $this->authorize('create', Brand::class);

        $this->form->reset();
        $this->form->resetValidation();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $brand = Brand::findOrFail($id);

        $this->authorize('update', $brand);

        $this->form->resetValidation();
        $this->form->setBrand($brand);
        $this->showForm = true;
    }

    public function save(CreateBrandAction $create, UpdateBrandAction $update): void
    {
        $data = $this->form->validate();

        if ($this->form->brandId) {
            $brand = Brand::findOrFail($this->form->brandId);
            $this->authorize('update', $brand);
            $update->handle($brand, $data);
        } else {
            $this->authorize('create', Brand::class);
            $create->handle($data);
        }

        $this->form->reset();
        $this->showForm = false;

        session()->flash('status', 'Brand saved.');
    }

    public function delete(int $id, DeleteBrandAction $action): void
    {
        $brand = Brand::findOrFail($id);

        $this->authorize('delete', $brand);

        if ($action->handle($brand)) {
            session()->flash('status', 'Brand deleted.');
        } else {
            session()->flash('error', 'Cannot delete a brand that still has products.');
        }
    }

    public function cancel(): void
    {
        $this->form->reset();
        $this->form->resetValidation();
        $this->showForm = false;
    }

    public function render(): View
    {
        return view('livewire.brands.index', [
            'brands' => Brand::withCount('products')->orderBy('name')->get(),
        ]);
    }
}
