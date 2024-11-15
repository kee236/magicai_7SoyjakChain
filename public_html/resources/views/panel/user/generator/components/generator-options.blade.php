<div
    class="lqd-generator-options !px-5 !pb-8"
    id="lqd-generator-options"
>
    <div class="card mb-[25px]">
        <div class="card-body">
            <h5 class="mb-3 text-[14px] font-normal">{{ __('Remaining Credits') }}</h5>
            <div class="progress progress-separated mb-2">
                @if ((int) $auth->remaining_words + (int) $auth->remaining_images != 0)
                    <div
                        class="progress-bar bg-primary shrink-0 grow-0 basis-auto"
                        role="progressbar"
                        style="width: {{ ((int) $auth->remaining_words / ((int) $auth->remaining_words + (int) $auth->remaining_images)) * 100 }}%"
                        aria-label="{{ __('Text') }}"
                    ></div>
                @endif
                @if ((int) $auth->remaining_words + (int) $auth->remaining_images != 0)
                    <div
                        class="progress-bar shrink-0 grow-0 basis-auto bg-[#9E9EFF]"
                        role="progressbar"
                        style="width: {{ ((int) $auth->remaining_images / ((int) $auth->remaining_words + (int) $auth->remaining_images)) * 100 }}%"
                        aria-label="{{ __('Images') }}"
                    ></div>
                @endif
            </div>
            <div class="flex flex-wrap justify-between">
                <div class="d-flex align-items-center">
                    <span class="legend bg-primary !me-2 rounded-full"></span>
                    <span>{{ __('Words') }}</span>
                    <span class="text-heading ms-2 font-medium">
                        @if ($auth->remaining_words == -1)
                            {{ __('Unlimited') }}
                        @else
                            {{ number_format((int) $auth->remaining_words) }}
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="legend !me-2 rounded-full bg-[#9E9EFF]"></span>
                    <span>{{ __('Images') }}</span>
                    <span class="text-heading ms-2 font-medium">
                        @if ($auth->remaining_images == -1)
                            {{ __('Unlimited') }}
                        @else
                            {{ number_format((int) $auth->remaining_images) }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
    <form
        class="row"
        id="openai_generator_form"
    >
        @foreach (json_decode($openai->questions) as $question)
            <div class="col-xs-12 @if ($question->type == 'rss_feed') relative @endif mb-3">
                @php
                    $placeholder = isset($question->description) && !empty($question->description) ? __($question->description) : __($question->question);
                @endphp
                @if ($question->type == 'text')
                    <label class="form-label">{{ __($question->question) }}</label>
                    <input
                        class="form-control"
                        id="{{ $question->name }}"
                        type="{{ $question->type }}"
                        name="{{ $question->name }}"
                        maxlength="{{ $setting->openai_max_input_length }}"
                        placeholder="{{ __($placeholder) }}"
                        required="required"
                    >
                @elseif($question->type == 'textarea')
                    <label class="form-label">{{ __($question->question) }}</label>
                    <textarea
                        class="form-control"
                        id="{{ $question->name }}"
                        name="{{ $question->name }}"
                        rows="12"
                        placeholder="{{ __($placeholder) }}"
                        maxlength="{{ $setting->openai_max_input_length }}"
                        required="required"
                    ></textarea>
                @elseif($question->type == 'select')
                    <div class="form-label">{{ __($question->question) }}</div>
                    <select
                        class="form-select"
                        id="{{ $question->name }}"
                        name="{{ $question->name }}"
                        required="required"
                    >
                        {!! $question->select !!}
                    </select>
                @elseif($question->type == 'rss_feed')
                    <label class="form-label">{{ __($question->question) }}</label>
                    <input
                        class="form-control"
                        id="{{ $question->name }}"
                        type="{{ $question->type }}"
                        name="{{ $question->name }}"
                        maxlength="{{ $setting->openai_max_input_length }}"
                        placeholder="{{ __($placeholder) }}"
                        required="required"
                    >
                    <div
                        class="fetch-rss absolute bottom-[1px] right-0 cursor-pointer border-b-0 border-l border-r-0 border-t-0 border-solid border-[var(--tblr-border-color)] pl-4 pr-6 leading-[45px] hover:scale-95">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                stroke="none"
                                d="M0 0h24v24H0z"
                                fill="none"
                            />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg>
                        {{ __('Fetch RSS') }}
                    </div>
                @elseif($question->type == 'url')
                    <label class="form-label">{{ __($question->question) }}</label>
                    <input
                        class="form-control"
                        id="{{ $question->name }}"
                        type="{{ $question->type }}"
                        name="{{ $question->name }}"
                        maxlength="{{ $setting->openai_max_input_length }}"
                        placeholder="{{ __($placeholder) }}"
                        required="required"
                    >
                @endif
            </div>
        @endforeach

        @if ($openai->type == 'youtube')
            <div class="col-xs-12 mb-3">
                <div class="form-label">{{ __('Action') }}</div>
                <select
                    class="form-select"
                    id="youtube_action"
                    name="youtube_action"
                    required="required"
                >
                    <option value="blog">{{ __('Prepare a Blog Post') }}</option>
                    <option value="short">{{ __('Explain the Main Idea') }}</option>
                    <option value="list">{{ __('Create a List') }}</option>
                    <option value="tldr">{{ __('Create TLDR') }}</option>
                    <option value="prons_cons">{{ __('Prepare Pros and Cons') }}</option>
                </select>
            </div>
            <div class="col-xs-12 mb-3">
                <label class="form-label">{{ __('Language') }}</label>
                <select
                    class="form-select"
                    id="language"
                    type="text"
                    name="language"
                    required
                >
                    @include('panel.user.openai.components.countries')
                </select>
            </div>
        @endif

        @if ($openai->type == 'text' || $openai->type == 'rss')
            <div class="col-xs-12 mb-3">
                <label class="form-label">{{ __('Language') }}</label>
                <select
                    class="form-select"
                    id="language"
                    type="text"
                    name="language"
                    required
                >
                    @include('panel.user.openai.components.countries')
                </select>
            </div>

            <div class="col-xs-12 col-md-6 mb-3">
                <label class="form-label">{{ __('Maximum Length') }}</label>
                <input
                    class="form-control"
                    id="maximum_length"
                    type="number"
                    name="maximum_length"
                    max="{{ $setting->openai_max_output_length }}"
                    value="200"
                    placeholder="{{ __('Maximum character length of text') }}"
                    required
                >
            </div>

            <div class="col-xs-12 col-md-6 mb-3">
                <label class="form-label">{{ __('Number of Results') }}</label>
                <input
                    class="form-control"
                    id="number_of_results"
                    type="number"
                    name="number_of_results"
                    value="1"
                    placeholder="{{ __('Number of results') }}"
                    required
                >
            </div>

            <div class="col-xs-12 col-md-6 mb-3">
                <label class="form-label">{{ __('Creativity') }}</label>
                <select
                    class="form-select"
                    id="creativity"
                    type="text"
                    name="creativity"
                    required
                >
                    <option
                        value="0.25"
                        {{ $setting->openai_default_creativity == 0.25 ? 'selected' : '' }}
                    >
                        {{ __('Economic') }}</option>
                    <option
                        value="0.5"
                        {{ $setting->openai_default_creativity == 0.5 ? 'selected' : '' }}
                    >
                        {{ __('Average') }}</option>
                    <option
                        value="0.75"
                        {{ $setting->openai_default_creativity == 0.75 ? 'selected' : '' }}
                    >
                        {{ __('Good') }}</option>
                    <option
                        value="1"
                        {{ $setting->openai_default_creativity == 1 ? 'selected' : '' }}
                    >
                        {{ __('Premium') }}</option>
                </select>
            </div>

            <div class="col-xs-12 col-md-6 mb-3">
                <div class="form-label">{{ __('Tone of Voice') }}</div>
                <select
                    class="form-select"
                    id="tone_of_voice"
                    name="tone_of_voice"
                    required
                >
                    <option
                        value="Professional"
                        {{ $setting->openai_default_tone_of_voice == 'Professional' ? 'selected' : null }}
                    >
                        {{ __('Professional') }}</option>
                    <option
                        value="Funny"
                        {{ $setting->opena_default_tone_of_voice == 'Funny' ? 'selected' : null }}
                    >
                        {{ __('Funny') }}</option>
                    <option
                        value="Casual"
                        {{ $setting->openai_default_tone_of_voice == 'Casual' ? 'selected' : null }}
                    >
                        {{ __('Casual') }}</option>
                    <option
                        value="Excited"
                        {{ $setting->openai_default_tone_of_voice == 'Excited' ? 'selected' : null }}
                    >
                        {{ __('Excited') }}</option>
                    <option
                        value="Witty"
                        {{ $setting->openai_default_tone_of_voice == 'Witty' ? 'selected' : null }}
                    >
                        {{ __('Witty') }}</option>
                    <option
                        value="Sarcastic"
                        {{ $setting->openai_default_tone_of_voice == 'Sarcastic' ? 'selected' : null }}
                    >
                        {{ __('Sarcastic') }}</option>
                    <option
                        value="Feminine"
                        {{ $setting->openai_default_tone_of_voice == 'Feminine' ? 'selected' : null }}
                    >
                        {{ __('Feminine') }}</option>
                    <option
                        value="Masculine"
                        {{ $setting->openai_default_tone_of_voice == 'Masculine' ? 'selected' : null }}
                    >
                        {{ __('Masculine') }}</option>
                    <option
                        value="Bold"
                        {{ $setting->openai_default_tone_of_voice == 'Bold' ? 'selected' : null }}
                    >
                        {{ __('Bold') }}</option>
                    <option
                        value="Dramatic"
                        {{ $setting->openai_default_tone_of_voice == 'Dramatic' ? 'selected' : null }}
                    >
                        {{ __('Dramatic') }}</option>
                    <option
                        value="Grumpy"
                        {{ $setting->openai_default_tone_of_voice == 'Grumpy' ? 'selected' : null }}
                    >
                        {{ __('Grumpy') }}</option>
                    <option
                        value="Secretive"
                        {{ $setting->openai_default_tone_of_voice == 'Secretive' ? 'selected' : null }}
                    >
                        {{ __('Secretive') }}</option>
                </select>
            </div>
        @endif
        <input
            id="openai_type"
            hidden
            value="{{ $openai->type }}"
        >
        <input
            id="openai_slug"
            hidden
            value="{{ $openai->slug }}"
        >
        <input
            id="openai_id"
            hidden
            value="{{ $openai->id }}"
        >
        <input
            id="openai_questions"
            hidden
            value="{{ $openai->questions }}"
        >

        <div class="col-xs-12 mt-[10px]">
            <button
                class="btn btn-primary w-100 group flex items-center py-[0.75em]"
                id="openai_generator_button"
                type="button"
            >
                <span class="hidden group-[.lqd-form-submitting]:inline-flex">{{ __('Please wait...') }}</span>
                <span class="group-[.lqd-form-submitting]:hidden">{{ __('Generate') }}</span>
            </button>
        </div>

    </form>
</div>
