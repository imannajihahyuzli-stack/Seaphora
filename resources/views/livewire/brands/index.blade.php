<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Brands</flux:heading>
        <flux:button variant="primary" wire:click="create">Add brand</flux:button>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="x-circle" :heading="session('error')" />
    @endif

    @if ($showForm)
        <form wire:submit="save" class="max-w-xl space-y-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:input wire:model="form.name" label="Name" />
            <flux:textarea wire:model="form.description" label="Description" rows="3" />

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">Save</flux:button>
                <flux:button type="button" wire:click="cancel">Cancel</flux:button>
            </div>
        </form>
    @endif

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Description</th>
                    <th class="p-3">Products</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($brands as $brand)
                    <tr wire:key="brand-{{ $brand->id }}" class="border-t border-zinc-200 dark:border-zinc-700">
                        <td class="p-3">{{ $brand->name }}</td>
                        <td class="p-3">{{ $brand->description }}</td>
                        <td class="p-3">{{ $brand->products_count }}</td>
                        <td class="space-x-2 p-3 text-right">
                            <flux:button size="sm" wire:click="edit({{ $brand->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger"
                                wire:click="delete({{ $brand->id }})"
                                wire:confirm="Delete this brand?">Delete</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-zinc-500">No brands yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>