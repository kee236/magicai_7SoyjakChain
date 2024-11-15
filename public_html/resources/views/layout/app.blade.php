<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}" class="max-sm:overflow-x-hidden">
<head>
	
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="{{getMetaDesc($setting)}}">
	@if(isset($setting->meta_keywords))
        <meta name="keywords" content="{{$setting->meta_keywords}}">
    @endif
    <link rel="icon" href="/{{$setting->favicon_path?? "assets/favicon.ico"}}">
	<title>
		{{getMetaTitle($setting)}}
	</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="/assets/css/frontend/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/frontend/flickity.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" />
    <link href="/assets/css/toastr.min.css" rel="stylesheet"/>

	@if(file_exists(base_path('resources/css/custom-frontend.scss')))
		@vite('resources/css/custom-frontend.scss')
	@else
		@vite('resources/css/frontend.scss')
	@endif

    @if($setting->frontend_custom_css != null)
        <link rel="stylesheet" href="{{$setting->frontend_custom_css}}" />
    @endif
  
	@if($setting->frontend_code_before_head != null)
        {!!$setting->frontend_code_before_head!!}
    @endif

	<script>
		window.liquid = {
			isLandingPage: true
		};
	</script>
	
	<style>
		.google-ads-728 {
			width: 100%;
			max-width: 728px;
			height: auto;
		}
	</style>
	
	<!--Google AdSense-->
	{!! adsense_header() !!}
	<!--Google AdSense End-->

    {{-- Rewordfull start --}}
    {{-- <script>(function(w,r){w._rwq=r;w[r]=w[r]||function(){(w[r].q=w[r].q||[]).push(arguments)}})(window,'rewardful');</script> --}}
    {{-- <script async src='https://r.wdfl.co/rw.js' data-rewardful='API_KEY'></script> --}}
    {{-- Rewordfull end --}}
	
</head>
<body class="font-golos bg-body-bg text-body group/body">
	<script src="/assets/js/tabler-theme.min.js"></script>
	<script src="/assets/js/navbar-shrink.js"></script>

	<div id="app-loading-indicator" class="fixed top-0 left-0 right-0 z-[99] opacity-0 transition-opacity">
		<div class="progress [--tblr-progress-height:3px]">
			<div class="progress-bar progress-bar-indeterminate bg-[--tblr-primary] before:[animation-timing-function:ease-in-out] dark:bg-white"></div>
		</div>
	</div>

	@include('layout.header')

	@yield('content')

	@include('layout.footer')

	@if($setting->frontend_custom_js != null)
		<script src="{{$setting->frontend_custom_js}}"></script>
	@endif

	@if($setting->frontend_code_before_body != null)
        {!!$setting->frontend_code_before_body!!}
    @endif


	@if(in_array($settings_two->chatbot_status, ['frontend', 'both']))
		<script src="/assets/openai/js/jquery.js"></script>
		<script src="/assets/js/panel/openai_chatbot.js"></script>
	@endif

	<script src="/assets/libs/vanillajs-scrollspy.min.js"></script>
	<script src="/assets/libs/flickity.pkgd.min.js"></script>
	@if(file_exists(base_path('public/assets/js/custom-frontend.js')))
		<script src="/assets/js/custom-frontend.js"></script>
	@else
		<script src="/assets/js/frontend.js"></script>
	@endif
	<script src="/assets/js/frontend/frontend-animations.js"></script>

	@if($setting->gdpr_status == 1)
	<script src="/assets/js/gdpr.js"></script>
	@endif

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
	<script src="/assets/openai/js/toastr.min.js"></script>

	<script>
		let mybutton = document.getElementById("myBtn");
		if (mybutton) {
			window.onscroll = function() {
				scrollFunction();
			};
		}
		
		function scrollFunction() {
		  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
			mybutton.style.display = "block";
		  } else {
			mybutton.style.display = "none";
		  }
		}
	</script>
	@if(\Session::has('message'))
		<script>
			toastr.{{\Session::get('type')}}('{{\Session::get('message')}}')
		</script>
	@endif
</body>
</html>
