<div class="chats-list-container flex w-1/4 shrink-0 grow-0 flex-col overflow-hidden border-b-0 border-l-0 border-r border-t-0 border-solid border-[--tblr-border-color] max-md:h-[50vh] max-md:w-full max-sm:absolute max-sm:start-0 max-sm:top-[82px] max-sm:z-20 max-sm:h-0 max-sm:overflow-hidden max-sm:border-none max-sm:bg-[--tblr-body-bg] max-sm:transition-all max-sm:duration-300 sm:!flex [&.lqd-is-active]:h-[calc(100%-82px)]"
    id="chats-list-container" x-init :class="{ 'lqd-is-active': $store.mobileChat.sidebarOpen }">
    <div
        class="chats-search border-b border-l-0 border-r-0 border-t-0 border-solid border-[--tblr-border-color] p-[20px] max-xl:p-[10px]">
        <form class="chats-search-form relative" action="#">
            <span class="input-icon-addon">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                    <path d="M21 21l-6 -6" />
                </svg>
            </span>
            <input class="form-control navbar-search-input peer max-lg:!rounded-md" id="chat_search_word"
                data-category-id="{{ $category->id }}" type="search" onkeydown="return event.key != 'Enter';"
                placeholder="{{ __('Search') }}" aria-label="{{ __('Search in website') }}">
        </form>
    </div>
    <div class="chats-list grow-0 overflow-hidden" id="chat_sidebar_container">
        @include('panel.user.openai_chat.components.chat_sidebar_list')
    </div>
    @if (isset($category) && $category->slug == 'ai_pdf')
        <div class="chats-new mt-auto px-6 py-11 max-xl:p-4">
            <input type="file" id="selectDocInput" style="display: none;" accept=".pdf, .csv, .docx" />
            <button class="btn btn-primary flex w-full items-center justify-center gap-[10px] py-2"
                href="javascript:void(0);" id="btn_upload_document"
                @if ($app_is_demo && (isset($category) && $category->slug == 'ai_pdf')) onclick="return toastr.info('This feature is disabled in Demo version.')" @else onclick="return $('#selectDocInput').click()" @endif>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.20312 1.33331V10.6666" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M1.53613 6H10.8695" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                {{ __('Upload Document') }}
            </button>
        </div>
    @else
        <div class="chats-new mt-auto px-6 py-11 max-xl:p-4">

            <a class="btn btn-primary flex w-full items-center justify-center gap-[10px] py-2"
                href="javascript:void(0);"
                @if ($app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_chat_image'))) onclick="return toastr.info('This feature is disabled in Demo version.')" @else onclick="return startNewChat({{ $category->id }}, '{{ LaravelLocalization::getCurrentLocale() }}');" @endif>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.20312 1.33331V10.6666" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M1.53613 6H10.8695" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                {{ __('New Conversation') }}
            </a>
        </div>
    @endif
</div>
