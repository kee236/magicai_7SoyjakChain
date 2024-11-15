@aware(['id', 'label', 'tooltip', 'error'])
@props([
    'id' => null,
    'tooltip' => null,
    'label' => null,
    'error' => null,
])
<label class="form-label" :for="$id('text-input')">
    <span>{{ $label }}</span>

    @if ($tooltip)
        <x-info-tooltip class="block ml-2" text="{{ $tooltip }}" />
    @endif
</label>