@props([
    'action',
    'label' => 'Delete',
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Yes, continue',
])

<div x-data="{ open: false }" class="inline-block">
    <flux:button size="sm" variant="danger" x-on:click="open = true">{{ $label }}</flux:button>

    <div
        x-show="open"
        x-transition.opacity
        x-on:keydown.escape.window="open = false"
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    >
        <div
            x-on:click.outside="open = false"
            class="w-full max-w-md space-y-4 rounded-lg bg-white p-6 text-left shadow-xl dark:bg-zinc-800"
        >
            <flux:heading size="lg">{{ $title }}</flux:heading>
            <flux:text>{{ $message }}</flux:text>

            <div class="flex justify-end gap-3">
                <flux:button x-on:click="open = false">Cancel</flux:button>
                <flux:button variant="danger" x-on:click="$wire.{{ $action }}; open = false">
                    {{ $confirmLabel }}
                </flux:button>
            </div>
        </div>
    </div>
</div>