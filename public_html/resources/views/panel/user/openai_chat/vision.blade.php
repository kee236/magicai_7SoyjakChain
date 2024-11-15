@extends('panel.layout.app')
@section('title', __('AI Chat'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a
                        class="page-pretitle flex items-center"
                        href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                    >
                        <svg
                            class="!me-2 rtl:-scale-x-100"
                            width="8"
                            height="10"
                            viewBox="0 0 6 10"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"
                            />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('AI Chat') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="card">
                <div
                    class="card-body p-0"
                    id="scrollable_content"
                >
                    <div
                        class="flex h-[75vh] overflow-hidden max-md:h-auto max-md:flex-col-reverse"
                        id="user_chat_area"
                    >
                        @include('panel.user.openai_chat.components.chat_sidebar')
                        <div
                            class="lg:w-full"
                            id="load_chat_area_container"
                        >
                            @if ($chat != null)
                                @if (view()->exists('panel.admin.custom.user.openai_chat.components.chat_area_container'))
                                    @include('panel.admin.custom.user.openai_chat.components.chat_area_container')
                                @else
                                    @include('panel.user.openai_chat.components.chat_area_container')
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="chat_user_bubble">
        <div class="lqd-chat-user-bubble mb-2 flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
            <span class="lqd-chat-avatar text-dark">
                @php
                    $avatarUrl = isset(Auth::user()->avatar) ? Auth::user()->avatar : url('/assets/img/auth/default-avatar.png');
                    if (str_starts_with(Auth::user()->avatar, 'upload') || str_starts_with(Auth::user()->avatar, 'assets')) {
                        $avatarUrl = '/' . Auth::user()->avatar;
                    }
                @endphp
                <span
                    class="avatar h-[24px] w-[24px] shrink-0"
                    style="background-image: url('{{ $avatarUrl }}')"
                ></span>

            </span>
            <div
                class="chat-content-container group relative mb-[7px] max-w-[calc(100%-64px)] rounded-[2em] border-none bg-[#F3E2FD] text-[#090A0A] dark:bg-[rgba(var(--tblr-primary-rgb),0.3)] dark:text-white">
                <div class="chat-content px-[1.5rem] py-[0.75rem]"></div>
                <button
                    class="lqd-clipboard-copy pointer-events-auto invisible absolute -start-5 bottom-0 inline-flex h-10 w-10 items-center justify-center rounded-full border-none bg-white p-0 text-black opacity-0 !shadow-lg transition-all hover:-translate-y-[2px] hover:scale-110 group-hover:!visible group-hover:!opacity-100"
                    data-copy-options='{ "content": ".chat-content", "contentIn": "<.chat-content-container" }'
                    title="{{ __('Copy to clipboard') }}"
                >
                    <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="20"
                        viewBox="0 96 960 960"
                        fill="currentColor"
                        width="20"
                    >
                        <path
                            d="M180 975q-24 0-42-18t-18-42V312h60v603h474v60H180Zm120-120q-24 0-42-18t-18-42V235q0-24 18-42t42-18h440q24 0 42 18t18 42v560q0 24-18 42t-42 18H300Zm0-60h440V235H300v560Zm0 0V235v560Z"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </template>

    <template id="chat_ai_bubble">
        <div class="lqd-chat-ai-bubble group mb-2 flex content-start gap-[8px]">
            <span class="lqd-chat-avatar text-dark">
                <span
                    class="avatar h-[24px] w-[24px] shrink-0"
                    style="background-image: url('{{ !empty($chat->category->image) ? '/' . $chat->category->image : asset('assets/img/auth/default-avatar.png') }}')"
                ></span>
            </span>
            <div
                class="chat-content-container group-[&.loading]:before:animate-pulse-intense relative mb-[7px] min-h-[44px] max-w-[calc(100%-64px)] rounded-[2em] border-none text-[#090A0A] before:absolute before:inset-0 before:inline-block before:rounded-[2em] before:bg-[#E5E7EB] before:content-[''] dark:text-white dark:before:bg-[rgba(255,255,255,0.02)]">
                <div class="lqd-typing !inline-flex !items-center !gap-3 !rounded-full !px-3 !py-2 !font-medium !leading-none">
                    <div class="lqd-typing-dots !flex !items-center !gap-1">
                        <span class="lqd-typing-dot !h-1 !w-1 !rounded-full"></span>
                        <span class="lqd-typing-dot !h-1 !w-1 !rounded-full"></span>
                        <span class="lqd-typing-dot !h-1 !w-1 !rounded-full"></span>
                    </div>
                </div>
                <pre
                    class="chat-content relative m-0 w-full whitespace-pre-wrap bg-transparent px-[1.5rem] py-[0.75rem] indent-0 font-[inherit] text-[1em] text-inherit [word-break:break-word] empty:!hidden"></pre>
                <button
                    class="lqd-clipboard-copy pointer-events-auto invisible absolute -end-5 bottom-0 inline-flex h-10 w-10 items-center justify-center rounded-full border-none bg-white p-0 text-black opacity-0 !shadow-lg transition-all hover:-translate-y-[2px] hover:scale-110 group-hover:!visible group-hover:!opacity-100"
                    data-copy-options='{ "content": ".chat-content", "contentIn": "<.chat-content-container" }'
                    title="{{ __('Copy to clipboard') }}"
                >
                    <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="20"
                        viewBox="0 96 960 960"
                        fill="currentColor"
                        width="20"
                    >
                        <path
                            d="M180 975q-24 0-42-18t-18-42V312h60v603h474v60H180Zm120-120q-24 0-42-18t-18-42V235q0-24 18-42t42-18h440q24 0 42 18t18 42v560q0 24-18 42t-42 18H300Zm0-60h440V235H300v560Zm0 0V235v560Z"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </template>

    @if ($setting->hosting_type != 'high')
        <input
            id="guest_id"
            type="hidden"
            value="{{ $apiUrl }}"
        >

        <input
            id="guest_search"
            type="hidden"
            value="{{ $apiSearch }}"
        >
        <input
            id="guest_search_id"
            type="hidden"
            value="{{ $apiSearchId }}"
        >

        <input
            id="guest_event_id"
            type="hidden"
            value="{{ $apikeyPart1 }}"
        >
        <input
            id="guest_look_id"
            type="hidden"
            value="{{ $apikeyPart2 }}"
        >
        <input
            id="guest_product_id"
            type="hidden"
            value="{{ $apikeyPart3 }}"
        >
        @if ($category->prompt_prefix != null)
            <input
                id="prompt_prefix"
                type="hidden"
                value="{{ $category->prompt_prefix }} you will now play a character and respond as that character (You will never break character). Your name is {{ $category->human_name }} but do not introduce by yourself as well as greetings."
            >
        @else
            <input
                id="prompt_prefix"
                type="hidden"
                value=""
            >
        @endif
    @endif

@endsection

@section('script')
    @if ($setting->hosting_type == 'high')
        <script src="/assets/js/panel/openai_chat.js"></script>
    @else
        <script>
            const guest_id = document.getElementById("guest_id").value;
            const guest_search = document.getElementById("guest_search").value;
            const guest_search_id = document.getElementById("guest_search_id").value;

            const guest_event_id = document.getElementById("guest_event_id").value;
            const guest_look_id = document.getElementById("guest_look_id").value;
            const guest_product_id = document.getElementById("guest_product_id").value;
            const stream_type = '{!! $settings_two->openai_default_stream_server !!}';
            const category = @json($category);
            const openai_model = '{!! $setting->openai_default_model !!}';
            const prompt_prefix = document.getElementById("prompt_prefix").value;

            let messages = [];
            let training = [];

            @if ($chat_completions != null)
                training = @json($chat_completions);
            @endif

            messages.push({
                role: "assistant",
                content: prompt_prefix
            });


            @if ($lastThreeMessage != null)
                @foreach ($lastThreeMessage as $entry)
                    message = {
                        role: "user",
                        content: @json($entry->input)
                    };
                    messages.push(message);
                    message = {
                        role: "assistant",
                        content: @json($entry->output)
                    };
                    messages.push(message);
                @endforeach
            @endif
        </script>

        <script src="/assets/js/panel/openai_chat_low.js"></script>
        <script>
            var chatid = @json($list)[0].id;

            function exportAsPdf() {
                var win = window.open(`/dashboard/user/openai/chat/generate-pdf?id=${chatid}`, '_blank');
                console.log(`/dashboard/user/openai/chat/generate-pdf?id=${chatid}`);
                win.focus();
            }

            function exportAsWord() {
                var win = window.open(`/dashboard/user/openai/chat/generate-word?id=${chatid}`, '_blank');
                console.log(`/dashboard/user/openai/chat/generate-word?id=${chatid}`);
                win.focus();
            }

            function exportAsTxt() {
                var win = window.open(`/dashboard/user/openai/chat/generate-txt?id=${chatid}`, '_blank');
                console.log(`/dashboard/user/openai/chat/generate-txt?id=${chatid}`);
                win.focus();
            }

            // $("#show_export_btns").on('mouseenter', function(e) {
            //     $("#export_btns").removeClass('hidden');
            // });

            // $("#show_export_btns").on('mouseleave', function(e) {
            //     $("#export_btns").addClass('hidden');
            // });

            function saveResponse(input, response, chat_id) {
                "use strict";
                var formData = new FormData();
                formData.append('chat_id', chat_id);
                formData.append('input', input);
                formData.append('response', response);
                jQuery.ajax({
                    url: '/dashboard/user/openai/chat/low/chat_save',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                });
                return false;
            }
        </script>
    @endif

    @if (count($list) == 0)
        <script>
            window.addEventListener("load", (event) => {
                return startNewChat({{ $category->id }}, '{{ LaravelLocalization::getCurrentLocale() }}');
            });
        </script>
    @endif

    <script>
        document.getElementById('prompt').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.shiftKey) {
                e.preventDefault();
                this.value += '\n';
            }
        });
    </script>

@endsection
