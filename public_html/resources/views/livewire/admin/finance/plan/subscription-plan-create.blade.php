@php
    $steps = [
        'first' => trans('Plan Details'),
        'second' => trans('Features'),
        'third' => trans('Templates'),
    ];
@endphp

<div class="row d-flex justify-center py-10">
    <form
        class="col-md-8 pt-5"
        wire:submit="submit"
    >
        <div
            class="row mb-10"
            style="--current-step: {{ $currentStep == 'first' ? 0 : ($currentStep == 'second' ? 1 : 2) }};"
        >
            @foreach ($steps as $key => $label)
                <button
                    data-step="1"
                    type="button"
                    @class([
                        'step group/step flex w-1/3 cursor-pointer flex-wrap items-center justify-center gap-2 rounded-md p-2 text-2xs leading-tight font-medium text-heading-foreground transition-colors hover:bg-foreground/5',
                        'active' => $currentStep == $key,
                    ])
                >
                    <span
                        class="size-6 m-0 flex items-center justify-center rounded-lg text-center transition-colors group-[&.active]/step:bg-primary group-[&.active]/step:text-primary-foreground"
                    >
                        {{ $loop->index + 1 }}
                    </span>
                    <span class="group-[&.active]/step:block">
                        {{ $label }}
                    </span>
                </button>
            @endforeach
            <div class="col-12 mt-3">
                <div class="lqd-progress relative h-2 w-full overflow-hidden rounded-full bg-foreground/5">
                    <div class="lqd-progress-bar absolute h-2 w-[calc((var(--current-step)+1)/3*100%)] rounded-full bg-gradient-to-br from-[#82E2F4] to-[#8A8AED]"></div>
                </div>
            </div>
        </div>

        @includeWhen($currentStep == 'first', 'panel.admin.finance.plan.includes.step-first')
        @includeWhen($currentStep == 'second', 'panel.admin.finance.plan.includes.step-second')
        @includeWhen($currentStep == 'third', 'panel.admin.finance.plan.includes.step-third')
    </form>
</div>

@push('live-script')
    <script>
        // Livewire.on('notify', postId => {
        //
        //     alert('A post was added with the id of: ' + postId);
        //
        // })
    </script>
@endpush
