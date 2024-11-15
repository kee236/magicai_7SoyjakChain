<ul class="chat-list-ul m-0 h-full list-none overflow-auto p-0 text-[14px]">
    @foreach ($list as $entry)
        <li
            class="chat-list-item @if (isset($chat)) {{ $chat->id == $entry->id ? 'active' : '' }} @endif group relative border-b border-l-0 border-r-0 border-t-0 border-solid border-[--tblr-border-color] [&.active]:before:absolute [&.active]:before:left-0 [&.active]:before:top-[25%] [&.active]:before:h-[50%] [&.active]:before:w-[3px] [&.active]:before:bg-gradient-to-b [&.active]:before:from-[--tblr-primary] [&.active]:before:to-transparent [&.active]:before:content-['']"
            id="chat_{{ $entry->id }}"
        >
            <div
                class="flex cursor-pointer items-center gap-3 p-[20px] text-[--lqd-heading-color] hover:text-[--tblr-primary] hover:no-underline group-[&.edit-mode]:pointer-events-none"
                onclick="return openChatAreaContainer({{ $entry->id }});"
            >
                <span class="shrink-0">
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M10 10V10.0108M5.66699 10V10.0108M14.333 10V10.0108M1 19L2.29899 14.6183C1.17631 12.7513 0.770172 10.5404 1.1561 8.39656C1.54202 6.25276 2.69374 4.32196 4.39712 2.96318C6.1005 1.6044 8.23963 0.910088 10.4168 1.00934C12.5939 1.1086 14.6609 1.99466 16.2334 3.50279C17.806 5.01092 18.777 7.03849 18.9661 9.20851C19.1551 11.3785 18.5493 13.5433 17.2612 15.3004C15.9731 17.0575 14.0905 18.2872 11.9633 18.7611C9.83606 19.2349 7.60906 18.9206 5.69635 17.8765L1 19Z"
                            stroke="var(--lqd-heading-color)"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </span>
                <span class="flex grow flex-col overflow-hidden">
                    <span class="chat-item-title w-full group-[&.edit-mode]:pointer-events-auto">{{ __($entry->title) }}</span>
                    @if ($entry->reference_url != '')
                        <a
                            class="chat-item-title text-nowrap w-full overflow-hidden text-ellipsis group-[&.edit-mode]:pointer-events-auto"
                            target="_blank"
                            title="{{ $entry->reference_url }}"
                            onclick="event.stopPropagation();"
                            href="{{ $entry->reference_url }}"
                        >{{ __($entry->doc_name) }}</a>
                    @endif
                    <span class="text-muted text-[11px]">{{ $entry->updated_at->diffForHumans() }}</span>
                </span>
            </div>
            <span
                class="absolute end-4 top-1/2 -translate-y-1/2 before:pointer-events-none before:absolute before:-inset-9 before:z-0 before:bg-[radial-gradient(closest-side,var(--tblr-body-bg)_50%,transparent)] before:opacity-0 before:transition-all group-hover:before:opacity-100"
            >
                <button
                    class="@if ($app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) @else  chat-item-update-title @endif btn relative h-[28px] w-[28px] border-[1px] border-solid border-[--tblr-border-color] bg-[--tblr-body-bg] p-0 opacity-0 group-hover:!opacity-100 group-[&.edit-mode]:!bg-[--tblr-green] group-[&.edit-mode]:!opacity-100 dark:bg-[rgba(255,255,255,0.08)] max-sm:!opacity-100"
                    @if ($app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) onclick="return toastr.info('This feature is disabled in Demo version.')" @endif
                >
                    <svg
                        class="group-[&.edit-mode]:hidden"
                        width="13"
                        height="11"
                        viewBox="0 0 13 11"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M1.5293 10.2941H4.15418L11.0445 3.7664C11.3926 3.43664 11.5881 2.98938 11.5881 2.52303C11.5881 2.05668 11.3926 1.60943 11.0445 1.27967C10.6964 0.949906 10.2243 0.764648 9.73205 0.764648C9.23979 0.764648 8.76769 0.949906 8.41961 1.27967L1.5293 7.80733V10.2941Z"
                            stroke="currentColor"
                            stroke-width="1.15"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M8.23535 1.82349L10.4706 3.94113"
                            stroke="currentColor"
                            stroke-width="1.15"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    <svg
                        class="hidden group-[&.edit-mode]:block"
                        xmlns="http://www.w3.org/2000/svg"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="white"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                </button>
                <button
                    class="@if ($app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) @else chat-item-delete @endif btn bg-red border-red relative h-[28px] w-[28px] border-[1px] border-solid p-0 opacity-0 group-hover:!opacity-100 group-[&.edit-mode]:hidden max-sm:!opacity-100"
                    @if ($app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'))) onclick="return toastr.info('This feature is disabled in Demo version.')" @endif
                >
                    <svg
                        width="10"
                        height="10"
                        viewBox="0 0 10 10"
                        fill="white"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"
                        >
                        </path>
                    </svg>
                </button>
            </span>
        </li>
    @endforeach
</ul>
