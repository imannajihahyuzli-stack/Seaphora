<?php

namespace App\Livewire\Reviews;

use App\Actions\CreateReviewAction;
use App\Actions\DeleteReviewAction;
use App\Livewire\Forms\ReviewForm;
use App\Models\Product;
use App\Models\Review;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    public Product $product;

    public ReviewForm $form;

    public function mount(Product $product): void
    {
        // Customers cannot see hidden products
        abort_if(! $product->is_active && ! auth()->user()->isStaff(), 404);

        $this->authorize('viewAny', Review::class);

        $this->product = $product;
    }

    public function save(CreateReviewAction $action): void
    {
        $this->authorize('create', [Review::class, $this->product]);

        $data = $this->form->validate();

        try {
            $action->handle(
                auth()->user(),
                $this->product,
                (int) $data['rating'],
                $data['comment'] ?? null,
            );
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->form->reset();

        session()->flash('status', 'Thanks for your review!');
    }

    public function delete(int $id, DeleteReviewAction $action): void
    {
        $review = Review::where('product_id', $this->product->id)->findOrFail($id);

        $this->authorize('delete', $review);

        $action->handle($review);

        session()->flash('status', 'Review deleted.');
    }

    public function render(): View
    {
        $reviews = Review::with('user')
            ->where('product_id', $this->product->id)
            ->latest()
            ->paginate(5);

        return view('livewire.reviews.index', [
            'reviews' => $reviews,
            'average' => round((float) $this->product->reviews()->avg('rating'), 1),
            'canReview' => auth()->user()->can('create', [Review::class, $this->product]),
        ]);
    }
}