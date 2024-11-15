@aware([
    'action',
    'icon',
    'stepper',
])
@props([
    'action' => null,
    'icon' => null,
    'stepper' => null,
    'force' => false,
])

@if($action || $stepper || $icon || $force)
    <div class="relative">
        {{ $slot }}

        {{-- Icon --}}
        @if ($icon)
            {!! $icon !!}
        @endif

        {{-- Action --}}
        @if ($action)
            <div class="absolute inset-y-0 end-0 border-s">
                {{ $action }}
            </div>
        @endif

        {{-- Stepper --}}
        @if ($stepper)
            <button
                    class="lqd-stepper-btn absolute start-0 top-0 inline-flex aspect-square h-full w-10 items-center justify-center rounded-input transition-colors hover:bg-heading-foreground hover:text-heading-background"
                    type="button"
                    @click="value = Math.max(min, value - step)"
            >
                <x-tabler-minus
                        class="w-4"
                        stroke-width="1.5"
                />
            </button>
            <button
                    class="lqd-stepper-btn absolute end-0 top-0 inline-flex aspect-square h-full w-10 items-center justify-center rounded-input transition-colors hover:bg-heading-foreground hover:text-heading-background"
                    type="button"
                    @click="value = Math.min(max, value + step)"
            >
                <x-tabler-plus
                        class="w-4"
                        stroke-width="1.5"
                />
            </button>
        @endif
    </div>
@else
    {{ $slot }}
@endif