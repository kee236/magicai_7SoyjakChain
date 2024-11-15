@extends('panel.layout.app')
@section('title', __('Add or Edit Email Templates'))
@section('additional_css')
	@error('content')
		<style>
			.ace_editor{
				border: 1px solid #dc3545 !important;
			}
		</style>
	@enderror
@endsection
@section('content')
    <div class="page-header" xmlns="http://www.w3.org/1999/html">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
					<div class="hstack gap-1">
						<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.index') ) }}" class="page-pretitle flex items-center">
							<svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
								<path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
							</svg>
							{{__('Back to dashboard')}}
						</a>
						<a href="{{route('dashboard.email-templates.index')}}" class="page-pretitle flex items-center">
							/ {{__('Email Templates')}}
						</a>
						<a href="{{route('dashboard.email-templates.index')}}" class="page-pretitle flex items-center">
							/ {{ $template->title }}
						</a>
					</div>
                    <h2 class="page-title mb-2">
                        {{ __('Send email') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
			<div class="row">
				<div class="col-md-8 mx-auto">
					<form
						id="form-submit"
						action="{{ route('dashboard.email-templates.send', $template->id) }}"
						enctype="multipart/form-data"
						method="post"
					>
						@csrf
						<div class="alert alert-warning">
							<p>If you want your email sending to be efficient, you should use Redis for the queue. If your queue is 'sync,' you won't benefit from this process.</p>

							<span>"You can separate email addresses with the following symbols in English: ',', '\n' (newline), '\r' (carriage return), ';', ' ', '|'</span>
						</div>
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('Receiver')}}
								<x-info-tooltip text="{{__('Please only include users available in the system, and if you have used {user_name} in the template, you should be mindful of this.')}}" />
							</label>

							<textarea class="form-control  @error('receivers') is-invalid @enderror" id="receivers" rows="10" name="receivers">{{ old('receivers') }}</textarea>
							@error('receiver')
							<small class="text-danger">
								{{ $message }}
							</small>
							@enderror
						</div>
						<div class="mb-[20px]">
							<div class="form-check">
								<input class="form-check-input" name="all_customers" {{ old('all_customers') ? 'checked' : '' }} type="checkbox" id="flexCheckDefault">
								<label class="form-check-label" for="flexCheckDefault">
									@lang('All customers')
								</label>
							</div>
						</div>
						<button type="submit" id="email_templates_button" class="btn btn-primary !py-3 w-100">
							{{__('Send')}}
						</button>
					</form>
				</div>
			</div>
        </div>
    </div>
@endsection
@section('script')

@endsection
