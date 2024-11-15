<div
    class="page-body mt-2 relative after:h-px after:w-full after:bg-[var(--tblr-body-bg)] after:absolute after:top-full after:left-0 after:-mt-px">
    <div class="container-fluid">
        <div class="row">
            @foreach ($aiList as $entry)
                <div data-filter="medical" category="{{ $entry->category }}" id="{{ $entry->id }}"
                    class="chat_element col-lg-4 col-xl-3 col-md-6 py-8 10 px-16 relative border-b border-solid border-t-0 border-s-0 border-[var(--tblr-border-color)] group max-xl:px-10 pt-[48px]">
                    <div class="absolute top-[10px] left-[20px] fav_chat" id="{{ $entry->id }}">
                        <button
                            class="flex justify-center items-center w-[34px] h-[34px] cursor-pointer rounded-full bg-white/5 text-heading border-none shadow-sm transition-all hover:scale-105 hover:-translate-y-1 hover:!shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                fill="none" stroke="currentColor" class="unselected">
                                <path
                                    d="M7.99794 11.8333L3.88327 13.9966L4.66927 9.41459L1.33594 6.16993L5.93594 5.50326L7.99327 1.33459L10.0506 5.50326L14.6506 6.16993L11.3173 9.41459L12.1033 13.9966L7.99794 11.8333Z"
                                    stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg" class="selected">
                                <path
                                    d="M7.99989 11.8333L3.88522 13.9966L4.67122 9.41459L1.33789 6.16993L5.93789 5.50326L7.99522 1.33459L10.0526 5.50326L14.6526 6.16993L11.3192 9.41459L12.1052 13.9966L7.99989 11.8333Z"
                                    stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                    @if ($entry->plan == 'premium')
                        <div class="absolute top-[10px] right-[20px]">
                            <div class="flex items-center gap-1 bg-[--lqd-pink] rounded-[4px] p-2 text-black">
                                <svg width="19" height="15" viewBox="0 0 19 15" fill="none"
                                    stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7.75 7.5002L6 5.5752L6.525 4.7002M4.25 1.375H14.75L17.375 5.75L9.9375 14.0625C9.88047 14.1207 9.8124 14.1669 9.73728 14.1985C9.66215 14.2301 9.58149 14.2463 9.5 14.2463C9.41851 14.2463 9.33785 14.2301 9.26272 14.1985C9.1876 14.1669 9.11953 14.1207 9.0625 14.0625L1.625 5.75L4.25 1.375Z"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <label class="text-[11px] font-medium color-[#000]">Premium</label>
                            </div>
                        </div>
                    @endif
                    <div class="flex flex-col justify-center text-center relative">
                        <div class="inline-flex items-center justify-center w-[128px] h-[128px] rounded-full mx-auto mb-6 transition-shadow text-[44px] font-medium overflow-hidden border-solid border-[6px] !border-white shadow-[0_1px_2px_rgba(0,0,0,0.07)] text-[rgba(0,0,0,0.65)] whitespace-nowrap overflow-ellipsis dark:!border-current group-hover:shadow-xl"
                            style="background: {{ $entry->color }};">
                            @if ($entry->slug === 'ai-chat-bot')
                                <img class="w-full h-full object-cover object-center" src="/assets/img/chat-default.jpg"
                                    alt="{{ __($entry->name) }}">
                            @elseif ($entry->image)
                                <img class="w-full h-full object-cover object-center" src="/{{ $entry->image }}"
                                    alt="{{ __($entry->name) }}">
                            @else
                                <span
                                    class="block w-full text-center whitespace-nowrap overflow-hidden overflow-ellipsis">{{ __($entry->short_name) }}</span>
                            @endif
                        </div>
                        <h3 class="mb-0 chat_name">{{ __($entry->name) }}</h3>
                        <p class="text-muted chat_description">{{ __($entry->description) }}</p>
                        <!-- link to the chat -->
                        <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.chat.chat', $entry->slug)) }}"
                            class="block w-full h-full absolute top-0 left-0 z-2"></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
