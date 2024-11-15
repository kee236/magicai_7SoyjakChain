@extends('vendor.installer.layouts.master', ['stepShow' => false])

@section('template_title')
    {{ trans('Marketplace Subscription') }}
@endsection

@section('title')
    {{ trans('installer_messages.welcome.title') }}
@endsection

@section('style')
    <style>
        body {
            background-image: url({{ custom_theme_url('assets/img/misc/promo-bg-1.jpg') }});
            background-size: 100%;
            background-position: top center;
            background-repeat: no-repeat;
        }
    </style>
@endsection

@section('content')
    <header class="fixed inset-x-0 top-0 z-5 w-full border-b border-black/5 py-5 shadow-[0_4px_14px_rgba(0,0,0,0.05)] backdrop-blur-md">
        <nav class="flex items-center justify-between px-5">
            <div class="hidden basis-1/3 md:flex">

            </div>
            <div class="flex basis-1/2 justify-start md:justify-center">
                @if (isset($setting->logo_dashboard))
                    <img
                        class="h-auto group-[.navbar-shrinked]/body:hidden dark:hidden"
                        src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                        @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden h-auto group-[.navbar-shrinked]/body:hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                        @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @else
                    <img
                        class="h-auto group-[.navbar-shrinked]/body:hidden dark:hidden"
                        src="{{ custom_theme_url($setting->logo_path, true) }}"
                        @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden h-auto group-[.navbar-shrinked]/body:hidden dark:block"
                        src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                        @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @endif
            </div>
            <div class="flex basis-1/3 justify-end">
                <a
                    class="opacity-65 inline-flex items-center gap-1 text-2xs transition-opacity hover:opacity-100"
                    href="#"
                >
                    {{ trans('Skip This Offer') }}
                    <x-tabler-chevron-right class="size-4" />
                </a>
            </div>
        </nav>
    </header>

    <div class="mx-auto w-full px-5 pb-20 pt-36 text-center text-sm/6 md:w-1/2 lg:w-5/12 xl:w-4/12">
        <div class="mb-11 text-center">
            <img
                class="mx-auto"
                width="95"
                height="90"
                src="{{ custom_theme_url('/assets/img/misc/gem.svg') }}"
                alt="{{ __('A shining gem') }}"
            >
        </div>

        <h6 class="mb-6 inline-block rounded-full bg-[#F5FAFF] px-4 py-1 font-body text-sm font-semibold">
            <span class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent">
                {{ trans('Limited Time Offer') }}
            </span>
        </h6>

        <h1 class="mb-6 text-4xl">
            {{ trans('Start Your ') }}
            <span class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent">
                {{ trans('Premium Membership.') }}
            </span>
        </h1>

        <p class="mx-auto mb-6 font-medium lg:w-9/12">
            <span class="opacity-50">
                {{ trans('Become a part of our exclusive membership program and enjoy') }}
            </span>
            {{ trans('unique benefits reserved solely for our premium members.') }}
        </p>

        <ul class="mx-auto mb-7 flex flex-col gap-2 text-start font-medium lg:w-9/12">
            @foreach (['VIP Support', 'Access to All Extensions - <span class="font-bold text-[#6977DE]">worth $585</span>', '5 Hours of Customization'] as $item)
                <li class="flex items-center gap-3.5">
                    <svg
                        width="16"
                        height="15"
                        viewBox="0 0 16 15"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M2.09635 6.87072C1.80296 6.87154 1.51579 6.95542 1.26807 7.11264C1.02035 7.26986 0.822208 7.494 0.696564 7.75914C0.570919 8.02427 0.522908 8.31956 0.558084 8.61084C0.59326 8.90212 0.710186 9.17749 0.895335 9.4051L4.84228 14.2401C4.98301 14.4148 5.1634 14.5535 5.36847 14.6445C5.57353 14.7355 5.79736 14.7763 6.02136 14.7635C6.50043 14.7377 6.93295 14.4815 7.20871 14.0601L15.4075 0.855925C15.4089 0.853735 15.4103 0.851544 15.4117 0.849387C15.4886 0.731269 15.4637 0.497192 15.3049 0.350142C15.2613 0.309761 15.2099 0.278736 15.1538 0.25898C15.0977 0.239223 15.0382 0.231153 14.9789 0.235266C14.9196 0.239379 14.8618 0.255589 14.809 0.282896C14.7562 0.310204 14.7095 0.348031 14.6719 0.394048C14.669 0.397666 14.6659 0.40123 14.6628 0.404739L6.39421 9.74702C6.36275 9.78257 6.32454 9.81152 6.28179 9.83218C6.23905 9.85283 6.19263 9.86479 6.14522 9.86736C6.09782 9.86992 6.05038 9.86304 6.00565 9.84711C5.96093 9.83119 5.91982 9.80653 5.88471 9.77458L3.14051 7.27735C2.8555 7.01608 2.48299 6.87102 2.09635 6.87072Z"
                            fill="url(#paint0_linear_6413_808)"
                        />
                        <defs>
                            <linearGradient
                                id="paint0_linear_6413_808"
                                x1="0.546875"
                                y1="3.19866"
                                x2="12.7738"
                                y2="14.2613"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="#82E2F4" />
                                <stop
                                    offset="0.502"
                                    stop-color="#8A8AED"
                                />
                                <stop
                                    offset="1"
                                    stop-color="#6977DE"
                                />
                            </linearGradient>
                        </defs>
                    </svg>
                    {!! trans($item) !!}
                </li>
            @endforeach
        </ul>
        @if ($data)
            <a
                class="group mb-10 flex w-full items-center justify-center gap-3 rounded-full bg-background px-4 py-5 text-center text-[18px] font-bold shadow-[0_14px_44px_#2D2C6A17] transition-all hover:-translate-y-1 hover:scale-[1.025] hover:bg-gradient-to-br hover:from-[#82E2F4] hover:via-[#8A8AED] hover:to-[#6977DE] hover:shadow-2xl hover:shadow-black/10"
                href="{{ '/dashboard' }}"
            >
                <span
                    class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent group-hover:from-white group-hover:via-white group-hover:to-white"
                >
                    {{ trans('Subscription activated') }}
                </span>
                <x-tabler-chevron-right class="size-4 text-[#6977DE] group-hover:text-white" />
            </a>
        @else
            <a
                class="group mb-10 flex w-full items-center justify-center gap-3 rounded-full bg-background px-4 py-5 text-center text-[18px] font-bold shadow-[0_14px_44px_#2D2C6A17] transition-all hover:-translate-y-1 hover:scale-[1.025] hover:bg-gradient-to-br hover:from-[#82E2F4] hover:via-[#8A8AED] hover:to-[#6977DE] hover:shadow-2xl hover:shadow-black/10"
                href="{{ $payment }}"
            >
                <span
                    class="inline-block bg-gradient-to-br from-[#82E2F4] via-[#8A8AED] to-[#6977DE] bg-clip-text text-transparent group-hover:from-white group-hover:via-white group-hover:to-white"
                >
                    {{ trans('Subscribe Now') }}
                </span>
                <x-tabler-chevron-right class="size-4 text-[#6977DE] group-hover:text-white" />
            </a>
        @endif

        <p class="text-2xs font-medium">
            <span class="opacity-50">
                {{ trans('Seats are limited.') }}
            </span>
            <a href="{{ $payment }}">
                {{ trans('Learn more about') }}
                <span class="underline">
                    {{ trans('Premium Membership') }}
                </span>
            </a>
        </p>

        {{-- @includeWhen(is_null($portal), 'vendor.installer.magicai_c4st_Act', [
            'button' =>
                'flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white',
            'target' => '',
            'return_url' => route('LaravelInstaller::license') . '?license=verified',
        ])
        @includeWhen($portal, 'vendor.installer.magicai_license_token', [
            'button' =>
                'flex items-center justify-center gap-2 rounded-xl p-2 font-medium shadow-[0_4px_10px_rgba(0,0,0,0.05)] transition-all duration-300 hover:scale-105 hover:bg-black hover:text-white',
        ]) --}}
    </div>
@endsection
