<div class="space-y-11">
    <div>
        <x-form-step
            class="mb-3 mt-3"
            step="1"
            label="{{ __('Global Settings') }}"
        />

        <div class="row">
            <div class="col-6 mt-3">
                <x-forms.input
                    class="{{ $errors->has('name') ? 'border-2 border-rose-500' : '' }}"
                    id="name"
                    wire:model="name"
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
                    class="{{ $errors->has('description') ? 'border-2 border-rose-500' : '' }}"
                    id="description"
                    wire:model="description"
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
                    class:container="w-full mt-4"
                    id="features"
                    wire:model="features"
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
                    id="default_ai_model"
                    wire:model="default_ai_model"
                    name="default_ai_model"
                    required
                    type="select"
                    label="{{ __('Default ai model') }}"
                >
                    @foreach ($models as $aimodel)
                        <option
                            value="{{ $aimodel->key }}"
                            @selected($plan->default_ai_model == $aimodel->key)
                        >
                            {{ $aimodel->title }}
                        </option>
                    @endforeach
                </x-forms.input>
                <x-forms.input
                    class:container="w-full mt-5"
                    id="plan_type"
                    wire:model="plan_type"
                    name="plan_type"
                    required
                    type="select"
                    label="{{ __('Template Access') }}"
                >
                    <option
                        value="All"
                        @selected($plan->plan_type == 'All')
                    >
                        {{ __('All') }}
                    </option>
                    <option
                        value="Premium"
                        @selected($plan->plan_type == 'Premium')
                    >
                        {{ __('Premium') }}
                    </option>
                    <option
                        value="Regular"
                        @selected($plan->plan_type == 'Regular')
                    >
                        {{ __('Regular') }}
                    </option>
                </x-forms.input>
                <x-forms.input
                    class:container="w-full mt-4"
                    id="is_featured"
                    type="checkbox"
                    wire:model="is_featured"
                    label="{{ __('Featured Plan') }}"
                    :checked="$plan?->is_featured == 1"
                    tooltip="{{ __('') }}"
                    switcher
                />
                <x-forms.input
                    class:container="w-full mt-4"
                    id="active"
                    type="checkbox"
                    wire:model="active"
                    label="{{ __('Active') }}"
                    :checked="$plan?->active == 1"
                    tooltip="{{ __('Plan status') }}"
                    switcher
                />
            </div>
        </div>
    </div>

    <div>
        <x-form-step
            class="mb-5 mt-5"
            step="2"
            label="{{ __('Pricing') }}"
        />

        <div class="row">
            <div class="col-6 mt-3">
                <x-forms.input
                    class="border-2 border-red-400 py-5 rounded-xl"
                    id="price"
                    wire:model="price"
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
                <x-alert class="text-red-700 mt-1">
                    <p>
                        @lang('Price is a sensitive field. Changing the price will affect the existing subscriptions. Please be careful.')
                    </p>
                </x-alert>
            </div>
            <div class="col-6 mt-3">
                <x-forms.input
                    class="border-2 border-red-400 rounded-xl"
                    id="frequency"
                    wire:model="frequency"
                    name="frequency"
                    required
                    type="select"
                    size="lg"
                    label="{{ __('Renewal Type') }}"
                    tooltip="{{ __('Renewal Type') }}"
                >
                    <option
                        value="monthly"
                        @selected($plan->frequency == 'monthly')
                    >
                        {{ __('Monthly') }}
                    </option>
                    <option
                        value="yearly"
                        @selected($plan->frequency == 'yearly')
                    >
                        {{ __('Yearly') }}
                    </option>
                    <option
                        value="lifetime_monthly"
                        @selected($plan->frequency == 'lifetime_monthly')
                    >
                        {{ __('Lifetime - Monthly Renewal') }}
                    </option>
                    <option
                        value="lifetime_yearly"
                        @selected($plan->frequency == 'lifetime_yearly')
                    >
                        {{ __('Lifetime - Yearly Renewal') }}
                    </option>
                </x-forms.input>
                <x-alert class="text-red-700 mt-1">
                    <p>
                        @lang('Renewal Type is a sensitive field. Changing the Renewal Type will affect the existing subscriptions. Please be careful.')
                    </p>
                </x-alert>
            </div>

            <div class="col-6 mt-5">
                <x-forms.input
                    class:container="mb-4"
                    id="is_team_plan"
                    wire:model.live="is_team_plan"
                    type="checkbox"
                    name="is_team_plan"
                    :checked="$is_team_plan == 1"
                    label="{{ __('Enable Team Plan') }}"
                    tooltip="{{ __('Enable Team Plan') }}"
                    switcher
                />
                <div
                    class="mb-[20px]"
                    id="countField"
                    style="{{ $is_team_plan ? '' : 'display:none' }}"
                >
                    <x-forms.input
                        id="plan_allow_seat"
                        wire:model="plan_allow_seat"
                        type="number"
                        name="plan_allow_seat"
                        value="{{ $plan->plan_allow_seat }}"
                        label="{{ __('Number of Seats') }}"
                        tooltip="{{ __('Plan name') }}"
                        step="1"
                        stepper
                    />
                </div>
            </div>

            <div class="col-6 mt-5">
                <x-forms.input
                    class:container="mb-4"
                    id="is_trial"
                    wire:model.live="is_trial"
                    type="checkbox"
                    name="is_trial"
                    :checked="$is_trial == 1"
                    label="{{ __('Trial') }}"
                    switcher
                />
                <div
                    class="mb-[20px]"
                    id="countField"
                    style="{{ $is_trial ? '' : 'display:none' }}"
                >
                    <x-forms.input
                        class="border-2 border-red-400 py-5 rounded-xl"
                        id="trial_days"
                        wire:model="trial_days"
                        type="number"
                        name="trial_days"
                        value="{{ $trial_days }}"
                        label="{{ __('Trial days') }}"
                        tooltip="{{ __('trial_days') }}"
                        step="1"
                        stepper
                    />
                    <x-alert class="text-red-700 mt-1">
                        <p>
                            @lang('Trial days is a sensitive field. Changing the Trial days will affect the existing subscriptions. Please be careful.')
                        </p>
                    </x-alert>

                </div>
            </div>

            <div class="col-6 mt-3">
                <x-forms.input
                    id="total_words"
                    label="{{ trans('Credits (Words)') }}"
                    tooltip="{{ __('Credits (Words)') }}"
                    wire:model="total_words"
                    type="number"
                    name="total_words"
                    size="lg"
                    value="{{ $total_words }}"
                    step="1"
                    stepper
                />

                <x-forms.input
                    class:container="mb-4"
                    class="mt-2"
                    id="display_imag_count"
                    wire:model.live="display_imag_count"
                    type="checkbox"
                    name="is_trial"
                    :checked="$display_imag_count == 1"
                    label="{{ __('Display Remaining Credits (Words)') }}"
                    switcher
                />
            </div>
            <div class="col-6 mt-3">
                <x-forms.input
                    id="total_images"
                    label="{{ trans('Credits (Image)') }}"
                    wire:model="total_images"
                    tooltip="{{ __('Credits (Image)') }}"
                    type="number"
                    name="total_images"
                    size="lg"
                    value="{{ $total_images }}"
                    step="1"
                    stepper
                />
                <x-forms.input
                    class:container="mb-4"
                    class="mt-2"
                    id="display_word_count"
                    wire:model.live="display_word_count"
                    type="checkbox"
                    name="is_trial"
                    :checked="$display_word_count == 1"
                    label="{{ __('Display Remaining Credits (Image)') }}"
                    switcher
                />
            </div>
        </div>
    </div>

    <x-button
        class="w-full"
        type="submit"
        size="lg"
    >
        {{ __('Next') }} <x-tabler-arrow-right class="ml-2" />
    </x-button>
</div>
