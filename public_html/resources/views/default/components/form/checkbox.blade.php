@aware(['error', 'label', 'error'])
@props([
    'label' => null,
    'tooltip' => null,
    'checked' => false,
    'error' => null,
])
<label class="form-check form-switch space-x-1" :for="$id('text-input')">
    <input {{ $attributes->class(['form-check-input']) }}
           :id="$id('text-input')"
           type="checkbox"
           @if($error)
               aria-invalid="true" autofocus x-bind:aria-describedby="@if ($id ?? '') {{ $id }}-error @else $id('text-input') + '-error' @endif"
           @endif
           @if($checked) checked @endif
    >

    <span class="form-check-label pl-1">{{ $label }}</span>

    @if ($tooltip)
        <x-info-tooltip class="block" text="{{ $tooltip }}" />
    @endif
</label>