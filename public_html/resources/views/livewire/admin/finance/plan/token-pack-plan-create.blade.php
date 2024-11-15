<div class="row d-flex justify-center">
    <form wire:submit="submit" class="col-md-8 pt-5">

        <x-form-step
                class="mb-4"
                step="1"
                label="{{ __('Global Settings') }}"
        />

        <div class="row">
            <div class="col-6 mt-3">
                <x-forms.input
                        wire:model="name"
                        id="name"
                        class="{{ $errors->has('name') ? 'border-2 border-rose-500' : '' }}"
                        type="text"
                        value="{{ $plan->name }}"
                        label="{{ __('Plan Name') }}"
                        tooltip="{{ __('Plan name') }}"
                        placeholder="{{ __('Plan name') }}"
                />
                @error('name')
                <small class="text-red-700">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-6 mt-3">
                <x-forms.input
                        wire:model="description"
                        id="description"
                        class="{{ $errors->has('description') ? 'border-2 border-rose-500' : '' }}"
                        type="text"
                        value="{{ $plan->description }}"
                        label="{{ __('Plan Description') }}"
                        tooltip="{{ __('Plan description') }}"
                        placeholder="{{ __('Plan description') }}"
                />
                @error('description')
                <small class="text-red-700">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-6 mt-3">
                <x-forms.input
                        class="{{ $errors->has('features') ? 'border-2 border-rose-500' : '' }}"
                        wire:model="features"
                        class:container="w-full mt-4"
                        id="features"
                        type="textarea"
                        name="features"
                        cols="30"
                        rows="10"
                        size="lg"
                        label="{{ __('Plan Features') }}"
                        placeholder="{{ __('Separate with comma') }}"
                >{{ isset($plan) ? $plan->features : null }}</x-forms.input>

                @error('features')
                <small class="text-red-700">{{ $message }}</small>
                @enderror
            </div>


            <div class="col-6 mt-3">

                <x-forms.input
                        class:container="w-full mt-4"
                        id="active"
                        type="checkbox"
                        name="active"
                        wire:model="active"
                        label="{{ __('Active') }}"
                        :checked="$plan?->active == 1"
                        tooltip="{{ __('Plan status') }}"
                        switcher
                />
            </div>
        </div>

        <x-form-step
                class="mb-4 mt-4"
                step="2"
                label="{{ __('Pricing') }}"
        />

        <div class="row">
            <div class="col-12 mt-3">
                <x-forms.input
                        wire:model="price"
                        id="price"
                        class="{{ $errors->has('price') ? 'border-2 border-rose-500' : '' }}"
                        type="number"
                        step="0.01"
                        value="{{ $plan->price }}"
                        label="{{ __('Price') }}"
                        tooltip="{{ __('Price') }}"
                        placeholder="{{ __('Price') }}"
                />
                @error('price')
                <small class="text-red-700">{{ $message }}</small>
                @enderror
            </div>



            <div class="col-6 mt-5">
                <x-forms.input
                        label="{{ trans('Credits (Words)') }}"
                        tooltip="{{ __('Credits (Words)') }}"
                        wire:model="total_words"
                        id="total_words"
                        type="number"
                        name="total_words"
                        size="lg"
                        value="{{ $total_words}}"
                        step="1"
                        stepper
                />

                <x-forms.input
                        wire:model.live="display_imag_count"
                        class:container="mb-4"
                        id="display_imag_count"
                        type="checkbox"
                        name="is_trial"
                        class="mt-2"
                        :checked="$display_imag_count == 1"
                        label="{{ __('Display Remaining Credits (Words)') }}"
                        switcher
                />
            </div>
            <div class="col-6 mt-5">
                <x-forms.input
                        label="{{ trans('Credits (Image)') }}"
                        wire:model="total_images"
                        tooltip="{{ __('Credits (Image)') }}"
                        id="total_images"
                        type="number"
                        name="total_images"
                        size="lg"
                        value="{{ $total_images }}"
                        step="1"
                        stepper
                />
                <x-forms.input
                        wire:model.live="display_word_count"
                        class:container="mb-4"
                        id="display_word_count"
                        type="checkbox"
                        name="is_trial"
                        class="mt-2"
                        :checked="$display_word_count == 1"
                        label="{{ __('Display Remaining Credits (Image)') }}"
                        switcher
                />
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mt-5">
                <x-button
                        class="w-full"
                        type="submit"
                        size="lg"
                >
                    {{ __('Next') }} <x-tabler-arrow-right class="ml-2" />
                </x-button>
            </div>
        </div>
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