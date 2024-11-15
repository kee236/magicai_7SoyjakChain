@extends('panel.layout.app')
@section('title', __('Subscription Plans'))
@inject('paymentControls', 'App\Http\Controllers\Finance\PaymentProcessController')
@inject('gatewayControls', 'App\Http\Controllers\Finance\GatewayController')

@section('content')
    <!-- Page header -->
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <a
                        class="page-pretitle flex items-center"
                        href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                    >
                        <svg
                            class="!me-2 rtl:-scale-x-100"
                            width="8"
                            height="10"
                            viewBox="0 0 6 10"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"
                            />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ __('Subscription Plans') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-8">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <h2>{{ __('Current plan') }}</h2>
                    @include('panel.user.finance.subscriptionStatus')
                </div>
                <div class="grid grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1">
                    @foreach ($plans as $plan)
                        <div class="{{ $plan->is_featured == 1 ? 'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' : '' }} w-full rounded-3xl border-solid border-[--tblr-border-color]">
                            <div class="flex h-full flex-col p-[1rem]">
                                <div class="text-heading mb-2 flex items-start text-[50px] font-bold leading-none">
                                    @if (currencyShouldDisplayOnRight(currency()->symbol))
                                        {{ $plan->price }} <small class='inline-flex text-[0.35em] font-normal'>{{ currency()->symbol }}</small>
                                    @else
                                        <small class='inline-flex text-[0.35em] font-normal'>{{ currency()->symbol }}</small> {{ $plan->price }}
                                    @endif
                                    <div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
                                        {{ __(formatCamelCase($plan->frequency)) }}
                                        @if ($plan->is_featured == 1)
                                            <div
                                                class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-[0.75rem] py-[0.25rem] text-[11px] text-black">
                                                {{ __('Popular plan') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-muted text-[15px] font-medium leading-none">{{ __($plan->name) }}</p>

                                <ul class="text-heading my-6 list-none p-0 text-[15px]">
                                    @if ($plan->trial_days != 0)
                                        <li class="mb-3">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[20px] w-[20px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            {{ number_format($plan->trial_days) . ' ' . __('Days of free trial.') }}
                                        </li>
                                    @endif
                                    <li class="mb-3">
                                        <span
                                            class="text-primary mr-1 inline-flex h-[20px] w-[20px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="14"
                                                height="14"
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
                                                />
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                        </span>
                                        {{ __('Access') }}
                                        <strong>{{ __($plan->checkOpenAiItemCount()) }}</strong> {{ __('Templates') }}
                                        <div class="group relative inline-block before:absolute before:-inset-2.5">
                                            <span class="peer relative -mt-6 inline-flex !h-6 !w-6 cursor-pointer items-center justify-center">
                                                <svg
                                                    class="fill-current opacity-20"
                                                    width="14"
                                                    height="14"
                                                    viewBox="0 0 14 14"
                                                    fill="none"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        d="M6.66732 0.333313C10.3473 0.333313 13.334 3.31998 13.334 6.99998C13.334 10.68 10.3473 13.6666 6.66732 13.6666C2.98732 13.6666 0.000652313 10.68 0.000652313 6.99998C0.000652313 3.31998 2.98732 0.333313 6.66732 0.333313ZM6.00065 10.3333H7.33398V6.33331H6.00065V10.3333ZM6.00065 4.99998H7.33398V3.66665H6.00065V4.99998Z"
                                                    />
                                                </svg>
                                            </span>
                                            <div
                                                class="min-w-60 border-px pointer-events-none invisible absolute start-full top-1/2 ms-2 max-h-96 -translate-y-1/2 translate-x-2 scale-105 overflow-y-auto rounded border-solid border-[--tblr-border-color] bg-[--tblr-body-bg] !p-5 opacity-0 transition-all before:absolute before:-start-2 before:top-0 before:h-full before:w-2 group-hover:pointer-events-auto group-hover:!visible group-hover:translate-x-0 group-hover:!opacity-100 [&.anchor-end]:!end-2 [&.anchor-end]:!start-auto [&.anchor-end]:!me-2 [&.anchor-end]:!ms-0"
                                                data-set-anchor="true"
                                            >
                                                <ul class="m-0 list-none p-0">
                                                    @foreach ($openAiList->groupBy('filters') as $key => $openAi)
                                                        <li class="!mb-3 !mt-5 first:!mt-0">
                                                            <h5 class="text-base">{{ ucfirst($key) }}</h5>
                                                        </li>
                                                        @php($openAi = \App\Helpers\Classes\Helper::sortingOpenAiSelected($openAi, $plan->open_ai_items))

                                                        @foreach ($openAi as $itemOpenAi)
                                                            <li>
                                                                <div class="form-check">
                                                                    <input
                                                                        class="form-check-input !rounded-full"
                                                                        id="{{ $itemOpenAi->slug }}"
                                                                        disabled
                                                                        {{ $plan->checkOpenAiItem($itemOpenAi->slug) ? 'checked' : '' }}
                                                                        type="checkbox"
                                                                        value=""
                                                                    >
                                                                    <label
                                                                        class="form-check-label "
                                                                        for="{{ $itemOpenAi->slug }}"
                                                                    >
                                                                        <small class="{{ $plan->checkOpenAiItem($itemOpenAi->slug) ? '' : 'text-gray-500' }}">{{ $itemOpenAi->title }}</small>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    @foreach (explode(',', $plan->features) as $item)
                                        <li class="mb-3">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[20px] w-[20px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            {{ $item }}
                                        </li>
                                    @endforeach
                                    @if ($plan->is_team_plan)
                                        <li class="mb-[0.625em]">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[19px] w-[19px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            <strong>{{ number_format($plan->plan_allow_seat) }}</strong> {{ __('Team allow seats') }}
                                        </li>
                                    @endif
                                    @if ($plan->display_word_count)
                                        <li class="mb-[0.625em]">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[19px] w-[19px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            @if ((int) $plan->total_words >= 0)
                                                <strong>{{ number_format($plan->total_words) }}</strong> {{ __('Word Tokens') }}
                                            @else
                                                <strong>{{ __('Unlimited') }}</strong> {{ __('Word Tokens') }}
                                            @endif
                                        </li>
                                    @endif
                                    @if ($plan->display_imag_count)
                                        <li class="mb-[0.625em]">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[19px] w-[19px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            @if ((int) $plan->total_images >= 0)
                                                <strong>{{ number_format($plan->total_images) }}</strong> {{ __('Image Tokens') }}
                                            @else
                                                <strong>{{ __('Unlimited') }}</strong> {{ __('Image Tokens') }}
                                            @endif
                                        </li>
                                    @endif

                                </ul>

                                @if ($activesubid == $plan->id)
                                    <div class="-mx-[1rem] mb-[1rem] mt-auto text-center">
                                        <div class="vstack gap-2">
                                            <span class="text-success"><b>{{ __('Already Subscribed') }}</b></span>
                                            <a
                                                class="text-muted"
                                                onclick="return confirm('Are you sure to cancel your plan? You will lose your remaining usage.');"
                                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.cancelActiveSubscription')) }}"
                                            >{{ __('Cancel Subscription') }}</a>
                                        </div>
                                    </div>
                                @elseif($activesubid != null)
                                    <div class="-mx-[1rem] mb-[1rem] mt-auto text-center">
                                        <div class="vstack gap-2">
                                            <span class="text-muted"><b>{{ __('You have an active subscription.') }}</b></span>
                                        </div>
                                    </div>
                                @else
                                    <div class="-mx-[1rem] -mb-[1rem] mt-auto text-center">
                                        @if ($is_active_gateway == 1)
                                            @php($planid = $plan->id)
                                            <a
                                                class="btn -mx-px -mb-px w-full rounded-3xl border border-[--tblr-border-color] py-[0.75rem] text-[15px] font-semibold shadow-none hover:bg-[--tblr-primary] hover:text-white"
                                                @if ($plan->price == 0) href = "{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])) }}"
                                               @else data-bs-toggle="modal" data-bs-target="#gatewayModal_{{ $planid }}" @endif
                                                role="button"
                                            >{{ __('Choose plan') }}</a>

                                            <div
                                                class="modal fade"
                                                id="gatewayModal_{{ $planid }}"
                                                tabindex="-1"
                                                aria-labelledby="gatewayModalLabel_{{ $planid }}"
                                                aria-hidden="true"
                                            >
                                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1
                                                                class="modal-title"
                                                                id="gatewayModalLabel_{{ $planid }}"
                                                            >{{ __('Continue with') }}</h1>
                                                            <button
                                                                class="btn-close"
                                                                data-bs-dismiss="modal"
                                                                type="button"
                                                                aria-label="Close"
                                                            ></button>
                                                        </div>
                                                        <div class="modal-body vstack gap-3">
                                                            @foreach ($activeGateways as $gateway)
                                                                @if ($gateway->code == 'revenuecat')
                                                                    @continue
                                                                @endif
                                                                @php($data = $gatewayControls->gatewayData($gateway->code))
                                                                <a
                                                                    class="btn -mx-px -mb-px flex h-[36px] w-full items-center rounded-3xl border border-[--tblr-border-color] px-3 py-0 text-[15px] font-semibold shadow-none hover:bg-[--tblr-primary] hover:text-white"
                                                                    href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.startSubscriptionProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])) }}"
                                                                >
                                                                    <div class="w-100 m-0 flex h-[36px] items-center justify-between p-0 align-middle">
                                                                        @if ($data['whiteLogo'] == 1)
                                                                            <img
                                                                                class="rounded-3xl bg-[--tblr-primary]"
                                                                                src="{{ $data['img'] }}"
                                                                                style="max-height:24px;"
                                                                                alt="{{ $data['title'] }}"
                                                                            />
                                                                        @else
                                                                            <img
                                                                                src="{{ $data['img'] }}"
                                                                                style="max-height:24px;"
                                                                                alt="{{ $data['title'] }}"
                                                                            />
                                                                        @endif
                                                                        <span class="">{{ $data['title'] }}</span>
                                                                    </div>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p>{{ __('Please enable a payment gateway') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="page-header pt-4">
                    <div class="container-xl">
                        <div class="row g-2 items-center">
                            <div class="col">
                                <h2 class="page-title mb-2">
                                    {{ __('Token Packs') }}
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-3 max-lg:grid-cols-2 max-md:grid-cols-1">
                    @foreach ($prepaidplans as $plan)
                        <div class="{{ $plan->is_featured == 1 ? 'shadow-[0_7px_20px_rgba(0,0,0,0.04)]' : '' }} w-full rounded-3xl border-solid border-[--tblr-border-color]">
                            <div class="flex h-full flex-col p-[1rem]">
                                <div class="text-heading mb-2 flex items-start text-[50px] font-bold leading-none">
                                    @if (currencyShouldDisplayOnRight(currency()->symbol))
                                        {{ $plan->price }} <small class='inline-flex text-[0.35em] font-normal'>{{ currency()->symbol }}</small>
                                    @else
                                        <small class='inline-flex text-[0.35em] font-normal'>{{ currency()->symbol }}</small> {{ $plan->price }}
                                    @endif

                                    <div class="ms-2 mt-2 inline-flex flex-col items-start gap-2 text-[0.3em]">
                                        {{ __('One time') }}
                                        @if ($plan->is_featured == 1)
                                            <div
                                                class="inline-flex rounded-full bg-gradient-to-r from-[#ece7f7] via-[#e7c5e6] to-[#e7ebf9] px-[0.75rem] py-[0.25rem] text-[11px] text-black">
                                                {{ __('Popular pack') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-muted text-[15px] font-medium leading-none">{{ __($plan->name) }}</p>

                                <ul class="text-heading my-6 list-none p-0 text-[15px]">
                                    <li class="mb-3">
                                        <span
                                            class="text-primary mr-1 inline-flex h-[20px] w-[20px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="14"
                                                height="14"
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
                                                />
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                        </span>
                                        {{ __('Access') }}
                                        <strong>{{ __($plan->checkOpenAiItemCount()) }}</strong> {{ __('Templates') }}
                                        <div class="group relative inline-block before:absolute before:-inset-2.5">
                                            <span class="peer relative -mt-6 inline-flex !h-6 !w-6 cursor-pointer items-center justify-center">
                                                <svg
                                                    class="fill-current opacity-20"
                                                    width="14"
                                                    height="14"
                                                    viewBox="0 0 14 14"
                                                    fill="none"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        d="M6.66732 0.333313C10.3473 0.333313 13.334 3.31998 13.334 6.99998C13.334 10.68 10.3473 13.6666 6.66732 13.6666C2.98732 13.6666 0.000652313 10.68 0.000652313 6.99998C0.000652313 3.31998 2.98732 0.333313 6.66732 0.333313ZM6.00065 10.3333H7.33398V6.33331H6.00065V10.3333ZM6.00065 4.99998H7.33398V3.66665H6.00065V4.99998Z"
                                                    />
                                                </svg>
                                            </span>
                                            <div
                                                class="min-w-60 border-px pointer-events-none invisible absolute start-full top-1/2 ms-2 max-h-96 -translate-y-1/2 translate-x-2 scale-105 overflow-y-auto rounded border-solid border-[--tblr-border-color] bg-[--tblr-body-bg] !p-5 opacity-0 transition-all before:absolute before:-start-2 before:top-0 before:h-full before:w-2 group-hover:pointer-events-auto group-hover:!visible group-hover:translate-x-0 group-hover:!opacity-100 [&.achor-end]:!start-auto [&.anchor-end]:!end-2 [&.anchor-end]:!me-2 [&.anchor-end]:!ms-0"
                                                data-set-anchor="true"
                                            >
                                                <ul class="m-0 list-none p-0">
                                                    @foreach ($openAiList->groupBy('filters') as $key => $openAi)
                                                        <li class="!mb-3 !mt-5 first:!mt-0">
                                                            <h5 class="text-base">{{ ucfirst($key) }}</h5>
                                                        </li>

                                                        @php($openAi = \App\Helpers\Classes\Helper::sortingOpenAiSelected($openAi, $plan->open_ai_items))

                                                        @foreach ($openAi as $itemOpenAi)
                                                            <li>
                                                                <div class="form-check">
                                                                    <input
                                                                        class="form-check-input !rounded-full"
                                                                        id="{{ $itemOpenAi->slug }}"
                                                                        disabled
                                                                        {{ $plan->checkOpenAiItem($itemOpenAi->slug) ? 'checked' : '' }}
                                                                        type="checkbox"
                                                                        value=""
                                                                    >
                                                                    <label
                                                                        class="form-check-label"
                                                                        for="{{ $itemOpenAi->slug }}"
                                                                    >
                                                                        <small class="{{ $plan->checkOpenAiItem($itemOpenAi->slug) ? '' : 'text-gray-500' }}">{{ $itemOpenAi->title }}</small>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    @foreach (explode(',', $plan->features) as $item)
                                        <li class="mb-3">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[20px] w-[20px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            {{ $item }}
                                        </li>
                                    @endforeach
                                    @if ($plan->display_word_count)
                                        <li class="mb-[0.625em]">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[19px] w-[19px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>

                                            @if ((int) $plan->total_words >= 0)
                                                <strong>{{ number_format($plan->total_words) }}</strong> {{ __('Word Tokens') }}
                                            @else
                                                <strong>{{ __('Unlimited') }}</strong> {{ __('Word Tokens') }}
                                            @endif

                                        </li>
                                    @endif
                                    @if ($plan->display_imag_count)
                                        <li class="mb-[0.625em]">
                                            <span
                                                class="text-primary mr-1 inline-flex h-[19px] w-[19px] items-center justify-center rounded-xl bg-[rgba(var(--tblr-primary-rgb),0.1)] align-middle"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="14"
                                                    height="14"
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
                                                    />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>

                                            @if ((int) $plan->total_images >= 0)
                                                <strong>{{ number_format($plan->total_images) }}</strong> {{ __('Image Tokens') }}
                                            @else
                                                <strong>{{ __('Unlimited') }}</strong> {{ __('Image Tokens') }}
                                            @endif
                                        </li>
                                    @endif

                                    {{--									@foreach ($openAiList as $openAi) --}}
                                    {{--										<li> --}}
                                    {{--											<div class="form-check"> --}}
                                    {{--												<input disabled class="form-check-input" {{ $plan->checkOpenAiItem($openAi->slug) ? 'checked': '' }} type="checkbox" value="" id="flexCheckDefault"> --}}
                                    {{--												<label class="form-check-label" for="flexCheckDefault"> --}}
                                    {{--													{{ $openAi->title }} --}}
                                    {{--												</label> --}}
                                    {{--											</div> --}}
                                    {{--										</li> --}}
                                    {{--									@endforeach --}}
                                </ul>
                                <div class="-mx-[1rem] -mb-[1rem] mt-auto text-center">
                                    @if ($is_active_gateway == 1)
                                        @php($planid = $plan->id)
                                        <a
                                            class="btn -mx-px -mb-px w-full rounded-3xl border border-[--tblr-border-color] py-[0.75rem] text-[15px] font-semibold shadow-none hover:bg-[--tblr-primary] hover:text-white"
                                            @if ($plan->price == 0) href = "{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.startPrepaidPaymentProcess', ['planId' => $planid, 'gatewayCode' => 'freeservice'])) }}"
                                           @else data-bs-toggle="modal"
                                           data-bs-target="#gatewayPrepaidModal_{{ $planid }}" @endif
                                            role="button"
                                        >{{ __('Choose pack') }}</a>
                                        <div
                                            class="modal fade"
                                            id="gatewayPrepaidModal_{{ $planid }}"
                                            tabindex="-1"
                                            aria-labelledby="gatewayPrepaidModalLabel_{{ $planid }}"
                                            aria-hidden="true"
                                        >
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1
                                                            class="modal-title"
                                                            id="gatewayPrepaidModalLabel_{{ $planid }}"
                                                        >{{ __('Continue with') }}</h1>
                                                        <button
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            type="button"
                                                            aria-label="Close"
                                                        ></button>
                                                    </div>
                                                    <div class="modal-body vstack gap-3">
                                                        @foreach ($activeGateways as $gateway)
                                                            @if ($gateway->code == 'revenuecat')
                                                                @continue
                                                            @endif
                                                            @php($data = $gatewayControls->gatewayData($gateway->code))
                                                            <a
                                                                class="btn -mx-px -mb-px flex h-[36px] w-full items-center rounded-3xl border border-[--tblr-border-color] px-3 py-0 text-[15px] font-semibold shadow-none hover:bg-[--tblr-primary] hover:text-white"
                                                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.startPrepaidPaymentProcess', ['planId' => $planid, 'gatewayCode' => $data['code']])) }}"
                                                            >
                                                                <div class="w-100 m-0 flex h-[36px] items-center justify-between p-0 align-middle">
                                                                    @if ($data['whiteLogo'] == 1)
                                                                        <img
                                                                            class="rounded-3xl bg-[--tblr-primary]"
                                                                            src="{{ $data['img'] }}"
                                                                            style="max-height:24px;"
                                                                            alt="{{ $data['title'] }}"
                                                                        />
                                                                    @else
                                                                        <img
                                                                            src="{{ $data['img'] }}"
                                                                            style="max-height:24px;"
                                                                            alt="{{ $data['title'] }}"
                                                                        />
                                                                    @endif
                                                                    <span class="">{{ $data['title'] }}</span>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p>{{ __('Please enable a payment gateway') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $('[data-click="tooltip"]').on('click', function() {

            let id = $(this).data('id');

            if ($('#' + id).hasClass('d-none')) {
                $('#' + id).removeClass('d-none');
            } else {
                $('#' + id).addClass('d-none');
            }

        })

        $(document).ready(function() {
            let plan = '{{ request('plan') }}';

            if (plan) {
                $('#gatewayModal_' + plan).modal('show');
            }
        });
    </script>
@endpush
