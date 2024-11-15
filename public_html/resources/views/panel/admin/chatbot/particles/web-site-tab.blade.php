<div class="tab-pane fade show active" id="web-site" role="tabpanel" aria-labelledby="web-site-tab">
    <div id="web-site-form" class="row">
        @csrf
        <div class="col-md-12 mt-2">
            <div class="mb-4 rounded-xl  p-2" style="background-color: rgba(157, 107, 221, 0.1);">
                <p class="fs-3 font-bold font-weight-medium mb-0 pb-0"><span class="ps-2 pe-2 rounded me-2 text-white"
                                                                             style="background-color: rgba(157, 107, 221, 1)">1</span>@lang('Add URL')
                </p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="type" value="web_site" id="web-site-radio"
                       autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="web-site-radio">@lang('Website')</label>
                <input type="radio" class="btn-check" name="type" value="single" id="single-url" autocomplete="off">
                <label class="btn btn-outline-primary" for="single-url">@lang('Single URL')</label>
            </div>
            <div class="input-group mt-2">
                <input
                        id="url"
                        name="url"
                        type="text"
                        class="form-control"
                        value=""
                        placeholder="https://example.com"
                >
            </div>
        </div>
        <div class="col-md-12">
            <button
                data-action="{{ route('dashboard.admin.chatbot.web-sites', $item) }}"
                class="btn btn-outline-secondary mt-2" type="button"
                id="web-site-form-submit"
            >@lang('Fetch')</button>
        </div>
    </div>
    <form id="form-train-web-site" action="{{ route('dashboard.admin.chatbot.training', $item->id) }}" class="row training-form">
        @csrf
        <input type="hidden" name="type" value="url" id="type">
        <div class="col-md-12 mt-4">
            @php
                $websites = $data->where('type', 'url');
            @endphp
            <div class="mb-3 rounded-xl p-2 d-flex justify-between" style="background-color: rgba(157, 107, 221, 0.1);">
                <p class="fs-3 font-bold font-weight-medium mb-0 pb-0"><span class="ps-2 pe-2 rounded me-2 text-white" id="selected-count" style="background-color: rgba(157, 107, 221, 1)">0</span>@lang('Select Pages')
                </p>
                <span class="font-bold" data-select="all" style="cursor: pointer">Select All</span>
            </div>
            <div class="d-flex flex-column justify-center align-center items-center crawler-spinner d-none">
                <div class=" spinner-border" role="status" style="--tblr-spinner-width: 7.5rem; --tblr-spinner-height: 7.5rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
{{--                <h1 id="">Training GPT...</h1>--}}
{{--                <p>We’re training your custom GPT with the related <br> resources. This may take a few minutes.</p>--}}
            </div>
            <input type="hidden" id="pages_total_count" value="{{ $websites?->count() }}">
            <div class="pages list-common" id="pages">
                @include('panel.admin.chatbot.particles.web-site.crawler', ['items' => $websites])
            </div>
        </div>
    </form>
</div>
