@php
    $planAiToolsMenu = \App\Services\Common\MenuService::planAiToolsMenu();
@endphp

<div class="space-y-11">

    @if ($planAiToolsMenu)
        <div>
            <x-form-step
                step="1"
                label="{{ __('AI Tools') }}"
            />

            <div class="row">
                @foreach ($planAiToolsMenu as $tool)
                    <div class="col-6 mt-4">
                        <x-forms.input
                            class:container="h-full bg-input-background"
                            class:label="w-full border h-full rounded px-3 py-4 hover:bg-foreground/5 transition-colors"
                            class="checked-item"
                            id="flex_check_{{ $tool['key'] }}"
                            wire:model="plan_ai_tools"
                            :checked="in_array($tool['key'], $plan_ai_tools)"
                            type="checkbox"
                            value="{{ $tool['key'] }}"
                            label="{{ $tool['label'] }}"
                            custom
                        />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div>
        <x-form-step
            class="mt-5"
            step="2"
            label="{{ __('Features') }}"
        />

        <div class="row">
            @foreach (\App\Services\Common\MenuService::planFeatureMenu() as $feature)
                <div class="col-6 mt-4">
                    <x-forms.input
                        class:container="h-full bg-input-background"
                        class:label="w-full border h-full rounded px-3 py-4 hover:bg-foreground/5 transition-colors"
                        class="checked-item"
                        id="flex_check_{{ $feature['key'] }}"
                        wire:model="plan_features"
                        :checked="in_array($feature['key'], $plan_features)"
                        type="checkbox"
                        value="{{ $feature['key'] }}"
                        label="{{ $feature['label'] }}"
                        custom
                    />
                </div>
            @endforeach
        </div>
    </div>

    <x-button
        class="w-full mt-3"
        type="submit"
        size="lg"
    >
        {{ __('Next') }} <x-tabler-arrow-right class="ml-2" />
    </x-button>
</div>
