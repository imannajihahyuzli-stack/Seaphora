<div class="max-w-2xl space-y-6">
    <flux:heading size="xl">{{ $product ? 'Edit product' : 'Add product' }}</flux:heading>

    <form wire:submit="save" class="space-y-5">
        <flux:select wire:model="form.brand_id" label="Brand">
            <option value="">Choose a brand...</option>
            @foreach ($brands as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
        </flux:select>

        <flux:select wire:model="form.category_id" label="Category">
            <option value="">Choose a category...</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </flux:select>

        <flux:input wire:model="form.name" label="Name" />

        <flux:textarea wire:model="form.description" label="Description" rows="4" />

        <div class="grid gap-4 md:grid-cols-2">
            <flux:input wire:model="form.price" type="number" step="0.01" label="Price (RM)" />
            <flux:input wire:model="form.stock" type="number" label="Stock" />
        </div>

        <flux:switch wire:model="form.is_active" label="Active (visible to customers)" />

        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">Save</flux:button>
            <flux:button href="{{ route('products.index') }}" wire:navigate>Cancel</flux:button>
        </div>
    </form>
</div>