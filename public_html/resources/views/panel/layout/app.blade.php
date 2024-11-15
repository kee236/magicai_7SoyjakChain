<!doctype html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
@include('panel.layout.head')

<body class="group/body @if (view()->exists('panel.admin.custom.layout.panel.header-top-bar')) overflow-hidden @endif">
    <script src="/assets/js/tabler-theme.min.js"></script>
    <script src="/assets/js/navbar-shrink.js"></script>

    <div class="fixed left-0 right-0 top-0 z-[99] opacity-0 transition-opacity" id="app-loading-indicator">
        <div class="progress [--tblr-progress-height:3px]">
            <div
                class="progress-bar progress-bar-indeterminate bg-[--tblr-primary] before:[animation-timing-function:ease-in-out] dark:bg-white">
            </div>
        </div>
    </div>

    <div class="page">
        <!-- Navbar -->
        @if (view()->exists('panel.admin.custom.layout.panel.header'))
            @include('panel.admin.custom.layout.panel.header')
        @elseif (!isset($disable_header))
            @include('panel.layout.header')
        @endif

        <div
            class="page-wrapper @if (view()->exists('panel.admin.custom.layout.panel.header-top-bar')) max-h-[calc(100vh-var(--lqd-body-pt)-(var(--lqd-body-pb)))] overflow-y-auto overflow-x-hidden bg-[--lqd-bg-alt] md:rounded-[40px] @else overflow-hidden @endif">
            @if (view()->exists('panel.admin.custom.layout.panel.header-top-bar'))
                @include('panel.admin.custom.layout.panel.header-top-bar')
            @endif
            <!-- Updater -->
            @if ($good_for_now)
                @yield('content')
            @elseif(!$good_for_now and Route::currentRouteName() != 'dashboard.admin.settings.general')
                @include('vendor.installer.magicai_c4st_Act')
            @else
                @yield('content')
            @endif
            @if (!isset($disable_footer))
                @include('panel.layout.footer')
            @endif
        </div>
    </div>

    @include('panel.layout.scripts')

    @if (\Session::has('message'))
        <script>
            toastr.{{ \Session::get('type') }}('{{ \Session::get('message') }}')
        </script>
    @endif

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
        </script>
    @endif

    @yield('script')

    @stack('script')

    @if (file_exists(base_path('public/assets/js/custom-frontend.js')))
        <script src="/assets/js/custom-frontend.js"></script>
    @else
        <script src="/assets/js/frontend.js"></script>
    @endif

    @if ($setting->dashboard_code_before_body != null)
        {!! $setting->dashboard_code_before_body !!}
    @endif

    @auth()
        @if (\Illuminate\Support\Facades\Auth::user()->type == 'admin')
            <script src="/assets/js/panel/update-check.js"></script>
        @endif
    @endauth

    <script src="/assets/js/chatbot.js"></script>
    <template id="typing-template">
        <div
            class="lqd-typing !inline-flex !items-center !gap-3 !rounded-full !bg-[#efd8fc] !px-3 !py-2 !text-xs !font-medium !leading-none !text-[--lqd-heading-color]">
            {{ __('Typing') }}
            <div class="lqd-typing-dots !flex !items-center !gap-1">
                <span class="lqd-typing-dot !h-1 !w-1 !rounded-full"></span>
                <span class="lqd-typing-dot !h-1 !w-1 !rounded-full"></span>
                <span class="lqd-typing-dot !h-1 !w-1 !rounded-full"></span>
            </div>
        </div>
    </template>

</body>

</html>
