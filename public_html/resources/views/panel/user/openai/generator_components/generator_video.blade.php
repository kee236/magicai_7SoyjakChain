<!-- Start video generator -->
@if ($openai->type == 'video')
    <div class="row row-deck row-cards">
        <div class="col-12 flex-column">
            <div class="card bg-[#F3E2FD] hover:!shadow-sm dark:bg-[#14171C] dark:shadow-black">
                <div class="card-body md:p-10">
                    <div
                        class=""
                        pictoryai
                    >
                        <form
                            id="openai_generator_form"
                            onsubmit="return sendOpenaiGeneratorForm();"
                        >
                            <div class="mt-0">
                                <div class="tab-pane">
                                    <label class="h3">{{ __('Source Image') }}</label>
                                    <div
                                        class="flex w-full items-center justify-center"
                                        ondrop="dropHandler(event, 'img2img_src');"
                                        ondragover="dragOverHandler(event);"
                                    >
                                        <label
                                            class="dark:hover:bg-bray-800 min-h-48 flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                                            for="img2img_src"
                                        >
                                            <div class="flex flex-col items-center justify-center px-5 pb-6 pt-5 text-center">
                                                <svg
                                                    class="mb-4 h-8 w-8 text-gray-500 dark:text-gray-400"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 20 16"
                                                >
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                                    />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                        class="font-semibold">{{ __('Drop your image here or browse. 1024x576, 576x1024, 768x768 images are avaiable.') }}
                                                </p>
                                                <p class="file-name text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('(Only jpg, png will be accepted)') }}</p>
                                            </div>
                                            <input
                                                class="hidden"
                                                id="img2img_src"
                                                type="file"
                                                accept=".png, .jpg, .jpeg"
                                                onchange="handleFileSelect('img2img_src')"
                                            />
                                        </label>
                                    </div>
                                    {{-- <div class="flex flex-wrap justify-between mb-3 mt-2">
                                        <a href="#advanced-settings"
                                            class="flex items-center text-[11px] font-semibold text-heading hover:no-underline group collapsed"
                                            data-bs-toggle="collapse" data-bs-auto-close="false">
                                            {{ __('Advanced Settings') }}
                                            <span
                                                class="inline-flex items-center justify-center w-[36px] h-[36px] p-0 !ms-2 bg-white !shadow-sm rounded-full dark:!bg-[--tblr-bg-surface]">
                                                <svg class="hidden group-[&.collapsed]:block" width="12"
                                                    height="12" viewBox="0 0 12 12" fill="var(--lqd-heading-color)"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M6.76708 5.464H11.1451V7.026H6.76708V11.558H5.18308V7.026H0.805078V5.464H5.18308V0.909999H6.76708V5.464Z" />
                                                </svg>
                                                <svg class="block group-[&.collapsed]:hidden" width="6"
                                                    height="2" viewBox="0 0 6 2" fill="var(--lqd-heading-color)"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.335078 1.962V0.246H5.65908V1.962H0.335078Z" />
                                                </svg>
                                            </span>
                                        </a>
                                    </div> --}}
                                    <div id="advanced-settings">
                                        <div class="mt-8 flex flex-col flex-wrap justify-between gap-3 md:flex-row">
                                            <div class="grow">
                                                <div class="relative flex justify-start">
                                                    <label class="form-label text-heading">{{ __('Seed') }}</label><svg
                                                        class="absolute right-2"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="A specific value from 0 to 4294967294 that is used to guide the 'randomness' of the generation."
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="20"
                                                        height="20"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    >
                                                        <path
                                                            stroke="none"
                                                            d="M0 0h24v24H0z"
                                                            fill="none"
                                                        />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 9h.01" />
                                                        <path d="M11 12h1v4h1" />
                                                    </svg>
                                                </div>
                                                <input
                                                    class="form-control h-[53px] resize-none rounded-xl bg-[#fff] text-[#000] !shadow-sm placeholder:text-black placeholder:text-opacity-50 focus:border-white focus:bg-white dark:!border-none dark:!bg-[--lqd-header-search-bg] dark:placeholder:text-[#a5a9b1] dark:focus:!bg-[--lqd-header-search-bg]"
                                                    id="video_seed"
                                                    type="number"
                                                    name="video_seed"
                                                    min="0"
                                                    max="4294967294"
                                                    value="0"
                                                >
                                            </div>
                                            <div class="grow">
                                                <div class="relative flex justify-start">
                                                    <label class="form-label text-heading">{{ __('Fidelity') }}</label><svg
                                                        class="absolute right-2"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="A specific value from 0 to 10 to express how strongly the video sticks to the original image."
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="20"
                                                        height="20"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    >
                                                        <path
                                                            stroke="none"
                                                            d="M0 0h24v24H0z"
                                                            fill="none"
                                                        />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 9h.01" />
                                                        <path d="M11 12h1v4h1" />
                                                    </svg>
                                                </div>
                                                <input
                                                    class="form-control h-[53px] resize-none rounded-xl bg-[#fff] text-[#000] !shadow-sm placeholder:text-black placeholder:text-opacity-50 focus:border-white focus:bg-white dark:!border-none dark:!bg-[--lqd-header-search-bg] dark:placeholder:text-[#a5a9b1] dark:focus:!bg-[--lqd-header-search-bg]"
                                                    id="video_cfg_scale"
                                                    type="number"
                                                    name="video_cfg_scale"
                                                    min="0"
                                                    max="10"
                                                    value="0"
                                                >
                                            </div>
                                            <div class="grow">
                                                <div class="relative flex justify-start">
                                                    <label class="form-label text-heading">{{ __('Motion intensity') }}</label><svg
                                                        class="absolute right-2"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Lower values generally result in less motion in the output video, while higher values generally result in more motion. The range is 0 ~ 255"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="20"
                                                        height="20"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    >
                                                        <path
                                                            stroke="none"
                                                            d="M0 0h24v24H0z"
                                                            fill="none"
                                                        />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 9h.01" />
                                                        <path d="M11 12h1v4h1" />
                                                    </svg>
                                                </div>
                                                <input
                                                    class="form-control h-[53px] resize-none rounded-xl bg-[#fff] text-[#000] !shadow-sm placeholder:text-black placeholder:text-opacity-50 focus:border-white focus:bg-white dark:!border-none dark:!bg-[--lqd-header-search-bg] dark:placeholder:text-[#a5a9b1] dark:focus:!bg-[--lqd-header-search-bg]"
                                                    id="video_motion_bucket_id"
                                                    type="number"
                                                    name="video_motion_bucket_id"
                                                    min="1"
                                                    max="255"
                                                    value="127"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        class="btn btn-primary mt-4 h-[36px] w-full"
                                        id="openai_generator_button"
                                        type="submit"
                                    >
                                        {{ __('Generate') }}
                                        <svg
                                            class="!ms-2 translate-x-0 translate-y-0 rtl:-scale-x-100"
                                            width="14"
                                            height="13"
                                            viewBox="0 0 14 13"
                                            fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path d="M7.25 13L6.09219 11.8625L10.6422 7.3125H0.75V5.6875H10.6422L6.09219 1.1375L7.25 0L13.75 6.5L7.25 13Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <template id="prompt-template">
            <div class="each-prompt d-flex align-items-center mt-3">
                <input
                    class="input-required form-control rounded-pill border-primary multi_prompts_description border bg-[#fff] text-[#000] placeholder:text-black placeholder:text-opacity-50 focus:border-white focus:bg-white dark:!border-none dark:!bg-[--lqd-header-search-bg] dark:placeholder:text-[#a5a9b1] dark:focus:!bg-[--lqd-header-search-bg]"
                    type="text"
                    name="titles[]"
                    placeholder="Type another title or description"
                    required
                >
                <button
                    class="text-heading border-none bg-transparent"
                    data-toggle="remove-parent"
                    type="button"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 32 32"
                        width="24px"
                        height="24px"
                        fill="currentColor"
                    >
                        <path
                            d="M 15 4 C 14.476563 4 13.941406 4.183594 13.5625 4.5625 C 13.183594 4.941406 13 5.476563 13 6 L 13 7 L 7 7 L 7 9 L 8 9 L 8 25 C 8 26.644531 9.355469 28 11 28 L 23 28 C 24.644531 28 26 26.644531 26 25 L 26 9 L 27 9 L 27 7 L 21 7 L 21 6 C 21 5.476563 20.816406 4.941406 20.4375 4.5625 C 20.058594 4.183594 19.523438 4 19 4 Z M 15 6 L 19 6 L 19 7 L 15 7 Z M 10 9 L 24 9 L 24 25 C 24 25.554688 23.554688 26 23 26 L 11 26 C 10.445313 26 10 25.554688 10 25 Z M 12 12 L 12 23 L 14 23 L 14 12 Z M 16 12 L 16 23 L 18 23 L 18 12 Z M 20 12 L 20 23 L 22 23 L 22 12 Z"
                        />
                    </svg>
                </button>
            </div>
        </template>
        <div id="generator_sidebar_table">
            @include('panel.user.openai.generator_components.generator_sidebar_table')
        </div>
    </div>

    <script>
        var resizedImage;
        var imageWidth = -1;
        var imageHeight = -1;
        var postImageWidth = -1;
        var postImageHeight = -1;

        function handleAddPrompt() {
            const mulPromptsContainer = document.querySelector('.multi-prompts')
            const promptTemplate = document.querySelector('#prompt-template').content.cloneNode(true)
            const removeBtn = promptTemplate.querySelector('[data-toggle="remove-parent"]')
            removeBtn.addEventListener('click', (e) => {
                event.preventDefault();
                e.currentTarget.parentElement.remove();
            })
            mulPromptsContainer.append(promptTemplate)
        }

        function dropHandler(ev, id) {
            // Prevent default behavior (Prevent file from being opened)
            ev.preventDefault();
            $('#' + id)[0].files = ev.dataTransfer.files;
            resizeImage();
            $('#' + id).prev().find(".file-name").text(ev.dataTransfer.files[0].name);
        }

        function dragOverHandler(ev) {
            ev.preventDefault();
        }

        function handleFileSelect(id) {
            $('#' + id).prev().find(".file-name").text($('#' + id)[0].files[0].name);
        }

        function resizeImage(e) {

            var file;
            file = $("#img2img_src")[0].files[0];
            if (file == undefined) return;
            var reader = new FileReader();

            reader.onload = function(event) {
                var img = new Image();

                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext("2d");

                    imageWidth = this.width;
                    imageHeight = this.height;

                    canvas.width = this.width;
                    canvas.height = this.height;
                    var ctx = canvas.getContext("2d");
                    ctx.drawImage(img, 0, 0, this.width, this.height);

                    var dataurl = canvas.toDataURL("image/png");

                    var byteString = atob(dataurl.split(',')[1]);
                    var mimeString = dataurl.split(',')[0].split(':')[1].split(';')[0];
                    var ab = new ArrayBuffer(byteString.length);
                    var ia = new Uint8Array(ab);
                    for (var i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    var blob = new Blob([ab], {
                        type: mimeString
                    });

                    resizedImage = new File([blob], file.name);
                    console.log(resizedImage);
                }
                img.src = event.target.result;
            }

            reader.readAsDataURL(file);

        }
        document.getElementById("img2img_src").addEventListener('change', resizeImage);
    </script>
@endif
<!-- End video generator -->
