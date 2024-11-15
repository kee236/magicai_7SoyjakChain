<div class="tab-pane fade" id="pdf" role="tabpanel" aria-labelledby="pdf-tab">
    <div
            class="z-10 mt-auto flex items-center justify-center "
            id="mainupscale_src"
            ondrop="dropHandler(event, 'upscale_src');"
            ondragover="dragOverHandler(event);"
    >
        <label
                class="z-5 dark:hover:bg-bray-800 flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-black/5 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                for="upscale_src"
        >
            <div class="flex flex-col items-center justify-center pb-6 pt-4">
                <svg
                        class="mb-1 h-8 w-8 text-gray-500 dark:text-gray-400"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 16"
                >
                    <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1"
                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                    />
                </svg>
                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold">{{ __('UPLOAD PDF') }}
                </p>
                <p class="file-name text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Upload a PDF File (Max: 25Mb)') }}</p>
            </div>
            <input
                    data-action="{{ route('dashboard.admin.chatbot.upload-pdf', $item->id) }}"
                    class="hidden"
                    name="file"
                    id="upscale_src"
                    type="file"
                    accept=".pdf"
                    onchange="handleFileSelect('upscale_src')"
            />
        </label>
    </div>

    <div class="d-flex flex-column justify-center align-center items-center crawler-spinner d-none mt-3 mb-3">
        <div class=" spinner-border" role="status" style="--tblr-spinner-width: 7.5rem; --tblr-spinner-height: 7.5rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        {{--                <h1 id="">Training GPT...</h1>--}}
        {{--                <p>We’re training your custom GPT with the related <br> resources. This may take a few minutes.</p>--}}
    </div>

    <form  id="form-train-pdf" method="post" action="{{ route('dashboard.admin.chatbot.training', $item->id) }}">
        <input type="hidden" name="type" value="pdf">

        <div class="pdf-list list-common" id="pdf-list">
            @php
                $pdf = $data->where('type', 'pdf');
            @endphp

            @include('panel.admin.chatbot.particles.pdf.list', ['items' => $pdf])
        </div>


    </form>
</div>