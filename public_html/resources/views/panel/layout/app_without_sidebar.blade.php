<!doctype html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
@include('panel.layout.head')
<body class="group/body">
<script src="/assets/js/tabler-theme.min.js"></script>
<script src="/assets/js/navbar-shrink.js"></script>

<div id="app-loading-indicator" class="fixed top-0 left-0 right-0 z-[99] opacity-0 transition-opacity">
	<div class="progress [--tblr-progress-height:3px]">
		<div class="progress-bar progress-bar-indeterminate bg-[--tblr-primary] before:[animation-timing-function:ease-in-out] dark:bg-white"></div>
	</div>
</div>

@yield('content')

@include('panel.layout.scripts')

@yield('script')
@if(file_exists(base_path('public/assets/js/custom-frontend.js')))
	<script src="/assets/js/custom-frontend.js"></script>
@else
	<script src="/assets/js/frontend.js"></script>
@endif

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

</body>
</html>
