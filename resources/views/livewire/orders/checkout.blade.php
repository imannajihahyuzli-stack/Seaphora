<div class="max-w-3xl space-y-6">
    <flux:heading size="xl">Checkout</flux:heading>

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle" :heading="session('error')" />
    @endif

    @if ($items->isEmpty())
        <flux:callout icon="shopping-cart" heading="Your cart is empty.">
            <x-slot name="actions">
                <flux:button href="{{ route('products.index') }}" wire:navigate>Browse products</flux:button>
            </x-slot>
        </flux:callout>
    @else
        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="p-3">Product</th>
                        <th class="p-3">Qty</th>
                        <th class="p-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr wire:key="checkout-item-{{ $item->id }}" class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="p-3">{{ $item->product->name }}</td>
                            <td class="p-3">{{ $item->quantity }}</td>
                            <td class="p-3">RM {{ number_format($item->quantity * $item->product->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-right text-lg font-semibold">
            Total: RM {{ number_format($total, 2) }}
        </div>

        <form wire:submit="placeOrder" class="space-y-5">
            <flux:textarea wire:model="form.shipping_address" label="Shipping address" rows="3" />

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">Place order</flux:button>
                <flux:button href="{{ route('cart.index') }}" wire:navigate>Back to cart</flux:button>
            </div>
        </form>
    @endif
</div>