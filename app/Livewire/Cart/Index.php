<?php

namespace App\Livewire\Cart;

use App\Actions\RemoveFromCartAction;
use App\Actions\UpdateCartItemQuantityAction;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Cart::class);
    }

    public function increment(int $id, UpdateCartItemQuantityAction $action): void
    {
        $item = $this->ownedItem($id);

        $action->handle($item, $item->quantity + 1);
    }

    public function decrement(int $id, UpdateCartItemQuantityAction $action): void
    {
        $item = $this->ownedItem($id);

        $action->handle($item, $item->quantity - 1);
    }

    public function remove(int $id, RemoveFromCartAction $action): void
    {
        $item = $this->ownedItem($id);

        $action->handle($item);

        session()->flash('status', 'Item removed from cart.');
    }

    private function ownedItem(int $id): CartItem
    {
        $item = CartItem::with(['cart', 'product'])->findOrFail($id);

        // Only the owner of the cart may change it
        $this->authorize('update', $item->cart);

        return $item;
    }

    public function render(): View
    {
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        $items = $cart->items ?? collect();

        $total = $items->sum(fn ($item) => $item->quantity * $item->product->price);

        return view('livewire.cart.index', [
            'items' => $items,
            'total' => $total,
        ]);
    }
}
