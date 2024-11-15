<footer class="relative pt-40 text-white bg-black site-footer pb-11">
	<div class="absolute inset-0" style="background: radial-gradient(circle at 0% -20%, #a12a91, rgba(33, 13, 123, 0.83), transparent, transparent, transparent)">
	</div>
	<div class="absolute inset-x-0 -top-px">
		<svg class="w-full fill-body-bg" preserveAspectRatio="none" width="1440" height="86" viewBox="0 0 1440 86" xmlns="http://www.w3.org/2000/svg">
			<path d="M0 85.662C240 29.1253 480 0.857 720 0.857C960 0.857 1200 29.1253 1440 85.662V0H0V85.662Z"/>
		</svg>
	</div>
	<div class="relative">
		<div class="container mb-28">
			<div class="w-1/2 mx-auto text-center max-lg:w-10/12 max-sm:w-full">
				<p class="text-xs font-semibold tracking-widest uppercase mb-9"><span class="inline-block px-3 py-1 !me-2 rounded-xl bg-[#262626]">{{__($setting->site_name)}}</span> {{__($fSetting->footer_text_small)}}</p>
				<p class="text-[100px] font-bold font-oneset leading-none tracking-tight mb-8 text-transparent bg-clip-text bg-gradient-to-br from-transparent -from-[5%] to-white to-50% max-sm:text-[18vw]">{{__($fSetting->footer_header)}}</p>
				<p class="text-[20px] font-oneset leading-[1.25em] opacity-50 font-normal mb-9 px-10">{{__($fSetting->footer_text)}}</p>
				<x-button link="{{$fSetting->footer_button_url}}" label="{{__($fSetting->footer_button_text)}}" size="lg" iconPos="end" bg="bg-white bg-opacity-10 border-[2px] border-white border-opacity-0 hover:bg-white hover:bg-opacity-100" text="hover:!text-black">
					<x-slot name="icon">
						<svg class="ml-2" width="11" height="14" viewBox="0 0 47 62" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path d="M27.95 0L0 38.213H18.633V61.141L46.583 22.928H27.95V0Z"/>
						</svg>
					</x-slot>
				</x-button>
			</div>
		</div>
		<hr class="border-white border-opacity-15">
		<div class="container">
			<div class="flex flex-wrap items-center justify-between gap-8 pt-10 pb-7 max-sm:justify-center">
				<a href="{{route('index')}}">
					@if(isset($setting->logo_2x_path))
						<img src="/{{$setting->logo_path}}" srcset="/{{$setting->logo_2x_path}} 2x" alt="{{$setting->site_name}} logo">
					@else
						<img src="/{{$setting->logo_path}}" alt="{{$setting->site_name}} logo">
					@endif
				</a>
				<ul class="flex flex-wrap items-center gap-7 text-[14px] max-sm:justify-center">


                        <li>
							<a href="https://github.com/SoyjakChain" target="_blank" class="p-2 text-yellow-500  hover:text-gray-900 dark:hover:text-white dark:text-gray-400">
								<svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path  fill-rule="evenodd" clip-rule="evenodd" d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />

								</svg>

							</a>
                        </li>

                        <li>
							<a  href="https://t.me/SoyjakChat" target="_blank" class=" p-2 text-yellow-500  hover:text-gray-900 dark:hover:text-white dark:text-gray-400">
								<svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path
											id="telegram-1"
											d="M18.384,22.779c0.322,0.228 0.737,0.285 1.107,0.145c0.37,-0.141 0.642,-0.457 0.724,-0.84c0.869,-4.084 2.977,-14.421 3.768,-18.136c0.06,-0.28 -0.04,-0.571 -0.26,-0.758c-0.22,-0.187 -0.525,-0.241 -0.797,-0.14c-4.193,1.552 -17.106,6.397 -22.384,8.35c-0.335,0.124 -0.553,0.446 -0.542,0.799c0.012,0.354 0.25,0.661 0.593,0.764c2.367,0.708 5.474,1.693 5.474,1.693c0,0 1.452,4.385 2.209,6.615c0.095,0.28 0.314,0.5 0.603,0.576c0.288,0.075 0.596,-0.004 0.811,-0.207c1.216,-1.148 3.096,-2.923 3.096,-2.923c0,0 3.572,2.619 5.598,4.062Zm-11.01,-8.677l1.679,5.538l0.373,-3.507c0,0 6.487,-5.851 10.185,-9.186c0.108,-0.098 0.123,-0.262 0.033,-0.377c-0.089,-0.115 -0.253,-0.142 -0.376,-0.064c-4.286,2.737 -11.894,7.596 -11.894,7.596Z"
									/>

								</svg>
							</a>
                        </li>

                        <li>
							<a href="https://twitter.com/soyjak_coin" target="_blank" class=" p-2 text-yellow-500 hover:text-gray-900 dark:hover:text-white dark:text-gray-400">
								<svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />


								</svg>
							</a>

                        </li>


				</ul>
				<ul>
					<li>
						<a href="https://soyjakai.com/terms"  class=" p-2 text-yellow-500 hover:text-gray-900 dark:hover:text-white dark:text-gray-400">
							Terms and Conditions
						</a>
						<a href="https://soyjakai.com/privacy-policy" target="_blank" class=" p-2 text-yellow-500 hover:text-gray-900 dark:hover:text-white dark:text-gray-400">
							Privacy Policy
						</a>

					</li>
				</ul>
			</div>
			<hr class="border-white border-opacity-15">
			<div class="flex flex-wrap items-center justify-center gap-4 py-9 max-sm:text-center">
				<p class="text-[14px] opacity-60 !text-end text-bold">{{__($fSetting->footer_copyright)}}</p>
			</div>
		</div>
	</div>
</footer>
