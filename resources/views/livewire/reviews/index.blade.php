<div class="max-w-3xl space-y-6">
    <div>
        <flux:button size="sm" variant="ghost" href="{{ route('products.index') }}" wire:navigate>
            Back to products
        </flux:button>
    </div>

    <div class="space-y-1">
        <flux:heading size="xl">{{ $product->name }}</flux:heading>
        <flux:text>{{ $product->brand->name }} | RM {{ number_format($product->price, 2) }}</flux:text>
        <flux:text>
            @if ($reviews->total() > 0)
                Average rating: {{ $average }} / 5 ({{ $reviews->total() }} {{ \Illuminate\Support\Str::plural('review', $reviews->total()) }})
            @else
                No reviews yet.
            @endif
        </flux:text>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="exclamation-circle" :heading="session('error')" />
    @endif

    @if ($canReview)
        <form wire:submit="save" class="space-y-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading size="lg">Write a review</flux:heading>

            <flux:select wire:model="form.rating" label="Rating">
                @foreach ([5, 4, 3, 2, 1] as $n)
                    <option value="{{ $n }}">{{ $n }} {{ $n === 1 ? 'star' : 'stars' }}</option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="form.comment" label="Comment (optional)" rows="3" />

            <flux:button type="submit" variant="primary">Submit review</flux:button>
        </form>
    @elseif (auth()->user()->role === 'customer')
        <flux:callout icon="information-circle"
            heading="You can review this product once you have a completed order for it (one review per product)." />
    @endif

    <div class="space-y-4">
        @foreach ($reviews as $review)
            <div wire:key="review-{{ $review->id }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold">{{ $review->user->name }}</div>
                        <div class="text-sm text-amber-500">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </div>
                    </div>

                    @can('delete', $review)
                        <flux:button size="sm" variant="danger"
                            wire:click="delete({{ $review->id }})"
                            wire:confirm="Delete this review?">
                            Delete
                        </flux:button>
                    @endcan
                </div>

                @if ($review->comment)
                    <p class="mt-2 text-sm">{{ $review->comment }}</p>
                @endif

                <div class="mt-2 text-xs text-zinc-500">{{ $review->created_at->format('d M Y') }}</div>
            </div>
        @endforeach
    </div>

    {{ $reviews->links() }}
</div>