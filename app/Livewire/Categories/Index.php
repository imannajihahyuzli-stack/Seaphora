<?php

namespace App\Livewire\Categories;

use App\Actions\CreateCategoryAction;
use App\Actions\DeleteCategoryAction;
use App\Actions\UpdateCategoryAction;
use App\Livewire\Forms\CategoryForm;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public CategoryForm $form;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorize('viewAny', Category::class);
    }

    public function create(): void
    {
        $this->authorize('create', Category::class);

        $this->form->reset();
        $this->form->resetValidation();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $this->form->resetValidation();
        $this->form->setCategory($category);
        $this->showForm = true;
    }

    public function save(CreateCategoryAction $create, UpdateCategoryAction $update): void
    {
        $data = $this->form->validate();

        if ($this->form->categoryId) {
            $category = Category::findOrFail($this->form->categoryId);
            $this->authorize('update', $category);
            $update->handle($category, $data);
        } else {
            $this->authorize('create', Category::class);
            $create->handle($data);
        }

        $this->form->reset();
        $this->showForm = false;

        session()->flash('status', 'Category saved.');
    }

    public function delete(int $id, DeleteCategoryAction $action): void
    {
        $category = Category::findOrFail($id);

        $this->authorize('delete', $category);

        if ($action->handle($category)) {
            session()->flash('status', 'Category deleted.');
        } else {
            session()->flash('error', 'Cannot delete a category that still has products.');
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
        return view('livewire.categories.index', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }
}
