@extends('panel.layout.app')
@section('title', 'Payment')

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
                    <h2 class="page-title mb-2">
                        {{ __('Payment') }}
                    </h2>
                </div>

            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="flex flex-col flex-wrap items-center justify-center relative">
                <p class="font-semibold text-[23px] dark:text-white">Select a payment method</p>
                <p class="font-bold text-[15px] text-[#485061] dark:text-white/80 max-w-[350px] text-center">
                    {{ __('How do you want to pay? Select a payment method to confirm your order') }}.</p>
                <div class="flex items-start flex-col my-2">
                    {{-- <p class="text-[12px] text-black font-semibold mx-2">COUPON</p>
                     <div class="flex items-center relative w-[350px] p-0">
                        <svg class="absolute left-[20px]" width="19" height="19" viewBox="0 0 19 19" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_3091_21876)">
                                <path d="M10.165 14.25H12.8793" stroke="#3E3E3E" stroke-width="1.71429"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15.5936 14.25H18.3079" stroke="#3E3E3E" stroke-width="1.71429"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M2.97217 6.6907L10.8436 11.21" stroke="#3E3E3E" stroke-width="1.71429"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M3.73216 6.7857C4.54202 6.7857 5.31871 6.46399 5.89136 5.89133C6.46402 5.31868 6.78573 4.54199 6.78573 3.73213C6.78573 2.92227 6.46402 2.14558 5.89136 1.57293C5.31871 1.00027 4.54202 0.678558 3.73216 0.678558C2.9223 0.678558 2.14561 1.00027 1.57296 1.57293C1.0003 2.14558 0.678589 2.92227 0.678589 3.73213C0.678589 4.54199 1.0003 5.31868 1.57296 5.89133C2.14561 6.46399 2.9223 6.7857 3.73216 6.7857Z"
                                    stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M2.97217 12.3093L18.3215 3.46069" stroke="#3E3E3E" stroke-width="1.71429"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M3.73216 18.3214C4.54202 18.3214 5.31871 17.9997 5.89136 17.427C6.46402 16.8544 6.78573 16.0777 6.78573 15.2678C6.78573 14.458 6.46402 13.6813 5.89136 13.1086C5.31871 12.536 4.54202 12.2143 3.73216 12.2143C2.9223 12.2143 2.14561 12.536 1.57296 13.1086C1.0003 13.6813 0.678589 14.458 0.678589 15.2678C0.678589 16.0777 1.0003 16.8544 1.57296 17.427C2.14561 17.9997 2.9223 18.3214 3.73216 18.3214Z"
                                    stroke="#3E3E3E" stroke-width="1.71429" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </g>
                            <defs>
                                <clipPath id="clip0_3091_21876">
                                    <rect width="19" height="19" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        <input class="form-control h-10 ps-5 rounded-[55px] shadow-[0_2px_1px_0_rgba(29,39,59,0.07)] my-2"
                            type="text" name="code" placeholder="Coupon Code">
                        <button class="absolute right-[20px] btn btn-sm border-0 rounded-0 shadow-none p-1 ">
                            Apply
                            <svg width="10" height="11" viewBox="0 0 10 11" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_3091_21888" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                    width="10" height="11">
                                    <rect y="0.5" width="10" height="10" fill="#D9D9D9" />
                                </mask>
                                <g mask="url(#mask0_3091_21888)">
                                    <path
                                        d="M3.3431 9.66658L2.60352 8.927L6.0306 5.49992L2.60352 2.07284L3.3431 1.33325L7.50977 5.49992L3.3431 9.66658Z"
                                        fill="#1C1B1F" />
                                </g>
                            </svg>
                        </button>
                    </div> --}}
                    <div
                        class="flex flex-col w-[350px] rounded-[13px] border-[1px] border-solid border-[#f9f9f9] dark:border-white/5 p-3 m-1 text-[#212121]">
                        <div class="flex justify-between">
                            <p class="m-0 dark:text-white">Add-on</p>
                            <p class="m-0 dark:text-white">{{ $extension->name }}</p>
                        </div>
                        <div class="w-full h-[1px] bg-[#f9f9f9] my-2"></div>
                        <div class="flex justify-between">
                            <p class="m-0 dark:text-white">Total: </p>
                            <p class="m-0 text-[21px] dark:text-white">${{ $extension->price }}</p>
                        </div>
                    </div>
                </div>
                <p>{{ __('Tax included. Your payment is secured vis SSL.') }}</p>
                <div class="shadow-[0_4px_10px_0_rgba(0,0,0,0.06)] flex justify-center items-center">
                    <p class="text-[#7949f9] text-[21px]">stripe</p>
                </div>
                <button id="btn_confirm_method" class="btn btn-primary w-[350px] py-[0.75em] flex items-center group">
                    <span class="hidden group-[.lqd-form-submitting]:inline-flex">Please wait...</span>
                    <span class="group-[.lqd-form-submitting]:hidden">{{ __('Confirm Method') }}</span>
                </button>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const publicKeyUrl = "https://portal.liquid-themes.com/api/pkey";
        fetch(publicKeyUrl)
            .then((response) => response.json())
            .then((data) => {
                const publicKey = data.public_key;
                var stripe = Stripe(publicKey);
            })
            .catch((error) => {
                console.error("Error fetching public key:", error);
            });
        var extension = @json($extension);
    </script>
    <script src="/assets/js/panel/marketplace.js"></script>
@endsection
