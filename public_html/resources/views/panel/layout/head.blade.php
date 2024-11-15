<head>
    @if(isset($setting->google_analytics_code))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{$setting->google_analytics_code}}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{$setting->google_analytics_code}}');
    </script>
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="stream-url" content="{{ $streamUrl??route('dashboard.user.openai.stream') }}">
    <link rel="icon" href="/{{$setting->favicon_path?? "assets/favicon.ico"}}">
    <title>{{$setting->site_name}} | @yield('title')</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/fonts.css" rel="stylesheet">

    <!-- CSS files -->
    <link href="/assets/css/tabler.min.css" rel="stylesheet"/>
    <link href="/assets/css/tabler-flags.min.css" rel="stylesheet"/>
    <link href="/assets/css/tabler-payments.min.css" rel="stylesheet"/>
    <link href="/assets/css/tabler-vendors.min.css" rel="stylesheet"/>
    <link href="/assets/css/demo.min.css" rel="stylesheet"/>
    <link href="/assets/css/toastr.min.css" rel="stylesheet"/>
    <link href="/assets/css/chatbot.css" rel="stylesheet"/>

    {{-- <link rel="stylesheet" href="https://flowbite.com/docs/flowbite.css?v=1.8.1a">
    <link rel="stylesheet" href="https://flowbite.com/docs/docs.css?v=1.8.1a"> --}}

    @yield('additional_css')

    @stack('css')

    @if(file_exists(base_path('public/assets/css/custom-magic-ai.css')))
        <link href="/assets/css/custom-magic-ai.css" rel="stylesheet"/>
    @else
		@vite('resources/scss/dashboard.scss')
    @endif

	@vite('resources/css/app.css')
    @if($setting->dashboard_code_before_head != null)
        {!!$setting->dashboard_code_before_head!!}
    @endif

    @stack('style')

	@vite('resources/js/app.js')
</head>
