@extends('panel.layout.app')
@section('title', __('Marketplace'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center justify-between max-md:flex-col max-md:items-start max-md:gap-4">
                <div class="col">
                    <a href="/dashboard" class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2 w-fit">
                        {{ __('Marketplace') }}
                    </h2>
                </div>
                <div class="flex items-center w-fit max-md:flex-col">
                    <button id="manage_add_ons" type="button"
                        onclick="window.location.href='{{ route('dashboard.admin.marketplace.liextension') }}'"
                        class="btn bg-[#fff] hover:bg-[#c1bDFF] text-[#000] flex items-center group dark:bg-white/5 dark:text-white mx-2">
                        Manage Addons
                    </button>
                    <button id="browse_add_ons"
                        onclick="window.location.href='{{ route('dashboard.admin.marketplace.index') }}'"
                        class="btn btn-primary flex items-center group mx-2">
                        <span class="group-[.lqd-form-submitting]:hidden"><svg width="17" height="16"
                                viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.5 3.33331V12.6666" stroke="#F8FAFC" stroke-width="1.25" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M3.83337 8H13.1667" stroke="#F8FAFC" stroke-width="1.25" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            Browse Add-ons</span>
                    </button>
                </div>
            </div>
            <div class="flex flex-row justify-start mt-4">
                <div class="row g-2 flex justify-start w-full">
                    <div class="input-icon">
                        <span class="input-icon-addon ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="17" height="17"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                <path d="M21 21l-6 -6"></path>
                            </svg>
                        </span>
                        <input type="search"
                            class="form-control navbar-search-input peer max-lg:!rounded-md dark:!bg-zinc-900"
                            id="search_str" placeholder="Search" aria-label="Search in website">
                    </div>
                </div>
            </div>
            <div class="flex flex-row justify-start items-center mt-3 gap-3">
                <button
                    class="addons_filter text-[#2B2F37] bg-[--lqd-header-search-bg] font-medium text-[13px] rounded-[55px] px-2 py-1 cursor-pointer bg-none border-none transition-all [&.active]:bg-[--tblr-primary] [&.active]:text-white active dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5"
                    data-filter="All">All</button>
                <button
                    class="addons_filter text-[#2B2F37] bg-[--lqd-header-search-bg] font-medium text-[13px] rounded-[55px] px-2 py-1 cursor-pointer bg-none border-none transition-all [&.active]:bg-[--tblr-primary] [&.active]:text-white dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5"
                    data-filter="Installed">Installed</button>
                <button
                    class="addons_filter text-[#2B2F37] bg-[--lqd-header-search-bg] font-medium text-[13px] rounded-[55px] px-2 py-1 cursor-pointer bg-none border-none transition-all [&.active]:bg-[--tblr-primary] [&.active]:text-white dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5"
                    data-filter="Free">Free</button>
                <button
                    class="addons_filter text-[#2B2F37] bg-[--lqd-header-search-bg] font-medium text-[13px] rounded-[55px] px-2 py-1 cursor-pointer bg-none border-none transition-all [&.active]:bg-[--tblr-primary] [&.active]:text-white dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5"
                    data-filter="Paid">Paid</button>
                {{-- @foreach ($categoryList as $category)
                    <button class="chat_filter text-[#2B2F37] bg-[--lqd-header-search-bg] font-medium text-[13px] rounded-[55px] px-2 py-1 cursor-pointer bg-none border-none transition-all [&.active]:bg-[--tblr-primary] [&.active]:text-white" data-filter="{{$category->name}}">{{$category->name}}</button>
                @endforeach --}}
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="flex flex-wrap justify-start relative">
                @foreach ($extensions as $extension)
                    <div class="extension rounded-[20px] border-[1px] border-[#f3f3f3] w-[350px] border-solid m-2 flex flex-col p-[30px] hover:shadow-md relative dark:border-white/5 cursor-pointer"
                        data-price="{{ $extension->price }}" data-installed="{{ $extension->installed }}"
                        data-name="{{ $extension->name }}"
                        onclick="window.location.href='{{ route('dashboard.admin.marketplace.extension', ['slug' => $extension->slug]) }}'">
                        @if (trim($extension->badge, ' ') != "")
                            <div class="badge absolute right-[10px] top-[10px] bg-[#FFF1DB] rounded-[4px]">
                                <p class="text-[#242425] text-[10px] font-semibold m-0 px-[6px] py-[2px]">
                                    {{ $extension->badge }}</p>
                            </div>
                        @endif
                        <div class="img_tag w-[53px] h-[53p] rounded-[10px] flex items-center">
                            <img src="{{ $extension->image_url }}">
                            <div class="flex mx-3 items-center {{ $extension->installed == 1 ? '' : 'hidden' }}">
                                <svg width="12" height="13" viewBox="0 0 12 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="5.5" cy="7" r="4.5" fill="#30A473" />
                                </svg>
                                <p class="mx-1 text-[14px] font-medium m-0 dark:text-white">Installed</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <p class="my-[20px] text-[17px] font-semibold">{{ $extension->name }}
                            </p>
                            <div class="review flex items-center mx-2">
                                <svg width="12" height="13" viewBox="0 0 12 13" fill="none" 
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M5.57168 1.07645L4.02518 4.40833L0.32768 4.88458C0.256413 4.89364 0.189249 4.92299 0.13418 4.96912C0.0791097 5.01526 0.0384512 5.07624 0.0170407 5.14482C-0.00436984 5.2134 -0.00563118 5.28668 0.0134067 5.35595C0.0324446 5.42523 0.0709804 5.48757 0.12443 5.53558L2.8593 7.99745L2.12243 11.6919C2.10825 11.7628 2.11481 11.8362 2.14133 11.9033C2.16785 11.9705 2.2132 12.0286 2.27193 12.0706C2.33066 12.1126 2.40027 12.1368 2.4724 12.1402C2.54454 12.1436 2.61612 12.1261 2.67856 12.0898L5.91143 10.216L9.14581 12.0898C9.2082 12.126 9.27969 12.1433 9.35172 12.1399C9.42374 12.1364 9.49324 12.1123 9.55188 12.0703C9.61053 12.0284 9.65584 11.9704 9.68239 11.9034C9.70893 11.8363 9.71559 11.7631 9.70156 11.6923L8.96468 7.99745L11.6996 5.53558C11.7529 5.48758 11.7913 5.42529 11.8103 5.3561C11.8293 5.28691 11.828 5.21373 11.8067 5.14523C11.7853 5.07674 11.7448 5.01581 11.6898 4.96968C11.6349 4.92354 11.5678 4.89414 11.4967 4.88495L7.79918 4.40833L6.25155 1.07645C6.22145 1.01161 6.17344 0.956728 6.11318 0.918269C6.05292 0.879809 5.98292 0.859375 5.91143 0.859375C5.83994 0.859375 5.76994 0.879809 5.70968 0.918269C5.64942 0.956728 5.60141 1.01161 5.57131 1.07645H5.57168Z"
                                        fill="#a0a000" />
                                </svg>
                                <p class="my-0 mx-1">{{ number_format($extension->review, 1) }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[16px] color-[#485061] font-normal">{{ $extension->description }}
                        </div>
                        @php
                            $tags = explode(',', $extension->category);
                        @endphp
                        <div class="flex absolute bottom-[20px]">
                            @foreach ($tags as $tag)
                                <div class="mx-1 flex"> {{ $tag }}<div
                                        class="flex items-center justify-center mx-1 p-0"><svg width="5"
                                            height="6" viewBox="0 0 5 6" fill="none"2
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="2.5" cy="3" r="2.5" fill="#F0F0F0" />
                                        </svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                {{-- <div class="rounded-[20px] border-[1px] border-[#f3f3f3] w-[350px] border-solid m-2 flex flex-col p-[30px]">
                    <div class="img_tag w-[53px] h-[53px] bg-black rounded-[10px]">
                    </div>
                    <p class="my-[20px] text-[17px] font-semibold">Mailchimp</p>
                    <p class="text-[16px] color-[#485061] font-normal">Say goodbye to writer's block and hello to
                        endlesss
                        content possiblites. Tranform your ideas into content.</p>
                </div>
                <div class="rounded-[20px] border-[1px] border-[#f3f3f3] w-[350px] border-solid m-2 flex flex-col p-[30px]">
                    <div class="img_tag w-[53px] h-[53px] bg-black rounded-[10px]">
                    </div>
                    <p class="my-[20px] text-[17px] font-semibold">Mailchimp</p>
                    <p class="text-[16px] color-[#485061] font-normal">Say goodbye to writer's block and hello to
                        endlesss
                        content possiblites. Tranform your ideas into content.</p>
                </div> --}}
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="/assets/js/panel/marketplace.js"></script>
@endsection
