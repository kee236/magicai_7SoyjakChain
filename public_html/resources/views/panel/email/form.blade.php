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
					</div>
                    <h2 class="page-title mb-2">
                        {{ __($title) }}
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
						action="{{ $action }}"
						enctype="multipart/form-data"
						method="post"
					>
						@csrf
						@method($method)
						@if($template->system)
							<input type="hidden" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ $template!=null ? $template->title : null }}">
						@else
							<div class="mb-[20px]">
								<label class="form-label">
									{{__('Title')}}
									<x-info-tooltip text="{{__('Template title.')}}" />
								</label>
								<input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $template!=null ? $template->title : null) }}">
								@error('title')
								<div class="invalid-feedback">
									{{ $message }}
								</div>
								@enderror
							</div>
						@endif
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('Subject')}}
								<x-info-tooltip text="{{__('Email Subject')}}" />
							</label>
							<input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('title', $template!=null ? $template->title : null) }}">
							@error('title')
							<div class="invalid-feedback">
								{{ $message }}
							</div>
							@enderror
						</div>
						<div class="mb-[20px]">
							<label class="form-label">
								{{__('Content')}}
								<x-info-tooltip text="{{__('You can use HTML. Not: All html elements not competible for mails.')}}" />
							</label>
							<div class="mb-3">
								{{__('You can use this tags')}} <code>{site_name}</code>, <code>{site_url}</code>, <code>{reset_url}</code>, <code>{affiliate_url}</code>, <code>{user_name}</code>, <code>{user_activation_url}</code>
							</div>
							<textarea class="form-control  @error('content') is-invalid @enderror" id="content" name="content">{{ old('content', $template!=null ? $template->content : null) }}</textarea>

							<textarea id="content_ace" name="content_ace" hidden=""></textarea>
							@error('content')
							<small class="text-danger">
								{{ $message }}
							</small>
							@enderror
						</div>
						<button type="submit" id="email_templates_button" class="btn btn-primary !py-3 w-100">
							{{__('Save')}}
						</button>
					</form>
				</div>
			</div>
        </div>
    </div>
@endsection
@section('script')
	<script src="/assets/libs/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>

	<style type="text/css" media="screen">
		.ace_editor{
			min-height: 400px;
		}
	</style>
	<script>
        var email_template_content = ace.edit("content");
        email_template_content.session.setMode("ace/mode/html");

		$('#form-submit').submit(function(event) {

			$('#content_ace').val(email_template_content.getValue());

			// event.preventDefault();
		});
	</script>
@endsection
