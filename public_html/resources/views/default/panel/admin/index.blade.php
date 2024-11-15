@php
    $sales_prev_week = cache('sales_previous_week');
    $sales_this_week = cache('sales_this_week');

    $popular_tools_data = cache('popular_tools_data');
    $popular_plans_data = cache('popular_plans_data');
    $user_behavior_data = cache('user_behavior_data');
    $currencySymbol = currency()->symbol;
@endphp

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Overview'))

@section('content')
    <div class="py-10">
        @if ($gatewayError == true)
            <x-alert class="mb-11">
                <p>
                    {{ __('Gateway is set to use sandbox. Please set mode to development!') }}<br><br>
                </p>
                <ul class="flex list-inside list-disc flex-col gap-3 [&_ol]:mt-2 [&_ol]:flex [&_ol]:list-inside [&_ol]:list-decimal [&_ol]:flex-col [&_ol]:gap-1 [&_ol]:ps-4">
                    <li>
                        {{ __('To use live settings:') }}
                        <ol>
                            <li>{{ __('Set mode to Production') }}</li>
                            <li>{{ __('Save gateway settings') }}</li>
                            <li>{{ __('Know that all defined products and prices will reset.') }}</li>
                        </ol>
                    </li>
                    <li>
                        {{ __('To use sandbox settings:') }}
                        <ol>
                            <li>{{ __('Set mode to Development') }}</li>
                            <li>{{ __('Save gateway settings') }}</li>
                            <li>{{ __('Know that all defined products and prices will reset.') }}</li>
                        </ol>
                    </li>
                    <li>{{ __('Beware of that order is important. First set mode then save gateway settings.') }}</li>
                </ul>
            </x-alert>
        @endif

        <div class="flex flex-col gap-11">
            <x-card
                class="overflow-hidden px-2 py-4 hover:-translate-y-1 hover:bg-foreground/5"
                variant="outline"
                size="lg"
            >
                <div class="relative z-1 w-full lg:w-1/2">
                    <h2 class="mb-2.5">
                        @lang('Marketplace is here.')
                    </h2>
                    <p class="mb-0 text-sm">
                        @lang('Extend the capabilities of MagicAI, explore new designs and unlock new horizons.')
                    </p>
                </div>
                <figure
                    class="absolute end-0 top-full max-w-md max-lg:-translate-y-16 lg:-end-24 lg:-top-16"
                    aria-hidden="true"
                >
                    <img
                        class="w-full"
                        alt="{{ __('marketplace') }}"
                        width="857"
                        height="470"
                        src="{{ custom_theme_url('/assets/img/misc/dash-marketplace-announce.png') }}"
                    >
                </figure>
                <a
                    class="absolute inset-0 z-1 inline-block overflow-hidden text-start -indent-96"
                    href="{{ route('dashboard.admin.marketplace.index') }}"
                >
                    {{ __('Explore Marketplace') }}
                </a>
            </x-card>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:gap-8 xl:grid-cols-4">
                <x-card
                    class="lqd-statistic-card w-full"
                    size="sm"
                >
                    @php
                        $sales_change = percentageChange($sales_prev_week, $sales_this_week);
                    @endphp
                    <div class="flex gap-4">
                        <x-lqd-icon
                            class="bg-background text-heading-foreground dark:bg-foreground/5"
                            size="xl"
                        >
                            <x-tabler-currency-dollar
                                class="size-6"
                                stroke-width="1.5"
                            />
                        </x-lqd-icon>
                        <div class="lqd-statistic-info grow">
                            <p class="lqd-statistic-title mb-1 text-2xs font-medium text-heading-foreground/50">
                                {{ __('Total Sales') }}
                            </p>
                            <h3 class="lqd-statistic-change m-0 flex items-center gap-2 text-xl">
                                @if (currencyShouldDisplayOnRight($currencySymbol))
                                    {{ number_format(cache('total_sales')) }} {{ $currencySymbol }}
                                @else
                                    {{ $currencySymbol }}{{ number_format(cache('total_sales')) }}
                                @endif
                                <x-change-indicator value="{{ floatval($sales_change) }}" />
                            </h3>
                        </div>
                    </div>
                </x-card>

                <x-card
                    class="lqd-statistic-card w-full"
                    size="sm"
                >
                    @php
                        $users_change = percentageChange(cache('users_previous_week'), cache('users_this_week'));
                    @endphp
                    <div class="flex gap-4">
                        <x-lqd-icon
                            class="bg-background text-heading-foreground dark:bg-foreground/5"
                            size="xl"
                        >
                            <x-tabler-user-plus
                                class="size-6"
                                stroke-width="1.5"
                            />
                        </x-lqd-icon>
                        <div class="lqd-statistic-info grow">
                            <p class="lqd-statistic-title mb-1 text-2xs font-medium text-heading-foreground/50">
                                {{ __('Total Users') }}
                            </p>
                            <h3 class="lqd-statistic-change m-0 flex items-center gap-2 text-xl">
                                {{ cache('total_users') }}
                                {{-- <x-change-indicator value="{{ floatval($users_change) }}" /> --}}
                            </h3>
                        </div>
                    </div>
                </x-card>

                <x-card
                    class="lqd-statistic-card w-full"
                    size="sm"
                >
                    @php
                        $generated_change = percentageChange(cache('words_previous_week'), cache('words_this_week'));
                    @endphp
                    <div class="flex gap-4">
                        <x-lqd-icon
                            class="bg-background text-heading-foreground dark:bg-foreground/5"
                            size="xl"
                        >
                            <x-tabler-pencil
                                class="size-6"
                                stroke-width="1.5"
                            />
                        </x-lqd-icon>
                        <div class="lqd-statistic-info grow">
                            <p class="lqd-statistic-title mb-1 text-2xs font-medium text-heading-foreground/50">
                                {{ __('Words Generated') }}
                            </p>
                            <h3 class="lqd-statistic-change m-0 flex items-center gap-2 text-xl">
                                {{ cache('words_this_week') }}
                                <x-change-indicator value="{{ floatval($generated_change) }}" />
                            </h3>
                        </div>
                    </div>
                </x-card>

                <x-card
                    class="lqd-statistic-card w-full"
                    size="sm"
                >
                    @php
                        $generated_change = percentageChange(cache('images_previous_week'), cache('images_this_week'));
                    @endphp
                    <div class="flex gap-4">
                        <x-lqd-icon
                            class="bg-background text-heading-foreground dark:bg-foreground/5"
                            size="xl"
                        >
                            <x-tabler-camera
                                class="size-6"
                                stroke-width="1.5"
                            />
                        </x-lqd-icon>
                        <div class="lqd-statistic-info grow">
                            <p class="lqd-statistic-title mb-1 text-2xs font-medium text-heading-foreground/50">
                                {{ __('Images Generated') }}
                            </p>
                            <h3 class="lqd-statistic-change m-0 flex items-center gap-2 text-xl">
                                {{ cache('images_this_week') }}
                                <x-change-indicator value="{{ floatval($generated_change) }}" />

                            </h3>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="grid grid-cols-1 gap-11 md:grid-cols-2">
                <x-card>
                    @php
                        if ($sales_prev_week != 0 && $sales_this_week != 0) {
                            $sales_percent = number_format((1 - $sales_prev_week / $sales_this_week) * 100);
                        } else {
                            $sales_percent = 0;
                        }
                    @endphp
                    <x-slot:head>
                        <h4 class="m-0 text-base font-medium">
                            {{ __('Revenue') }}
                        </h4>
                    </x-slot:head>
                    <p class="mb-1">
                        {{ __('Total Sales') }}
                    </p>
                    <h3 class="flex items-center gap-2">
                        @if (currencyShouldDisplayOnRight($currencySymbol))
                            {{ number_format(cache('total_sales')) }}{{ $currencySymbol }}
                        @else
                            {{ $currencySymbol }}{{ number_format(cache('total_sales')) }}
                        @endif
                        <x-change-indicator value="{{ floatval($sales_percent) }}" />
                    </h3>

                    <div
                        class="[&_.apexcharts-legend-text]:!text-foreground"
                        id="chart-daily-sales"
                    ></div>
                </x-card>

                <x-card>
                    <x-slot:head>
                        <h4 class="m-0 text-base font-medium">
                            {{ __('AI Usage') }}
                        </h4>
                    </x-slot:head>

                    <div
                        class="[&_.apexcharts-legend-text]:!m-0 [&_.apexcharts-legend-text]:!pe-2 [&_.apexcharts-legend-text]:ps-2 [&_.apexcharts-legend-text]:!text-foreground"
                        id="chart-daily-usages"
                    ></div>

                    <div class="chart-navigation absolute end-2 top-2 flex items-center gap-1">
                        <button
                            class="size-7 inline-flex items-center justify-center rounded-md bg-foreground/10 text-foreground transition-colors hover:bg-foreground hover:text-background"
                            id="btnPreviousMonth"
                            type="button"
                        >
                            <x-tabler-arrow-left class="size-4" />
                        </button>
                        <button
                            class="size-7 inline-flex items-center justify-center rounded-md bg-foreground/10 text-foreground transition-colors hover:bg-foreground hover:text-background"
                            id="btnNextMonth"
                            type="button"
                        >
                            <x-tabler-arrow-right class="size-4" />
                        </button>
                    </div>
                </x-card>

                <x-card
                    class="flex flex-col"
                    class:body="flex flex-col justify-center grow"
                >
                    <x-slot:head>
                        <h4 class="m-0 text-base font-medium">
                            {{ __('Popular Plans') }}
                        </h4>
                    </x-slot:head>

                    <div
                        class="min-h-[350px] w-full [&_.apexcharts-legend-text]:!m-0 [&_.apexcharts-legend-text]:!pe-2 [&_.apexcharts-legend-text]:ps-2 [&_.apexcharts-legend-text]:!text-foreground"
                        id="popular-plans-chart"
                    ></div>
                </x-card>

                <x-card
                    class="flex flex-col"
                    class:body="flex flex-col justify-center grow"
                >
                    <x-slot:head>
                        <h4 class="m-0 text-base font-medium">
                            {{ __('New Users') }}
                        </h4>
                    </x-slot:head>

                    <div
                        class="min-h-[350px] w-full [&_.apexcharts-legend-text]:!m-0 [&_.apexcharts-legend-text]:!pe-2 [&_.apexcharts-legend-text]:ps-2 [&_.apexcharts-legend-text]:!text-foreground"
                        id="new-users-chart"
                    ></div>
                </x-card>

                <x-card
                    class="flex flex-col"
                    class:body="flex flex-col justify-center grow"
                >
                    <x-slot:head>
                        <h4 class="m-0 text-base font-medium">
                            {{ __('Popular AI Tools') }}
                        </h4>
                    </x-slot:head>

                    <div
                        class="min-h-[350px] w-full [&_.apexcharts-legend-text]:!m-0 [&_.apexcharts-legend-text]:!pe-2 [&_.apexcharts-legend-text]:ps-2 [&_.apexcharts-legend-text]:!text-foreground"
                        id="popular-tools-chart"
                    ></div>
                </x-card>

                <x-card
                    class="flex flex-col"
                    class:body="flex flex-col justify-center grow min-h-[350px] w-full"
                    size="none"
                >
                    <x-slot:head>
                        <h4 class="m-0 text-base font-medium">
                            {{ __('User Behaviour') }}
                        </h4>
                    </x-slot:head>

                    @php
                        $values_sum = array_sum(array_column($user_behavior_data, 'value'));
                        $values_sum = $values_sum == 0 ? 1 : $values_sum;
                    @endphp

                    <div id="user-behaviour-chart">
                        <div>
                            <div class="lqd-progress flex h-1.5 overflow-hidden rounded-full">
                                @foreach ($user_behavior_data as $data)
                                    <div
                                        class="lqd-progress-bar h-full grow"
                                        style="width: {{ ($data['value'] / $values_sum) * 100 }}%; background-color: {{ $data['color'] }};"
                                    ></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex">
                            @foreach ($user_behavior_data as $data)
                                <div class="group flex shrink-0 grow basis-0 flex-col justify-center space-y-3 px-9 pt-9 text-xs text-heading-foreground last:text-end">
                                    <div class="flex items-center gap-2 group-last:flex-row-reverse">
                                        <span
                                            class="h-[18px] w-1 rounded-full"
                                            style="background-color: {{ $data['color'] }}"
                                        ></span>
                                        {{ $data['label'] }}
                                    </div>
                                    <div class="text-[28px] font-bold opacity-70">
                                        {{ number_format(($data['value'] / $values_sum) * 100, 2) }}%
                                    </div>
                                    <div>
                                        {{ $data['value'] }}
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <div class="relative flex w-px items-center justify-center bg-border">
                                        <div class="size-[50px] inline-flex shrink-0 items-center justify-center rounded-full border bg-background text-sm font-medium shadow-sm">
                                            @lang('vs')
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </x-card>

                <x-card
                    class:body="h-80 grow overflow-y-auto"
                    size="none"
                >
                    <x-slot:head
                        class="mb-2"
                    >
                        <h4 class="m-0 text-base font-medium">
                            {{ __('Top Countries') }}
                        </h4>
                    </x-slot:head>

                    <x-table
                        class="text-xs"
                        variant="plain"
                    >
                        <x-slot:head>
                            <tr>
                                <th class="ps-6">
                                    {{ __('Country') }}
                                </th>
                                <th>
                                    {{ __('Users') }}
                                </th>
                                <th colspan="2">
                                    {{ __('Popularity') }}
                                </th>
                            </tr>
                        </x-slot:head>

                        <x-slot:body>
                            @foreach (json_decode(cache('top_countries') ?? '[]') as $top_countries)
                                <tr>
                                    <td class="ps-6">
                                        {{ __($top_countries->country ?? 'Not Specified') }}
                                    </td>
                                    <td>
                                        {{ $top_countries->total }}
                                    </td>
                                    <td colspan="2">
                                        <div class="lqd-progress h-2 w-full overflow-hidden rounded-full bg-foreground/5">
                                            <div
                                                class="lqd-progress-bar h-full shrink-0 grow-0 basis-auto bg-primary"
                                                style="width: {{ (100 * $top_countries->total) / cache('total_users') }}%"
                                            >
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot:body>
                    </x-table>
                </x-card>

                <x-card
                    class:body="h-80 grow overflow-y-auto"
                    size="none"
                >
                    <x-slot:head
                        class="mb-2"
                    >
                        <h4 class="m-0 text-base font-medium">
                            {{ __('Activity') }}
                        </h4>
                    </x-slot:head>

                    @if (count($activity) == 0)
                        <div class="flex h-full flex-col items-center justify-center gap-2 overflow-hidden text-center">
                            <x-tabler-article-off
                                class="h-24 w-24 opacity-60"
                                stroke-width="1.5"
                            />
                            <h3 class="m-0">
                                {{ __('No activity logged yet.') }}
                            </h3>
                        </div>
                    @else
                        <x-table
                            class="text-xs"
                            variant="plain"
                        >
                            <x-slot:body>
                                @foreach ($activity as $entry)
                                    <tr>
                                        <td class="w-1 pe-0">
                                            @if ($entry->user)
                                                <x-avatar :user="$entry->user" />
                                            @endif
                                        </td>
                                        <td>
                                            <div class="w-0 min-w-full overflow-hidden overflow-ellipsis whitespace-nowrap">
                                                @if ($entry->user)
                                                    <strong>{{ $entry->user->fullName() }}</strong>
                                                @endif
                                                {{ __($entry->activity_type) }}
                                                @if (isset($entry->activity_title))
                                                    <strong>"{{ __($entry->activity_title) }}"</strong>
                                                @endif
                                            </div>
                                            <div class="opacity-50">
                                                {{ $entry->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            @if (isset($entry->url))
                                                <x-button
                                                    size="sm"
                                                    href="{{ $entry->url }}"
                                                >
                                                    {{ __('Go') }}
                                                </x-button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot:body>
                        </x-table>
                    @endif
                </x-card>
            </div>

            <x-card size="none">
                <x-slot:head
                    class="mb-2"
                >
                    <h4 class="m-0 text-base font-medium">
                        {{ __('Latest Transactions') }}
                    </h4>
                </x-slot:head>

                <x-table variant="plain">
                    <x-slot:head>
                        <tr>
                            <th class="ps-6">
                                {{ __('Method') }}
                            </th>
                            <th>
                                {{ __('Status') }}
                            </th>
                            <th>
                                {{ __('Info') }}
                            </th>
                            <th colspan="3">
                                {{ __('Plan') }} / {{ __('Words') }} / {{ __('Images') }}
                            </th>
                        </tr>
                    </x-slot:head>

                    <x-slot:body>
                        @foreach ($latestOrders as $order)
                            <tr>
                                <td class="ps-6">
                                    {{ __($order->payment_type) }}
                                </td>

                                @php
                                    switch ($order->status) {
                                        case 'Success':
                                            $badge_type = 'success';
                                            break;
                                        case 'Waiting':
                                            $badge_type = 'secondary';
                                            break;
                                        case 'Approved':
                                            $badge_type = 'success';
                                            break;
                                        case 'Rejected':
                                            $badge_type = 'danger';
                                            break;
                                        default:
                                            $badge_type = 'default';
                                            break;
                                    }
                                @endphp
                                <td>
                                    <x-badge
                                        class="text-[12px]"
                                        variant="{{ $badge_type }}"
                                    >
                                        {{ __($order->status) }}
                                    </x-badge>
                                </td>

                                <td class="text-foreground/60">
                                    <span class="text-heading-foreground">
                                        {{ $order->user->fullName() }}
                                    </span>
                                    <br>
                                    <span class="opacity-70">
                                        {{ __($order->type) }}
                                    </span>
                                </td>

                                <td
                                    class="w-1"
                                    colspan="3"
                                >
                                    <span class="font-medium text-primary">
                                        {{ @$order->plan->name ?? 'Archived Plan' }}
                                    </span>
                                    /<span class="ms-1 text-heading-foreground">
                                        {{ @$order->plan->total_words === '-1' ? __('Unlimited') : @$order->plan->total_words ?? '-' }}
                                    </span>
                                    /<span class="ms-1 text-heading-foreground">
                                        {{ @$order->plan->total_images === '-1' ? __('Unlimited') : @$order->plan->total_images ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot:body>
                </x-table>
            </x-card>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        (() => {
            "use strict";

            function mapRange(value, in_min, in_max, out_min, out_max) {
                return ((value - in_min) * (out_max - out_min)) / (in_max - in_min) + out_min;
            }

            @php
                $daily_usages = json_decode(cache('daily_usages'));

                if (empty($daily_usages) || !is_array($daily_usages)) {
                    $daily_usages = [];
                }

                $daily_users = json_decode(cache('daily_users'));

                if (empty($daily_users) || !is_array($daily_users)) {
                    $daily_users = [];
                }

                $daily_sales = json_decode(cache('daily_sales'));

                if (empty($daily_sales) || !is_array($daily_sales)) {
                    $daily_sales = [];
                }
            @endphp

            const currentDate = new Date();
            const targetDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 0, 1);
            const firstDayOfMonth = new Date(targetDate.getFullYear(), targetDate.getMonth(), 1);
            const lastDayOfMonth = new Date(targetDate.getFullYear(), targetDate.getMonth() + 1, 0);

            // Start Sales Chart
            const dailySalesChartOptions = {
                series: [{
                    name: 'Sales',
                    data: [
                        @foreach ($daily_sales as $dailySales)
                            [{{ strtotime($dailySales->days) * 1000 }}, {{ $dailySales->sums }}],
                        @endforeach
                    ]
                }],
                chart: {
                    id: 'area-datetime',
                    type: 'area',
                    height: 210,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    show: false,
                },
                stroke: {
                    show: false,
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        offsetY: 0,
                        style: {
                            colors: 'hsl(var(--foreground) / 40%)',
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                yaxis: {
                    labels: {
                        offsetX: -15,
                        style: {
                            colors: 'hsl(var(--foreground) / 40%)',
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy'
                    }
                },
                stroke: {
                    width: 2,
                    colors: ['hsl(var(--primary))'],
                    curve: 'smooth'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.6,
                        stops: [0, 100],
                        colorStops: [
                            [{
                                    offset: 50,
                                    color: 'hsl(var(--primary))',
                                    opacity: 0.1
                                },
                                {
                                    offset: 150,
                                    color: '#6A22C5',
                                    opacity: 0
                                },
                            ]
                        ]
                    }
                },
            };

            const dailySalesChart = new ApexCharts(document.querySelector("#chart-daily-sales"), dailySalesChartOptions);
            dailySalesChart.render();
            // End Sales Chart

            // Start Usage Chart
            const dailyUsageChartOptions = {
                series: [{
                    name: 'Words',
                    data: [
                        @foreach ($daily_usages as $dailySales)
                            '{{ (int) $dailySales->sumsWord }}',
                        @endforeach
                    ]
                }, {
                    name: 'Images',
                    data: [
                        @foreach ($daily_usages as $dailySales)
                            '{{ (int) $dailySales->sumsImage }}',
                        @endforeach
                    ]
                }],
                colors: ['hsl(var(--primary))', 'hsl(var(--primary) / 15%)'],
                chart: {
                    type: 'bar',
                    height: 260,
                    stacked: true,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '100%',
                        borderRadius: 5,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    show: false
                },
                xaxis: {
                    type: 'datetime',
                    categories: [
                        @foreach ($daily_usages as $dailySales)
                            '{{ $dailySales->days }}',
                        @endforeach
                    ],
                    labels: {
                        offsetY: 0,
                        style: {
                            colors: 'hsl(var(--foreground) / 40%)',
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                    min: firstDayOfMonth.getTime(),
                    max: lastDayOfMonth.getTime(),
                },
                yaxis: {
                    labels: {
                        offsetX: -10,
                        style: {
                            colors: 'hsl(var(--foreground) / 40%)',
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy'
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['var(--background)', 'var(--background)']
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetY: 0,
                    offsetX: -40,
                    markers: {
                        width: 8,
                        height: 8,
                        radius: 10,
                    },
                    itemMargin: {
                        horizontal: 15,
                    },
                },
                fill: {
                    opacity: 1
                }
            };

            const dailyUsageChart = new ApexCharts(document.querySelector("#chart-daily-usages"), dailyUsageChartOptions);

            // Function to update chart based on selected month
            function updateChartForMonth(monthOffset) {

                targetDate.setMonth(targetDate.getMonth() + monthOffset); // Adjust the target date
                var firstDayOfMonth = new Date(targetDate.getFullYear(), targetDate.getMonth(), 1);
                var lastDayOfMonth = new Date(targetDate.getFullYear(), targetDate.getMonth() + 1, 0);


                dailyUsageChart.updateOptions({
                    xaxis: {
                        min: firstDayOfMonth.getTime(),
                        max: lastDayOfMonth.getTime(),
                    }
                });
            }

            // Example buttons to navigate to previous and next months
            document.getElementById('btnPreviousMonth').addEventListener('click', function() {
                updateChartForMonth(-1);
            });

            document.getElementById('btnNextMonth').addEventListener('click', function() {
                updateChartForMonth(1);
            });

            dailyUsageChart.render();
            // End Usage Chart

            // Start Popular Plans Chart
            const data = @json($popular_plans_data);
            const dataLength = data.length;
            const series = [];
            let minBubbleRadius = 40;
            let maxBubbleRadius = 90;

            // first, add invisible data in all 4 corners to prevent overflow hidden
            series.push({
                name: '',
                data: [
                    [-2, -2, 0]
                ],
                color: '#ffffff00'
            }, {
                name: '',
                data: [
                    [dataLength + 2, -2, 0]
                ],
                color: '#ffffff00'
            }, {
                name: '',
                data: [
                    [dataLength + 2, dataLength + 2, 0]
                ],
                color: '#ffffff00'
            }, {
                name: '',
                data: [
                    [-2, dataLength + 2, 0]
                ],
                color: '#ffffff00'
            });

            // adding actual data
            // Find the biggest value
            let biggestValue = data[0];
            for (let i = 1; i < dataLength; i++) {
                if (data[i]?.value > biggestValue?.value) {
                    const cache = data[i];
                    biggestValue = cache;
                    data.splice(1, i);
                    data.unshift(cache);
                }
            }

            // Calculate the coordinates for the biggest value
            let centerX = dataLength <= 1 ? 0.5 : Math.round((dataLength / 2) - 0.5);
            let centerY = dataLength <= 1 ? 0.5 : Math.round(dataLength / 2);

            // Add the biggest value in the middle of the chart
            series.push({
                name: biggestValue?.label,
                data: [
                    [centerX, centerY, mapRange(biggestValue?.value, 0, biggestValue?.value, minBubbleRadius, maxBubbleRadius)]
                ],
                color: biggestValue?.color
            });

            // Calculate the remaining coordinates
            let angle = 0;
            let angleIncrement = (2 * Math.PI) / dataLength;
            for (let i = 0; i < dataLength; i++) {
                if (data[i]?.label === biggestValue?.label) continue;

                let radius = Math.random() + 2;
                let x = centerX + radius * Math.cos(angle);
                let y = Math.min(dataLength + 1, centerY + radius * Math.sin(angle) + 1);
                let value = data[i]?.value;

                series.push({
                    name: data[i]?.label,
                    data: [
                        [x, y, mapRange(value, 0, biggestValue?.value, minBubbleRadius, maxBubbleRadius)]
                    ],
                    color: data[i]?.color
                });

                angle += angleIncrement;
            }

            const popularPlansChartOptions = {
                series,
                chart: {
                    height: 300,
                    type: 'bubble',
                    dropShadow: {
                        enabled: false,
                        enabledOnSeries: undefined,
                        top: 4,
                        left: 0,
                        blur: 6,
                        color: '#000',
                        opacity: 0.1
                    },
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts, e, o, v) {
                        if (typeof val === 'undefined' || opts.seriesIndex <= 3) {
                            return '';
                        }

                        let total = 0;
                        for (let i = 0; i < dataLength; i++) {
                            total += data[i]?.value;
                        }

                        let percentage = Math.round((data[opts.seriesIndex - 4]?.value / total) * 100);
                        if (isNaN(percentage)) {
                            percentage = 0;
                        }
                        return `${percentage}%`;
                    },
                    style: {
                        fontFamily: 'var(--font-heading)',
                        fontSize: '18px',
                        fontWeight: 600,
                        colors: ['hsl(var(--foreground))'],
                    },
                },
                plotOptions: {
                    bubble: {
                        zScaling: false,
                        minBubbleRadius,
                        maxBubbleRadius,
                    }
                },
                grid: {
                    show: false,
                },
                stroke: {
                    show: false,
                    width: 0
                },
                markers: {
                    strokeWidth: 0,
                },
                tooltip: {
                    enabled: false
                },
                xaxis: {
                    labels: {
                        show: false
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    }
                },
                yaxis: {
                    labels: {
                        show: false
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    }
                }
            };

            const popularPlansChart = new ApexCharts(document.querySelector("#popular-plans-chart"), popularPlansChartOptions);
            popularPlansChart.render();
            // End Popular Plans Chart

            const dailyUserChartOptions = {
                series: [{
                    name: 'Total',
                    data: [
                        @foreach ($daily_users as $user)
                            '{{ (int) $user->total }}',
                        @endforeach
                    ]
                }],
                colors: ['hsl(var(--primary))', 'hsl(var(--primary) / 15%)'],
                chart: {
                    type: 'bar',
                    height: 260,
                    stacked: true,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '100%',
                        borderRadius: 5,
                    },
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    show: false
                },
                xaxis: {
                    type: 'datetime',
                    categories: [
                        @foreach ($daily_users as $users)
                            '{{ $users->days }}',
                        @endforeach
                    ],
                    labels: {
                        offsetY: 0,
                        style: {
                            colors: 'hsl(var(--foreground) / 40%)',
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                    min: firstDayOfMonth.getTime(),
                    max: lastDayOfMonth.getTime(),
                },
                yaxis: {
                    labels: {
                        offsetX: -10,
                        style: {
                            colors: 'hsl(var(--foreground) / 40%)',
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy'
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['var(--background)', 'var(--background)']
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetY: 0,
                    offsetX: -40,
                    markers: {
                        width: 8,
                        height: 8,
                        radius: 10,
                    },
                    itemMargin: {
                        horizontal: 15,
                    },
                },
                fill: {
                    opacity: 1
                }
            };

            // Start New Users Chart
            const newUsersChart = new ApexCharts(document.querySelector("#new-users-chart"), dailyUserChartOptions);
            newUsersChart.render();
            // End New Users Chart

            // Start Popular Tools Chart
            const popularToolsData = @json($popular_tools_data);
            const popularToolsChartOptions = {
                series: [],
                labels: [],
                colors: [],
                chart: {
                    height: 210,
                    type: 'donut',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '90%',
                            labels: {
                                show: true,
                                name: {
                                    show: false
                                },
                                value: {
                                    show: true,
                                    fontSize: '36px',
                                    fontFamily: 'var(--headings-font-family)',
                                    fontWeight: 700,
                                    color: 'hsl(var(--heading-foreground)/70%)',
                                    formatter: function(val) {
                                        return `${val}%`;
                                    },
                                },
                            }
                        }
                    },
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    fontSize: '14px',
                    fontFamily: 'var(--font-body)',
                    fontWeight: 400,
                    formatter: function(seriesName, opts) {
                        return [seriesName, `<span>${opts.w.globals.series[opts.seriesIndex]}%</span>`];
                    },
                    markers: {
                        width: 8,
                        height: 8,
                        radius: 2,
                    },
                    itemMargin: {
                        horizontal: 0,
                        vertical: 0
                    }
                },
                stroke: {
                    colors: ['hsl(var(--border))']
                },
                responsive: [{
                    breakpoint: 501,
                    options: {
                        chart: {
                            height: 500,
                        },
                        legend: {
                            position: 'bottom',
                        }
                    }
                }]
            };

            popularToolsData.forEach(tool => {
                popularToolsChartOptions.series.push(Number(tool.value));
                popularToolsChartOptions.labels.push(tool.label);
                popularToolsChartOptions.colors.push(tool.color);
            });

            const popularToolsChart = new ApexCharts(document.querySelector("#popular-tools-chart"), popularToolsChartOptions);
            popularToolsChart.render();
            // End Popular Tools Chart

        })();
    </script>
@endpush
