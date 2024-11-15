@if ($openai->type == 'image')
    <div class="col-12" x-data="{
        modalShow: false,
        activeItem: null,
        activeItemId: null,
        setActiveItem(data, id) {
            this.activeItem = data;
            this.activeItemId = id;
        },
        prevItem() {
            const currentEl = document.querySelector(`.image-result[data-id='${this.activeItemId}']`);
            const prevEl = currentEl.parentElement?.previousElementSibling?.querySelector('.image-result');
            if (!prevEl) return;
            const data = JSON.parse(prevEl.querySelector('a.gallery').getAttribute('data-payload') || {});
            const id = data.id;
            this.setActiveItem(data, id);
        },
        nextItem() {
            const currentEl = document.querySelector(`.image-result[data-id='${this.activeItemId}']`);
            const nextEl = currentEl.parentElement?.nextElementSibling?.querySelector('.image-result');
            if (!nextEl) return;
            const data = JSON.parse(nextEl.querySelector('a.gallery').getAttribute('data-payload') || {});
            const id = data.id;
            this.setActiveItem(data, id);
        },
    }">
        <h2 class="mb-3">{{ __('Result') }}</h2>
        <div class="image-results row"></div>

        <div class="lqd-modal-img fixed start-0 top-0 z-[999] hidden h-screen w-screen flex-col items-center border p-3 [&.is-active]:flex"
            id="modal_image" x-data :class="{ 'is-active': modalShow }">
            <div class="lqd-modal-img-backdrop absolute start-0 top-0 z-0 h-screen w-screen bg-black/10 backdrop-blur-sm"
                @click="modalShow = false"></div>

            <div class="lqd-modal-img-content-wrap relative z-10 my-auto max-h-[90vh] w-full overflow-y-auto">
                <div class="container max-w-6xl">
                    <div
                        class="lqd-modal-img-content relative flex flex-wrap justify-between rounded-xl bg-[--tblr-body-bg] !p-5 xl:min-h-[570px]">
                        <a class="absolute end-2 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full border bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black"
                            @click.prevent="modalShow = false" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </a>

                        <div class="lqd-modal-img-img relative aspect-square min-h-[1px] w-full rounded-lg bg-cover bg-center max-md:min-h-[350px] md:!w-6/12"
                            :style="{ backgroundImage: `url(${activeItem?.output})` }">
                            <a class="absolute !bottom-7 !end-7 inline-flex h-9 w-9 items-center justify-center rounded-full bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:scale-105"
                                href="#" :href="activeItem?.output" download>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                    <path d="M7 11l5 5l5 -5" />
                                    <path d="M12 4l0 12" />
                                </svg>
                            </a>
                        </div>

                        <div class="relative flex w-full flex-col p-3 md:!w-5/12">
                            <div class="relative flex flex-col items-start !pb-6">
                                <h3 class="!mb-4">
                                    {{ __('Image Details') }}
                                </h3>

                                <span
                                    class="!mb-3 inline-flex cursor-copy items-center justify-center !gap-2 rounded-md bg-[--lqd-pink] !px-2 !py-1 text-center text-[11px] font-semibold text-black"
                                    @click="navigator.clipboard.writeText(activeItem?.input); toastr.success('{{ __('Copied prompt') }}');">
                                    <svg class="icon icon-tabler icon-tabler-copy" xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                        <path
                                            d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                                    </svg>
                                    {{ __('Prompt') }}
                                </span>

                                <span class="mt-2" x-text="activeItem?.input"></span>
                            </div>

                            <div class="mt-auto flex flex-wrap justify-between !gap-y-3 text-[13px] font-medium">
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Date')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.format_date ?? '{{ __('None') }}'"></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Resolution')</p>
                                        <p class="mb-0 opacity-60" x-text="activeItem?.size ?? '{{ __('None') }}'">
                                        </p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Credit')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.credits ?? '{{ __('None') }}'"></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('AI Model')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.response ?? '{{ __('None') }}'"></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Art Style')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.image_style ?? '{{ __('None') }}'"></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Mood')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.image_mood ?? '{{ __('None') }}'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prev/Next buttons -->
                        <a class="absolute -start-5 top-1/2 z-10 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:scale-110 hover:bg-[--tblr-primary] hover:text-white"
                            href="#" @click.prevent="prevItem()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                        </a>
                        <a class="absolute -end-5 top-1/2 z-10 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:scale-110 hover:bg-[--tblr-primary] hover:text-white"
                            href="#" @click.prevent="nextItem()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="image_result">
        <div class="col-6 col-md-4 col-xl-2 mb-8">
            <div class="image-result group"
                :class="{ 'info-showing': modalShow && activeItemId == $el.getAttribute('data-id') }">
                <div
                    class="relative mb-2 aspect-square overflow-hidden rounded-lg transition-all group-hover:shadow-lg">
                    <img class="aspect-square h-full w-full object-cover object-center" src=""
                        loading="lazy">
                    <span class="badge text-red bg-white"></span>
                    <div
                        class="absolute left-0 top-0 flex h-full w-full items-center justify-center gap-2 opacity-0 transition-opacity group-hover:!opacity-100">
                        <a class="btn download h-9 w-9 items-center justify-center p-0" href="" download>
                            <svg width="8" height="11" viewBox="0 0 8 11" fill="var(--lqd-heading-color)"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.57422 0.5V8.75781L6.67969 6.67969L7.5 7.5L4 11L0.5 7.5L1.32031 6.67969L3.42578 8.75781V0.5H4.57422Z" />
                            </svg>
                        </a>
                        <a class="btn lb gallery h-9 w-9 items-center justify-center p-0"
                            @click.prevent="setActiveItem( JSON.parse($el.getAttribute('data-payload') || {}), JSON.parse($el.getAttribute('data-payload'))?.id ); modalShow = true"
                            href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                <path
                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6">
                                </path>
                            </svg>
                        </a>
                        <a class="btn delete h-9 w-9 items-center justify-center p-0" href=""
                            onclick="return confirm('Are you sure?')">
                            <svg width="10" height="9" viewBox="0 0 10 9" fill="var(--lqd-heading-color)"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.08789 1.49609L5.80664 4.75L9.08789 8.00391L8.26758 8.82422L4.98633 5.57031L1.73242 8.82422L0.912109 8.00391L4.16602 4.75L0.912109 1.49609L1.73242 0.675781L4.98633 3.92969L8.26758 0.675781L9.08789 1.49609Z" />
                            </svg>
                        </a>

                    </div>
                </div>
                <p class="text-heading mb-1 w-full overflow-hidden overflow-ellipsis whitespace-nowrap"
                    title="">
                </p>
                <p class="text-muted mb-0"></p>
            </div>
        </div>
    </template>
@elseif ($openai->type == 'video')
    <div class="col-12" x-data="{
        modalShow: false,
        activeItem: null,
        activeItemId: null,
        setActiveItem(data, id) {
            this.activeItem = data;
            this.activeItemId = id;
            videoItem = document.querySelector(`.lqd-modal-img-content > video`);
            currentVideoItem = document.querySelector(`.lqd-modal-img-content > video > source`);
    
            const currenturl = window.location.href;
            const server = currenturl.split('/')[0];
            const delete_url =
                `${server}/dashboard/user/openai/documents/delete/image/${data.slug}`;
            deleteVideoBtn = document.querySelector(`.lqd-modal-img-content .delete`);
            deleteVideoBtn.href = delete_url;
    
            currentVideoItem.src = data.output;
            videoItem.load();
    
        },
        prevItem() {
            const currentEl = document.querySelector(`.video-result[data-id='${this.activeItemId}']`);
            const prevEl = currentEl.parentElement?.previousElementSibling?.querySelector('.video-result');
            if (!prevEl) return;
            const data = JSON.parse(prevEl.querySelector('a.gallery').getAttribute('data-payload') || {});
            const id = data.id;
            this.setActiveItem(data, id);
        },
        nextItem() {
            const currentEl = document.querySelector(`.video-result[data-id='${this.activeItemId}']`);
            const nextEl = currentEl.parentElement?.nextElementSibling?.querySelector('.video-result');
            if (!nextEl) return;
            const data = JSON.parse(nextEl.querySelector('a.gallery').getAttribute('data-payload') || {});
            const id = data.id;
            this.setActiveItem(data, id);
        },
    }">
        <h2 class="mb-3">{{ __('Result') }}</h2>
        <div class="video-results row"></div>

        <div class="lqd-modal-img fixed start-0 top-0 z-[999] hidden h-screen w-screen flex-col items-center border p-3 [&.is-active]:flex"
            id="modal_image" x-data :class="{ 'is-active': modalShow }">
            <div class="lqd-modal-img-backdrop absolute start-0 top-0 z-0 h-screen w-screen bg-black/10 backdrop-blur-sm"
                @click="modalShow = false"></div>

            <div class="lqd-modal-img-content-wrap relative z-10 my-auto max-h-[90vh] w-full overflow-y-auto">
                <div class="container max-w-6xl">
                    <div
                        class="lqd-modal-img-content relative flex flex-wrap justify-between rounded-xl bg-[--tblr-body-bg] !p-5 xl:min-h-[570px]">
                        <a class="absolute end-2 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full border bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black"
                            @click.prevent="modalShow = false" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </a>

                        <video
                            class="videotag lqd-modal-img-img relative aspect-square min-h-[1px] w-full rounded-lg bg-cover bg-center max-md:min-h-[350px] md:!w-6/12"
                            loading="lazy" controls>
                            <source src="" type="video/mp4">
                            <a class="absolute !bottom-7 !end-7 inline-flex h-9 w-9 items-center justify-center rounded-full bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:scale-105"
                                href="#" :href="activeItem?.output" download target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                    <path d="M7 11l5 5l5 -5" />
                                    <path d="M12 4l0 12" />
                                </svg>
                            </a>
                        </video>

                        <div class="videoinfo relative flex w-full flex-col p-3 md:!w-5/12">
                            <div class="relative flex flex-col items-start !pb-6">
                                <h3 class="!mb-4">
                                    {{ __('Video Details') }}
                                </h3>
                            </div>

                            <div class="mt-auto flex flex-wrap justify-between !gap-y-3 text-[13px] font-medium">
                                <a class="btn !bottom-0 delete w-full" href=""
                                onclick="return confirm('Are you sure?')">{{ __('Delete Video') }}</a>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Date')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.format_date ?? '{{ __('None') }}'"></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Resolution')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.payload.size ?? '{{ __('None') }}'"></p>
                                    </div>
                                </div>
                                <div class="w-full md:!w-[30%]">
                                    <div class="rounded-lg bg-black/[3%] p-2.5 dark:bg-white/[3%]">
                                        <p class="!mb-1">@lang('Credit')</p>
                                        <p class="mb-0 opacity-60"
                                            x-text="activeItem?.credits ?? '{{ __('None') }}'">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prev/Next buttons -->
                        <a class="absolute -start-5 top-1/2 z-10 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:scale-110 hover:bg-[--tblr-primary] hover:text-white"
                            href="#" @click.prevent="prevItem()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                        </a>
                        <a class="absolute -end-5 top-1/2 z-10 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-[--tblr-body-bg] text-inherit shadow-sm transition-all hover:scale-110 hover:bg-[--tblr-primary] hover:text-white"
                            href="#" @click.prevent="nextItem()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="video_result">
        <div class="col-6 col-md-4 col-xl-2 mb-8">
            <div class="video-result group rounded-lg shadow transition-all hover:-translate-y-1"
                :class="{ 'info-showing': modalShow && activeItemId == $el.getAttribute('data-id') }">
                <div
                    class="relative mb-2 flex aspect-square justify-center overflow-hidden rounded-lg transition-all group-hover:shadow-lg">
                    <video class="h-full w-full object-cover object-center" loading="lazy">
                        <source src="" type="video/mp4">
                    </video>
                    <div
                        class="absolute left-0 top-0 flex h-full w-full items-center justify-center gap-2 opacity-0 transition-opacity group-hover:!opacity-100">
                        <a class="btn playvideo h-9 w-9 items-center justify-center p-0"
                            data-fslightbox="video-gallery" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" stroke="currentColor" fill="var(--lqd-heading-color)">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 4v16l13 -8z" />
                            </svg>
                        </a>
                        <a class="btn lb gallery h-9 w-9 items-center justify-center p-0"
                            @click.prevent="setActiveItem( JSON.parse($el.getAttribute('data-payload') || {}), JSON.parse($el.getAttribute('data-payload'))?.id ); modalShow = true"
                            href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M12 9h.01" />
                                <path d="M11 12h1v4h1" />
                            </svg>
                        </a>
                        <a class="btn download h-9 w-9 items-center justify-center p-0" href="" download
                            target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                <path d="M7 11l5 5l5 -5" />
                                <path d="M12 4l0 12" />
                            </svg>
                        </a>

                    </div>
                </div>
                <p class="text-heading mb-1 hidden w-full overflow-hidden overflow-ellipsis whitespace-nowrap"
                    title="">
                </p>
                <p class="text-muted mb-0"></p>
            </div>
        </div>
    </template>
@elseif($openai->type == 'voiceover')
    <div class="table-responsive">
        <table class="card-table table">
            <thead>
                <tr>
                    <th>{{ __('File') }}</th>
                    <th>{{ __('Language') }}</th>
                    <th>{{ __('Voice') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Play') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($userOpenai as $entry)
                    @if (empty(json_decode($entry->response)))
                        @continue
                    @endif
                    <tr class="text-[13px]">
                        <td>{{ $entry->title }}</td>
                        <td class="text-[11px]">
                            <span
                                class="inline-block rounded-sm bg-black/[0.06] px-[6px] py-[3px] dark:bg-white/[0.06]">
                                @foreach (array_unique(json_decode($entry->response)->language) as $lang)
                                    {{ country2flag(explode('-', $lang)[1]) }}
                                @endforeach
                                {{ $lang }}
                            </span>
                        </td>
                        <td>
                            @foreach (array_unique(json_decode($entry->response)->voices) as $voice)
                                {{ getVoiceNames($voice) }}
                            @endforeach
                        </td>
                        <td>
                            <span>{{ $entry->created_at->format('M d, Y') }}, <span
                                    class="opacity-60">{{ $entry->created_at->format('H:m') }}</span></span>
                        </td>
                        <td class="data-audio" data-audio="/uploads/{{ $entry->output }}">
                            <div class="audio-preview"></div>
                        </td>
                        <td>
                            <a class="btn relative z-10 h-[36px] w-[36px] border p-0 hover:bg-[var(--tblr-primary)] hover:text-white"
                                href="/uploads/{{ $entry->output }}" target="_blank"
                                title="{{ __('View and edit') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="12"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                    <path d="M7 11l5 5l5 -5"></path>
                                    <path d="M12 4l0 12"></path>
                                </svg>
                            </a>
                            <a class="btn relative z-10 h-[36px] w-[36px] border p-0 hover:bg-red-600 hover:text-white"
                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.image.delete', $entry->slug)) }}"
                                onclick="return confirm('Are you sure?')" title="{{ __('Delete') }}">
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z" />
                                </svg>
                            </a>
                        </td>

                    </tr>
                @endforeach
                @if (count($userOpenai) == 0)
                    <tr>
                        <td colspan="6">{{ __('No entries created yet.') }}</td>
                    </tr>
                @endif

            </tbody>

        </table>
    </div>

    <div class="float-right m-4">
        {{ $userOpenai->links() }}
    </div>
@else
    <div class="table-responsive">
        <table class="table-vcenter card-table table">
            <thead>
                <tr>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Result') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($userOpenai as $entry)
                    <tr>
                        <td class="text-muted">
                            <span class="avatar h-[43px] w-[43px] [&_svg]:h-[20px] [&_svg]:w-[20px]"
                                style="background: {{ $entry->generator->color }}">
                                @if ($entry->generator->image !== 'none')
                                    {!! html_entity_decode($entry->generator->image) !!}
                                @endif
                            </span>
                        </td>
                        @if ($openai->type == 'text')
                            <td>
                                {!! $entry->output !!}
                            </td>
                        @elseif($openai->type == 'code')
                            <td>
                                <div
                                    class="mt-[15px] min-h-full border-b-0 border-l-0 border-r-0 border-t border-solid border-[var(--tblr-border-color)] pt-[30px]">
                                    <pre class="line-numbers min-h-full [direction:ltr]" id="code-pre"><code id="code-output">{{ $entry->output }}</code></pre>
                                </div>
                            </td>
                        @else
                            <td>
                                {{ $entry->output }}
                            </td>
                        @endif
                    </tr>
                @endforeach
                @if (count($userOpenai) == 0)
                    <tr>
                        <td colspan="2">{{ __('No entries created yet.') }}</td>
                    </tr>
                @endif
            </tbody>

        </table>
    </div>
@endif
@if ($openai->slug == 'ai_image_generator')
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            "use strict";
            // fsLightbox.props.disableLocalStorage = true;
            let offset = 0; // Declare offset globally
            const imageContainer = document.querySelector('.image-results');

            function lazyLoadImages() {

                fetch(
                        `{{ route('dashboard.user.openai.lazyloadimage') }}?offset=${offset}&post_type={{ $openai->slug }}`
                    )
                    .then(response => response.json())
                    .then(data => {
                        const images = data.images;
                        const hasMore = data.hasMore;
                        images.forEach(image => {
                            const imageResultTemplate = document.querySelector('#image_result').content
                                .cloneNode(true);
                            imageResultTemplate.querySelector('.image-result').setAttribute('data-id',
                                image.id);
                            imageResultTemplate.querySelector('.image-result img').setAttribute('src',
                                image.output);
                            imageResultTemplate.querySelector('.image-result img').setAttribute(
                                'loading', 'lazy');
                            imageResultTemplate.querySelector('.image-result span').innerHTML = image
                                .response == "SD" ? "SD" : "DE";
                            imageResultTemplate.querySelector('.image-result span').setAttribute(
                                'class', image.response == "SD" ? "badge bg-blue text-white" :
                                "badge bg-white text-red")
                            imageResultTemplate.querySelector('.image-result a.download')
                                .setAttribute('href', image.output);

                            let galleryDiv = imageResultTemplate.querySelector(
                                '.image-result a.gallery');

                            galleryDiv.setAttribute('href', '#');
                            galleryDiv.setAttribute('data-payload', JSON.stringify(image))

                            const currenturl = window.location.href;
                            const server = currenturl.split('/')[0];
                            const delete_url =
                                `${server}/dashboard/user/openai/documents/delete/image/${image.slug}`;
                            imageResultTemplate.querySelector('.image-result a.delete').setAttribute(
                                'href', delete_url);
                            imageResultTemplate.querySelector('.image-result p.text-heading')
                                .setAttribute('title', image.input);
                            imageResultTemplate.querySelector('.image-result p.text-heading')
                                .innerHTML = image.input;
                            imageResultTemplate.querySelector('.image-result p.text-muted').innerHTML =
                                '';
                            imageContainer.append(imageResultTemplate);
                        });

                        // Update the offset for the next lazy loading request
                        offset += images.length;

                        // Refresh lightbox, check if there are more images
                        // refreshFsLightbox();

                        if (hasMore) {
                            // Attach a scroll event listener to the window
                            window.addEventListener('scroll', handleScroll);
                        }
                    });
            }

            function handleScroll() {
                const scrollY = window.scrollY;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;

                if (scrollY + windowHeight >= documentHeight) {
                    // Remove the scroll event listener to avoid multiple triggers
                    window.removeEventListener('scroll', handleScroll);
                    lazyLoadImages();
                }
            }

            // Initial loading of images
            lazyLoadImages();

        });
    </script>
@elseif ($openai->slug == 'ai_video')
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            "use strict";
            // fsLightbox.props.disableLocalStorage = true;
            let offset = 0; // Declare offset globally
            const videoContainer = document.querySelector('.video-results');

            function lazyLoadImages() {

                fetch(
                        `{{ route('dashboard.user.openai.lazyloadimage') }}?offset=${offset}&post_type=ai_video`
                    )
                    .then(response => response.json())
                    .then(data => {
                        const videos = data.images;
                        const hasMore = data.hasMore;
                        videos.forEach(video => {
                            const videoResultTemplate = document.querySelector('#video_result').content
                                .cloneNode(true);
                            videoResultTemplate.querySelector('.video-result').setAttribute('data-id',
                                video.id);
                            videoResultTemplate.querySelector('.video-result video source')
                                .setAttribute('src', video.output);
                            videoResultTemplate.querySelector('.video-result video').setAttribute(
                                'loading', 'lazy');
                            let playVideoDiv = videoResultTemplate.querySelector(
                                '.video-result a.playvideo');
                            let galleryDiv = videoResultTemplate.querySelector(
                                '.video-result a.gallery');
                            playVideoDiv.setAttribute('href', video.output);
                            playVideoDiv.setAttribute('data-payload', JSON.stringify(video));
                            playVideoDiv.setAttribute('data-fslightbox', 'video-gallery');

                            galleryDiv.setAttribute('href', '#');
                            galleryDiv.setAttribute('data-payload', JSON.stringify(video))

                            const currenturl = window.location.href;
                            const server = currenturl.split('/')[0];
                            const delete_url =
                                `${server}/dashboard/user/openai/documents/delete/image/${video.slug}`;
                            videoResultTemplate.querySelector('.video-result a.download')
                                .setAttribute('href', video.output);
                            videoResultTemplate.querySelector('.video-result p.text-heading')
                                .setAttribute('title', video.input);
                            videoResultTemplate.querySelector('.video-result p.text-heading')
                                .innerHTML = video.input;
                            videoResultTemplate.querySelector('.video-result p.text-muted').innerHTML =
                                '';
                            videoContainer.append(videoResultTemplate);
                        });

                        // Update the offset for the next lazy loading request
                        offset += videos.length;

                        // Refresh lightbox, check if there are more images
                        refreshFsLightbox();

                        if (hasMore) {
                            // Attach a scroll event listener to the window
                            window.addEventListener('scroll', handleScroll);
                        }
                    });
            }

            function handleScroll() {
                const scrollY = window.scrollY;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;

                if (scrollY + windowHeight >= documentHeight) {
                    // Remove the scroll event listener to avoid multiple triggers
                    window.removeEventListener('scroll', handleScroll);
                    lazyLoadImages();
                }
            }

            // Initial loading of images
            lazyLoadImages();

        });
    </script>
@endif
