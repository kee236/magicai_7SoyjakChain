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
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="flex flex-col md:flex-row">
                <div
                    class="w-full mb-3 relative mx-[15px]  rounded-[20px] flex flex-col border-[1px] border-solid border-[#f9f9f9] dark:border-white/5">
                    {{-- shadow-[0_4px_44px_0_rgba(113,136,197,0.07)] --}}
                    <div class="m-3">
                        <img src="{{ $extension->image_url }}" class="w-[89px] h-[89px]">
                    </div>
                    <div class="m-3 flex items-center">
                        <p class="text-[23px] font-semibold dark:text-white m-0">{{ $extension->name }}</p>
                        @if ($extension->installed)
                            <p class="text-[12px] font-medium text-[#3C344B] flex items-center mx-3 my-0"><svg
                                    width="12" height="13" viewBox="0 0 12 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="5.5" cy="7" r="4.5" fill="#30A473" />
                                </svg>
                                {{ __('Installed') }}</p>
                        @endif
                    </div>
                    <div class="m-3 flex items-center">
                        <div class="flex items-center">
                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.7619 7.99999L14.9028 5.87428L15.1619 3.06285L12.4114 2.43809L10.9714 0L8.38094 1.11238L5.79047 0L4.35047 2.43047L1.6 3.04762L1.85905 5.86666L0 7.99999L1.85905 10.1257L1.6 12.9447L4.35047 13.5695L5.79047 16L8.38094 14.88L10.9714 15.9924L12.4114 13.5619L15.1619 12.9371L14.9028 10.1257L16.7619 7.99999ZM6.92571 11.5962L4.03047 8.69332L5.15809 7.56571L6.92571 9.34094L11.3828 4.86857L12.5105 5.99618L6.92571 11.5962Z"
                                    fill="#347AE2" />
                            </svg>
                            <p class="text-[15px] font-medium text-[#354D6E] dark:text-white/80 mx-1 my-0">Tested with
                                MagicAI</p>
                        </div>
                        {{-- <div class="w-[1px] h-[20px] bg-[#E1E5EB] mx-[25px]"></div>
                        <div class="flex items-center">
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M9.37843 1.125C9.664 1.12427 9.92521 1.28578 10.0522 1.54158L12.1997 5.86865L16.9891 6.56701C17.2712 6.60815 17.5056 6.80568 17.594 7.07674C17.6823 7.3478 17.6093 7.64552 17.4056 7.845L13.922 11.2567L14.7492 15.996C14.7985 16.2784 14.6825 16.5642 14.4504 16.7324C14.2183 16.9005 13.9106 16.9217 13.6576 16.7869L9.38039 14.5071L5.10411 16.7868C4.851 16.9218 4.5431 16.9005 4.31092 16.7321C4.07874 16.5637 3.96294 16.2776 4.01261 15.9951L4.84579 11.2567L1.35658 7.84545C1.15256 7.646 1.07938 7.34805 1.16779 7.07677C1.2562 6.80549 1.49088 6.60787 1.77325 6.56693L6.58861 5.86867L8.70684 1.54503C8.83248 1.28858 9.09286 1.12574 9.37843 1.125ZM9.38471 3.57082L7.76149 6.88407C7.65252 7.1065 7.44074 7.26079 7.19561 7.29634L3.49021 7.83364L6.17727 10.4606C6.35429 10.6337 6.43451 10.883 6.39164 11.1268L5.75645 14.7392L9.02753 12.9954C9.24804 12.8778 9.51261 12.8778 9.73312 12.9953L13.0074 14.7405L12.3765 11.1259C12.3341 10.8825 12.4141 10.6339 12.5906 10.4611L15.2743 7.83283L11.5944 7.29625C11.3509 7.26074 11.1402 7.10799 11.0308 6.88752L9.38471 3.57082Z"
                                    fill="#354D6E" />
                            </svg>

                            <p class="text-[15px] font-medium text-[#354D6E] dark:text-white/80 mx-1 my-0">
                                {{ number_format($extension->review, 1) }}</p>
                        </div> --}}
                        <div class="w-[1px] h-[20px] bg-[#E1E5EB] mx-[25px]"></div>
                        <div class="flex items-center">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_3091_21346" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                    width="20" height="20">
                                    <rect width="20" height="20" fill="#D9D9D9" />
                                </mask>
                                <g mask="url(#mask0_3091_21346)">
                                    <path
                                        d="M12.7084 4.16666C12.4167 4.16666 12.1702 4.06596 11.9688 3.86457C11.7674 3.66318 11.6667 3.41666 11.6667 3.12499C11.6667 2.83332 11.7674 2.5868 11.9688 2.38541C12.1702 2.18402 12.4167 2.08332 12.7084 2.08332C13.0001 2.08332 13.2466 2.18402 13.448 2.38541C13.6494 2.5868 13.7501 2.83332 13.7501 3.12499C13.7501 3.41666 13.6494 3.66318 13.448 3.86457C13.2466 4.06596 13.0001 4.16666 12.7084 4.16666ZM12.7084 17.9167C12.4167 17.9167 12.1702 17.816 11.9688 17.6146C11.7674 17.4132 11.6667 17.1667 11.6667 16.875C11.6667 16.5833 11.7674 16.3368 11.9688 16.1354C12.1702 15.934 12.4167 15.8333 12.7084 15.8333C13.0001 15.8333 13.2466 15.934 13.448 16.1354C13.6494 16.3368 13.7501 16.5833 13.7501 16.875C13.7501 17.1667 13.6494 17.4132 13.448 17.6146C13.2466 17.816 13.0001 17.9167 12.7084 17.9167ZM16.0417 7.08332C15.7501 7.08332 15.5036 6.98263 15.3022 6.78124C15.1008 6.57985 15.0001 6.33332 15.0001 6.04166C15.0001 5.74999 15.1008 5.50346 15.3022 5.30207C15.5036 5.10068 15.7501 4.99999 16.0417 4.99999C16.3334 4.99999 16.5799 5.10068 16.7813 5.30207C16.9827 5.50346 17.0834 5.74999 17.0834 6.04166C17.0834 6.33332 16.9827 6.57985 16.7813 6.78124C16.5799 6.98263 16.3334 7.08332 16.0417 7.08332ZM16.0417 15C15.7501 15 15.5036 14.8993 15.3022 14.6979C15.1008 14.4965 15.0001 14.25 15.0001 13.9583C15.0001 13.6667 15.1008 13.4201 15.3022 13.2187C15.5036 13.0174 15.7501 12.9167 16.0417 12.9167C16.3334 12.9167 16.5799 13.0174 16.7813 13.2187C16.9827 13.4201 17.0834 13.6667 17.0834 13.9583C17.0834 14.25 16.9827 14.4965 16.7813 14.6979C16.5799 14.8993 16.3334 15 16.0417 15ZM17.2917 11.0417C17.0001 11.0417 16.7536 10.941 16.5522 10.7396C16.3508 10.5382 16.2501 10.2917 16.2501 9.99999C16.2501 9.70832 16.3508 9.4618 16.5522 9.26041C16.7536 9.05902 17.0001 8.95832 17.2917 8.95832C17.5834 8.95832 17.8299 9.05902 18.0313 9.26041C18.2327 9.4618 18.3334 9.70832 18.3334 9.99999C18.3334 10.2917 18.2327 10.5382 18.0313 10.7396C17.8299 10.941 17.5834 11.0417 17.2917 11.0417ZM10.0001 18.3333C8.8473 18.3333 7.76397 18.1146 6.75008 17.6771C5.73619 17.2396 4.85425 16.6458 4.10425 15.8958C3.35425 15.1458 2.7605 14.2639 2.323 13.25C1.8855 12.2361 1.66675 11.1528 1.66675 9.99999C1.66675 8.84721 1.8855 7.76388 2.323 6.74999C2.7605 5.7361 3.35425 4.85416 4.10425 4.10416C4.85425 3.35416 5.73619 2.76041 6.75008 2.32291C7.76397 1.88541 8.8473 1.66666 10.0001 1.66666V3.33332C8.13897 3.33332 6.56258 3.97916 5.27091 5.27082C3.97925 6.56249 3.33341 8.13888 3.33341 9.99999C3.33341 11.8611 3.97925 13.4375 5.27091 14.7292C6.56258 16.0208 8.13897 16.6667 10.0001 16.6667V18.3333ZM10.0001 11.6667C9.54175 11.6667 9.14939 11.5035 8.823 11.1771C8.49661 10.8507 8.33341 10.4583 8.33341 9.99999C8.33341 9.93054 8.33689 9.85763 8.34383 9.78124C8.35078 9.70485 8.36814 9.63193 8.39591 9.56249L6.66675 7.83332L7.83341 6.66666L9.56258 8.39582C9.61814 8.38193 9.76397 8.3611 10.0001 8.33332C10.4584 8.33332 10.8508 8.49652 11.1772 8.82291C11.5036 9.1493 11.6667 9.54166 11.6667 9.99999C11.6667 10.4583 11.5036 10.8507 11.1772 11.1771C10.8508 11.5035 10.4584 11.6667 10.0001 11.6667Z"
                                        fill="#354D6E" />
                                </g>
                            </svg>
                            <p class="text-[15px] font-medium text-[#354D6E] dark:text-white/80 mx-1 my-0">Recently Updated
                            </p>
                        </div>
                    </div>
                    <div class="m-3 flex items-center">
                        <p class="text-[23px] font-semibold dark:text-white m-0">{{ __('About this add-on') }}</p>
                    </div>
                    <div class="m-3 flex items-center">
                        <div class="text-[15px] leading-6 font-normal text-[#595959] dark:text-white/80 m-0 ">
                            {!! $extension->detail !!}</div>
                    </div>
                    <div class="m-3 flex flex-col">
                        @php
                            $tags = explode(',', $extension->category);
                        @endphp
                        @foreach ($tags as $tag)
                            <div class="flex">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" rx="12" fill="#F1EDFF" />
                                    <path
                                        d="M10.275 17.0188L6 12.7438L7.06875 11.675L10.275 14.8813L17.1563 8L18.225 9.06875L10.275 17.0188Z"
                                        fill="#1C1B1F" />
                                </svg>
                                <p class="text-[#272D38] dark:text-white/80 mx-2 text-[16px] font-normal leading-5">
                                    {{ $tag }} </p>
                            </div>
                        @endforeach
                    </div>
                    @if (!empty($extensionQAs))
                        <div class="m-3 flex flex-col">
                            <p class="text-[#002033] dark:text-white/80 font-semibold text-[21px]">
                                {{ __('Have a question?') }} </p>
                            @foreach ($extensionQAs as $extensionQA)
                                <div
                                    class="custom-accordion-item flex flex-col items-start justify-center rounded-[15px] shadow-[0_2px_8px_0_rgba(29,39,59,0.06)] py-[11px] px-[30px] mr-[100px] mb-[10px] relative active">
                                    <div class="custom-accordion-header w-full cursor-pointer">
                                        <p class="dark:text-white font-semibold text-[16px]">
                                            {{ __($extensionQA['question']) }}
                                        </p>
                                        <svg class="absolute top-[19px] right-[20px] rotate-180 custom-accordion-icon"
                                            width="12" height="8" viewBox="0 0 12 8" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M10.4023 0.0957031L11.75 1.44336L6 7.19336L0.25 1.44336L1.59766 0.0957031L6 4.49805L10.4023 0.0957031Z"
                                                fill="#26585A" />
                                        </svg>
                                    </div>
                                    <p
                                        class="text-[15px] text-[#595959] dark:text-white/80 font-normal mr-[50px] custom-accordion-body">
                                        {{ $extensionQA['answer'] }}
                                    </p>
                                </div>
                            @endforeach
                            {{-- <div
                            class="flex items-center rounded-[55px] border-[1px] border-solid border-[#f6f6f6] py-[11px] px-[30px] mr-[100px] mb-[10px] relative">
                            <p class="text-black font-semibold text-[16px] m-0">
                                {{ __('Do I get support and free updates?') }}</p>
                            <svg class="absolute right-[20px]" width="12" height="8" viewBox="0 0 12 8"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.4023 0.0957031L11.75 1.44336L6 7.19336L0.25 1.44336L1.59766 0.0957031L6 4.49805L10.4023 0.0957031Z"
                                    fill="#26585A" />
                            </svg>
                        </div> --}}
                        </div>
                    @endif
                </div>
                <div class="w-full mx-[15px] md:min-w-[360px] md:max-w-[360px] flex flex-col">
                    <div
                        class="flex flex-col rounded-[11px] border-[1px] border-solid border-[#f9f9f9] dark:border-white/5 p-4 mb-3">
                        <p
                            class="text-[16px] font-semibold text-[#002033] dark:text-white/80 border-b-[1px] border-b-solid dark:border-white/5 border-b-[#fafafa] text-center">
                            Limited Offer</p>
                        @if ($extension->price != 0)
                            <div class="flex items-center justify-between">
                                <p class="text-[36px] font-semibold dark:text-white my-0">${{ $extension->price }}</p>
                                <p class="line-through text-[28px] font-semibold dark:text-white opacity-25 my-0 hidden">
                                    $65</p>
                                <div class="w-fit bg-[#dee6f3] rounded-[20px] p-2 flex items-center justify-center">
                                    <p class="font-semibold text-[11px] text-[#A488FF] m-0">For a limited time only</p>
                                </div>
                            </div>
                        @else
                        <div class="flex items-center justify-center">
                            <p class="text-[36px] font-semibold dark:text-white my-0">Free</p>
                        </div>
                        @endif
                        <div class="flex justify-center">
                            <p class="dark:text-white opacity-50 text-[12px] font-medium py-2">
                                {{ __('Price is in US dollars. Tax included.') }}</p>
                        </div>
                        <div>
                            @if($extension->price !=0)
                            <button id="btn_buy_extension" {{ $extension->licensed ? 'disabled' : '' }}
                                onclick="window.location.href='{{ route('dashboard.admin.marketplace.buyextesion', ['slug' => $extension->slug]) }}'"
                                class="btn btn-primary w-100 py-[0.75em] flex items-center group">
                                <span class="hidden group-[.lqd-form-submitting]:inline-flex">Please wait...</span>
                                <span class="group-[.lqd-form-submitting]:hidden">Buy Now</span>
                            </button>
                            @else
                            <button id="redirect_install_extension"
                                onclick="window.location.href='{{ route('dashboard.admin.marketplace.liextension')}}'"
                                class="btn btn-primary w-100 py-[0.75em] flex items-center group">
                                <span class="hidden group-[.lqd-form-submitting]:inline-flex">Please wait...</span>
                                <span class="group-[.lqd-form-submitting]:hidden">Install Now</span>
                            </button>
                            @endif
                            {{-- <button type="button"
                                class="btn bg-[#fff] hover:bg-[#c1bDFF] text-[#000] w-100 py-[0.75em] flex items-center group mt-[1.5rem] dark:bg-white/5 dark:text-white">
                                Live Preview &ThickSpace;<svg width="19" height="19" viewBox="0 0 19 19"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.286499 9.50001C0.286499 4.4374 4.39055 0.333344 9.45317 0.333344C14.5158 0.333344 18.6198 4.4374 18.6198 9.50001C18.6198 14.5626 14.5158 18.6667 9.45317 18.6667C4.39055 18.6667 0.286499 14.5626 0.286499 9.50001ZM8.79242 5.16075C8.46698 4.83532 7.93935 4.83532 7.61391 5.16075C7.28847 5.48619 7.28847 6.01383 7.61391 6.33926L10.7747 9.50001L7.61391 12.6608C7.28847 12.9862 7.28847 13.5138 7.61391 13.8393C7.93935 14.1647 8.46698 14.1647 8.79242 13.8393L12.5424 10.0893C12.8679 9.76383 12.8679 9.23619 12.5424 8.91075L8.79242 5.16075Z"
                                        fill="#8695AA" />
                                </svg>
                            </button> --}}
                        </div>
                    </div>

                    <div
                        class="flex flex-col rounded-[11px] border-[1px] border-solid border-[#f9f9f9] dark:border-white/5 p-4">
                        <p
                            class="text-[16px] font-semibold dark:text-white/80 border-b-[1px] border-b-solid border-b-[#fafafa]">
                            Details</p>
                        <div class="flex items-center justify-between flex-wrap">
                            <div
                                class="flex flex-col w-[45%] px-[18px] py-[12px] rounded-[11px] border-[1px] border-solid border-[#F8F8F8] dark:border-white/5 my-2">
                                <p class="text-[#64748B] dark:text-white/80 text-[10px] font-semibold">FREE UPDATES</p>
                                <p class="text-[#334155] dark:text-white/70 text-[14px] font-semibold mt-3 mb-1">Lifetime
                                </p>
                            </div>
                            <div
                                class="flex flex-col w-[45%] px-[18px] py-[12px] rounded-[11px] border-[1px] border-solid border-[#F8F8F8] dark:border-white/5 my-2">
                                <p class="text-[#64748B] dark:text-white/80 text-[10px] font-semibold">SUPPORT</p>
                                <p class="text-[#334155] dark:text-white/70  text-[14px] font-semibold mt-3 mb-1">6 months
                                </p>
                            </div>
                            <div
                                class="flex flex-col w-[45%] px-[18px] py-[12px] rounded-[11px] border-[1px] border-solid border-[#F8F8F8] dark:border-white/5 my-2">
                                <p class="text-[#64748B] dark:text-white/80 text-[10px] font-semibold">LICENSE</p>
                                <p class="text-[#334155] dark:text-white/70 text-[14px] font-semibold mt-3 mb-1">Extended
                                </p>
                            </div>
                            <div
                                class="flex flex-col w-[45%] px-[18px] py-[12px] rounded-[11px] border-[1px] border-solid border-[#F8F8F8] dark:border-white/5 my-2">
                                <p class="text-[#64748B] dark:text-white/80 text-[10px] font-semibold">INSTALLATION</p>
                                <p class="text-[#334155] dark:text-white/70 text-[14px] font-semibold mt-3 mb-1">One Click
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('script')
        <script src="/assets/js/panel/marketplace.js"></script>
    @endsection
