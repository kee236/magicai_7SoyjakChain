<div
    class="row row-deck row-cards"
    id="overview"
>
    <div class="col-12">
        <div class="card !bg-gradient-to-b from-[#F4E3FD] from-0% to-[#F1EEFF] to-100% dark:from-pink-300/10 dark:to-transparent max-md:text-center">
            <div class="card-body px-10 py-8">
                <div class="row align-items-center subheader text-[1em] font-medium leading-[1.5em]">
                    <div class="col-12 col-md-6 col-lg-5 max-md:mb-5">
                        <h3 class="mb-4">
                            @lang('Invite your colleagues and <br> collaborators to join your team and <br>maximize the benefits of AI.')
                        </h3>
                        <div class="flex flex-wrap justify-center gap-2 md:justify-start">
                            <a
                                class="btn hover:bg-green-500 hover:text-white dark:!bg-[rgba(255,255,255,0.2)]"
                                href="#email"
                                onclick="document.getElementById('email').focus()"
                            >
                                <svg
                                    class="!me-2"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
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
                                    ></path>
                                    <path d="M12 5l0 14"></path>
                                    <path d="M5 12l14 0"></path>
                                </svg>
                                @lang('Add a New Member')
                            </a>
                            @if (!$subscription || $user?->relationPlan?->is_team_plan == 0)
                                <a
                                    class="btn hover:bg-green-500 hover:text-white dark:!bg-[rgba(255,255,255,0.2)]"
                                    href="{{ route('dashboard.user.payment.subscription') }}"
                                >
                                    @lang('Upgrade Team Plan')
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-6 ms-auto">
                        <div class="d-flex md:justify-content-end w-full justify-center">
                            <div class="d-flex flex-column align-items-center">
                                <p class="text-heading font-bold">@lang('Team Members')</p>
                                <p
                                    class="fs-1 text-heading p-3 font-bold"
                                    style="font-size: 58px !important;"
                                >{{ $app_is_demo? 4 :$team->members->count() }}</p>
                                <p class="mb-0 p-0">@lang('Allowed Seats'): <b class="text-heading fw-bold">{{ $app_is_demo? 2 : $team->allow_seats }}</b></p>
                                <p class="mb-0 p-0">@lang('Total Words Generated'): <b class="text-heading fw-bold">{{ $app_is_demo? 2400 :$team->members?->sum('used_word_credit') }}</b></p>
                                <p class="mb-0 p-0">@lang('Total Images Generated'): <b class="text-heading fw-bold">{{ $app_is_demo? 2400 :$team->members?->sum('used_image_credit') }}</b></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
