@extends('panel.layout.app')
@section('title', __('Payment'))

@section('additional_css')
<style>
	.checkmark {
		width: 100px;
		height: 100px;
		border-radius: 50%;
		display: block;
		stroke-width: 2;
		stroke: #228F75;
		stroke-miterlimit: 10;
		box-shadow: inset 0px 0px 0px #228F75;
		animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
		position:relative;
		top: 5px;
		right: 5px;
		margin: 0 auto;
	}
	.checkmark__circle {
		stroke-dasharray: 166;
		stroke-dashoffset: 166;
		stroke-width: 2;
		stroke-miterlimit: 10;
		stroke: #228F75;
		fill: transparent;
		animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
	
	}

	.checkmark__check {
		transform-origin: 50% 50%;
		stroke-dasharray: 48;
		stroke-dashoffset: 48;
		animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
	}

	@keyframes stroke {
		100% {
			stroke-dashoffset: 0;
		}
	}

	@keyframes scale {
		0%, 100% {
			transform: none;
		}

		50% {
			transform: scale3d(1.1, 1.1, 1);
		}
	}

	@keyframes fill {
		100% {
			box-shadow: inset 0px 0px 0px 4px #228F75;
		}
	}
</style>
@endsection

@section('content')
    <!-- Page header -->
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
					<a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}" class="page-pretitle flex items-center">
						<svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
						</svg>
						{{__('Back to dashboard')}}
					</a>
                    <h2 class="page-title mb-2">
                        {{__('Payment')}}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6 ">
        <div class="container-xl">
			<div class="success-animation mb-3">
				<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
					<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
					<path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
				</svg>
			</div>
			<div class="text-center">
				<h1 class="text-center text-heading">{{__('Payment Succesful')}}</h1>
				<p class="text-center text-heading my-3">{{__('Thanks for your purchase! Now, you can explore our AI tools and start generating content in seconds.')}}</p>
				<a class="btn btn-primary" href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}">
					<span>{{__('Generate New Content')}}</span>
					<svg xmlns="http://www.w3.org/2000/svg" class="ms-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
				</a>
			</div>
        </div>
    </div>

@endsection
