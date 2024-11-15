@extends('panel.layout.app')

@section('title', $title)

@push('style')
    <style>
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: #fff;
            color: #000;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        .list-common .item {
            box-shadow: 0px 3px 19px 0px rgba(47, 58, 99, 0.0608);
            border: 1px solid #f2f1f1;
            border-radius: 5px;
        }

    </style>
@endpush
@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <div class="hstack gap-1">
                        <a href="{{ LaravelLocalization::localizeUrl( route('dashboard.index') ) }}"
                           class="page-pretitle flex items-center">
                            <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
                            </svg>
                            {{__('Back to dashboard')}}
                        </a>
                        <a href="{{route('dashboard.admin.chatbot.index')}}" class="page-pretitle flex items-center">
                            / {{__('Chatbot Training')}}
                        </a>
                    </div>
                    <h2 class="page-title mb-2">
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <h2>@lang('Choose a training method')</h2>
                    <p>@lang('Simply select the source and MagicAI will do the rest to train your GPT in seconds.')</p>

                    <div class="bg-blue-100 text-blue-600 rounded-xl mb-4 !p-3 dark:bg-blue-600/20 dark:text-blue-200 mb-2">
                        <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
                        You can deploy your trained chatbot to an existing AI Chat template. Simply navigate to "<a href="{{ route('dashboard.admin.openai.chat.list') }}" class="text-dark" target="_blank">Chat Templates,</a>" select "Edit Template," and assign your chatbot there.
                    </div>

                    <div class="@if(view()->exists('panel.admin.custom.layout.panel.header-top-bar')) bg-[--tblr-bg-surface] px-8 py-10 rounded-[--tblr-border-radius]  @endif">
                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <ul class="nav nav-pills mb-3 d-flex justify-between p-1 rounded" id="pills-tab" role="tablist" style="background: rgba(217, 217, 217, 0.3)">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active rounded" id="web-site-tab" data-bs-toggle="pill" data-bs-target="#web-site" type="button" role="tab" aria-controls="web-site" aria-selected="true">@lang('Website')</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded" id="pdf-tab" data-bs-toggle="pill" data-bs-target="#pdf" type="button" role="tab" aria-controls="pdf" aria-selected="false">@lang('PDF')</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded" id="text-tab" data-bs-toggle="pill" data-bs-target="#text" type="button" role="tab" aria-controls="text" aria-selected="false">@lang('Text')</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded" id="qa-tab" data-bs-toggle="pill" data-bs-target="#qa" type="button" role="tab" aria-controls="qa" aria-selected="false">@lang('Q&A')</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    @include('panel.admin.chatbot.particles.web-site-tab')
                                    @include('panel.admin.chatbot.particles.pdf-tab')
                                    @include('panel.admin.chatbot.particles.text-tab')
                                    @include('panel.admin.chatbot.particles.qa-tab')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/js/panel/admin.chatbot.js"></script>
    <script>

    </script>
@endsection
