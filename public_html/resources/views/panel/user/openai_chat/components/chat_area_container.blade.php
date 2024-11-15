<div
    class="group/modal absolute z-50 hidden [&.lqd-is-active]:block"
    id='modal'
>
    <div class="modal__back fixed start-0 top-0 z-10 hidden h-screen w-screen bg-black/25"></div>
    <div
        class="modal__content invisible fixed start-1/2 top-1/2 z-20 w-[90%] -translate-x-1/2 -translate-y-1/2 rounded-2xl bg-[--tblr-body-bg] !p-5 opacity-0 shadow-xl group-[&.lqd-is-active]/modal:!visible group-[&.lqd-is-active]/modal:!opacity-100 sm:w-[500px] lg:w-[1000px]">
        <div class="modal__header mx-[20px] my-[5px] flex h-[50px] items-center justify-between rounded-lg border-solid border-[--tblr-border-color] !p-4">
            <label class="font-semibold">{{ __('Prompt Library') }}</label>
            <div>
                <a
                    class="btn-primary cursor-default font-bold"
                    id="add_btn"
                >{{ __('Add +') }}</a>
                <div
                    class="absolute"
                    id='custom__popover'
                >
                    <div class="custom__popover__back hidden"></div>
                    <div class="custom__popover__content left-[-400px] w-[400px] bg-[#FaFaFc] p-[15px] dark:bg-[--tblr-body-bg]">
                        <div class="absolute h-[100vh] w-[100wh] bg-black"></div>
                        <input
                            class="form-control my-2 bg-[--tblr-body-bg]"
                            id="new_prompt_title"
                            type="text"
                            placeholder="{{ __('Add Title') }}"
                        >
                        <textarea
                            class="form-control my-2 w-full bg-[--tblr-body-bg]"
                            id="new_prompt"
                            rows=6
                            placeholder="{{ __('Add custom prompt') }}"
                        ></textarea>
                        <div class="modal-footer p-1">
                            <button
                                class="btn me-auto w-full"
                                id="btn_add_new_prompt"
                                type="button"
                            >{{ __('Done') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="flex flex-wrap items-center justify-between px-4 py-2 sm:flex-nowrap"
            id="prompt_filter"
        >
            <div class="flex flex-wrap content-center justify-evenly gap-2 sm:justify-start">
                <button
                    class="filter_btn btn active flex items-center justify-center gap-2 border-none bg-[transparent] !py-1 px-3 shadow-none hover:bg-black/5 dark:hover:bg-white/10 [&.active]:bg-[--lqd-pink]"
                    filter="all"
                >
                    {{ __('All') }}
                </button>
                <button
                    class="filter_btn btn flex items-center justify-center gap-2 border-none bg-[transparent] !py-1 px-3 shadow-none hover:bg-black/5 dark:hover:bg-white/10 [&.active]:bg-[--lqd-pink]"
                    filter="fav"
                >
                    {{ __('Favourites') }}
                    <label
                        class="color-[#2B2F37] mb-0 text-[1em] opacity-30"
                        id="fav_count"
                    >8</label>
                </button>
                <button
                    class="filter_btn btn flex items-center justify-center gap-2 border-none bg-[transparent] !py-1 px-3 shadow-none hover:bg-black/5 dark:hover:bg-white/10 [&.active]:bg-[--lqd-pink]"
                    filter="per"
                >
                    {{ __('Personal') }}
                    <label
                        class="color-[#2B2F37] mb-0 text-[1em] opacity-30"
                        id="per_count"
                    >8</label>
                </button>
            </div>
            <div class="input-icon w-[100%] sm:w-full lg:w-auto">
                <span class="input-icon-addon">
                    <svg
                        class="icon"
                        xmlns="http://www.w3.org/2000/svg"
                        width="17"
                        height="17"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        ></path>
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                        <path d="M21 21l-6 -6"></path>
                    </svg>
                </span>
                <input
                    class="form-control navbar-search-input peer dark:!bg-zinc-900 max-lg:!rounded-md"
                    id="search_str"
                    type="search"
                    onkeyup="searchStringChange()"
                    placeholder="{{ __('Search') }}"
                    aria-label="{{ __('Search in website') }}"
                >
            </div>
        </div>

        <div
            class="flex-colmy-2 my-[20px] flex hidden cursor-default rounded-[15px] p-[30px]"
            id="no_prompt"
            style="box-shadow: 0px 2px 5px 0px rgba(29,39, 59, 0.05); hover: {cursor-default hidden"
            style="box-shadow: 0px 2px 5px 0px3gb}"
        >
            <div class="prompt_header my-[10px] flex content-center justify-between">
                <p class="my-2 text-[17px] font-semibold">{{ __('No Prompts, Please input new one') }}</p>
            </div>
        </div>

        <div
            class="mx-2 flex max-h-[550px] flex-wrap justify-between gap-y-6 overflow-scroll p-[10px]"
            id="prompts"
        >
        </div>
    </div>
</div>

<div class="flex h-[82px] justify-between border-b border-l-0 border-r-0 border-t-0 border-solid border-[--tblr-border-color] p-[20px] max-sm:px-4">
    <div class="flex shrink-0 gap-2">
        <div
            class="inline-flex h-[50px] w-[50px] items-center justify-center overflow-hidden overflow-ellipsis whitespace-nowrap rounded-full border-[6px] border-solid !border-white text-[13px] font-medium text-[rgba(0,0,0,0.65)] shadow-[0_1px_2px_rgba(0,0,0,0.07)] transition-shadow group-hover:shadow-xl dark:!border-current"
            style="background: {{ $category->color }};"
        >
            @if ($category->image != null)
                <img
                    class="h-full w-full object-cover object-center"
                    src="/{{ $category->image }}"
                    alt="{{ __($category->name) }}"
                >
            @else
                <span class="block w-full overflow-hidden overflow-ellipsis whitespace-nowrap text-center">{{ __($category->short_name) }}</span>
            @endif
        </div>
        <div class="flex flex-col items-start justify-center">
            @if ($category->human_name != '')
                <p class="text-heading m-0 p-0 text-sm font-semibold">
                    {{ __($category->human_name) }}</p>
            @endif
            @if ($category->role != '')
                <p class="m-0 p-0 text-sm font-normal">
                    {{ __($category->role) }}</p>
            @endif
        </div>
    </div>
    <div class="flex gap-2">
        <div class="flex gap-2">
            <div
                class="group inline-flex flex-row items-center justify-center"
                id="show_export_btns"
            >
                <button class="text-heading border-none bg-transparent">
                    <svg
                        width="19"
                        height="23"
                        viewBox="0 0 19 23"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        stroke="currentColor"
                    >
                        <path
                            d="M5.74996 3.41675H3.58329C3.00866 3.41675 2.45756 3.64502 2.05123 4.05135C1.6449 4.45768 1.41663 5.00878 1.41663 5.58342V18.5834C1.41663 19.1581 1.6449 19.7092 2.05123 20.1155C2.45756 20.5218 3.00866 20.7501 3.58329 20.7501H6.83329M5.74996 3.41675C5.74996 2.84211 5.97827 2.29093 6.3846 1.8846C6.79093 1.47827 7.34203 1.25 7.91667 1.25H10.0833C10.658 1.25 11.2091 1.47827 11.6154 1.8846C12.0217 2.29093 12.25 2.84211 12.25 3.41675M5.74996 3.41675C5.74996 3.99138 5.97827 4.5424 6.3846 4.94873C6.79093 5.35506 7.34203 5.58333 7.91667 5.58333H10.0833C10.658 5.58333 11.2091 5.35506 11.6154 4.94873C12.0217 4.5424 12.25 3.99138 12.25 3.41675M16.5833 11.0001V5.58342C16.5833 5.00878 16.355 4.45768 15.9487 4.05135C15.5424 3.64502 14.9913 3.41675 14.4166 3.41675H12.25M10.0834 16.4167V15.3333C10.0834 15.046 10.1975 14.7705 10.4007 14.5673C10.6038 14.3641 10.8794 14.25 11.1667 14.25H12.25M15.5 14.25H16.5834C16.8707 14.25 17.1462 14.3641 17.3494 14.5673C17.5526 14.7705 17.6667 15.046 17.6667 15.3333V16.4167M17.6667 19.6667V20.75C17.6667 21.0373 17.5526 21.3129 17.3494 21.516C17.1462 21.7192 16.8707 21.8333 16.5834 21.8333H15.5M12.25 21.8333H11.1667C10.8794 21.8333 10.6038 21.7192 10.4007 21.516C10.1975 21.3129 10.0834 21.0373 10.0834 20.75V19.6667"
                            stroke-width="1.25"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>
                <div
                    class="invisible absolute !right-[-15px] end-0 top-0 flex translate-y-1 scale-95 flex-row items-center justify-center opacity-0 transition-all group-hover:!visible group-hover:translate-y-0 group-hover:scale-100 group-hover:!opacity-100"
                    id="export_btns"
                >
                    <button
                        class="btn btn-primary rounded-e-none rounded-s-md border-none px-2 py-[0.25em] text-xs hover:translate-y-0"
                        id="export_pdf"
                        onclick="exportAsPdf();"
                    >
                        {{ __('PDF') }}
                    </button>
                    <button
                        class="btn btn-primary border-y-none rounded-none border-x-[1px] border-x-white/20 px-2 py-[0.25em] text-xs hover:translate-y-0"
                        id="export_word"
                        onclick="exportAsWord();"
                    >
                        {{ __('Word') }}
                    </button>
                    <button
                        class="btn btn-primary rounded-e-md rounded-s-none border-none px-2 py-[0.25em] text-xs hover:translate-y-0"
                        id="export_txt"
                        onclick="exportAsTxt();"
                    >
                        {{ __('Txt') }}
                    </button>
                </div>
            </div>
            <label
                class="form-check form-switch relative mx-2 mt-2 md:-order-1 {{($category->slug == 'ai_pdf' ? 'hidden': '')}}"
                title="{{ __('Use Real-Time Data') }}"
            >
                <input
                    class="form-check-input peer max-md:!h-8 max-md:!w-8 max-md:border-none max-md:!bg-none max-md:shadow"
                    id="realtime"
                    type="checkbox"
                    name="realtime"
                    onchange="const checked = document.querySelector('#realtime').checked; if ( checked ) { toastr.success('Real-Time data activated') } else { toastr.warning('Real-Time data deactivated') }"
                >
                <svg
                    class="absolute start-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 peer-checked:stroke-white md:hidden"
                    xmlns="http://www.w3.org/2000/svg"
                    width="22"
                    height="22"
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
                    <path d="M21 12a9 9 0 1 0 -9 9" />
                    <path d="M3.6 9h16.8" />
                    <path d="M3.6 15h8.4" />
                    <path d="M11.578 3a17 17 0 0 0 0 18" />
                    <path d="M12.5 3c1.719 2.755 2.5 5.876 2.5 9" />
                    <path d="M18 14v7m-3 -3l3 3l3 -3" />
                </svg>
                <span class="form-check-label max-md:hidden">
                    {{ __('Use Real-Time Data') }}
                </span>
            </label>
            <div class="lqd-chat-mobile-sidebar-trigger self-center">
                <button
                    class="btn group h-8 w-8 grid-flow-row place-items-center p-0 max-sm:grid sm:hidden"
                    type="button"
                    x-init
                    @click.prevent="$store.mobileChat.toggleSidebar()"
                    :class="{ 'lqd-is-active': $store.mobileChat.sidebarOpen }"
                >
                    <svg
                        class="col-start-1 row-start-1 transition-all group-[&.lqd-is-active]:rotate-45 group-[&.lqd-is-active]:scale-75 group-[&.lqd-is-active]:opacity-0"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        stroke-width="2"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M2 12a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"
                        />
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M10 12a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"
                        />
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M18 12a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"
                        />
                    </svg>
                    <svg
                        class="col-start-1 row-start-1 -rotate-45 opacity-0 transition-all group-[&.lqd-is-active]:rotate-0 group-[&.lqd-is-active]:!opacity-100"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path d="M20 20L4 4m16 0L4 20" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<div
    class="conversation-area flex grow flex-col justify-between overflow-y-auto max-md:max-h-[70vh] max-sm:max-h-full"
    id="chat_area_to_hide"
>
    {{-- @if (count($chat->messages) == 1)
        <div class="h-full overflow-hidden">
            <div class="h-full p-8 overflow-auto chats-container max-md:p-4">
                @include('panel.user.openai_chat.components.chat_area')
            </div>
        </div>
    @else --}}
    <div class="relative flex grow flex-col">
        <div class="chats-container @if ($category->slug == 'ai_vision') mb-32 md:mb-0 relative z-10 @else h-full @endif p-8 max-md:p-4">

            @include('panel.user.openai_chat.components.chat_area')

            @if ($category->slug == 'ai_vision' && ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
                <div
                    class="flex flex-col items-center justify-center gap-y-3"
                    id="sugg"
                >
                    <div class="text-heading text-xs font-medium leading-relaxed">
                        {{ __('Upload an image and ask me anything') }} <span><svg
                                class="icon icon-tabler icon-tabler-chevron-down"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15"
                                height="15"
                                viewBox="0 0 30 24"
                                stroke-width="2"
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
                                <path d="M6 9l6 6l6 -6" />
                            </svg></span> </div>

                    <div class="cursor-pointer flex-col items-center justify-start rounded-3xl bg-purple-100 px-4 py-2">
                        <div
                            class="text-sm font-normal leading-tight text-black"
                            onclick="addText('{{ __('Explain an Image') }}');"
                        >{{ __('Explain an Image') }}</div>
                    </div>
                    <div
                        class="cursor-pointer flex-col items-center justify-start rounded-3xl bg-purple-100 px-4 py-2"
                        onclick="addText('{{ __('Summarize a book for Research') }}');"
                    >
                        <div class="text-sm font-normal leading-tight text-black">
                            {{ __('Summarize a book for Research') }}</div>
                    </div>
                    <div class="cursor-pointer flex-col items-center justify-start rounded-3xl bg-purple-100 px-4 py-2">
                        <div
                            class="text-sm font-normal leading-tight text-black"
                            onclick="addText('{{ __('Translate a book') }}');"
                        >{{ __('Translate a book') }}</div>
                    </div>
                </div>
            @endif

        </div>

        @if ($category->slug == 'ai_vision' && ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
            <div
                class="z-10 mt-auto flex items-center justify-center px-4"
                id="mainupscale_src"
                ondrop="dropHandler(event, 'upscale_src');"
                ondragover="dragOverHandler(event);"
            >
                <label
                    class="z-5 dark:hover:bg-bray-800 flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-black/5 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                    for="upscale_src"
                >
                    <div class="flex flex-col items-center justify-center pb-6 pt-4">
                        <svg
                            class="mb-1 h-8 w-8 text-gray-500 dark:text-gray-400"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 16"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                            />
                        </svg>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-semibold">{{ __('Drop your image here or browse') }}
                        </p>
                        <p class="file-name text-xs text-gray-500 dark:text-gray-400">
                            {{ __('(Only jpg, png, webp will be accepted)') }}</p>
                    </div>
                    <input
                        class="hidden"
                        id="upscale_src"
                        type="file"
                        accept=".png, .jpg, .jpeg"
                        onchange="handleFileSelect('upscale_src')"
                    />
                </label>
            </div>
        @endif
    </div>
    {{-- @endif --}}

    <form
        class="sticky bottom-0 z-10 flex w-full items-end gap-3 self-end bg-[--tblr-body-bg] p-8 py-[1.5rem] max-md:p-4 max-sm:items-end max-sm:p-3"
        id="chat_form"
    >
        <input
            id="category_id"
            type="hidden"
            value="{{ $category->id }}"
        >
        <input
            id="chat_id"
            type="hidden"
            value="{{ $chat->id }}"
        >
        <div class="form-control flex min-h-[52px] flex-col rounded-[26px] p-0 max-sm:min-h-[45px]">
            <div
                class="flex hidden max-h-[120px] flex-wrap overflow-scroll p-[10px]"
                id="chat_images"
            >

            </div>
            <div
                class="flex hidden max-h-[120px] flex-wrap overflow-scroll p-[10px]"
                id="chat_pdfs"
            >

            </div>
            <hr class="split_line border-1 hidden w-full" />
            <div class="relative flex grow items-center">
                <input
                    id="selectImageInput"
                    type="file"
                    style="display: none;"
                    accept="image/*"
                />
                <button
                    class="lqd-chat-mobile-options-trigger btn text-heading collapsed mt-[3px] h-8 w-8 shrink-0 origin-center bg-transparent p-0 shadow-none transition-transform sm:hidden [&:not(.collapsed)]:rotate-45"
                    data-bs-toggle="collapse"
                    data-bs-target="#chat-options"
                    type="button"
                    aria-controls="chat-options"
                >
                    <svg
                        class="ai ai-Plus"
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M12 20v-8m0 0V4m0 8h8m-8 0H4" />
                    </svg>
                    <span class="sr-only">{{ __('Options') }}</span>
                </button>
                <textarea
                    class="text-heading m-0 w-full border-none bg-transparent !py-3 pe-[100px] ps-16 outline-none focus:border-none focus:ring-0 max-sm:max-h-[200px] max-sm:pe-2 max-sm:ps-0 max-sm:text-[16px]"
                    id="prompt"
                    placeholder="{{ __('Type a message') }}"
                    name="prompt"
                    rows="1"
                ></textarea>
                <div class="pointer-events-none absolute bottom-0 end-2 start-2 flex items-end justify-between py-[5px] text-sm max-sm:static">
                    <div
                        class="flex grow items-center justify-between max-sm:absolute max-sm:!-end-[calc(52px+1rem)] max-sm:!bottom-full max-sm:-start-2 max-sm:mb-3 max-sm:flex-col max-sm:items-start max-sm:gap-4 max-sm:rounded-xl max-sm:bg-[--tblr-body-bg] max-sm:px-4 max-sm:py-0 max-sm:shadow-lg sm:!flex sm:h-full max-sm:[&:not(.collapsing):not(.show)]:hidden"
                        id="chat-options"
                    >
                        <div class="pointer-events-auto flex items-center max-sm:pt-4">
                            <button
                                class="lqd-chat-attach max-sm:!text-heading flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full border-none bg-[--lqd-pink] p-0 text-black transition-all hover:bg-[--lqd-pink] hover:opacity-80 max-sm:h-auto max-sm:w-auto max-sm:!bg-transparent"
                                type="button"
                                @if ($app_is_demo) onclick="return toastr.info('This feature is disabled in Demo version.')" @else id="chat_add_image" @endif
                            >
                                <svg
                                    class="max-sm:w-5"
                                    width="11"
                                    height="21"
                                    viewBox="0 0 11 21"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M5.49994 20.8845C4.03712 20.8845 2.79321 20.372 1.76821 19.347C0.743212 18.3221 0.230713 17.0782 0.230713 15.6153V4.80761C0.230713 3.75521 0.605438 2.8543 1.35489 2.10486C2.10432 1.35543 3.00524 0.980713 4.05764 0.980713C5.11004 0.980713 6.01096 1.35543 6.76039 2.10486C7.50982 2.8543 7.88454 3.75521 7.88454 4.80761V14.5576C7.88454 15.2253 7.654 15.7897 7.19294 16.2507C6.73185 16.7117 6.16743 16.9422 5.49966 16.9422C4.8319 16.9422 4.26756 16.7117 3.80666 16.2507C3.34578 15.7897 3.11534 15.2253 3.11534 14.5576V4.80761H4.30761V14.5576C4.30761 14.8955 4.42188 15.1786 4.65041 15.4072C4.87895 15.6357 5.16212 15.75 5.49994 15.75C5.83775 15.75 6.12093 15.6357 6.34946 15.4072C6.578 15.1786 6.69226 14.8955 6.69226 14.5576V4.80761C6.69226 4.06991 6.4375 3.44639 5.92799 2.93704C5.41847 2.42767 4.79475 2.17299 4.05681 2.17299C3.3189 2.17299 2.69545 2.42767 2.18646 2.93704C1.6775 3.44639 1.42301 4.06991 1.42301 4.80761V15.6153C1.42301 16.741 1.82109 17.7019 2.61724 18.498C3.41339 19.2942 4.37429 19.6923 5.49994 19.6923C6.62559 19.6923 7.58649 19.2942 8.38264 18.498C9.17879 17.7019 9.57686 16.741 9.57686 15.6153V4.80761H10.7692V15.6153C10.7692 17.0782 10.2567 18.3221 9.23166 19.347C8.20666 20.372 6.96275 20.8845 5.49994 20.8845Z"
                                    />
                                </svg>
                                <span class="sm:hidden">{{ __('Upload a document or image') }}</span>
                            </button>
                        </div>
                        <div class="pointer-events-auto flex items-center max-sm:flex-col max-sm:items-start max-sm:gap-4 max-sm:pb-4 sm:ms-auto">
                            <button
                                class="text-heading flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full border-none bg-[transparent] p-0 transition-all hover:bg-[--lqd-pink] hover:text-black max-sm:h-auto max-sm:w-auto max-sm:!bg-transparent"
                                id="prompt_library"
                                type="button"
                            >
                                <svg
                                    class="max-sm:w-5"
                                    width="19"
                                    height="20"
                                    viewBox="0 0 19 20"
                                    fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M4.16168 15.4519H10.9687V13.7683H4.16168V15.4519ZM4.16168 10.9622H14.1105V9.27859H4.16168V10.9622ZM4.16168 6.47245H14.1105V4.78885H4.16168V6.47245ZM2.12761 19.6611C1.59861 19.6611 1.15084 19.4646 0.784303 19.0718C0.417766 18.6789 0.234497 18.199 0.234497 17.632V2.60874C0.234497 2.04176 0.417766 1.56184 0.784303 1.16899C1.15084 0.776138 1.59861 0.579712 2.12761 0.579712H16.1446C16.6736 0.579712 17.1213 0.776138 17.4879 1.16899C17.8544 1.56184 18.0377 2.04176 18.0377 2.60874V17.632C18.0377 18.199 17.8544 18.6789 17.4879 19.0718C17.1213 19.4646 16.6736 19.6611 16.1446 19.6611H2.12761ZM2.12761 17.9774H16.1446C16.2251 17.9774 16.299 17.9415 16.3661 17.8695C16.4333 17.7976 16.4668 17.7184 16.4668 17.632V2.60874C16.4668 2.52239 16.4333 2.44323 16.3661 2.37126C16.299 2.29931 16.2251 2.26334 16.1446 2.26334H2.12761C2.04704 2.26334 1.97318 2.29931 1.90604 2.37126C1.83891 2.44323 1.80534 2.52239 1.80534 2.60874V17.632C1.80534 17.7184 1.83891 17.7976 1.90604 17.8695C1.97318 17.9415 2.04704 17.9774 2.12761 17.9774Z"
                                    />
                                </svg>
                                <span class="sm:hidden">{{ __('Browse prompt library') }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="pointer-events-auto max-sm:absolute max-sm:bottom-[10px] max-sm:end-2">
                        <button
                            class="text-heading flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full border-none bg-[transparent] p-0 transition-all hover:bg-[--lqd-pink] hover:text-black max-sm:h-auto max-sm:w-auto max-sm:!bg-transparent"
                            id="voice_record_button"
                            type="button"
                            title={{ __('Record audio') }}
                        >
                            <svg
                                class="max-sm:h-5 max-sm:w-5"
                                aria-hidden="true"
                                width="17"
                                height="23"
                                viewBox="0 0 17 23"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M8.53278 13.2778C7.69031 13.2778 6.97729 12.986 6.39372 12.4025C5.81016 11.8189 5.51839 11.1059 5.51839 10.2634V3.0288C5.51839 2.18633 5.81016 1.47331 6.39372 0.889733C6.97729 0.306181 7.69031 0.0144043 8.53278 0.0144043C9.37525 0.0144043 10.0883 0.306181 10.6718 0.889733C11.2554 1.47331 11.5472 2.18633 11.5472 3.0288V10.2634C11.5472 11.1059 11.2554 11.8189 10.6718 12.4025C10.0883 12.986 9.37525 13.2778 8.53278 13.2778ZM7.62849 22.0196V18.0452C5.63897 17.818 3.98606 16.9647 2.66976 15.4853C1.35346 14.0059 0.695312 12.2653 0.695312 10.2634H2.50394C2.50394 11.9314 3.09175 13.3532 4.26737 14.5288C5.443 15.7044 6.8648 16.2923 8.53278 16.2923C10.2008 16.2923 11.6226 15.7044 12.7982 14.5288C13.9738 13.3532 14.5616 11.9314 14.5616 10.2634H16.3703C16.3703 12.2653 15.7121 14.0059 14.3958 15.4853C13.0795 16.9647 11.4266 17.818 9.43708 18.0452V22.0196H7.62849ZM8.53278 11.4692C8.87442 11.4692 9.16079 11.3536 9.39189 11.1225C9.623 10.8914 9.73855 10.605 9.73855 10.2634V3.0288C9.73855 2.68716 9.623 2.40079 9.39189 2.16969C9.16079 1.93858 8.87442 1.82303 8.53278 1.82303C8.19115 1.82303 7.90478 1.93858 7.67367 2.16969C7.44257 2.40079 7.32701 2.68716 7.32701 3.0288V10.2634C7.32701 10.605 7.44257 10.8914 7.67367 11.1225C7.90478 11.3536 8.19115 11.4692 8.53278 11.4692Z"
                                />
                            </svg>
                        </button>
                        <button
                            class="text-heading flex hidden h-10 w-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full border-none bg-[transparent] p-0 transition-all hover:bg-[--lqd-pink] hover:text-black max-sm:h-auto max-sm:w-auto max-sm:!bg-transparent"
                            id="voice_record_stop_button"
                            type="button"
                            title={{ __('Stop recording') }}
                        >
                            <svg
                                class="max-sm:h-5 max-sm:w-5"
                                aria-hidden="true"
                                width="20"
                                height="22"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path d="M7 5v14M17 5v14" />
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- <textarea class="form-control min-h-[52px] rounded-[25px] grow pe-[100px]" name="prompt" id="prompt"
				@if ($setting->openai_api_secret == null) placeholder="{{ __('Please ask system administrator to add API key to the system.') }}" disabled @else  placeholder="{{ __('Your Message') }}" @endif
				rows="1"></textarea> --}}
            </div>
        </div>
        @if ($setting->hosting_type != 'high' || $category->slug == 'ai_chat_image')
            <input
                    id="chatbot_id"
                    type="hidden"
                    value="{{ $category->chatbot_id }}"
            >
            <input
                id="category_id"
                type="hidden"
                value="{{ $category->id }}"
            >
            <input
                id="chat_id"
                type="hidden"
                value="{{ $chat->id }}"
            >
            <button
                class="btn btn-primary h-[52px] w-[52px] shrink-0 rounded-full p-0 max-sm:h-10 max-sm:w-10"
                @if ($app_is_demo && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image')) onclick="return toastr.info('This feature is disabled in Demo version.')" @endif
                @if ($app_is_demo && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image')) @else id="send_message_button" @endif
                type="button"
            >
                <svg
                    class="rtl:-scale-x-100"
                    width="16"
                    height="14"
                    viewBox="0 0 16 14"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M0.125 14V8.76172L11.375 7.25L0.125 5.73828V0.5L15.875 7.25L0.125 14Z" />
                </svg>
            </button>
            <button
                class="btn btn-primary hidden h-[52px] w-[52px] shrink-0 rounded-full p-0 max-sm:h-10 max-sm:w-10"
                id="stop_button"
                type="button"
            >
                <svg
                    class="rtl:-scale-x-100"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path
                        stroke="none"
                        d="M0 0h24v24H0z"
                        fill="none"
                    ></path>
                    <path d="M8 13v-7.5a1.5 1.5 0 0 1 3 0v6.5"></path>
                    <path d="M11 5.5v-2a1.5 1.5 0 1 1 3 0v8.5"></path>
                    <path d="M14 5.5a1.5 1.5 0 0 1 3 0v6.5"></path>
                    <path
                        d="M17 7.5a1.5 1.5 0 0 1 3 0v8.5a6 6 0 0 1 -6 6h-2h.208a6 6 0 0 1 -5.012 -2.7a69.74 69.74 0 0 1 -.196 -.3c-.312 -.479 -1.407 -2.388 -3.286 -5.728a1.5 1.5 0 0 1 .536 -2.022a1.867 1.867 0 0 1 2.28 .28l1.47 1.47"
                    >
                    </path>
                </svg>
            </button>
        @else
            <button
                class="btn btn-primary h-[52px] w-[52px] shrink-0 rounded-full p-0 max-sm:h-10 max-sm:w-10"
                @if ($app_is_demo && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image')) onclick="return toastr.info('This feature is disabled in Demo version.')" @endif
                @if ($app_is_demo && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image')) @else id="send_message_button" @endif
                form="chat_form"
            >
                <svg
                    class="rtl:-scale-x-100"
                    width="16"
                    height="14"
                    viewBox="0 0 16 14"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M0.125 14V8.76172L11.375 7.25L0.125 5.73828V0.5L15.875 7.25L0.125 14Z" />
                </svg>
            </button>
        @endif
    </form>

</div>

<template id="unselected_prompt">
    <div
        class="prompt bg- w-[48.5%] translate-x-0 translate-y-0 cursor-pointer rounded-2xl border-none shadow-[0_2px_5px_rgba(29,39,59,0.05)] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-white/[1%]">
        <div class="card-body flex justify-between gap-2">
            <div class="grow">
                <div class="prompt_header flex items-center justify-between">
                    <p class="prompt_title my-2 text-[17px] font-semibold"></p>
                </div>
                <div class="prompt-body">
                    <p class="prompt_text text-[13px] font-normal"></p>
                </div>
            </div>
            <div
                class="favbtn group/favbtn flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[--tblr-body-bg] shadow-md transition-all hover:-translate-y-1 hover:scale-105 [&.active]:bg-[--lqd-pink] dark:[&.active]:text-black">
                <svg
                    class="group-[&.active]/favbtn:hidden"
                    width="15"
                    height="13"
                    viewBox="0 0 15 13"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M7.5 13L6.4125 12.079C5.15 11.0045 4.10625 10.0777 3.28125 9.29836C2.45625 8.51907 1.8 7.81948 1.3125 7.19959C0.825 6.5797 0.484375 6.00999 0.290625 5.49046C0.096875 4.97094 0 4.4396 0 3.89646C0 2.78656 0.39375 1.85967 1.18125 1.1158C1.96875 0.371935 2.95 0 4.125 0C4.775 0 5.39375 0.129882 5.98125 0.389646C6.56875 0.64941 7.075 1.01544 7.5 1.48774C7.925 1.01544 8.43125 0.64941 9.01875 0.389646C9.60625 0.129882 10.225 0 10.875 0C12.05 0 13.0313 0.371935 13.8188 1.1158C14.6063 1.85967 15 2.78656 15 3.89646C15 4.4396 14.9031 4.97094 14.7094 5.49046C14.5156 6.00999 14.175 6.5797 13.6875 7.19959C13.2 7.81948 12.5437 8.51907 11.7188 9.29836C10.8938 10.0777 9.85 11.0045 8.5875 12.079L7.5 13ZM7.5 11.0872C8.7 10.0718 9.6875 9.20095 10.4625 8.4748C11.2375 7.74864 11.85 7.11694 12.3 6.5797C12.75 6.04246 13.0625 5.56426 13.2375 5.1451C13.4125 4.72593 13.5 4.30972 13.5 3.89646C13.5 3.18801 13.25 2.59764 12.75 2.12534C12.25 1.65304 11.625 1.41689 10.875 1.41689C10.2875 1.41689 9.74375 1.57334 9.24375 1.88624C8.74375 2.19914 8.4 2.59764 8.2125 3.08174H6.7875C6.6 2.59764 6.25625 2.19914 5.75625 1.88624C5.25625 1.57334 4.7125 1.41689 4.125 1.41689C3.375 1.41689 2.75 1.65304 2.25 2.12534C1.75 2.59764 1.5 3.18801 1.5 3.89646C1.5 4.30972 1.5875 4.72593 1.7625 5.1451C1.9375 5.56426 2.25 6.04246 2.7 6.5797C3.15 7.11694 3.7625 7.74864 4.5375 8.4748C5.3125 9.20095 6.3 10.0718 7.5 11.0872Z"
                    />
                </svg>
                <svg
                    class="hidden group-[&.active]/favbtn:block"
                    width="15"
                    height="13"
                    viewBox="0 0 14 13"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M6.64744 12.3854L5.72321 11.5568C4.65025 10.59 3.7632 9.75611 3.06206 9.05497C2.36092 8.35383 1.8032 7.72439 1.38889 7.16667C0.974578 6.60894 0.685092 6.09637 0.52043 5.62894C0.355768 5.16151 0.273438 4.68346 0.273438 4.19479C0.273438 3.19619 0.608073 2.36226 1.27734 1.69299C1.94661 1.02372 2.78055 0.689087 3.77914 0.689087C4.33155 0.689087 4.85741 0.805944 5.3567 1.03966C5.856 1.27337 6.28625 1.60269 6.64744 2.02763C7.00863 1.60269 7.43888 1.27337 7.93818 1.03966C8.43747 0.805944 8.96333 0.689087 9.51574 0.689087C10.5143 0.689087 11.3483 1.02372 12.0175 1.69299C12.6868 2.36226 13.0214 3.19619 13.0214 4.19479C13.0214 4.68346 12.9391 5.16151 12.7745 5.62894C12.6098 6.09637 12.3203 6.60894 11.906 7.16667C11.4917 7.72439 10.934 8.35383 10.2328 9.05497C9.53168 9.75611 8.64463 10.59 7.57167 11.5568L6.64744 12.3854Z"
                    />
                </svg>
            </div>
        </div>
    </div>
</template>

<template id="selected_prompt">
    <div
        class="prompt bg- w-[48.5%] translate-x-0 translate-y-0 cursor-pointer rounded-2xl border-none shadow-[0_2px_5px_rgba(29,39,59,0.05)] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-white/[1%]">
        <div class="card-body flex justify-between gap-2">
            <div class="grow">
                <div class="prompt_header flex items-center justify-between">
                    <p class="prompt_title my-2 text-[17px] font-semibold"></p>
                </div>
                <div class="prompt-body">
                    <p class="prompt_text text-[13px] font-normal"></p>
                </div>
            </div>
            <div
                class="favbtn group/favbtn flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[--tblr-body-bg] shadow-md transition-all hover:-translate-y-1 hover:scale-105 [&.active]:bg-[--lqd-pink] dark:[&.active]:text-black">
                <svg
                    class="group-[&.active]/favbtn:hidden"
                    width="15"
                    height="13"
                    viewBox="0 0 15 13"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M7.5 13L6.4125 12.079C5.15 11.0045 4.10625 10.0777 3.28125 9.29836C2.45625 8.51907 1.8 7.81948 1.3125 7.19959C0.825 6.5797 0.484375 6.00999 0.290625 5.49046C0.096875 4.97094 0 4.4396 0 3.89646C0 2.78656 0.39375 1.85967 1.18125 1.1158C1.96875 0.371935 2.95 0 4.125 0C4.775 0 5.39375 0.129882 5.98125 0.389646C6.56875 0.64941 7.075 1.01544 7.5 1.48774C7.925 1.01544 8.43125 0.64941 9.01875 0.389646C9.60625 0.129882 10.225 0 10.875 0C12.05 0 13.0313 0.371935 13.8188 1.1158C14.6063 1.85967 15 2.78656 15 3.89646C15 4.4396 14.9031 4.97094 14.7094 5.49046C14.5156 6.00999 14.175 6.5797 13.6875 7.19959C13.2 7.81948 12.5437 8.51907 11.7188 9.29836C10.8938 10.0777 9.85 11.0045 8.5875 12.079L7.5 13ZM7.5 11.0872C8.7 10.0718 9.6875 9.20095 10.4625 8.4748C11.2375 7.74864 11.85 7.11694 12.3 6.5797C12.75 6.04246 13.0625 5.56426 13.2375 5.1451C13.4125 4.72593 13.5 4.30972 13.5 3.89646C13.5 3.18801 13.25 2.59764 12.75 2.12534C12.25 1.65304 11.625 1.41689 10.875 1.41689C10.2875 1.41689 9.74375 1.57334 9.24375 1.88624C8.74375 2.19914 8.4 2.59764 8.2125 3.08174H6.7875C6.6 2.59764 6.25625 2.19914 5.75625 1.88624C5.25625 1.57334 4.7125 1.41689 4.125 1.41689C3.375 1.41689 2.75 1.65304 2.25 2.12534C1.75 2.59764 1.5 3.18801 1.5 3.89646C1.5 4.30972 1.5875 4.72593 1.7625 5.1451C1.9375 5.56426 2.25 6.04246 2.7 6.5797C3.15 7.11694 3.7625 7.74864 4.5375 8.4748C5.3125 9.20095 6.3 10.0718 7.5 11.0872Z"
                    />
                </svg>
                <svg
                    class="hidden group-[&.active]/favbtn:block"
                    width="15"
                    height="13"
                    viewBox="0 0 14 13"
                    fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M6.64744 12.3854L5.72321 11.5568C4.65025 10.59 3.7632 9.75611 3.06206 9.05497C2.36092 8.35383 1.8032 7.72439 1.38889 7.16667C0.974578 6.60894 0.685092 6.09637 0.52043 5.62894C0.355768 5.16151 0.273438 4.68346 0.273438 4.19479C0.273438 3.19619 0.608073 2.36226 1.27734 1.69299C1.94661 1.02372 2.78055 0.689087 3.77914 0.689087C4.33155 0.689087 4.85741 0.805944 5.3567 1.03966C5.856 1.27337 6.28625 1.60269 6.64744 2.02763C7.00863 1.60269 7.43888 1.27337 7.93818 1.03966C8.43747 0.805944 8.96333 0.689087 9.51574 0.689087C10.5143 0.689087 11.3483 1.02372 12.0175 1.69299C12.6868 2.36226 13.0214 3.19619 13.0214 4.19479C13.0214 4.68346 12.9391 5.16151 12.7745 5.62894C12.6098 6.09637 12.3203 6.60894 11.906 7.16667C11.4917 7.72439 10.934 8.35383 10.2328 9.05497C9.53168 9.75611 8.64463 10.59 7.57167 11.5568L6.64744 12.3854Z"
                    />
                </svg>
            </div>
        </div>
    </div>
</template>

<template id="prompt_image">
    <div class="relative m-2 rounded-[10px]">
        <button
            class="prompt_image_close absolute right-[2px] top-[2px] flex h-[20px] w-[20px] items-center justify-center rounded-[10px] border-none outline-none"
            onclick="document.getElementById('mainupscale_src').style.display = 'block';"
        >
            <svg
                class="h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>

        </button>
        <img
            class="m-0 rounded-[10px]"
            style="width: 80px;height:60px"
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAUCAYAAADskT9PAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAUmSURBVEhLtVbbaxxlFP/N7M7Mzu7sfdfNJrWlF4ut1koQqU2xtlKpoEJfCuKD/gW+SPFRBa2iKVLUJ/FFUKHggxYRLN6gIm1TjWlNY5LaJk3abLLZzV6yt7l5zje7mwtWQelvmd35vvnmnN+5r/TMiffcxcoyOnDpw5Dooyl+cX8n4LguInoA0sBrb7l779mKbCQMnyyj1jKh+X1oWTZOD1+64ySkg28MupbrgO49SHTRvUS/qr+tnB46TucAvUjk3O4L/w2sRiJDpcffPOEu5ctYrtSE1RwCmR4zO/aI4vNB01WkeuKCBCsPb+yFEgwIYqvBK9NxRPjYAJbBqlSZ1a2Gty6MXYX0xNvvuqNDE1jIFXH0wB5oPhnF5RoyiSimcov47pffEQrp6N+/C1bLgtVoYuvTB+HXmIADm3R0xOtE9tFUCvlWCxXTRDagg3Wfmc8RCbl9yoOf4j/6yRcegbFf/0SpVBPWd8TJbAKDrNBUGbsH7usS2Hx4P/wBDTZZG6UwWXSmYllo2DZZ7nmPFes+v5Amro68Nvj9sVNfeQSuDF+He20UbrMJSVXbR9iRgK3qaMQy6B/YAZMI2M0WNj9JBDQNJbOFl7fvgEPWNRwbJpHoQJd9+PDaVfhIcdeYVegQEH5xJB9i+SnsOnIUPWEVGzJJZONBZA0/AoUZ7NyyCQnDQIoqJR2NIEXK40R0uxHGw4kE0jdvYkuhiEN3ZXAomcKhRBL7KBT2uhyhnKPE5r2VfS8wTNCx0Lw1BbdRR2NqHM5SAc5yBRa5uS8Vx7Z0EhvjMfQRAVWSEaJ4z9br4nWOfSQUwsL0NMrFIpbyebG/Gqz83KiDr8/5YFmksO0UQUC2LeQ29WN0eBizUhDXE9txET0YQhb1vp04/dN5EW92Z75SxXRtGTOkvEAhYITJWomTTFG8zG+Xb9dOuuH82paOI5ExENLFhoBHgKxvRDYh99hJzD30Ku5PKnCP0fqlDVgwVRiqgkuzt1Cs1ZArl6GQMo4rlysjnEwimskgSKHRSXmQPMLoRp5uqlUbtfoMmTSLxYIlypTRbXN1y8ULj2xEr97CmXdoo3EvQos3iV2eDiswKcMncnmqPOpebdFpyoNnz/0sjOFQ7SFPnKcQ+Em6S2sODZ/kphWKKjBiXoLzupMewgMuxdSwl3Dqo1dw8v3XcUP2Y+DzSzjwwxxZ304TEuqnHtG1isB7XN8aXToFOUi/AdrT6WIvdM7KJGPpxyJqQ2WUL5RQPVuicHlPO9LhUB4otRy0Zh5NWo/UShgjl3PC3Q5sdZbcng0EqPFYuFKuUGmaKFAjWn2ZpgPjwTC03QaM/giCe6PUMj0XeJ3w4iQixDhmBDE+MycSid0kONI5Rdf+thHNNxr4cu8+IeifcGxkmHKpRfFe8d+aPsDbczSRm/4o6oEUyloCSqIXTjyLVryHStThY2uwIgrY+fxxDH72DeZmb2BychIT439gZHwa41Nz4jlPGLJK3K+HSEKZDhSg4uRzh/Hx2cvYcXeaOpkLm8pq8NshGNUFcXg9OiJ/++BFLFbq6Mmm2ztrsTZz1sILwYUJlEhA1bTFfwHuYCyc+3mA1qrk0jB6YGUYPUXDiFxYo9Z7pLcPJguiy6JKWQ+NWvL3C/NoUqvuVA9D4WH0KQ2jg8cH3eJCCcs0jLiXrIawkL5UyoFMX4oi4VDDtBCj1qzQhOQ84QH0b+AqWT+MGPMjYxB/yao0hHj23w5cs6y8Aybxf/+Q8OuxiIG/ANTLQ4Xeth7OAAAAAElFTkSuQmCC"
        />
    </div>
</template>

<template id="prompt_pdf">
    <div class="relative m-2 flex h-[80px] items-end rounded-[10px]">
        <button
            class="prompt_pdf_close absolute right-[2px] top-[2px] flex h-[20px] w-[20px] items-center justify-center rounded-[10px] border-none outline-none"
            onclick="document.getElementById('mainupscale_src').style.display = 'block';"
        >
            <svg
                class="h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"
                />
            </svg>

        </button>
        <label></label>
    </div>
</template>

<template id="prompt_image_add_btn">
    <div class="promt_image_btn m-2 h-[60px] w-[60px] rounded-[10px]">
        <button class="h-full w-full rounded-[10px] border-none outline-none hover:bg-gray-300 focus:border-none focus:ring-0">+</button>
    </div>
</template>

<template id="chat_pdf">
    <div class="mb-2 mr-[30px] flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
        <svg
            width="36"
            height="36"
            viewBox="0 0 36 36"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M23.7762 0H5.11921C4.59978 0 4.17871 0.421071 4.17871 1.23814V35.3571C4.17871 35.5789 4.59978 36 5.11921 36H30.8811C31.4005 36 31.8216 35.5789 31.8216 35.3571V8.343C31.8216 7.89557 31.7618 7.75157 31.6564 7.6455L24.1761 0.165214C24.07 0.0597857 23.926 0 23.7762 0Z"
                fill="#E9E9E0"
            />
            <path
                d="M24.1074 0.0970459V7.71426H31.7246L24.1074 0.0970459Z"
                fill="#D9D7CA"
            />
            <path
                d="M12.5445 21.4226C12.3208 21.4226 12.1061 21.35 11.9229 21.2131C11.2537 20.711 11.1637 20.1523 11.2061 19.7718C11.3231 18.7252 12.6172 17.6298 15.0536 16.5138C16.0205 14.3949 16.9404 11.7843 17.4888 9.60306C16.8472 8.20677 16.2236 6.3952 16.6781 5.33256C16.8375 4.96035 17.0362 4.67492 17.4071 4.55149C17.5537 4.50263 17.924 4.44092 18.0603 4.44092C18.3843 4.44092 18.669 4.85813 18.8709 5.11527C19.0605 5.35699 19.4906 5.86935 18.6311 9.48799C19.4977 11.2777 20.7255 13.1008 21.902 14.3493C22.7448 14.1969 23.4699 14.1191 24.0607 14.1191C25.0674 14.1191 25.6775 14.3538 25.9263 14.8372C26.132 15.2371 26.0478 15.7044 25.6755 16.2258C25.3175 16.7266 24.8238 16.9914 24.2484 16.9914C23.4667 16.9914 22.5564 16.4977 21.5413 15.5225C19.7175 15.9037 17.5878 16.5838 15.8662 17.3366C15.3288 18.4771 14.8138 19.3957 14.3343 20.0694C13.6753 20.9919 13.107 21.4226 12.5445 21.4226ZM14.2558 18.1273C12.882 18.8994 12.3221 19.5339 12.2816 19.8913C12.2752 19.9505 12.2578 20.1061 12.5587 20.3362C12.6545 20.306 13.2138 20.0508 14.2558 18.1273ZM23.0225 15.2718C23.5464 15.6748 23.6743 15.8786 24.017 15.8786C24.1674 15.8786 24.5962 15.8722 24.7948 15.5951C24.8906 15.4608 24.9279 15.3746 24.9427 15.3283C24.8636 15.2866 24.7588 15.2017 24.1873 15.2017C23.8627 15.2023 23.4545 15.2165 23.0225 15.2718ZM18.2203 11.0405C17.7607 12.6309 17.1538 14.348 16.5013 15.9031C17.8449 15.3817 19.3055 14.9266 20.6773 14.6045C19.8095 13.5965 18.9423 12.3378 18.2203 11.0405ZM17.8301 5.60063C17.7671 5.62185 16.9751 6.73013 17.8918 7.66806C18.5019 6.30842 17.8578 5.59163 17.8301 5.60063Z"
                fill="#CC4B4C"
            />
            <path
                d="M30.8811 36H5.11921C4.59978 36 4.17871 35.5789 4.17871 35.0595V25.0714H31.8216V35.0595C31.8216 35.5789 31.4005 36 30.8811 36Z"
                fill="#CC4B4C"
            />
            <path
                d="M11.176 34.0714H10.1211V27.594H11.9841C12.2592 27.594 12.5318 27.6377 12.8012 27.7258C13.0705 27.8139 13.3122 27.9456 13.5263 28.1211C13.7404 28.2966 13.9133 28.5094 14.0451 28.7582C14.1769 29.007 14.2431 29.2866 14.2431 29.5978C14.2431 29.9263 14.1872 30.2233 14.076 30.4901C13.9647 30.7569 13.8092 30.9812 13.6099 31.1625C13.4106 31.3438 13.1702 31.4846 12.8892 31.5842C12.6083 31.6839 12.2972 31.7334 11.9577 31.7334H11.1754L11.176 34.0714ZM11.176 28.3937V30.96H12.1429C12.2715 30.96 12.3987 30.9381 12.5254 30.8938C12.6514 30.8501 12.7671 30.7781 12.8725 30.6784C12.978 30.5788 13.0628 30.4399 13.1271 30.2612C13.1914 30.0825 13.2235 29.8614 13.2235 29.5978C13.2235 29.4924 13.2087 29.3702 13.1798 29.2333C13.1502 29.0957 13.0905 28.9639 12.9998 28.8379C12.9085 28.7119 12.7812 28.6065 12.6173 28.5216C12.4534 28.4368 12.2361 28.3944 11.9667 28.3944L11.176 28.3937Z"
                fill="white"
            />
            <path
                d="M20.7121 30.6527C20.7121 31.1856 20.6549 31.6414 20.5404 32.0194C20.426 32.3974 20.2814 32.7137 20.1052 32.9689C19.9291 33.2241 19.7317 33.4247 19.5119 33.5713C19.292 33.7179 19.0799 33.8271 18.8748 33.9011C18.6697 33.9744 18.482 34.0213 18.3123 34.0419C18.1426 34.0611 18.0166 34.0714 17.9343 34.0714H15.4824V27.594H17.4335C17.9786 27.594 18.4576 27.6808 18.8703 27.8531C19.283 28.0254 19.6263 28.2561 19.8989 28.5429C20.1714 28.8296 20.3746 29.1568 20.5096 29.5226C20.6446 29.889 20.7121 30.2657 20.7121 30.6527ZM17.5833 33.2981C18.2981 33.2981 18.8137 33.0699 19.13 32.6128C19.4463 32.1557 19.6044 31.4936 19.6044 30.6264C19.6044 30.357 19.5723 30.0902 19.508 29.8266C19.4431 29.5631 19.319 29.3246 19.1345 29.1105C18.95 28.8964 18.6993 28.7235 18.383 28.5917C18.0667 28.4599 17.6566 28.3937 17.1526 28.3937H16.5374V33.2981H17.5833Z"
                fill="white"
            />
            <path
                d="M23.3135 28.3937V30.4329H26.0206V31.1535H23.3135V34.0714H22.2412V27.594H26.2925V28.3937H23.3135Z"
                fill="white"
            />
        </svg>
    </div>
    <div class="mb-2 mr-[30px] flex flex-row-reverse content-end gap-[8px] lg:ms-auto">
        <a
            class="pdfpath flex"
            href=""
            target="_blank"
        >
            <label class="pdfname"></label>
            <svg
                width="17"
                height="18"
                viewBox="0 0 17 18"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <mask
                    id="mask0_3243_893"
                    style="mask-type:alpha"
                    maskUnits="userSpaceOnUse"
                    x="0"
                    y="0"
                    width="17"
                    height="18"
                >
                    <rect
                        y="0.43103"
                        width="17"
                        height="17"
                        fill="#D9D9D9"
                    />
                </mask>
                <g mask="url(#mask0_3243_893)">
                    <path
                        d="M4.45937 12.9289L3.71973 12.1892L10.69 5.21212H4.35314V4.14966H12.4989V12.2955H11.4365V5.95858L4.45937 12.9289Z"
                        fill="#1C1B1F"
                    />
                </g>
            </svg>
        </a>
    </div>
</template>
