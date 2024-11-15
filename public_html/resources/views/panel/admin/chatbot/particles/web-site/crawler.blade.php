@foreach($items as $item)
    <div id="div-item-{{ $item->id }}" class="p-2 mt-2 item d-flex justify-between">
        <div class="form-check m-0">
            <input class="form-check-input" data-select="item" {{ $item->status == 'trained' ? 'checked': '' }} name="chatbot_data[]" type="checkbox" value="{{ $item->id }}" id="item_{{ $item->id }}">
            <label class="form-check-label" for="item_{{ $item->id }}">
                {{ $item->getAttribute('type_value') }}
            </label>
        </div>
        <div class="d-flex justify-between">
            <span class="mr-2 badge bg-{{ $item->status == 'waiting' ? '' : 'success' }}"> {{ $item->status }} </span>
            <a
                data-url="{{ route('dashboard.admin.chatbot.item.delete', [$item->chatbot_id, $item->id]) }}"
                data-item="delete"
                data-parent="#div-item-{{ $item->id }}"
                class="text-danger"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
            </a>
        </div>
    </div>
@endforeach

<button
        data-submit="train"
        data-form="#form-train-web-site"
        data-list="#pages"
        type="button"
        class="btn btn-primary mt-3 btn-block w-100"
> @lang('Train GPT') </button>
