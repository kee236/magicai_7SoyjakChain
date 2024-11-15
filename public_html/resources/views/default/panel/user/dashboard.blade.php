@php
    $plan = Auth::user()->activePlan();
    $plan_type = 'regular';
    // $team = Auth::user()->getAttribute('team');
    $teamManager = Auth::user()->getAttribute('teamManager');

    if ($plan != null) {
        $plan_type = strtolower($plan->plan_type);
    }

    $titlebar_links = [
        [
            'label' => 'All',
            'link' => '#all',
        ],
        [
            'label' => 'AI Assistant',
            'link' => '#all',
        ],
        [
            'label' => 'Your Plan',
            'link' => '#plan',
        ],
        [
            'label' => 'Team Members',
            'link' => '#team',
        ],
        [
            'label' => 'Recent',
            'link' => '#recent',
        ],
        [
            'label' => 'Documents',
            'link' => '#documents',
        ],
        [
            'label' => 'Templates',
            'link' => '#templates',
        ],
        [
            'label' => 'Overview',
            'link' => '#all',
        ],
    ];

@endphp

@push('css')
    <style>
        @if (setting('announcement_background_color'))
            .lqd-card.lqd-announcement-card {
                background-color: {{ setting('announcement_background_color') }};
            }
        @endif
        @if (setting('announcement_background_image'))
            .lqd-card.lqd-announcement-card {
                background-image: url({{ setting('announcement_background_image') }});
            }
        @endif
        @if (setting('announcement_background_color_dark'))
            .theme-dark .lqd-card.lqd-announcement-card {
                background-color: {{ setting('announcement_background_color_dark') }};
            }
        @endif
        @if (setting('announcement_background_image_dark'))
            .theme-dark .lqd-card.lqd-announcement-card {
                background-image: url({{ setting('announcement_background_image_dark') }});
            }
        @endif
    </style>
@endpush

@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Dashboard'))
@section('titlebar_title')
    {{ __('Welcome') }}, {{ auth()->user()->name }}.
@endsection
@section('titlebar_after')
    <ul
        class="lqd-filter-list mt-1 flex list-none flex-wrap items-center gap-x-4 gap-y-2 text-heading-foreground max-sm:gap-3"
        x-data
    >
        @foreach ($titlebar_links as $link)
            <li>
                <x-button
                    @class([
                        'lqd-filter-btn inline-flex rounded-full px-2.5 py-0.5 text-2xs leading-tight transition-colors hover:translate-y-0 hover:bg-foreground/5 [&.active]:bg-foreground/5',
                        'active' => $loop->first,
                    ])
                    variant="ghost"
                    href="{{ $link['link'] }}"
                    x-data
                >
                    @lang($link['label'])
                </x-button>
            </li>
        @endforeach
    </ul>
@endsection

@section('content')
    <div class="flex flex-wrap justify-between gap-8 py-5">
        <div
            class="grid w-full grid-cols-1 gap-10"
            id="all"
        >
            @if (setting('announcement_active', 0) && !auth()->user()->dash_notify_seen)
                <div
                    class="lqd-announcement"
                    x-data="{ show: true }"
                    x-ref="announcement"
                >
                    <script>
                        const announcementDismissed = localStorage.getItem('lqd-announcement-dismissed');
                        if (announcementDismissed) {
                            document.querySelector('.lqd-announcement').style.display = 'none';
                        }
                    </script>

                    <x-card
                        class="lqd-announcement-card relative bg-cover bg-center"
                        size="lg"
                        x-ref="announcementCard"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="mb-3">
                                    @lang(setting('announcement_title', 'Welcome'))
                                </h3>
                                <p class="mb-4">
                                    @lang(setting('announcement_description', 'We are excited to have you here. Explore the marketplace to find the best AI models for your needs.'))
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <x-button
                                        class="font-medium"
                                        href="{{ setting('announcement_url', '#') }}"
                                    >
                                        <x-tabler-plus class="size-4" />
                                        {{ setting('announcement_button_text', 'Try it Now') }}
                                    </x-button>
                                    <x-button
                                        class="font-medium"
                                        href="javascript:void(0)"
                                        variant="ghost-shadow"
                                        hover-variant="danger"
                                        @click.prevent="{{ $app_is_demo ? 'toastr.info(\'This feature is disabled in Demo version.\')' : ' dismiss()' }}"
                                    >
                                        @lang('Dismiss')
                                    </x-button>
                                </div>
                            </div>
                            @if (setting('announcement_image_dark'))
                                <img
                                    class="announcement-img announcement-img-dark peer hidden w-28 shrink-0 dark:block"
                                    src="{{ setting('announcement_image_dark', '/upload/images/speaker.png') }}"
                                    alt="@lang(setting('announcement_title', 'Welcome to MagicAI!'))"
                                >
                            @endif
                            <img
                                class="announcement-img announcement-img-light w-28 shrink-0 dark:peer-[&.announcement-img-dark]:hidden"
                                src="{{ setting('announcement_image', '/upload/images/speaker.png') }}"
                                alt="@lang(setting('announcement_title', 'Welcome to MagicAI!'))"
                            >
                        </div>
                    </x-card>
                </div>
            @endif
            <x-card size="lg">
                <h3 class="mb-6 flex items-center gap-3">
                    {{-- blade-formatter-disable --}}
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" > <path fill-rule="evenodd" clip-rule="evenodd" d="M18.7588 7.85618L17.1437 8.18336V8.18568C16.3659 8.34353 15.6517 8.72701 15.0905 9.28825C14.5292 9.8495 14.1458 10.5636 13.9879 11.3415L13.6607 12.9565C13.6262 13.1155 13.5383 13.2578 13.4117 13.3599C13.285 13.462 13.1273 13.5177 12.9646 13.5177C12.8019 13.5177 12.6442 13.462 12.5175 13.3599C12.3909 13.2578 12.303 13.1155 12.2685 12.9565L11.9413 11.3415C11.7837 10.5635 11.4003 9.84922 10.839 9.28793C10.2777 8.72663 9.56345 8.34324 8.78546 8.18568L7.17042 7.8585C7.00937 7.82552 6.86464 7.73795 6.76071 7.61058C6.65678 7.48321 6.60001 7.32386 6.60001 7.15946C6.60001 6.99507 6.65678 6.83572 6.76071 6.70835C6.86464 6.58098 7.00937 6.4934 7.17042 6.46043L8.78546 6.13324C9.56339 5.97554 10.2776 5.5921 10.8389 5.03084C11.4001 4.46957 11.7836 3.75536 11.9413 2.97743L12.2685 1.36239C12.303 1.20344 12.3909 1.06109 12.5175 0.959015C12.6442 0.856935 12.8019 0.80127 12.9646 0.80127C13.1273 0.80127 13.285 0.856935 13.4117 0.959015C13.5383 1.06109 13.6262 1.20344 13.6607 1.36239L13.9879 2.97743C14.1458 3.75529 14.5292 4.46943 15.0905 5.03067C15.6517 5.59192 16.3659 5.9754 17.1437 6.13324L18.7588 6.45811C18.9198 6.49108 19.0645 6.57866 19.1685 6.70603C19.2724 6.8334 19.3292 6.99275 19.3292 7.15714C19.3292 7.32154 19.2724 7.48089 19.1685 7.60826C19.0645 7.73563 18.9198 7.8232 18.7588 7.85618ZM6.94895 16.0393L6.51038 16.1286C5.96946 16.2383 5.47282 16.5037 5.08244 16.8939C4.69206 17.2841 4.42523 17.7806 4.31524 18.3214L4.2259 18.76C4.202 18.8835 4.13584 18.9949 4.03877 19.075C3.9417 19.1551 3.81978 19.1989 3.69394 19.1989C3.56809 19.1989 3.44617 19.1551 3.3491 19.075C3.25204 18.9949 3.18587 18.8835 3.16197 18.76L3.07263 18.3214C2.96278 17.7805 2.69599 17.2839 2.30559 16.8937C1.91518 16.5035 1.41847 16.237 0.877485 16.1274L0.43892 16.0381C0.315366 16.0142 0.203985 15.948 0.123895 15.851C0.0438042 15.7539 0 15.632 0 15.5061C0 15.3803 0.0438042 15.2584 0.123895 15.1613C0.203985 15.0642 0.315366 14.9981 0.43892 14.9742L0.877485 14.8848C1.41862 14.7752 1.91545 14.5085 2.30587 14.1181C2.69629 13.7276 2.96299 13.2308 3.07263 12.6897L3.16197 12.2511C3.18587 12.1276 3.25204 12.0162 3.3491 11.9361C3.44617 11.856 3.56809 11.8122 3.69394 11.8122C3.81978 11.8122 3.9417 11.856 4.03877 11.9361C4.13584 12.0162 4.202 12.1276 4.2259 12.2511L4.31524 12.6897C4.42482 13.231 4.69148 13.728 5.08189 14.1186C5.4723 14.5092 5.96915 14.7761 6.51038 14.886L6.94895 14.9753C7.0725 14.9992 7.18388 15.0654 7.26397 15.1625C7.34407 15.2595 7.38787 15.3814 7.38787 15.5073C7.38787 15.6331 7.34407 15.7551 7.26397 15.8521C7.18388 15.9492 7.0725 16.0154 6.94895 16.0393Z" fill="url(#paint0_linear_213_525)" /> <defs> <linearGradient id="paint0_linear_213_525" x1="1.1976e-07" y1="4.55439" x2="15.5124" y2="18.9291" gradientUnits="userSpaceOnUse" > <stop stop-color="#82E2F4" /> <stop offset="0.502" stop-color="#8A8AED" /> <stop offset="1" stop-color="#6977DE" /> </linearGradient> </defs> </svg>
					{{-- blade-formatter-enable --}}
                    @lang('Hey, How can I help you?')
                </h3>
                <x-header-search
                    class="mb-5 w-full"
                    class:input="bg-background border-none h-12 text-heading-foreground shadow-[0_4px_8px_rgba(0,0,0,0.05)] placeholder:text-heading-foreground"
                    size="lg"
                    in-content
                />
                <x-button
                    class="group text-[12px] font-medium text-heading-foreground"
                    variant="link"
                    href="{{ $setting->feature_ai_advanced_editor ? LaravelLocalization::localizeUrl(route('dashboard.user.generator.index')) : LaravelLocalization::localizeUrl(route('dashboard.user.openai.list')) }}"
                >
                    @lang('Create a Blank Document')
                    <span
                        class="size-9 inline-flex items-center justify-center rounded-full bg-background shadow transition-all group-hover:scale-110 group-hover:bg-heading-foreground group-hover:text-header-background"
                    >
                        <x-tabler-plus class="size-4" />
                    </span>
                </x-button>
            </x-card>
        </div>

        @if ($ongoingPayments != null)
            <div class="w-full">
                @include('panel.user.finance.ongoingPayments')
            </div>
        @endif

        <x-card
            class="{{ showTeamFunctionality() ? 'lg:w-[48%]' : 'lg:w-full' }} w-full text-center"
            id="plan"
            size="lg"
        >
            @include('panel.user.finance.subscriptionStatus')
        </x-card>

        @if (showTeamFunctionality())
            <x-card
                class="w-full lg:w-[48%]"
                id="team"
                size="lg"
            >
                @if ($team)
                    <figure class="mb-7">
                        <img
                            class="mx-auto w-full lg:w-7/12"
                            src="{{ custom_theme_url('assets/img/team/team.png') }}"
                            alt="Team"
                        >
                    </figure>
                    <p class="mb-6 text-center text-xl font-semibold">
                        @lang('Add your team members’ email address <br> to start collaborating.')
                        📧
                    </p>
                    <form
                        class="flex flex-col gap-3"
                        action="{{ route('dashboard.user.team.invitation.store', $team->id) }}"
                        method="post"
                    >
                        @csrf
                        <input
                            type="hidden"
                            name="team_id"
                            value="{{ $team?->id }}"
                        >
                        <x-forms.input
                            id="email"
                            size="lg"
                            type="email"
                            name="email"
                            placeholder="{{ __('Email address') }}"
                            required
                        >
                            <x-slot:icon>
                                <x-tabler-mail class="size-5 absolute end-3 top-1/2 -translate-y-1/2" />
                            </x-slot:icon>
                        </x-forms.input>
                        @if ($app_is_demo)
                            <x-button onclick="return toastr.info('This feature is disabled in Demo version.')">
                                @lang('Invite Friends')
                            </x-button>
                        @else
                            <x-button
                                data-name="{{ \App\Enums\Introduction::AFFILIATE_SEND }}"
                                type="submit"
                            >
                                @lang('Invite Friends')
                            </x-button>
                        @endif
                    </form>
                @else
                    <h3 class="mb-6">
                        {{ __('How it Works') }}
                    </h3>

                    <ol class="mb-12 flex flex-col gap-4 text-heading-foreground">
                        <li>
                            <span class="size-7 me-2 inline-flex items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                                1
                            </span>
                            {!! __('You <strong>send your invitation link</strong> to your friends.') !!}
                        </li>
                        <li>
                            <span class="size-7 me-2 inline-flex items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                                2
                            </span>
                            {!! __('<strong>They subscribe</strong> to a paid plan by using your refferral link.') !!}
                        </li>
                        <li>
                            <span class="size-7 me-2 inline-flex items-center justify-center rounded-full bg-primary/10 font-extrabold text-primary">
                                3
                            </span>
                            {!! __('From their first purchase, you will begin <strong>earning recurring commissions</strong>.') !!}
                        </li>
                    </ol>

                    <form
                        class="flex flex-col gap-3"
                        id="send_invitation_form"
                        onsubmit="return sendInvitationForm();"
                    >
                        <x-forms.input
                            class:label="text-heading-foreground"
                            id="to_mail"
                            label="{{ __('Affiliate Link') }}"
                            size="sm"
                            type="email"
                            name="to_mail"
                            placeholder="{{ __('Email address') }}"
                            required
                        >
                            <x-slot:icon>
                                <x-tabler-mail class="size-5 absolute end-3 top-1/2 -translate-y-1/2" />
                            </x-slot:icon>
                        </x-forms.input>

                        <x-button
                            class="w-full rounded-xl"
                            id="send_invitation_button"
                            type="submit"
                            form="send_invitation_form"
                        >
                            {{ __('Send') }}
                        </x-button>
                    </form>
                @endif
            </x-card>
        @endif

        <x-card
            class="w-full"
            id="recent"
            size="lg"
        >
            <h3 class="mb-7">
                @lang('Recently Launched')
            </h3>

            <div
                class="lqd-docs-container group"
                data-view-mode="grid"
            >
                <div class="lqd-docs-list grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                    @foreach (Auth::user()->openai()->orderBy('updated_at', 'desc')->take(5)->get() as $entry)
                        @if ($entry->generator != null)
                            <x-documents.item
                                :$entry
                                style="extended"
                                trim="100"
                                hide-fav
                            />
                        @endif
                    @endforeach
                </div>
            </div>
        </x-card>

        <div
            class="grow basis-full md:basis-0"
            id="documents"
        >
            <x-card size="none">
                <x-slot:head>
                    <h4 class="m-0">{{ __('Documents') }}</h4>
                </x-slot:head>
                @foreach (Auth::user()->openai()->with('generator')->take(4)->get() as $entry)
                    @if ($entry->generator != null)
                        <x-documents.item :$entry />
                    @endif
                @endforeach
            </x-card>
        </div>

        <div
            class="grow basis-full md:basis-0"
            id="templates"
        >
            <x-card size="none">
                <x-slot:head>
                    <h4 class="m-0">{{ __('Favorite Templates') }}</h4>
                </x-slot:head>
                @foreach (\Illuminate\Support\Facades\Auth::user()->favoriteOpenai as $entry)
                    @php
                        $upgrade = false;
                        if ($entry->premium == 1 && $plan_type === 'regular') {
                            $upgrade = true;
                        }

                        if ($upgrade) {
                            $href = LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription'));
                        } else {
                            $href = LaravelLocalization::localizeUrl(route('dashboard.user.openai.generator', $entry->slug));
                        }
                    @endphp
                    @if ($upgrade || $entry->active == 1)
                        <a
                            class="lqd-fav-temp-item relative flex w-full flex-wrap items-center gap-3 border-b p-4 text-xs transition-colors last:border-none hover:bg-foreground/5"
                            href="{{ $href }}"
                        >
                        @else
                            <p class="lqd-fav-temp-item relative flex w-full flex-wrap items-center gap-3 border-b p-4 text-xs last:border-none">
                    @endif
                    <x-lqd-icon
                        size="lg"
                        style="background: {{ $entry->color }}"
                        active-badge
                        active-badge-condition="{{ $entry->active == 1 }}"
                    >
                        <span class="size-5 flex">
                            @if ($entry->image !== 'none')
                                {!! html_entity_decode($entry->image) !!}
                            @endif
                        </span>
                    </x-lqd-icon>
                    <span class="w-2/5 grow">
                        <span class="lqd-fav-temp-item-title block text-sm font-medium">
                            {{ __($entry->title) }}
                        </span>
                        <span class="lqd-fav-temp-item-desc opacity-45 block max-w-full overflow-hidden overflow-ellipsis whitespace-nowrap italic">
                            {{ str()->words(__($entry->description), 5) }}
                        </span>
                    </span>
                    <span class="flex flex-col whitespace-nowrap">
                        {{ __('in Workbook') }}
                        <span class="lqd-fav-temp-item-date opacity-45 italic">
                            {{ $entry->created_at->format('M d, Y') }}
                        </span>
                    </span>
                    @if ($upgrade)
                        <span class="absolute inset-0 flex items-center justify-center bg-background/50">
                            <x-badge
                                class="rounded-md py-1.5"
                                variant="info"
                            >
                                {{ __('Upgrade') }}
                            </x-badge>
                        </span>
                    @endif
                    @if ($upgrade || $entry->active == 1)
                        </a>
                    @else
                        </p>
                    @endif
                    @if ($loop->iteration == 4)
                    @break
                @endif
            @endforeach
        </x-card>
    </div>
</div>
@endsection

@push('script')
@includeWhen(\Illuminate\Support\Facades\Route::has('dashboard.introductions.index'), 'panel/admin/introduction/include/introduction')
<script>
    function dismiss() {
        // localStorage.setItem('lqd-announcement-dismissed', true);
        document.querySelector('.lqd-announcement').style.display = 'none';
        $.ajax({
            url: '{{ route('dashboard.user.dash_notify_seen') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                /* console.log(response); */
            }
        });
    }
</script>
@endpush
