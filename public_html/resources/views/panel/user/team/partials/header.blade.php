@props([
    'id' => 'overview',
])

<div class="row g-2 items-end justify-between max-md:flex-col max-md:items-start max-md:gap-4">
    <div class="col">
        <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
           class="page-pretitle flex items-center">
            <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                 xmlns="http://www.w3.org/2000/svg">
                <path
                        d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
            </svg>
            {{ __('Back to dashboard') }}
        </a>
        <h2 class="page-title mb-2">
            {{ __('Team Members') }}
        </h2>
        <div class="flex items-center flex-wrap !mt-5">
            <div class="flex flex-wrap items-center gap-3">
                <form id="filterForm" method="GET">
                    <ul
                            class="flex flex-wrap items-center m-0 p-0 list-none text-[13px] text-[#2B2F37] gap-[20px] max-sm:gap-[10px]">
                        <li>
                            <a href="#overview" data-filter-trigger="all"
                               class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5 {{ $id == 'overview' ? 'active' : '' }}">
                                {{ __('Overview') }}
                            </a>
                        </li>
                        <li>
                            <a href="#invite-a-friend" data-filter-trigger="favorite"
                               class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5  {{ $id == 'invite-a-friend' ? 'active' : '' }}">
                                {{ __('Invite a Friend') }}
                            </a>
                        </li>
                        <li>
                            <a href="#team-members" data-filter-trigger="favorite"
                               class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5  {{ $id == 'team-members' ? 'active' : '' }}">
                                {{ __('Team Members') }}
                            </a>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>