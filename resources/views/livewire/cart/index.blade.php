<div class="space-y-6">
    <flux:heading size="xl">My Cart</flux:heading>

    @if (session('status'))
        <x-auto-alert :message="session('status')" />
    @endif

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="p-3">Product</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Quantity</th>
                    <th class="p-3">Subtotal</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr wire:key="cart-item-{{ $item->id }}" class="border-t border-zinc-200 dark:border-zinc-700">
                        <td class="p-3">{{ $item->product->name }}</td>
                        <td class="p-3">RM {{ number_format($item->product->price, 2) }}</td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <flux:button size="sm" wire:click="decrement({{ $item->id }})">-</flux:button>
                                <span>{{ $item->quantity }}</span>
                                <flux:button size="sm" wire:click="increment({{ $item->id }})">+</flux:button>
                            </div>
                        </td>
                        <td class="p-3">RM {{ number_format($item->quantity * $item->product->price, 2) }}</td>
                        <td class="p-3 text-right">
                            <x-confirm-modal
                                label="Remove"
                                title="Remove item?"
                                :message="'Remove '.$item->product->name.' from your cart?'"
                                :action="'remove('.$item->id.')'"
                                confirm-label="Yes, remove" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-zinc-500">Your cart is empty.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-end gap-4">
        <div class="text-lg font-semibold">
            Total: RM {{ number_format($total, 2) }}
        </div>

        @if ($items->isNotEmpty())
            <flux:button variant="primary" href="{{ route('checkout') }}" wire:navigate>Checkout</flux:button>
        @endif
    </div>
</div>