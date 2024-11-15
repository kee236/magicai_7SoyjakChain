<div class="tab-pane fade" id="text" role="tabpanel" aria-labelledby="text-tab">
    <form class="row" id="add-form" action="{{ route('dashboard.admin.chatbot.text', $item) }}">
        @csrf
        <div class="col-md-12 mt-2">
            <div class="rounded-xl  p-2" style="background-color: rgba(157, 107, 221, 0.1);">
                <p class="fs-3 font-bold font-weight-medium mb-0 pb-0">
                    <span class="ps-2 pe-2 rounded me-2 text-white" style="background-color: rgba(157, 107, 221, 1)">1</span>@lang('Add Text')
                </p>
            </div>
        </div>

        <input type="hidden" name="text_id" id="text_id" value="">

        <div class="col-md-12 mt-3">
            <label class="font-bold mb-1" for="title">@lang('Title')</label>
            <input
                    id="title"
                    name="title"
                    class="form-control"
                    placeholder="@lang('Type your title here')"
            >
        </div>

        <div class="col-md-12 mt-3">
            <label class="font-bold mb-1" for="text">@lang('Text')</label>
             <textarea
                     id="content_text"
                     name="text"
                     class="form-control"
                     placeholder="@lang('Type your text here')"
                     rows="6"
             ></textarea>
        </div>

        <div>
            <button
                    data-submit="addtext"
                    data-form="#add-form"
                    data-list="#text-list"
                    type="button"
                    class="btn btn-primary mt-3 btn-block w-100"
            > @lang('Save') </button>
        </div>

    </form>

    <div class="d-flex flex-column justify-center align-center items-center crawler-spinner d-none mt-3 mb-3">
        <div class=" spinner-border" role="status" style="--tblr-spinner-width: 7.5rem; --tblr-spinner-height: 7.5rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <form  id="form-train-text" method="post" action="{{ route('dashboard.admin.chatbot.training', $item->id) }}">
        @php
            $texts = $data->where('type', 'text');
        @endphp

        <input type="hidden" name="type" value="text">
        <div class="col-md-12 mt-4 {{ $texts->count() ? '': 'd-none' }} text-list-alert" id="text-list-alert">
            <div class="rounded-xl  p-2" style="background-color: rgba(157, 107, 221, 0.1);">
                <p class="fs-3 font-bold font-weight-medium mb-0 pb-0">
                    <span class="ps-2 pe-2 rounded me-2 text-white" style="background-color: rgba(157, 107, 221, 1)">2</span>@lang('Text list')
                </p>
            </div>
        </div>

        <div class="text-list list-common" id="text-list">
            @include('panel.admin.chatbot.particles.text.list', ['items' => $texts])
        </div>
    </form>
</div>