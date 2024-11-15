@extends('panel.layout.app')
@section('title', __('AI ReWriter'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                        class="flex items-center page-pretitle">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('AI ReWriter') }}
                    </h2>
                    <div class="mt-3 page-pretitle">
                        {{ __('Effortlessly reshape and elevate your pre-existing content with a single click.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body page-generator pt-6">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-6 col-lg-5 lg:pr-14">
                    <div class="card mb-[25px]">
                        <div class="card-body">
                            <div class="flex items-center">
                                <h5 class="mb-3 text-[14px] font-normal">{{ __('Remaining Credits') }}</h5>
                                <span class="ms-2 text-heading font-bold mb-3">
                                    @if (Auth::user()->remaining_words == -1)
                                        Unlimited
                                    @else
                                        {{ number_format((int) Auth::user()->remaining_words) }}
                                    @endif
                                </span>
                            </div>
                            <div class="progress progress-separated mb-2">
                                @if ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images != 0)
                                    <div class="progress-bar grow-0 shrink-0 basis-auto bg-primary" role="progressbar"
                                        style="width: {{ ((int) Auth::user()->remaining_words / ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images)) * 100 }}%"
                                        aria-label="{{ __('Text') }}"></div>
                                @endif
                                @if ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images != 0)
                                    <div class="progress-bar grow-0 shrink-0 basis-auto bg-[#9E9EFF]" role="progressbar"
                                        style="width: {{ ((int) Auth::user()->remaining_images / ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images)) * 100 }}%"
                                        aria-label="{{ __('Images') }}"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <form id="rewrite_content_form" onsubmit="return sendOpenaiGeneratorForm();" enctype="multipart/form-data">
                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="content_rewrite" name="content_rewrite" rows="20" required="required"></textarea>
                        </div>

                        <div class="mb-[20px]">
                            <div class="form-label">{{ __('Mode') }}</div>
                            <select class="form-select" id="rewrite_mode" name="rewrite_mode" required>
                                <option value="Funny">{{ __('Funny') }}</option>
                                <option value="Casual">{{ __('Casual') }}</option>
                                <option value="Excited">{{ __('Excited') }}</option>
                                <option value="Professional" selected>{{ __('Professional') }}</option>
                                <option value="Witty">{{ __('Witty') }}</option>
                                <option value="Sarcastic">{{ __('Sarcastic') }}</option>
                                <option value="Feminine">{{ __('Feminine') }}</option>
                                <option value="Masculine">{{ __('Masculine') }}</option>
                                <option value="Bold">{{ __('Bold') }}</option>
                                <option value="Dramatic">{{ __('Dramatic') }}</option>
                                <option value="Grumpy">{{ __('Grumpy') }}</option>
                                <option value="Secretive">{{ __('Secretive') }}</option>
                            </select>
                        </div>

                        <div class="mb-[20px]">
                            <label class="form-label">{{ __('Output Language') }}</label>
                            <select type="text" class="form-select" name="language" id="language" required>
                                @include('panel.user.openai.components.countries')
                            </select>
                        </div>

                        <button form="rewrite_content_form" id="openai_generator_button" type="submit" class="btn btn-primary w-100 py-[0.75em]">
                            {{ __('Generate') }}
                        </button>
                    </form>
                </div>
                <div class="col-sm-6 col-lg-7 lg:pl-16 lg:border-l lg:border-solid border-t-0 border-r-0 border-b-0 border-[var(--tblr-border-color)] [&_.tox-edit-area__iframe]:!bg-transparent"
                    id="workbook_textarea">
                    <div class="row text-[13px] items-center justify-between flex">
                        <div class="w-fit">
                            <button class="btn border-none shadow-none" type="submit" id="btn_regenerate" form="rewrite_content_form"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M6.25 17.5L2.5 13.75M2.5 13.75L6.25 10M2.5 13.75H13.75M13.75 2.5L17.5 6.25M17.5 6.25L13.75 10M17.5 6.25H6.25"
                                        stroke="black" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <label class="mx-2">{{ __('Regenerate') }}</label></button>
                        </div>
                        <div class="flex items-center w-fit">
                            <div class="flex rtl:flex-row-reverse">
                                <button
                                    class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                    id="workbook_undo" title="{{ __('Undo') }}">
                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M4.75342 12.7378H10.0868C11.9268 12.7378 13.4201 11.2445 13.4201 9.4045C13.4201 7.5645 11.9268 6.07117 10.0868 6.07117H2.75342"
                                            stroke="var(--lqd-heading-color)" stroke-width="1.25" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M4.28674 7.7378L2.58008 6.03113L4.28674 4.32446"
                                            stroke="var(--lqd-heading-color)" stroke-width="1.25" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span class="sr-only">{{ __('Undo') }}</span>
                                </button>
                                <button
                                    class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                    id="workbook_redo" title="{{ __('Redo') }}">
                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11.2467 12.7378H5.91341C4.07341 12.7378 2.58008 11.2445 2.58008 9.4045C2.58008 7.5645 4.07341 6.07117 5.91341 6.07117H13.2467"
                                            stroke="var(--lqd-heading-color)" stroke-width="1.25" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M11.7134 7.7378L13.42 6.03113L11.7134 4.32446"
                                            stroke="var(--lqd-heading-color)" stroke-width="1.25" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span class="sr-only">{{ __('Redo') }}</span>
                                </button>
                            </div>
                            <button
                                class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                id="workbook_copy" title="{{ __('Copy to clipboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 96 960 960"
                                    fill="var(--lqd-heading-color)" width="20">
                                    <path
                                        d="M180 975q-24 0-42-18t-18-42V312h60v603h474v60H180Zm120-120q-24 0-42-18t-18-42V235q0-24 18-42t42-18h440q24 0 42 18t18 42v560q0 24-18 42t-42 18H300Zm0-60h440V235H300v560Zm0 0V235v560Z" />
                                </svg>
                                <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                            </button>
                            <div class="relative">
                                <button
                                    class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                    title="{{ __('Download') }}" data-bs-toggle="dropdown" tabindex="-1">
                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.01025 10.7186V12.3124C1.01025 12.7351 1.17817 13.1404 1.47705 13.4393C1.77594 13.7382 2.18132 13.9061 2.604 13.9061H12.1665C12.5892 13.9061 12.9946 13.7382 13.2935 13.4393C13.5923 13.1404 13.7603 12.7351 13.7603 12.3124V10.7186M10.5728 7.53113L7.38525 10.7186M7.38525 10.7186L4.19775 7.53113M7.38525 10.7186V1.15613"
                                            stroke="var(--lqd-heading-color)" stroke-width="1.25" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span class="sr-only">{{ __('Download') }}</span>
                                </button>
                                <div
                                    class="dropdown-menu dropdown-menu-end text-center whitespace-nowrap p-0 [--tblr-dropdown-min-width:150px]">
                                    <div class="flex flex-col p-1 gap-1">
                                        <button
                                            class="workbook_download flex items-center gap-1 p-2 border-none rounded-md bg-[transparent] text-[12px] font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900"
                                            data-doc-type="doc" data-doc-name="{{ 'AI ReWriter' }}">
                                            <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M4 18h9v-12l-5 2v5l-4 2v-8l9 -4l7 2v13l-7 3z"></path>
                                            </svg>
                                            MS Word
                                        </button>
                                        <button
                                            class="workbook_download flex items-center gap-1 p-2 border-none rounded-md bg-[transparent] text-[12px] font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900"
                                            data-doc-type="html" data-doc-name="{{ 'AI ReWriter' }}">
                                            <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M20 4l-2 14.5l-6 2l-6 -2l-2 -14.5z"></path>
                                                <path d="M15.5 8h-7l.5 4h6l-.5 3.5l-2.5 .75l-2.5 -.75l-.1 -.5"></path>
                                            </svg>
                                            HTML
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <a href="javascript:void(0);"
                                class="bg-transparent -mr-1 p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                id="workbook_delete" title="{{ __('Delete') }}">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_1_7315" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="20" height="20">
                                        <rect x="0.885254" width="19.0623" height="19.0623" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_1_7315)">
                                        <path
                                            d="M6.44519 10.3254H14.3878V8.73687H6.44519V10.3254ZM10.4165 17.4738C9.31778 17.4738 8.28524 17.2653 7.31888 16.8483C6.35253 16.4313 5.51193 15.8654 4.7971 15.1505C4.08226 14.4357 3.51635 13.5951 3.09936 12.6288C2.68237 11.6624 2.47388 10.6299 2.47388 9.53113C2.47388 8.4324 2.68237 7.39986 3.09936 6.43351C3.51635 5.46715 4.08226 4.62656 4.7971 3.91172C5.51193 3.19688 6.35253 2.63097 7.31888 2.21398C8.28524 1.797 9.31778 1.5885 10.4165 1.5885C11.5152 1.5885 12.5478 1.797 13.5141 2.21398C14.4805 2.63097 15.3211 3.19688 16.0359 3.91172C16.7508 4.62656 17.3167 5.46715 17.7337 6.43351C18.1506 7.39986 18.3591 8.4324 18.3591 9.53113C18.3591 10.6299 18.1506 11.6624 17.7337 12.6288C17.3167 13.5951 16.7508 14.4357 16.0359 15.1505C15.3211 15.8654 14.4805 16.4313 13.5141 16.8483C12.5478 17.2653 11.5152 17.4738 10.4165 17.4738ZM10.4165 15.8852C12.1904 15.8852 13.6928 15.2697 14.924 14.0386C16.1551 12.8075 16.7706 11.305 16.7706 9.53113C16.7706 7.75728 16.1551 6.2548 14.924 5.02369C13.6928 3.79258 12.1904 3.17703 10.4165 3.17703C8.64265 3.17703 7.14017 3.79258 5.90907 5.02369C4.67796 6.2548 4.0624 7.75728 4.0624 9.53113C4.0624 11.305 4.67796 12.8075 5.90907 14.0386C7.14017 15.2697 8.64265 15.8852 10.4165 15.8852Z"
                                            fill="#CE3A3A" />
                                    </g>
                                </svg>
                                <span class="sr-only">{{ __('Delete') }}</span>
                            </a>
                        </div>
                        <div id="savedDiv" class="col items-end text-end hidden">
                            <div class="outer-div flex justify-end">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.1718 11.1436C15.1718 13.678 13.6779 15.1719 11.1435 15.1719H5.63125C3.09046 15.1719 1.59375 13.678 1.59375 11.1436V5.61862C1.59375 3.08775 2.5245 1.59387 5.05963 1.59387H6.47629C6.98488 1.59458 7.46371 1.83329 7.76829 2.24058L8.415 3.1005C8.721 3.50708 9.19983 3.7465 9.70842 3.74721H11.713C14.2538 3.74721 15.1916 5.04062 15.1916 7.62675L15.1718 11.1436Z"
                                        stroke="#20725E" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M5.29932 10.2447H11.4866" stroke="#20725E" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="inner-div">
                                    <span>Save to </span>
                                    <a href="{{ route('dashboard.user.openai.documents.all') }}"
                                        style="color: #20725E; text-decoration: underline;cursor: pointer;">Drafts</a>
                                </div>
                            </div>
                        </div>
                        <div
                            class="border-solid border-t border-r-0 border-b-0 border-l-0 border-[var(--tblr-border-color)] pt-[30px] mt-[15px]">
                            <form class="workbook-form">
                                <div class="mb-[20px]">
                                    <input type="text" class="form-control rounded-md"
                                        placeholder="{{ __('Untitled Document...') }}">
                                </div>
                                <div class="mb-[20px]">
                                    <textarea class="form-control tinymce" id="default" rows="25"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($setting->hosting_type != 'high')
            <input type="hidden" id="guest_id" value="{{ $apiUrl }}">
            <input type="hidden" id="guest_event_id" value="{{ $apikeyPart1 }}">
            <input type="hidden" id="guest_look_id" value="{{ $apikeyPart2 }}">
            <input type="hidden" id="guest_product_id" value="{{ $apikeyPart3 }}">
        @endif
    </div>
@endsection
@section('script')
    <script src="/assets/libs/tinymce/tinymce.min.js" defer></script>
    <script src="/assets/js/panel/openai_generator_workbook.js"></script>
    @if ($setting->hosting_type != 'high')
        <script src="/assets/js/panel/openai_generator_workbook_low.js"></script>
    @endif
    <script src="/assets/libs/wavesurfer/wavesurfer.js"></script>
    <script>
        const stream_type = '{!! $settings_two->openai_default_stream_server !!}';
        const openai_model = '{{ $setting->openai_default_model }}';

        function sendOpenaiGeneratorForm(ev) {
            "use strict";
            $('#savedDiv').addClass('hidden');

            tinyMCE?.activeEditor?.setContent('');

            ev?.preventDefault();
            ev?.stopPropagation();
            const submitBtn = document.getElementById("openai_generator_button");
            const editArea = document.querySelector('.tox-edit-area');
            const typingTemplate = document.querySelector('#typing-template').content.cloneNode(true);
            const typingEl = typingTemplate.firstElementChild;
            document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
            submitBtn.classList.add('lqd-form-submitting');
            submitBtn.disabled = true;
            
            if (editArea) {
                if (!editArea.querySelector('.lqd-typing')) {
                    editArea.appendChild(typingEl);
                } else {
                    editArea.querySelector('.lqd-typing')?.classList?.remove('lqd-is-hidden');
                }
            }

            var formData = new FormData();
            formData.append('post_type', 'ai_rewriter');
            formData.append('content_rewrite', $('#content_rewrite').val());

            formData.append('rewrite_mode', $("#rewrite_mode").val());
            formData.append('language', $("#language").val());

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: "/dashboard/user/openai/generate",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    const typingEl = document.querySelector('.tox-edit-area > .lqd-typing');
                    @if ($setting->hosting_type == 'high')

                        let responseText = '';
                        const message_id = data.message_id;
                        const eventSource = new EventSource(
                            "/dashboard/user/openai/generate?message_id=" +
                            message_id + "&maximum_length=" + data.maximum_length +
                            "&number_of_results=" + data.number_of_results + "&creativity=" + data
                            .creativity);
                        eventSource.onmessage = function(e) {
                            let txt = e.data;
                            typingEl.classList.add('lqd-is-hidden');
                            if (txt === '[DONE]') {
                                //This is the area when the chat ends.
                                eventSource.close();
                                submitBtn.classList.remove('lqd-form-submitting');
                                document.querySelector('#app-loading-indicator')?.classList?.add(
                                    'opacity-0');
                                document.querySelector('#workbook_regenerate')?.classList?.remove(
                                    'hidden');
                                submitBtn.disabled = false;
                            }
                            if (txt && txt !== '[DONE]') {
                                responseText += txt.split("/**")[0];
                                tinyMCE.activeEditor.setContent(responseText, {
                                    format: 'raw'
                                });
                            }
                        };
                    @else
                        // $("#workbook_textarea").html(data.html);

                        const message_no = data.message_id;
                        const creativity = data.creativity;
                        const maximum_length = parseInt(data.maximum_length);
                        const number_of_results = data.number_of_results;
                        const prompt = data.inputPrompt;

                        return generate(message_no, creativity, maximum_length, number_of_results,
                            prompt);
                    @endif

                    setTimeout(function() {
                        $('#savedDiv').removeClass('hidden');
                    }, 1000);
                },
                error: function(data) {
                    if (data.responseJSON.errors) {
                        toastr.error(data.responseJSON.errors);
                    } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                    }
                    submitBtn.classList.remove('lqd-form-submitting');
                    document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                    document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
                    submitBtn.disabled = false;
                    const editArea = document.querySelector('.tox-edit-area');
                    editArea.querySelector('.lqd-typing')?.classList?.add('lqd-is-hidden');
                }
            });
            return false;
        };

        $("#btn_regenerate").on('click', function() {
            $("#openai_generator_button").click();
        })

        const deleteButton = document.getElementById("workbook_delete");
        deleteButton.addEventListener("click", clearWorkbookContent);

        function clearWorkbookContent() {
            const editor = tinyMCE.activeEditor;
            if (editor) {
                editor.setContent("");
            }
        }
    </script>
@endsection
