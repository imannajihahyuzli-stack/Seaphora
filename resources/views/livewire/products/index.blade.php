<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Products</flux:heading>

        @can('create', App\Models\Product::class)
            <flux:button variant="primary" href="{{ route('products.create') }}" wire:navigate>Add product</flux:button>
        @endcan
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search products..." icon="magnifying-glass" />

        <flux:select wire:model.live="brand">
            <option value="">All brands</option>
            @foreach ($brands as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="category">
            <option value="">All categories</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </flux:select>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Stock</th>
                    <th class="p-3">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="product-{{ $product->id }}" class="border-t border-zinc-200 dark:border-zinc-700">
                        <td class="p-3">{{ $product->name }}</td>
                        <td class="p-3">{{ $product->brand->name }}</td>
                        <td class="p-3">{{ $product->category->name }}</td>
                        <td class="p-3">RM {{ number_format($product->price, 2) }}</td>
                        <td class="p-3">{{ $product->stock }}</td>
                        <td class="p-3">
                            <flux:badge :color="$product->is_active ? 'green' : 'zinc'">
                                {{ $product->is_active ? 'Active' : 'Hidden' }}
                            </flux:badge>
                        </td>
                        <td class="p-3 text-right">
                            @can('update', $product)
                                <flux:button size="sm" href="{{ route('products.edit', $product) }}" wire:navigate>Edit</flux:button>
                            @endcan
                            @can('delete', $product)
                                <flux:button size="sm" variant="danger"
                                    wire:click="delete({{ $product->id }})"
                                    wire:confirm="Delete this product?">
                                    Delete
                                </flux:button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-zinc-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>