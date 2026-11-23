@props(['message', 'type' => 'success'])

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition.opacity.duration.500ms
>
    <flux:callout
        :variant="$type"
        :icon="$type === 'success' ? 'check-circle' : 'exclamation-circle'"
        :heading="$message"
    />
</div>