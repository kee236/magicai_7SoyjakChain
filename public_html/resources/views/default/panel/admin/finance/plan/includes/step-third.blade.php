@php
    use App\Models\OpenAIGenerator;
    use Illuminate\Support\Str;
@endphp

@php

    $openAIGenerators = OpenAIGenerator::query()
        ->where('active', true)
        ->get()
        ->filter(function ($item) {
            return !Str::startsWith($item->slug, 'ai_');
        })
        ->groupBy('filters')
        ->filter(function ($item, $key) {
            return $key;
        });
@endphp

<div class="space-y-11">
    @foreach ($openAIGenerators as $step => $items)
        <div>
            <x-form-step
                class="mt-5"
                step="{{$loop->index+1}}"
                label="{{ __(ucfirst($step)) }}"
            />
            <div class="row">
                @foreach ($items as $generator)
                    <div class="col-6 mt-4">
                        <x-forms.input
                            class:container="h-full bg-input-background"
                            class:label="w-full border h-full rounded px-3 py-4 hover:bg-foreground/5 transition-colors"
                            class="checked-item"
                            id="flex_check_{{ $generator['slug'] }}"
                            wire:model="open_ai_items"
                            :checked="in_array($generator['slug'], $open_ai_items)"
                            type="checkbox"
                            value="{{ $generator['slug'] }}"
                            label="{{ $generator['title'] }}"
                            custom
                        />
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <x-button
        class="w-full mt-3"
        type="submit"
        size="lg"
    >
        {{ __('Save') }}
    </x-button>
</div>
