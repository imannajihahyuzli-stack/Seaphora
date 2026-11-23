<?php

namespace App\Livewire\Orders;

use App\Actions\UpdateOrderStatusAction;
use App\Models\Order;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Order::class);
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    /** Staff and admin: move an order to its next status */
    public function advance(int $id, string $newStatus, UpdateOrderStatusAction $action): void
    {
        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        $this->changeStatus($action, $order, $newStatus);
    }

    /** Customer: cancel own pending order */
    public function cancel(int $id, UpdateOrderStatusAction $action): void
    {
        $order = Order::findOrFail($id);

        $this->authorize('cancel', $order);

        $this->changeStatus($action, $order, 'cancelled');
    }

    private function changeStatus(UpdateOrderStatusAction $action, Order $order, string $newStatus): void
    {
        try {
            $action->handle($order, $newStatus);
            session()->flash('status', "Order #{$order->id} is now {$newStatus}.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): View
    {
        $orders = Order::with(['user', 'items.product'])
            ->when(! auth()->user()->isStaff(), fn ($q) => $q->where('user_id', auth()->id()))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('livewire.orders.index', ['orders' => $orders]);
    }
}
