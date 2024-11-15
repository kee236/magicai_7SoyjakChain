@extends('panel.layout.app')
@section('title', __('API Keys'))
@section('additional_css')
    <link href="/assets/select2/select2.min.css" rel="stylesheet" />
    <style>
    </style>
@endsection
@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                        class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10"
                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714
    8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071
    8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643
     1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071
    0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357
    0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0
    5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393
     9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('API Keys') }}
                    </h2>
                    <p class="mt-3">
                        {{ __('Integrate your own personal OpenAI API Key and generate AI content. (Optional)') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <form
                        class="@if (view()->exists('panel.admin.custom.layout.panel.header-top-bar')) bg-[--tblr-bg-surface] px-8 py-10 rounded-[--tblr-border-radius] @endif"
                        id="settings_form" onsubmit="return apiKeysSettingsSave();" enctype="multipart/form-data">
                        <h3 class="mb-[25px] text-[20px]">{{ __('Api Keys Setting') }}</h3>
                        <div class="row">
                            @if ($app_is_demo)
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Api Keys Secret') }}</label>
                                        <input type="text" class="form-control" id="openai_api_secret"
                                            name="openai_api_secret" readonly placeholder="Paste your API Key here. Need help? Learn more about API Keys">
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12">
                                    <div
                                        class="form-control border-none p-0 mb-3
[&_.select2-selection--multiple]:!border-[--tblr-border-color]
 [&_.select2-selection--multiple]:!p-[1em_1.23em]
 [&_.select2-selection--multiple]:!rounded-[--tblr-border-radius]">
                                        <label class="form-label">{{ __('OpenAi API Secret') }}</label>
                                        <select class="form-control select2" name="api_keys" id="api_keys" multiple>
                                            @foreach (explode(',', $list) as $secret)
                                                <option value="{{ $secret }}" selected>{{ $secret }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="bg-blue-100 text-blue-600 rounded-xl !p-3 !mt-2
 dark:bg-blue-600/20 dark:text-blue-200">
                                            <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22"
                                                height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                                <path d="M12 9h.01"></path>
                                                <path d="M11 12h1v4h1"></path>
                                            </svg>
                                            {{ __('You can enter as much API KEY as you want. Click "Enter"
                                            after each api key.') }}
                                        </div>
                                        <div
                                            class="bg-orange-100 text-orange-600 rounded-xl !p-3 !mt-2
dark:bg-orange-600/20 dark:text-orange-200">
                                            <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="20"
                                                height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7
    2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z">
                                                </path>
                                                <path d="M12 9v4"></path>
                                                <path d="M12 17h.01"></path>
                                            </svg>
                                            {{ __('Please ensure that your OpenAI API key is fully functional
                                            and billing defined on your OpenAI account.') }}
                                        </div>
                                        <a href="{{ route('dashboard.admin.settings.openai.test') }}" target="_blank"
                                            class="btn btn-primary w-100 mt-2 mb-2">
                                            {{ __('After Saving Setting, Click Here to Test Your Api Keys') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button form="settings_form" id="settings_button" class="btn btn-primary w-100 mt-5">
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/js/panel/settings.js"></script>
    <script src="/assets/select2/select2.min.js"></script>
@endsection
