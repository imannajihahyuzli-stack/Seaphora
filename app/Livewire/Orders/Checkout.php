<?php

namespace App\Livewire\Orders;

use App\Actions\CreateOrderAction;
use App\Livewire\Forms\CheckoutForm;
use App\Models\Cart;
use App\Models\Order;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Checkout extends Component
{
    use AuthorizesRequests;

    public CheckoutForm $form;

    public function mount(): void
    {
        $this->authorize('create', Order::class);
    }

    public function placeOrder(CreateOrderAction $action): void
    {
        $this->authorize('create', Order::class);

        $data = $this->form->validate();

        try {
            $action->handle(auth()->user(), $data['shipping_address']);
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        session()->flash('status', 'Order placed successfully!');

        $this->redirectRoute('orders.index', navigate: true);
    }

    public function render(): View
    {
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        $items = $cart->items ?? collect();

        $total = $items->sum(fn ($item) => $item->quantity * $item->product->price);

        return view('livewire.orders.checkout', [
            'items' => $items,
            'total' => $total,
        ]);
    }
}
