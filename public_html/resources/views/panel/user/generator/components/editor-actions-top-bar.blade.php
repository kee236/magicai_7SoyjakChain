<div
    class="lqd-generator-actions-top-bar relative flex h-[--editor-tb-h] justify-between border-b border-e-0 border-s-0 border-t-0 border-solid border-[--tblr-border-color] bg-[--tblr-body-bg]"
    x-data="{ mobileOptionsShow: false }"
>
    <div class="flex w-full justify-between !gap-4 lg:!hidden">
        <a
            class="text-heading flex grow basis-1/3 items-center !gap-2 !px-4 text-[14px] font-medium leading-tight lg:hidden"
            href="#"
            x-data
            @click.prevent="mobileOptionsShow = !mobileOptionsShow"
        >
            <svg
                class="hidden !h-5 !w-5"
                :class="{ hidden: !mobileOptionsShow }"
                xmlns="http://www.w3.org/2000/svg"
                width="44"
                height="44"
                viewBox="0 0 24 24"
                stroke-width="1.5"
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
                <path d="M18 6l-12 12" />
                <path d="M6 6l12 12" />
            </svg>
            <svg
                class="!h-5 !w-5"
                :class="{ hidden: mobileOptionsShow }"
                xmlns="http://www.w3.org/2000/svg"
                width="44"
                height="44"
                viewBox="0 0 24 24"
                stroke-width="1.5"
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
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M8 12l0 .01" />
                <path d="M12 12l0 .01" />
                <path d="M16 12l0 .01" />
            </svg>
            {{ __('Options') }}
        </a>

        <a
            class="flex shrink-0 basis-1/3 items-center justify-center text-center"
            href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
        >
            @if (isset($setting->logo_dashboard))
                <img
                    class="h-auto max-h-8 w-full shrink-0 dark:hidden"
                    src="/{{ $setting->logo_dashboard_path }}"
                    @if (isset($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto max-h-8 w-full shrink-0 dark:block"
                    src="/{{ $setting->logo_dashboard_dark_path }}"
                    @if (isset($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @else
                <img
                    class="h-auto max-h-8 w-full shrink-0 dark:hidden"
                    src="/{{ $setting->logo_path }}"
                    @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
                <img
                    class="hidden h-auto max-h-8 w-full shrink-0 dark:block"
                    src="/{{ $setting->logo_dark_path }}"
                    @if (isset($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                    alt="{{ $setting->site_name }}"
                >
            @endif
        </a>

        {{-- There is a separate save button for mobile at bottom --}}
        <a
            class="text-heading flex basis-1/3 items-center justify-end !gap-1 !px-4 !py-4 text-[14px] font-medium leading-tight"
            id="web_hook_save"
        >
            {{ __('Save') }}
            <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                stroke-width="1.5"
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
                <path d="M20.94 13.045a9 9 0 1 0 -8.953 7.955" />
                <path d="M3.6 9h16.8" />
                <path d="M3.6 15h9.4" />
                <path d="M11.5 3a17 17 0 0 0 0 18" />
                <path d="M12.5 3a16.991 16.991 0 0 1 2.529 10.294" />
                <path d="M16 22l5 -5" />
                <path d="M21 21.5v-4.5h-4.5" />
            </svg>
        </a>
    </div>

    <div
        class="flex w-full justify-between max-lg:invisible max-lg:absolute max-lg:start-0 max-lg:top-full max-lg:flex-col max-lg:bg-[--tblr-body-bg] max-lg:shadow-lg max-lg:shadow-black/10 max-lg:[&.active]:visible"
        x-data
        :class="{ 'active': mobileOptionsShow }"
    >
        <div class="lqd-generator-actions-top-bar-col-start flex w-[--sidebar-w] max-lg:w-full max-lg:flex-col">
            <a
                class="text-heading flex items-center !gap-1 !px-5 !py-4 text-[12px] font-medium leading-tight"
                href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
            >
                <svg
                    class="!h-4 !w-4"
                    xmlns="http://www.w3.org/2000/svg"
                    width="44"
                    height="44"
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
                    <path d="M5 12l14 0" />
                    <path d="M5 12l6 6" />
                    <path d="M5 12l6 -6" />
                </svg>
                {{ __('Back to Dashboard') }}
            </a>

            <hr class="m-0 h-full w-px border-none bg-black/5 dark:bg-white/5">

            <form
                class="relative grow"
                action="#"
            >
                <input
                    class="peer w-full border-none bg-transparent !px-5 !py-4 text-[12px] font-medium text-inherit focus:outline-none max-lg:border max-lg:border-e-0 max-lg:border-s-0 max-lg:border-t max-lg:border-solid max-lg:border-[--tblr-border-color]"
                    id="document_title"
                    type="text"
                    placeholder="@lang('Untitled Document')"
                    value="@lang('Untitled Document')"
                />
                <span
                    class="pointer-events-none absolute !end-3 top-1/2 inline-flex !h-7 !w-7 -translate-y-1/2 items-center justify-center rounded-full border border-solid border-[--tblr-border-color] shadow-sm transition-opacity peer-focus:opacity-0"
                >
                    <svg
                        class="!h-4 !w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        width="44"
                        height="44"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
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
                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                        <path d="M13.5 6.5l4 4" />
                    </svg>
                </span>
            </form>

            <hr class="m-0 h-full w-px border-none bg-black/5 dark:bg-white/5">
        </div>

        <div
            class="lqd-generator-actions-top-bar-col-middle flex grow transition-opacity duration-[0.4s] group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:pointer-events-none group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:opacity-10 max-lg:-order-1 max-lg:hidden">
            <div class="flex w-full items-center justify-center text-center">
                <a
                    class="shrink-0"
                    href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                >
                    @if (isset($setting->logo_dashboard))
                        <img
                            class="h-auto max-h-8 w-full dark:hidden"
                            src="/{{ $setting->logo_dashboard_path }}"
                            @if (isset($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                        <img
                            class="hidden h-auto max-h-8 w-full dark:block"
                            src="/{{ $setting->logo_dashboard_dark_path }}"
                            @if (isset($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                    @else
                        <img
                            class="h-auto max-h-8 w-full dark:hidden"
                            src="/{{ $setting->logo_path }}"
                            @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                        <img
                            class="hidden h-auto max-h-8 w-full dark:block"
                            src="/{{ $setting->logo_dark_path }}"
                            @if (isset($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                            alt="{{ $setting->site_name }}"
                        >
                    @endif
                </a>
            </div>
        </div>

        <div
            class="lqd-generator-actions-top-bar-col-end flex min-w-[--sidebar-w] justify-end transition-opacity duration-[0.4s] group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:pointer-events-none group-[&:not(.lqd-generator-sidebar-collapsed)]/generator:opacity-10 max-lg:flex-col max-lg:!opacity-100">
            <hr class="m-0 h-full w-px border-none bg-black/5 dark:bg-white/5">

            <div class="flex items-center !gap-2 !px-7 max-lg:justify-center max-lg:py-2">
                <div class="flex !gap-1 rtl:flex-row-reverse">
                    <button
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                        id="workbook_undo"
                        title="{{ __('Undo') }}"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
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
                            <path d="M9 14l-4 -4l4 -4" />
                            <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                        </svg>
                        <span class="sr-only">{{ __('Undo') }}</span>
                    </button>
                    <button
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                        id="workbook_redo"
                        title="{{ __('Redo') }}"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
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
                            <path d="M15 14l4 -4l-4 -4" />
                            <path d="M19 10h-11a4 4 0 1 0 0 8h1" />
                        </svg>
                        <span class="sr-only">{{ __('Redo') }}</span>
                    </button>
                </div>

                <button
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                    id="workbook_copy"
                    title="{{ __('Copy to clipboard') }}"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
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
                        <path
                            d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z"
                        />
                        <path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                    </svg>
                    <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                </button>

                <button
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                    id="workbook_print"
                    title="{{ __('Print') }}"
                    onclick="return tinymce?.activeEditor?.execCommand('mcePrint');"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
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
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                    </svg>
                    <span class="sr-only">{{ __('Print') }}</span>
                </button>

                <div class="relative">
                    <button
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                        data-bs-toggle="dropdown"
                        title="{{ __('Download') }}"
                        tabindex="-1"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
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
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                        <span class="sr-only">{{ __('Download') }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end whitespace-nowrap p-0 text-center [--tblr-dropdown-min-width:150px]">
                        <div class="flex flex-col gap-0 p-0">
                            <button
                                class="workbook_download text-heading flex items-center !gap-2 rounded-md border-none bg-[transparent] p-2 text-[12px] font-medium hover:bg-slate-100 dark:hover:bg-zinc-900"
                                data-doc-type="doc"
                                data-doc-name="{{ __('MagicAI Doc') }}"
                            >
                                <svg
                                    class="shrink-0"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M4 18h9v-12l-5 2v5l-4 2v-8l9 -4l7 2v13l-7 3z"></path>
                                </svg>
                                MS Word
                            </button>
                            <button
                                class="workbook_download text-heading flex items-center !gap-2 rounded-md border-none bg-[transparent] p-2 text-[12px] font-medium hover:bg-slate-100 dark:hover:bg-zinc-900"
                                data-doc-type="pdf"
                                data-doc-name="{{ __('MagicAI Doc') }}"
                            >
                                <svg
                                    class="shrink-0"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
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
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                                    <path d="M17 18h2" />
                                    <path d="M20 15h-3v6" />
                                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                                </svg>
                                PDF
                            </button>
                            <button
                                class="workbook_download text-heading flex items-center !gap-2 rounded-md border-none bg-[transparent] p-2 text-[12px] font-medium hover:bg-slate-100 dark:hover:bg-zinc-900"
                                data-doc-type="html"
                                data-doc-name="{{ __('MagicAI Doc') }}"
                            >
                                <svg
                                    class="shrink-0"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M20 4l-2 14.5l-6 2l-6 -2l-2 -14.5z"></path>
                                    <path d="M15.5 8h-7l.5 4h6l-.5 3.5l-2.5 .75l-2.5 -.75l-.1 -.5"></path>
                                </svg>
                                HTML
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-[13px] text-inherit transition-colors hover:!bg-black/5 dark:hover:!bg-white/5"
                    title="{{ __('Add New Document') }}"
                    x-init
                    @click.prevent="toggleSideNavCollapse('expand')"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
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
                        <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                        <path d="M15 12h-6" />
                        <path d="M12 9v6" />
                    </svg>
                    <span class="sr-only">{{ __('Add New Document') }}</span>
                </button>

            </div>

            <hr class="m-0 h-full w-px border-none bg-black/5 dark:bg-white/5">

            {{-- There is a separate save button for mobile on top --}}
            <a
                class="text-heading flex items-center !gap-1 !py-4 px-11 text-[14px] font-medium leading-tight max-lg:hidden"
                id="web_hook_save"
            >
                {{ __('Save') }}
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
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
                    <path d="M20.94 13.045a9 9 0 1 0 -8.953 7.955" />
                    <path d="M3.6 9h16.8" />
                    <path d="M3.6 15h9.4" />
                    <path d="M11.5 3a17 17 0 0 0 0 18" />
                    <path d="M12.5 3a16.991 16.991 0 0 1 2.529 10.294" />
                    <path d="M16 22l5 -5" />
                    <path d="M21 21.5v-4.5h-4.5" />
                </svg>
            </a>
        </div>
    </div>
</div>
