@extends('panel.layout.app')
@section('title', __('Add or Edit Page'))

@section('content')
    <div class="page-header" xmlns="http://www.w3.org/1999/html">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <div class="hstack gap-1">
                        <a class="page-pretitle flex items-center"
                            href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}">
                            <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10"
                                fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                            </svg>
                            {{ __('Back to dashboard') }}
                        </a>
                        <a class="page-pretitle flex items-center" href="{{ route('dashboard.page.list') }}">
                            / {{ __('Pages') }}
                        </a>
                    </div>
                    <h2 class="page-title mb-2">
                        {{ __('Add or Edit Page') }}
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
                        class="@if (view()->exists('panel.admin.custom.layout.panel.header-top-bar')) bg-[--tblr-bg-surface] px-8 py-10 rounded-[--tblr-border-radius] @endif"
                        id="page_form" onsubmit="return pageSave({{ $page != null ? $page->id : null }});" action=""
                        enctype="multipart/form-data">
                        <div class="mb-[20px]">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="titlebar_status"
                                    {{ $page != null && $page->titlebar_status ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('Titlebar Status') }}</span>
                            </label>
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Page Title') }}
                                <x-info-tooltip text="{{ __('Add a page title. Example: Privacy Policy.') }}" />
                            </label>
                            <input class="form-control" id="title" type="text" name="title"
                                value="{{ $page != null ? $page->title : null }}">
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Slug') }}
                                <x-info-tooltip text="{{ __('Add Slug for SEO. Example: privaciy-policy') }}" />
                            </label>
                            <input class="form-control" id="slug" type="text" name="slug"
                                value="{{ $page != null ? $page->slug : null }}">
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-label">
                                {{ __('Content') }}
                                <x-info-tooltip
                                    text="{{ __('A short description of what this chat template can help with.') }}" />
                            </label>
                            <textarea class="form-control" id="content" name="content">{{ $page != null ? $page->content : null }}</textarea>
                        </div>
                        <div class="mb-[20px]">
                            <label class="form-check form-switch">
                                <input class="form-check-input" id="status" type="checkbox"
                                    {{ $page != null && $page->status ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('Page Status') }}</span>
                                <x-info-tooltip
                                    text="{{ __('You can disable or enable this page. When this option is disabled, the page cannot be accessible to users.') }}" />
                            </label>
                        </div>
                        <button class="btn btn-primary w-100 !py-3" id="page_button" form="page_form">
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/assets/js/panel/page.js"></script>
    <script src="/assets/libs/tinymce/tinymce.min.js"></script>
    <script>
        (() => {
            const options = {
                selector: '#content',
                plugins: ['quickbars', 'link', 'image', 'lists', 'code', 'table'],
                toolbar: 'undo redo | blocks | bold italic mark underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | lists | indent outdent | image | code',
                quickbars_insert_toolbar: false,
                block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Liquid Blocks=LiquidBlocks; Highlight=highlight; Number block=numBlock; Leading=leading; Info box=infoBox;',
                formats: {
                    highlight: {
                        title: 'Highlight',
                        inline: 'span',
                        classes: 'highlight',
                    },
                    leading: {
                        title: 'Leading',
                        block: 'p',
                        classes: 'leading',
                    },
                    numBlock: {
                        title: 'Number block',
                        inline: 'span',
                        classes: 'num-block',
                    },
                    infoBox: {
                        title: 'Info box',
                        inline: 'span',
                        classes: 'info-box',
                    },
                },
                style_formats: [{
                    title: 'Liquid Blocks'
                }, {
                    name: 'highlight',
                    format: 'highlight',
                }, {
                    name: 'numBlock',
                    format: 'numBlock'
                }, {
                    name: 'leading',
                    format: 'leading',
                }, {
                    name: 'infoBox',
                    format: 'infoBox',
                }],
                content_style: `
				*, *:before, *:after {
					box-sizing: border-box;
				}

				hr {
					border-color: rgb(0 0 0 / 10%);
					padding: 1em 0;
				}

				.highlight {
					font-size: 12px;
					line-height: 1.5em;
					font-weight: 500;
					display: inline-block;
					border-radius: 6px;
					padding: 2px 14px 2px 14px;
					background: linear-gradient(90deg, #ECEAF8 0%, #E9C4E6 35%, #D2CDE2 70%, #EAEDFB 100%);
				}

				.leading {
					font-size: 20px;
					font-weight: 600;
					line-height: 1.2em;
					margin-bottom: 2.5em;
				}

				.num-block {
					display: inline-flex;
					min-width: 1.9412em;
					min-height: 1.9412em;
					padding: 0.25em 0.5em;
					align-items: center;
					justify-content: center;
					font-size: 17px;
					font-weight: 700;
					border-radius: 10px;
					background-color: #F3E3F8;
				}

				.info-box {
					display: inline-block;
					font-size: 14px;
					font-weight: 500;
					background-color: rgb(0 0 0 / 10%);
					padding: 4px 17px 4px 17px;
					border-radius: 11px;
				}
				`,
                // The following option is used to append style formats rather than overwrite the default style formats.
                style_formats_merge: true
            };
            if (localStorage.getItem("tablerTheme") === 'dark') {
                options.skin = 'oxide-dark';
                options.content_css = 'dark';
            }
            tinymce.init(options);
        })();
    </script>

@endsection
