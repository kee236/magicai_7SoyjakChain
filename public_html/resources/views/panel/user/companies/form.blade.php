@extends('panel.layout.app')
@section('title', 'Brand Voice')
@section('additional_css')
    <link href="/assets/select2/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="page-header" xmlns="http://www.w3.org/1999/html">
        <div class="container-xl">
            <div class="items-center flex flex-wrap justify-between">
                <div class="grow">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                        class="flex items-center page-pretitle">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10"
                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="mb-2 page-title">
                        {{ __('Brand Voice') }}
                    </h2>
                    <p class="mt-3">
                        {{ __('Generate AI content exclusive to your brand and eliminate the need for repetitive introductions of your company.') }}
                    </p>
                </div>
                <div class="flex justify-between sm:justify-end items-center grow">
                    <div class="mx-1">
                        <div>
                            <a class="btn me-auto"
                                href="{{ route('dashboard.user.brand.index') }}">{{ __('Manage Voices') }}</a>
                        </div>
                    </div>
                    <div class="mx-1">
                        <div>
                            <a class="btn btn-primary"
                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.brand.edit')) }}">+
                                {{ __('New Company') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-5 mx-auto">
                    <form id="custom_company_form" onsubmit="return companySave({{ $item?->id }});" action=""
                        enctype="multipart/form-data">

                        <div
                            class="flex items-center !p-4 !py-3 !gap-3 rounded-xl text-[17px] bg-[rgba(157,107,221,0.1)] font-semibold mb-10">
                            <span
                                class="inline-flex items-center justify-center !w-6 !h-6 rounded-full bg-[#9D6BDD] text-white text-[15px] font-bold">1</span>
                            {{ __('Company') }}
                        </div>

                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Company Name') }}
                                <x-info-tooltip text="{{ __('Enter the name of your company or organization.') }}" />
                            </label>
                            <input type="text" class="form-control"
                                placeholder="{{ __('The official name of your business entity.') }}" id="c_name"
                                name="c_name" value="{{ $item?->name }}">
                        </div>
                        <div
                            class="form-control border-none p-0 mb-[20px] [&_.select2-selection--multiple]:!border-[--tblr-border-color] [&_.select2-selection--multiple]:!p-[1em_1.23em] [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius]">
                            <label class="form-label">
                                {{ __('Industry') }}
                                <x-info-tooltip
                                    text="{{ __('The field or sector of business activity your company primarily belongs to.') }}" />
                            </label>
                            <select class="form-control select2" name="c_industry" id="c_industry" multiple>§
                                @foreach (explode(',', $item?->industry) ?? [] as $industry)
                                    <option value="{{ $industry }}" {{ $industry !== '' ? 'selected' : '' }}>
                                        {{ $industry }}</option>
                                @endforeach
                            </select>
                            <div
                                class="bg-blue-100 text-blue-600 rounded-xl !p-3 !mt-2 dark:bg-blue-600/20 dark:text-blue-200">
                                <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                    <path d="M12 9h.01"></path>
                                    <path d="M11 12h1v4h1"></path>
                                </svg>
                                {{ __('You can enter as much Industry as you want. Click "Enter" after each Industry.') }}
                            </div>
                        </div>
                        <style>
                            .select-wrapper {
                                position: relative;
                            }

                            .arrow-down {
                                position: absolute;
                                right: 10px;
                                top: 50%;
                                transform: translateY(-50%);
                                width: 0;
                                height: 0;
                                border-left: 5px solid transparent;
                                border-right: 5px solid transparent;
                                border-top: 5px solid #ccc;
                                /* Change the color as needed */
                            }
                        </style>
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Description') }}
                                <x-info-tooltip
                                    text="{{ __('A concise summary describing your company, its mission, and what sets it apart.') }}" />
                            </label>
                            <textarea class="form-control" rows="3" id="c_description" name="c_description"
                                placeholder="{{ __('Provide a brief description of your company.') }}">{{ $item?->description }}</textarea>
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Website') }}
                                <x-info-tooltip
                                    text="{{ __('Please provide the full web address (URL) of your company’s official website.') }}" />
                            </label>
                            <input type="text" placeholder="{{ __('Enter the URL of your company’s website.') }}"
                                class="form-control" id="c_website" name="c_website" value="{{ $item?->website }}">
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Tagline') }}
                                <x-info-tooltip
                                    text="{{ __('A memorable and succinct phrase encapsulating your company’s mission or value proposition.') }}" />
                            </label>
                            <input type="text" placeholder="{{ __('Write a catchy tagline for your company.') }}"
                                class="form-control" id="c_tagline" name="c_tagline" value="{{ $item?->tagline }}">
                        </div>
                        {{-- tone of voice --}}
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Tone of Voice') }}
                                <x-info-tooltip
                                    text="{{ __('Select the tone of voice that best represents your company’s brand.') }}" />
                            </label>
                            <div class="select-wrapper">
                                <select class="form-select" name="tone_of_voice" id="tone_of_voice">
                                    <option value="Professional"
                                        {{ $item?->tone_of_voice == 'Professional' ? 'selected' : null }}>
                                        {{ __('Professional') }}</option>
                                    <option value="Funny" {{ $item?->tone_of_voice == 'Funny' ? 'selected' : null }}>
                                        {{ __('Funny') }}</option>
                                    <option value="Casual" {{ $item?->tone_of_voice == 'Casual' ? 'selected' : null }}>
                                        {{ __('Casual') }}</option>
                                    <option value="Excited" {{ $item?->tone_of_voice == 'Excited' ? 'selected' : null }}>
                                        {{ __('Excited') }}</option>
                                    <option value="Witty" {{ $item?->tone_of_voice == 'Witty' ? 'selected' : null }}>
                                        {{ __('Witty') }}</option>
                                    <option value="Sarcastic"
                                        {{ $item?->tone_of_voice == 'Sarcastic' ? 'selected' : null }}>
                                        {{ __('Sarcastic') }}</option>
                                    <option value="Feminine"
                                        {{ $item?->tone_of_voice == 'Feminine' ? 'selected' : null }}>
                                        {{ __('Feminine') }}</option>
                                    <option value="Masculine"
                                        {{ $item?->tone_of_voice == 'Masculine' ? 'selected' : null }}>
                                        {{ __('Masculine') }}</option>
                                    <option value="Bold" {{ $item?->tone_of_voice == 'Bold' ? 'selected' : null }}>
                                        {{ __('Bold') }}</option>
                                    <option value="Dramatic"
                                        {{ $item?->tone_of_voice == 'Dramatic' ? 'selected' : null }}>
                                        {{ __('Dramatic') }}</option>
                                    <option value="Grumpy" {{ $item?->tone_of_voice == 'Grumpy' ? 'selected' : null }}>
                                        {{ __('Grumpy') }}</option>
                                    <option value="Secretive"
                                        {{ $item?->tone_of_voice == 'Secretive' ? 'selected' : null }}>
                                        {{ __('Secretive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Target Audience') }}
                                <x-info-tooltip
                                    text="{{ __('Describe the primary demographic or audience your company is targeting.') }}" />
                            </label>
                            <textarea class="form-control" rows="3" id="target_audience" name="target_audience"
                                placeholder="{{ __('Describe the primary demographic or audience your company is targeting.') }}">{{ $item?->target_audience }}</textarea>
                        </div>

                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Logo') }}
                                <x-info-tooltip
                                    text="{{ __('Please upload a high-resolution image file (JPEG, PNG, or SVG) of your company logo.') }}" />
                            </label>
                            <input type="file" class="form-control" id="c_logo" name="c_logo"
                                value="{{ $item?->logo }}">
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Brand Color') }}
                                <x-info-tooltip
                                    text="{{ __('Pick a color for for the icon container shape. Color is in HEX format.') }}" />
                            </label>
                            <div class="form-control flex items-center gap-2 relative">
                                <div class="w-[17px] h-[17px] rounded-full overflow-hidden">
                                    <input type="color"
                                        class="w-[150%] h-[150%] relative -start-1/4 -top-1/4 p-0 rounded-full border-none cursor-pointer appearance-none"
                                        id="c_color" name="c_color"
                                        value="{{ $item != null ? $item?->brand_color : '#8fd2d0' }}">
                                </div>
                                <input class="bg-transparent border-none outline-none text-inherit" id="c_color_value"
                                    name="c_color_value" value="{{ $item != null ? $item?->brand_color : '#8fd2d0' }}" />
                            </div>
                        </div>
                        <div
                            class="flex items-center !p-4 !py-3 !gap-3 rounded-xl text-[17px] bg-[rgba(157,107,221,0.1)] font-semibold mb-10">
                            <span
                                class="inline-flex items-center justify-center !w-6 !h-6 rounded-full bg-[#9D6BDD] text-white text-[15px] font-bold">2</span>
                            {{ __('Products or Services') }}
                            <button type="button"
                                class="add-more inline-flex items-center justify-center w-8 h-8 border-none rounded-full !ms-auto bg-[#fff] text-[#000] transition-all duration-300 hover:bg-black hover:text-white hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5l0 14"></path>
                                    <path d="M5 12l14 0"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mb-20">
                            @if ($item != null)
                                <?php $question_i = 1; ?>
                                @foreach ($item->products ?? [] as $product)
                                    <div class="user-input-group input-group control-group flex-col !p-5 mb-[20px] relative border border-solid rounded-[--tblr-border-radius]"
                                        data-input-name="{{ $product?->name }}" data-inputs-id="{{ $question_i }}">
                                        <div class="mb-[20px]">
                                            <label class="form-label">
                                                {{ __('Name') }}
                                                <x-info-tooltip
                                                    text="{{ __('The primary item or service your company provides to its customers.') }}" />
                                            </label>
                                            <input type="text" class="form-control input_name"
                                                placeholder="{{ __('Specify the main product offered by your company.') }}"
                                                value="{{ $product?->name }}">
                                        </div>
                                        <div class="mb-[20px]">
                                            <label class="form-label">
                                                {{ __('Type') }}
                                            </label>
                                            <select class="form-select input_type">
                                                <option value="0" {{ $product?->type == 0 ? 'selected' : null }}>
                                                    {{ __('Product') }}</option>
                                                <option value="1" {{ $product?->type == 1 ? 'selected' : null }}>
                                                    {{ __('Service') }}</option>
                                                <option value="3" {{ $product?->type == 2 ? 'selected' : null }}>
                                                    {{ __('Other') }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-[20px]">
                                            <label class="form-label">
                                                {{ __('Key Features') }}
                                                <x-info-tooltip
                                                    text="{{ __('Describe the key services your company offers to its clients or customers.') }}" />
                                            </label>
                                            <textarea class="form-control input_features" rows="3" name="input_features"
                                                placeholder="{{ __('Explain the features of your Product/Service.') }}">{{ $product?->key_features }}</textarea>
                                        </div>
                                        <button
                                            class="remove-inputs-group inline-flex items-center justify-center !w-6 !h-6 p-0 border-none rounded-md absolute !top-4 !end-5 bg-[transparent] text-red-700 transition-all hover:text-white hover:bg-red-600"
                                            type="button">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                                <path d="M9 12l6 0"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="add-more-placeholder"></div>
                                    <?php $question_i++; ?>
                                @endforeach
                            @else
                                <div class="user-input-group input-group control-group flex-col !p-5 mb-[20px] relative border border-solid rounded-[--tblr-border-radius]"
                                    data-inputs-id="1">
                                    <div class="mb-[20px]">
                                        <label class="form-label">
                                            {{ __('Name') }}
                                            <x-info-tooltip
                                                text="{{ __('The primary item or service your company provides to its customers.') }}" />
                                        </label>
                                        <input required type="text" class="form-control input_name"
                                            placeholder="{{ __('Specify the main product offered by your company.') }}">
                                    </div>
                                    <div class="mb-[20px]">
                                        <label class="form-label">
                                            {{ __('Type') }}
                                        </label>
                                        <select class="form-select input_type">
                                            <option value="0">{{ __('Product') }}</option>
                                            <option value="1">{{ __('Service') }}</option>
                                            <option value="3">{{ __('Other') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-[20px]">
                                        <label class="form-label">
                                            {{ __('Key Features') }}
                                            <x-info-tooltip text="{{ __('') }}" />
                                        </label>
                                        <textarea required class="form-control input_features" rows="3" name="input_features"
                                            placeholder="{{ __('Explain the features of your Product/Service.') }}"></textarea>
                                    </div>
                                    <button
                                        class="remove-inputs-group inline-flex items-center justify-center !w-6 !h-6 p-0 border-none rounded-md absolute !top-4 !end-5 bg-[transparent] text-red-700 transition-all hover:text-white hover:bg-red-600"
                                        type="button" disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path d="M9 12l6 0"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="add-more-placeholder"></div>
                            @endif

                        </div>

                        @if (env('APP_STATUS') == 'Demo')
                            <button type="button"
                                onclick="return toastr.info('This feature is disabled in Demo version.');"
                                class="btn btn-primary !py-3 w-100">
                                {{ __('Save') }}
                            </button>
                        @else
                            <button form="custom_company_form" id="custom_company_button"
                                class="btn btn-primary !py-3 w-100">
                                {{ __('Save') }}
                            </button>
                        @endif


                    </form>
                </div>
            </div>
        </div>
    </div>

    <template id="user-input-company">
        <div class="user-input-group input-group control-group flex-col !p-5 mb-[20px] relative border border-solid rounded-[--tblr-border-radius]"
            data-inputs-id="1">
            <div class="mb-[20px]">
                <label class="form-label">
                    {{ __('Name') }}
                    <x-info-tooltip
                        text="{{ __('The primary item or service your company provides to its customers.') }}" />
                </label>
                <input required type="text" class="form-control input_name"
                    placeholder="{{ __('Specify the main product offered by your company.') }}">
            </div>
            <div class="mb-[20px]">
                <label class="form-label">
                    {{ __('Type') }}
                </label>
                <select class="form-select input_type">
                    <option value="0">{{ __('Product') }}</option>
                    <option value="1">{{ __('Service') }}</option>
                    <option value="2">{{ __('Other') }}</option>
                </select>
            </div>
            <div class="mb-[20px]">
                <label class="form-label">
                    {{ __('Key Features') }}
                    <x-info-tooltip
                        text="{{ __('Describe the key services your company offers to its clients or customers.') }}" />
                </label>
                <textarea required class="form-control input_features" rows="3" name="input_features"
                    placeholder="{{ __('Explain the features of your Product/Service.') }}"></textarea>
            </div>
            <button
                class="remove-inputs-group inline-flex items-center justify-center !w-6 !h-6 p-0 border-none rounded-md absolute !top-4 !end-5 bg-[transparent] text-red-700 transition-all hover:text-white hover:bg-red-600"
                type="button" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                    <path d="M9 12l6 0"></path>
                </svg>
            </button>
        </div>
    </template>
@endsection

@section('script')
    <script src="/assets/select2/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            "use strict";

            if ($.fn.select2) {
                $('.select2').select2({
                    tags: true
                });
            };

            const colorInput = document.querySelector('#c_color');
            const colorValue = document.querySelector('#c_color_value');
            const chatCompletionFillBtn = document.querySelector('.chat-completions-fill-btn');

            colorInput?.addEventListener('input', ev => {
                const input = ev.currentTarget;

                if (colorValue) {
                    colorValue.value = input.value
                };
            });

            colorValue?.addEventListener('input', ev => {
                const input = ev.currentTarget;

                if (colorInput) {
                    colorInput.value = input.value
                };
            });


            const slugify = str =>
                `**${ str.toLowerCase().trim().replace( /[^\w\s-]/g, '' ).replace( /[\s_-]+/g, '-' ).replace( /^-+|-+$/g, '' ) }** `;
            /** @type {HTMLTemplateElement} */
            const userInputTemplate = document.querySelector('#user-input-company');
            const addMorePlaceholder = document.querySelector('.add-more-placeholder');
            let currentInputGroupts = document.querySelectorAll('.user-input-group');
            let lastInputsParent = [...currentInputGroupts].at(-1);
            let lastInpusGroupId = lastInputsParent ? parseInt(lastInputsParent.getAttribute('data-inputs-id'),
                10) : 0;

            $(".add-more").click(function() {
                const button = this;
                const currentInputs = document.querySelectorAll(
                    '.input_name, .input_features, .input_type');
                let anInputIsEmpty = false;
                currentInputs.forEach(input => {
                    const {
                        value
                    } = input;
                    if (!value || value.length === 0 || value.replace(/\s/g, '') === '') {
                        return anInputIsEmpty = true;
                    }
                });
                if (anInputIsEmpty) {
                    return toastr.error('Please fill all fields in User Group Input areas.');
                }
                const newInputsMarkup = userInputTemplate.content.cloneNode(true);
                const newInputsWrapper = newInputsMarkup.firstElementChild;
                newInputsWrapper.dataset.inputsId = lastInpusGroupId + 1;
                addMorePlaceholder.before(newInputsMarkup);
                currentInputGroupts = document.querySelectorAll('.user-input-group');
                lastInputsParent = [...currentInputGroupts].at(-1);
                if (currentInputGroupts.length > 1) {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.removeAttribute(
                        'disabled'));
                }
                lastInpusGroupId++;
                const timeout = setTimeout(() => {
                    newInputsWrapper.querySelector('.input_name').focus();
                    clearTimeout(timeout);
                }, 100);
                return;
            });

            $("body").on("click", ".remove-inputs-group", function() {
                const button = $(this);
                const parent = button.closest('.user-input-group');
                const inputsId = parent.attr('data-inputs-id');

                $(`[data-inputs-id=${ inputsId }]`).remove();

                currentInputGroupts = document.querySelectorAll('.user-input-group');
                lastInputsParent = [...currentInputGroupts].at(-1);

                if (currentInputGroupts.length > 1) {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.removeAttribute(
                        'disabled'));
                } else {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.setAttribute(
                        'disabled', true));
                }
            });

            $('body').on('input', '.input_name', ev => {
                const input = ev.currentTarget;
                const parent = input.closest('.user-input-group');
                const parentId = parent.getAttribute('data-inputs-id');
                const inputName = slugify(input.value);
                let button = document.querySelector(`button[data-inputs-id="${ parentId }"]`);

                if (!button) {
                    button = document.createElement('button');
                    button.className =
                        'bg-[#EFEFEF] text-black cursor-pointer py-[0.15rem] px-[0.5rem] border-none rounded-full transition-all duration-300 hover:bg-black hover:!text-white';
                    button.dataset.inputsId = parentId;
                    button.type = 'button';
                }

                parent.dataset.inputName = inputName;
                button.dataset.inputName = inputName;
                button.innerText = inputName;
            });


        });

        function companySave(company_id) {
            "use strict";
            document.getElementById("custom_company_button").disabled = true;
            document.getElementById("custom_company_button").innerHTML = magicai_localize.please_wait;

            var input_name = [];
            $(".input_name").each(function() {
                input_name.push($(this).val());
            });

            var input_features = [];
            $(".input_features").each(function() {
                input_features.push($(this).val());
            });

            var input_type = [];
            $(".input_type").each(function() {
                input_type.push($(this).val());
            });

            var formData = new FormData();
            formData.append('item_id', company_id);
            formData.append('c_name', $("#c_name").val());
            formData.append('c_industry', $("#c_industry").val());
            formData.append('c_description', $("#c_description").val());
            formData.append('c_website', $("#c_website").val());
            formData.append('c_tagline', $("#c_tagline").val());
            formData.append('c_logo', $('#c_logo').prop('files')[0]);
            formData.append('c_color', $("#c_color").val());
            formData.append('input_name', input_name);
            formData.append('input_features', input_features);
            formData.append('input_type', input_type);
            formData.append('tone_of_voice', $("#tone_of_voice").val());
            formData.append('target_audience', $("#target_audience").val());

            $.ajax({
                type: "post",
                url: "/dashboard/user/brand/save",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    toastr.success('Template Saved Succesfully.');
                    document.getElementById("custom_company_button").disabled = false;
                    document.getElementById("custom_company_button").innerHTML = "Save";
                    setTimeout(function() {
                        window.location.href = "/dashboard/user/brand";
                    }, 200);
                },
                error: function(data) {
                    var err = data.responseJSON.errors;
                    $.each(err, function(index, value) {
                        toastr.error(value);
                    });
                    document.getElementById("custom_company_button").disabled = false;
                    document.getElementById("custom_company_button").innerHTML = "Save";
                }
            });
            return false;
        }
    </script>

@endsection