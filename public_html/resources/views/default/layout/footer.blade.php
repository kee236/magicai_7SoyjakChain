<footer class="site-footer relative bg-black pb-11 pt-40 text-white">
    <div
        class="absolute inset-0"
        style="background-image: linear-gradient( 64.3deg, rgba(254,122,152,0.81) 17.7%, rgba(255,206,134,1) 64.7%, rgba(172,253,163,0.64) 112.1% );"
    >
    </div>
    <div class="absolute inset-x-0 -top-px">
        <svg
            class="w-full fill-background"
            preserveAspectRatio="none"
            width="1440"
            height="86"
            viewBox="0 0 1440 86"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path d="M0 85.662C240 29.1253 480 0.857 720 0.857C960 0.857 1200 29.1253 1440 85.662V0H0V85.662Z" />
        </svg>
    </div>
    <div class="relative">

        <hr class="border-white border-opacity-15">
        <div class="container">

            <p class="text-white text-center mt-2">hello@soyjakai.com</p>

            <p class="text-white text-center mt-4">
                <span>Sales and partnerships: sales@soyjakai.com</span>
                <span class="ml-2">Support request: support@soyjakai.com</span>
            </p>

            <div class="flex flex-wrap items-center justify-between gap-8 pb-7 pt-10 max-sm:justify-center">
                <a href="{{ route('index') }}">
                    @if (isset($setting->logo_2x_path))
                        <img
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            srcset="/{{ $setting->logo_2x_path }} 2x"
                            alt="{{ $setting->site_name }} logo"
                        >
                    @else
                        <img
                            src="{{ custom_theme_url($setting->logo_path, true) }}"
                            alt="{{ $setting->site_name }} logo"
                        >
                    @endif
                </a>
                <ul class="flex flex-wrap items-center gap-7 text-[14px] max-sm:justify-center">
                    @foreach (\App\Models\SocialMediaAccounts::where('is_active', true)->get() as $social)
                        <li>
                            <a
                                class="inline-flex items-center gap-2"
                                href="{{ $social['link'] }}"
                            >
                                <span class="w-3.5 [&_svg]:h-auto [&_svg]:w-full">
                                    {!! $social['icon'] !!}
                                </span>
                                {{ $social['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <ul class="flex flex-wrap items-center gap-7 text-[14px] max-sm:justify-center">
                    @foreach (\App\Models\Page::where(['status' => 1, 'show_on_footer' => 1])->get() ?? [] as $page)
                        <li>
                            <a
                                class="inline-flex items-center gap-2"
                                href="/page/{{ $page->slug }}"
                            >
                                {{ $page->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <hr class="border-white border-opacity-15">
            <div class="flex flex-wrap items-center justify-center gap-4 py-9 max-sm:text-center">
                <p
                    class="text-[14px] opacity-60 text-bold"
                    style="color: {{ $fSetting->footer_text_color }}; font-weight: bold !important;"
                >
                    {{ date('Y') . ' ' . $setting->site_name . '. ' . __($fSetting->footer_copyright) }}
                </p>
            </div>
        </div>
    </div>
</footer>
