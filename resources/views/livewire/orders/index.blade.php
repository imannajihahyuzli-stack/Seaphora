@php
    $cols = auth()->user()->isStaff() ? 6 : 5;
@endphp

<div class="space-y-6">
    <flux:heading size="xl">{{ auth()->user()->isStaff() ? 'Orders' : 'My Orders' }}</flux:heading>

    @if (session('status'))
        <x-auto-alert :message="session('status')" />
    @endif

    @if (session('error'))
        <x-auto-alert type="danger" :message="session('error')" />
    @endif

    <div class="max-w-xs">
        <flux:select wire:model.live="status">
            <option value="">All statuses</option>
            @foreach (['pending', 'paid', 'shipped', 'completed', 'cancelled'] as $s)
                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
            @endforeach
        </flux:select>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="p-3">Order</th>
                    @if (auth()->user()->isStaff())
                        <th class="p-3">Customer</th>
                    @endif
                    <th class="p-3">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Date</th>
                    <th class="p-3"></th>
                </tr>
            </thead>

            @forelse ($orders as $order)
                @php
                    $color = match ($order->status) {
                        'pending' => 'yellow',
                        'paid' => 'blue',
                        'shipped' => 'purple',
                        'completed' => 'green',
                        default => 'zinc',
                    };
                    $next = ['pending' => 'paid', 'paid' => 'shipped', 'shipped' => 'completed'][$order->status] ?? null;
                    $staffCancelAction = "advance({$order->id}, 'cancelled')";
                @endphp

                <tbody wire:key="order-{{ $order->id }}" x-data="{ open: false }">
                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                        <td class="p-3">#{{ $order->id }}</td>
                        @if (auth()->user()->isStaff())
                            <td class="p-3">{{ $order->user->name }}</td>
                        @endif
                        <td class="p-3">RM {{ number_format($order->total, 2) }}</td>
                        <td class="p-3">
                            <flux:badge :color="$color">{{ ucfirst($order->status) }}</flux:badge>
                        </td>
                        <td class="p-3">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="p-3 text-right">
                            <flux:button size="sm" x-on:click="open = !open">
                                <span x-text="open ? 'Hide' : 'Details'"></span>
                            </flux:button>

                            @can('update', $order)
                                @if ($next)
                                    <flux:button size="sm" variant="primary"
                                        wire:click="advance({{ $order->id }}, '{{ $next }}')">
                                        Mark as {{ $next }}
                                    </flux:button>
                                @endif
                                @if (in_array($order->status, ['pending', 'paid']))
                                    <x-confirm-modal
                                        label="Cancel"
                                        title="Cancel this order?"
                                        message="The products will go back into stock."
                                        :action="$staffCancelAction"
                                        confirm-label="Yes, cancel order" />
                                @endif
                            @endcan

                            @can('cancel', $order)
                                <x-confirm-modal
                                    label="Cancel order"
                                    title="Cancel your order?"
                                    message="The products will go back into stock."
                                    :action="'cancel('.$order->id.')'"
                                    confirm-label="Yes, cancel order" />
                            @endcan
                        </td>
                    </tr>

                    <tr x-show="open" style="display: none;" class="bg-zinc-50 dark:bg-zinc-900">
                        <td colspan="{{ $cols }}" class="p-4">
                            <div class="space-y-2">
                                <div class="font-semibold">Items</div>
                                <ul class="list-disc pl-5">
                                    @foreach ($order->items as $i)
                                        <li>{{ $i->quantity }} x {{ $i->product->name }} at RM {{ number_format($i->unit_price, 2) }}</li>
                                    @endforeach
                                </ul>
                                <div><span class="font-semibold">Ship to:</span> {{ $order->shipping_address }}</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="{{ $cols }}" class="p-6 text-center text-zinc-500">No orders found.</td>
                    </tr>
                </tbody>
            @endforelse
        </table>
    </div>

    {{ $orders->links() }}
</div>