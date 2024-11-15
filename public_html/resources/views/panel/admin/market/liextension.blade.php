@extends('panel.layout.app')
@section('title', 'Marketplace')

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
                    <div class="extension rounded-[20px] border-[1px] border-[#f3f3f3] w-full border-solid m-2 flex items-center p-[20px] relative dark:border-white/5"
                        data-price="{{ $extension->price }}" data-installed="{{ $extension->installed }}"
                        data-name="{{ $extension->slug }}">
                        <div class="img_tag w-[53px] h-[53px] rounded-[10px] mr-[30px] flex justify-center items-center">
                            <img src="{{ $extension->image_url }}">
                        </div>
                        <div class="flex flex-col">
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
                                <p class="text-[16px] color-[#485061] font-normal">{{ $extension->short_description }}
                            </div>
                        </div>
                        <div class="absolute right-[20px] flex items-center">
                            {{-- <svg width="79" height="14" viewBox="0 0 79 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13.9286 5.55859C13.9286 5.68136 13.856 5.81529 13.7109 5.96038L10.6724 8.92355L11.3923 13.1088C11.3979 13.1479 11.4007 13.2037 11.4007 13.2762C11.4007 13.3934 11.37 13.4911 11.3086 13.5692C11.2528 13.6529 11.1691 13.6948 11.0575 13.6948C10.9515 13.6948 10.8398 13.6613 10.7227 13.5943L6.96429 11.6189L3.20592 13.5943C3.08315 13.6613 2.97154 13.6948 2.87109 13.6948C2.75391 13.6948 2.66462 13.6529 2.60324 13.5692C2.54743 13.4911 2.51953 13.3934 2.51953 13.2762C2.51953 13.2427 2.52511 13.1869 2.53627 13.1088L3.25614 8.92355L0.209263 5.96038C0.0697545 5.80971 0 5.67578 0 5.55859C0 5.35212 0.15625 5.22377 0.46875 5.17355L4.67076 4.5625L6.55413 0.753905C6.66016 0.525111 6.79688 0.410714 6.96429 0.410714C7.1317 0.410714 7.26842 0.525111 7.37444 0.753905L9.25781 4.5625L13.4598 5.17355C13.7723 5.22377 13.9286 5.35212 13.9286 5.55859ZM30.1092 5.55859C30.1092 5.68136 30.0367 5.81529 29.8916 5.96038L26.8531 8.92355L27.573 13.1088C27.5785 13.1479 27.5813 13.2037 27.5813 13.2762C27.5813 13.3934 27.5506 13.4911 27.4893 13.5692C27.4335 13.6529 27.3497 13.6948 27.2381 13.6948C27.1321 13.6948 27.0205 13.6613 26.9033 13.5943L23.145 11.6189L19.3866 13.5943C19.2638 13.6613 19.1522 13.6948 19.0518 13.6948C18.9346 13.6948 18.8453 13.6529 18.7839 13.5692C18.7281 13.4911 18.7002 13.3934 18.7002 13.2762C18.7002 13.2427 18.7058 13.1869 18.7169 13.1088L19.4368 8.92355L16.3899 5.96038C16.2504 5.80971 16.1807 5.67578 16.1807 5.55859C16.1807 5.35212 16.3369 5.22377 16.6494 5.17355L20.8514 4.5625L22.7348 0.753905C22.8408 0.525111 22.9775 0.410714 23.145 0.410714C23.3124 0.410714 23.4491 0.525111 23.5551 0.753905L25.4385 4.5625L29.6405 5.17355C29.953 5.22377 30.1092 5.35212 30.1092 5.55859ZM46.2899 5.55859C46.2899 5.68136 46.2174 5.81529 46.0723 5.96038L43.0338 8.92355L43.7536 13.1088C43.7592 13.1479 43.762 13.2037 43.762 13.2762C43.762 13.3934 43.7313 13.4911 43.6699 13.5692C43.6141 13.6529 43.5304 13.6948 43.4188 13.6948C43.3128 13.6948 43.2012 13.6613 43.084 13.5943L39.3256 11.6189L35.5672 13.5943C35.4445 13.6613 35.3329 13.6948 35.2324 13.6948C35.1152 13.6948 35.0259 13.6529 34.9646 13.5692C34.9088 13.4911 34.8809 13.3934 34.8809 13.2762C34.8809 13.2427 34.8864 13.1869 34.8976 13.1088L35.6175 8.92355L32.5706 5.96038C32.4311 5.80971 32.3613 5.67578 32.3613 5.55859C32.3613 5.35212 32.5176 5.22377 32.8301 5.17355L37.0321 4.5625L38.9155 0.753905C39.0215 0.525111 39.1582 0.410714 39.3256 0.410714C39.493 0.410714 39.6297 0.525111 39.7358 0.753905L41.6191 4.5625L45.8212 5.17355C46.1337 5.22377 46.2899 5.35212 46.2899 5.55859ZM62.4706 5.55859C62.4706 5.68136 62.398 5.81529 62.2529 5.96038L59.2144 8.92355L59.9343 13.1088C59.9399 13.1479 59.9427 13.2037 59.9427 13.2762C59.9427 13.3934 59.912 13.4911 59.8506 13.5692C59.7948 13.6529 59.7111 13.6948 59.5995 13.6948C59.4934 13.6948 59.3818 13.6613 59.2646 13.5943L55.5063 11.6189L51.7479 13.5943C51.6251 13.6613 51.5135 13.6948 51.4131 13.6948C51.2959 13.6948 51.2066 13.6529 51.1452 13.5692C51.0894 13.4911 51.0615 13.3934 51.0615 13.2762C51.0615 13.2427 51.0671 13.1869 51.0783 13.1088L51.7981 8.92355L48.7513 5.96038C48.6117 5.80971 48.542 5.67578 48.542 5.55859C48.542 5.35212 48.6982 5.22377 49.0107 5.17355L53.2128 4.5625L55.0961 0.753905C55.2021 0.525111 55.3389 0.410714 55.5063 0.410714C55.6737 0.410714 55.8104 0.525111 55.9164 0.753905L57.7998 4.5625L62.0018 5.17355C62.3143 5.22377 62.4706 5.35212 62.4706 5.55859Z"
                                    fill="black" />
                                <path
                                    d="M78.6512 5.55859C78.6512 5.68136 78.5787 5.81529 78.4336 5.96038L75.3951 8.92355L76.115 13.1088C76.1205 13.1479 76.1233 13.2037 76.1233 13.2762C76.1233 13.3934 76.0926 13.4911 76.0313 13.5692C75.9754 13.6529 75.8917 13.6948 75.7801 13.6948C75.6741 13.6948 75.5625 13.6613 75.4453 13.5943L71.6869 11.6189L67.9286 13.5943C67.8058 13.6613 67.6942 13.6948 67.5938 13.6948C67.4766 13.6948 67.3873 13.6529 67.3259 13.5692C67.2701 13.4911 67.2422 13.3934 67.2422 13.2762C67.2422 13.2427 67.2478 13.1869 67.2589 13.1088L67.9788 8.92355L64.9319 5.96038C64.7924 5.80971 64.7227 5.67578 64.7227 5.55859C64.7227 5.35212 64.8789 5.22377 65.1914 5.17355L69.3934 4.5625L71.2768 0.753905C71.3828 0.525111 71.5195 0.410714 71.6869 0.410714C71.8544 0.410714 71.9911 0.525111 72.0971 0.753905L73.9805 4.5625L78.1825 5.17355C78.495 5.22377 78.6512 5.35212 78.6512 5.55859Z"
                                    fill="black" fill-opacity="0.09" />
                            </svg> --}}
                            <div class="btn_installed mx-[20px] flex items-center justify-center {{ $extension->installed == 1 ? '' : 'hidden' }}"
                                data-name="{{ $extension->slug }}">
                                <button class="btn btn-primary svg_install">Uninstall</button>
                                {{-- <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-trash svg_install" width="44" height="44"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="#070707" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 7l16 0" />
                                    <path d="M10 11l0 6" />
                                    <path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                                <svg class="svg_loading hidden animate-spin" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_3091_21590" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_3091_21590)">
                                        <path
                                            d="M12 22C10.6333 22 9.34167 21.7375 8.125 21.2125C6.90833 20.6875 5.84583 19.9708 4.9375 19.0625C4.02917 18.1542 3.3125 17.0917 2.7875 15.875C2.2625 14.6583 2 13.3667 2 12C2 10.6167 2.2625 9.32083 2.7875 8.1125C3.3125 6.90417 4.02917 5.84583 4.9375 4.9375C5.84583 4.02917 6.90833 3.3125 8.125 2.7875C9.34167 2.2625 10.6333 2 12 2C12.2833 2 12.5208 2.09583 12.7125 2.2875C12.9042 2.47917 13 2.71667 13 3C13 3.28333 12.9042 3.52083 12.7125 3.7125C12.5208 3.90417 12.2833 4 12 4C9.78333 4 7.89583 4.77917 6.3375 6.3375C4.77917 7.89583 4 9.78333 4 12C4 14.2167 4.77917 16.1042 6.3375 17.6625C7.89583 19.2208 9.78333 20 12 20C14.2167 20 16.1042 19.2208 17.6625 17.6625C19.2208 16.1042 20 14.2167 20 12C20 11.7167 20.0958 11.4792 20.2875 11.2875C20.4792 11.0958 20.7167 11 21 11C21.2833 11 21.5208 11.0958 21.7125 11.2875C21.9042 11.4792 22 11.7167 22 12C22 13.3667 21.7375 14.6583 21.2125 15.875C20.6875 17.0917 19.9708 18.1542 19.0625 19.0625C18.1542 19.9708 17.0958 20.6875 15.8875 21.2125C14.6792 21.7375 13.3833 22 12 22Z"
                                            fill="#1C1B1F" />
                                    </g>
                                </svg> --}}
                                <button class="btn btn-primary svg_loading hidden" disabled>Uninstall</button>
                            </div>
                            <div class="btn_install mx-[20px] flex items-center justify-center {{ $extension->installed == 0 ? '' : 'hidden' }}"
                                data-name="{{ $extension->slug }}">
                                <button class="btn btn-primary svg_install svg_installed">Install</button>
                                <button class="btn btn-primary svg_loading hidden" disabled>Install</button>
                                {{-- <svg class="svg_install svg_installed" width="54" height="54" viewBox="0 0 54 54"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="27" cy="27" r="26.5" fill="white" stroke="#F8F8F8" />
                                    <path d="M26 36V28H18V26H26V18H28V26H36V28H28V36H26Z" fill="#1C1B1F" />
                                </svg> --}}
                                {{-- <svg class="svg_loading hidden animate-spin" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_3091_21590" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_3091_21590)">
                                        <path
                                            d="M12 22C10.6333 22 9.34167 21.7375 8.125 21.2125C6.90833 20.6875 5.84583 19.9708 4.9375 19.0625C4.02917 18.1542 3.3125 17.0917 2.7875 15.875C2.2625 14.6583 2 13.3667 2 12C2 10.6167 2.2625 9.32083 2.7875 8.1125C3.3125 6.90417 4.02917 5.84583 4.9375 4.9375C5.84583 4.02917 6.90833 3.3125 8.125 2.7875C9.34167 2.2625 10.6333 2 12 2C12.2833 2 12.5208 2.09583 12.7125 2.2875C12.9042 2.47917 13 2.71667 13 3C13 3.28333 12.9042 3.52083 12.7125 3.7125C12.5208 3.90417 12.2833 4 12 4C9.78333 4 7.89583 4.77917 6.3375 6.3375C4.77917 7.89583 4 9.78333 4 12C4 14.2167 4.77917 16.1042 6.3375 17.6625C7.89583 19.2208 9.78333 20 12 20C14.2167 20 16.1042 19.2208 17.6625 17.6625C19.2208 16.1042 20 14.2167 20 12C20 11.7167 20.0958 11.4792 20.2875 11.2875C20.4792 11.0958 20.7167 11 21 11C21.2833 11 21.5208 11.0958 21.7125 11.2875C21.9042 11.4792 22 11.7167 22 12C22 13.3667 21.7375 14.6583 21.2125 15.875C20.6875 17.0917 19.9708 18.1542 19.0625 19.0625C18.1542 19.9708 17.0958 20.6875 15.8875 21.2125C14.6792 21.7375 13.3833 22 12 22Z"
                                            fill="#1C1B1F" />
                                    </g>
                                </svg> --}}


                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="/assets/js/panel/marketplace.js"></script>
@endsection
