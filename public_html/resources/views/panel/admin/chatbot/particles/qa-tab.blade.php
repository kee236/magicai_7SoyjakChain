<div class="tab-pane fade" id="qa" role="tabpanel" aria-labelledby="qa-tab">
    <form class="row" id="add-qa-form" action="{{ route('dashboard.admin.chatbot.qa', $item) }}">
        @csrf
        <div class="col-md-12 mt-2">
            <div class="rounded-xl  p-2" style="background-color: rgba(157, 107, 221, 0.1);">
                <p class="fs-3 font-bold font-weight-medium mb-0 pb-0">
                    <span class="ps-2 pe-2 rounded me-2 text-white" style="background-color: rgba(157, 107, 221, 1)">1</span>@lang('Add Qa')
                </p>
            </div>
        </div>

        <input type="hidden" name="qa_id" id="qa_id" value="">

        <div class="col-md-12 mt-3">
            <label class="font-bold mb-1" for="title">@lang('Question')</label>
            <input
                    id="question"
                    name="question"
                    class="form-control"
                    placeholder="@lang('Type your question here')"
            >
        </div>

        <div class="col-md-12 mt-3">
            <label class="font-bold mb-1" for="text">@lang('Answer')</label>
            <textarea
                    id="answer"
                    name="answer"
                    class="form-control"
                    placeholder="@lang('Type your answer here')"
                    rows="6"
            ></textarea>
        </div>

        <div>
            <button
                    data-submit="addqa"
                    data-form="#add-qa-form"
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

    <form  id="form-train-qa" method="post" action="{{ route('dashboard.admin.chatbot.training', $item->id) }}">
        @php
            $qa = $data->where('type', 'qa');
        @endphp

        <input type="hidden" name="type" value="qa">
        <div class="col-md-12 mt-4 {{ $qa->count() ? '': 'd-none' }} text-list-alert" id="text-list-alert">
            <div class="rounded-xl  p-2" style="background-color: rgba(157, 107, 221, 0.1);">
                <p class="fs-3 font-bold font-weight-medium mb-0 pb-0">
                    <span class="ps-2 pe-2 rounded me-2 text-white" style="background-color: rgba(157, 107, 221, 1)">2</span>@lang('Qa list')
                </p>
            </div>
        </div>

        <div class="qa-list list-common" id="qa-list">
            @include('panel.admin.chatbot.particles.qa.list', ['items' => $qa])
        </div>
    </form>
</div>