@if ($currfolder == null)
    <div class="grid grid-cols-3 max-md:grid-cols-1 !gap-5 !mb-6">
        @foreach (auth()->user()->folders ?? [] as $folder)
            <div
                class="flex items-center justify-between bg-[#f2f2f4] rounded-lg px-[1.375rem] py-[0.6rem] text-heading transition-all duration-300 hover:bg-black hover:bg-opacity-90 hover:text-white hover:shadow-md group-[.theme-dark]/body:bg-zinc-700 group-[.theme-dark]/body:hover:bg-zinc-600">
                <div class="flex items-center !gap-5 grow relative">
                    <svg class="fill-[#c1c1c3]" width="29" height="28" viewBox="0 0 29 28" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15.6547 6.62605L13.0714 1.45939C12.9641 1.24484 12.7992 1.06441 12.5951 0.93832C12.391 0.812229 12.1559 0.745456 11.916 0.745483H1.58268C1.24011 0.745483 0.91157 0.881569 0.669336 1.1238C0.427101 1.36604 0.291016 1.69458 0.291016 2.03715V7.20382C0.291016 7.54639 0.427101 7.87493 0.669336 8.11716C0.91157 8.3594 1.24011 8.49548 1.58268 8.49548H14.4993C14.7195 8.49551 14.9361 8.43924 15.1284 8.33203C15.3208 8.22481 15.4825 8.07021 15.5982 7.8829C15.714 7.69559 15.78 7.48179 15.7899 7.26182C15.7998 7.04184 15.7532 6.82299 15.6547 6.62605Z" />
                        <path
                            d="M27.416 6.75H1.29102C0.738731 6.75 0.291016 7.19772 0.291016 7.75V26.125C0.291016 26.4676 0.427101 26.7961 0.669336 27.0383C0.91157 27.2806 1.24011 27.4167 1.58268 27.4167H27.416C27.7586 27.4167 28.0871 27.2806 28.3294 27.0383C28.5716 26.7961 28.7077 26.4676 28.7077 26.125V8.04167C28.7077 7.6991 28.5716 7.37056 28.3294 7.12832C28.0871 6.88609 27.7586 6.75 27.416 6.75Z"
                            fill-opacity="0.45" />
                    </svg>
                    <div class="text-[13px]">
                        <p class="m-0 font-medium" id="folder{{ $folder->id }}">{{ $folder->name }}</p>
                        <small class="opacity-70">{{ $folder->updated_at->diffForHumans() }}</small>
                    </div>
                    <a href="{{ route('dashboard.user.openai.documents.all', $folder->id) }}"
                        class="absolute -inset-y-[0.6rem] -start-[1.375rem] !end-0"></a>
                </div>
                <div class="grow-0 shrink-0 relative">
                    <button
                        class="inline-flex items-center justify-center p-0 border-none bg-[transparent] w-9 h-9 rounded-full text-inherit transition-[background] hover:bg-white hover:text-black group-[.theme-dark]/body:hover:bg-zinc-800 group-[.theme-dark]/body:hover:text-white"
                        data-bs-toggle="dropdown">
                        <svg width="5" height="16" viewBox="0 0 5 16" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.50065 15.4066C1.97357 15.4066 1.52235 15.219 1.14701 14.8436C0.771658 14.4683 0.583984 14.0171 0.583984 13.49C0.583984 12.9629 0.771658 12.5117 1.14701 12.1363C1.52235 11.761 1.97357 11.5733 2.50065 11.5733C3.02773 11.5733 3.47895 11.761 3.8543 12.1363C4.22964 12.5117 4.41732 12.9629 4.41732 13.49C4.41732 14.0171 4.22964 14.4683 3.8543 14.8436C3.47895 15.219 3.02773 15.4066 2.50065 15.4066ZM2.50065 9.65664C1.97357 9.65664 1.52235 9.46896 1.14701 9.09362C0.771658 8.71827 0.583984 8.26705 0.583984 7.73997C0.583984 7.21289 0.771658 6.76167 1.14701 6.38632C1.52235 6.01098 1.97357 5.8233 2.50065 5.8233C3.02773 5.8233 3.47895 6.01098 3.8543 6.38632C4.22964 6.76167 4.41732 7.21289 4.41732 7.73997C4.41732 8.26705 4.22964 8.71827 3.8543 9.09362C3.47895 9.46896 3.02773 9.65664 2.50065 9.65664ZM2.50065 3.90664C1.97357 3.90664 1.52235 3.71896 1.14701 3.34362C0.771658 2.96827 0.583984 2.51705 0.583984 1.98997C0.583984 1.46289 0.771658 1.01167 1.14701 0.636324C1.52235 0.260977 1.97357 0.0733032 2.50065 0.0733032C3.02773 0.0733032 3.47895 0.260977 3.8543 0.636324C4.22964 1.01167 4.41732 1.46289 4.41732 1.98997C4.41732 2.51705 4.22964 2.96827 3.8543 3.34362C3.47895 3.71896 3.02773 3.90664 2.50065 3.90664Z" />
                        </svg>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end [&.show]:z-20">
                        <a href="button" data-bs-toggle="modal" data-bs-target="#renameModal"
                            data-folder-id="{{ $folder->id }}"
                            class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M8 20l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4h4z"></path>
                                <path d="M13.5 6.5l4 4"></path>
                                <path d="M16 18h4"></path>
                            </svg>
                            {{ __('Rename') }}
                        </a>
                        <a href="javascript:void(0)" onclick="removeFolder({{ $folder->id }})"
                            class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24"
                                stroke-width="2" stroke="var(--tblr-red)" fill="none" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                <path d="M9 12l6 0"></path>
                            </svg>
                            {{ __('Remove') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div
        class="flex items-center justify-between bg-[#f2f2f4] rounded-lg px-[1.375rem] py-[0.6rem] text-heading transition-all duration-300 hover:bg-black hover:bg-opacity-90 hover:text-white hover:shadow-md group-[.theme-dark]/body:bg-zinc-700 group-[.theme-dark]/body:hover:bg-zinc-600">
        <div class="flex items-center !gap-5 grow relative">
            <svg class="fill-[#c1c1c3]" width="29" height="28" viewBox="0 0 29 28" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M15.6547 6.62605L13.0714 1.45939C12.9641 1.24484 12.7992 1.06441 12.5951 0.93832C12.391 0.812229 12.1559 0.745456 11.916 0.745483H1.58268C1.24011 0.745483 0.91157 0.881569 0.669336 1.1238C0.427101 1.36604 0.291016 1.69458 0.291016 2.03715V7.20382C0.291016 7.54639 0.427101 7.87493 0.669336 8.11716C0.91157 8.3594 1.24011 8.49548 1.58268 8.49548H14.4993C14.7195 8.49551 14.9361 8.43924 15.1284 8.33203C15.3208 8.22481 15.4825 8.07021 15.5982 7.8829C15.714 7.69559 15.78 7.48179 15.7899 7.26182C15.7998 7.04184 15.7532 6.82299 15.6547 6.62605Z" />
                <path
                    d="M27.416 6.75H1.29102C0.738731 6.75 0.291016 7.19772 0.291016 7.75V26.125C0.291016 26.4676 0.427101 26.7961 0.669336 27.0383C0.91157 27.2806 1.24011 27.4167 1.58268 27.4167H27.416C27.7586 27.4167 28.0871 27.2806 28.3294 27.0383C28.5716 26.7961 28.7077 26.4676 28.7077 26.125V8.04167C28.7077 7.6991 28.5716 7.37056 28.3294 7.12832C28.0871 6.88609 27.7586 6.75 27.416 6.75Z"
                    fill-opacity="0.45" />
            </svg>
            <div class="text-[13px]">
                <p class="m-0 font-medium" id="folder{{ $currfolder->id }}">{{ $currfolder->name }}</p>
                <small class="opacity-70">{{ $currfolder->updated_at->diffForHumans() }}</small>
            </div>
            <a href="{{ route('dashboard.user.openai.documents.all', $currfolder->id) }}"
                class="absolute -inset-y-[0.6rem] -start-[1.375rem] !end-0"></a>
        </div>
        <div class="grow-0 shrink-0 relative">
            <button
                class="inline-flex items-center justify-center p-0 border-none bg-[transparent] w-9 h-9 rounded-full text-inherit transition-[background] hover:bg-white hover:text-black group-[.theme-dark]/body:hover:bg-zinc-800 group-[.theme-dark]/body:hover:text-white"
                data-bs-toggle="dropdown">
                <svg width="5" height="16" viewBox="0 0 5 16" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2.50065 15.4066C1.97357 15.4066 1.52235 15.219 1.14701 14.8436C0.771658 14.4683 0.583984 14.0171 0.583984 13.49C0.583984 12.9629 0.771658 12.5117 1.14701 12.1363C1.52235 11.761 1.97357 11.5733 2.50065 11.5733C3.02773 11.5733 3.47895 11.761 3.8543 12.1363C4.22964 12.5117 4.41732 12.9629 4.41732 13.49C4.41732 14.0171 4.22964 14.4683 3.8543 14.8436C3.47895 15.219 3.02773 15.4066 2.50065 15.4066ZM2.50065 9.65664C1.97357 9.65664 1.52235 9.46896 1.14701 9.09362C0.771658 8.71827 0.583984 8.26705 0.583984 7.73997C0.583984 7.21289 0.771658 6.76167 1.14701 6.38632C1.52235 6.01098 1.97357 5.8233 2.50065 5.8233C3.02773 5.8233 3.47895 6.01098 3.8543 6.38632C4.22964 6.76167 4.41732 7.21289 4.41732 7.73997C4.41732 8.26705 4.22964 8.71827 3.8543 9.09362C3.47895 9.46896 3.02773 9.65664 2.50065 9.65664ZM2.50065 3.90664C1.97357 3.90664 1.52235 3.71896 1.14701 3.34362C0.771658 2.96827 0.583984 2.51705 0.583984 1.98997C0.583984 1.46289 0.771658 1.01167 1.14701 0.636324C1.52235 0.260977 1.97357 0.0733032 2.50065 0.0733032C3.02773 0.0733032 3.47895 0.260977 3.8543 0.636324C4.22964 1.01167 4.41732 1.46289 4.41732 1.98997C4.41732 2.51705 4.22964 2.96827 3.8543 3.34362C3.47895 3.71896 3.02773 3.90664 2.50065 3.90664Z" />
                </svg>
            </button>
            <div class="dropdown-menu dropdown-menu-end [&.show]:z-20">
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#renameModal"
                    data-folder-id="{{ $currfolder->id }}"
                    class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M8 20l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4h4z"></path>
                        <path d="M13.5 6.5l4 4"></path>
                        <path d="M16 18h4"></path>
                    </svg>
                    {{ __('Rename') }}
                </a>
                <a href="javascript:void(0)" onclick="removeFolder({{ $currfolder->id }})"
                    class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24"
                        stroke-width="2" stroke="var(--tblr-red)" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                        <path d="M9 12l6 0"></path>
                    </svg>
                    {{ __('Remove') }}
                </a>
            </div>
        </div>
    </div>
@endif
<div class="dlist">
    <div class="card border-none">
        <div id="table-default" class="card-table table-responsive text-sm">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Cost') }}</th>
                        <th class="!text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-tbody align-middle text-heading">
                    @foreach ($items as $entry)
                        @if ($entry->generator != null && $entry->generator->type != 'image')
                            <tr
                                class="relative transition-colors hover:bg-black hover:bg-opacity-[0.03] group-[.theme-dark]/body:hover:bg-white group-[.theme-dark]/body:hover:bg-opacity-[0.03]">
                                <td class="sort-name text-capitalize" data-name="{{ trim($entry->generator->type) }}">
                                    <div class="flex items-center !gap-3 relative">
                                        <span class="avatar w-[36px] h-[36px] [&_svg]:w-[20px] [&_svg]:h-[20px] shrink-0"
                                            style="background: {{ $entry->generator->color }}">
                                            @if ($entry->generator->image !== 'none')
                                                {!! html_entity_decode($entry->generator->image) !!}
                                            @endif
                                        </span>
                                        @if ( in_array( $entry->generator->type, ['text', 'youtube', 'rss'] ) )
                                            {{ \Illuminate\Support\Str::limit(strip_tags($entry->title . " | " .$entry->output), 50) }}
                                        @elseif($entry->generator->type == 'audio')
                                            {!! \Illuminate\Support\Str::limit($entry->title . " | " .$entry->output, 50) !!}
                                        @elseif($entry->generator->type == 'code')
                                            {{ \Illuminate\Support\Str::limit(strip_tags($entry->title . " | " .$entry->output), 50) }}
                                        @else
                                            <a href="{{ $entry->output }}" target="_blank"><img
                                                    src="{{ $entry->output }}" class="img-fluid" alt=""></a>
                                        @endif
                                        <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
                                            class="absolute top-0 left-0 w-full h-full z-[2] hidden max-md:block"
                                            title="{{ __('View and edit') }}">
                                        </a>
                                    </div>
                                </td>
                                <td class="sort-file" data-file="{{ trim($entry->generator->title) }}">
                                    <span id="file{{ $entry->slug }}"
                                        class="inline-block !py-[0.15em] !px-1 rounded-md text-[12px] font-medium text-black relative"
                                        style="background: {{ $entry->generator->color }}">
                                        {{ __($entry->generator->title) }}
                                        <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
                                            class="absolute top-0 left-0 w-full h-full z-[2] hidden max-md:block"
                                            title="{{ __('View and edit') }}"></a>
                                    </span>
                                </td>
                                <td class="sort-date text-[13px]"
                                    data-date="{{ trim(strtotime($entry->created_at)) }}">
                                    <p class="m-0">{{ date('M j Y', strtotime($entry->created_at)) }}, <span
                                            class="opacity-50">{{ date('H:i', strtotime($entry->created_at)) }}</span>
                                    </p>
                                </td>
                                <td class="sort-cost text-[13px]" data-cost="{{ trim($entry->credits) }}">
                                    {{ $entry->credits }}</td>
                                <td class="whitespace-nowrap">
                                    <div class="flex items-center justify-end !gap-2">
                                        <a onclick="return favoriteTemplate({{ $entry->generator->id }});"
                                            id="favorite_area_{{ $entry->generator->id }}"
                                            class="btn relative z-10 w-[36px] shrink-0 h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white">
                                            @if (!isFavorited($entry->generator->id))
                                                <svg width="16" height="15" viewBox="0 0 16 15"
                                                    fill="none" stroke="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7.99989 11.8333L3.88522 13.9966L4.67122 9.41459L1.33789 6.16993L5.93789 5.50326L7.99522 1.33459L10.0526 5.50326L14.6526 6.16993L11.3192 9.41459L12.1052 13.9966L7.99989 11.8333Z"
                                                        stroke-width="1.25" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            @else
                                                <svg width="16" height="15" viewBox="0 0 16 15"
                                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7.99989 11.8333L3.88522 13.9966L4.67122 9.41459L1.33789 6.16993L5.93789 5.50326L7.99522 1.33459L10.0526 5.50326L14.6526 6.16993L11.3192 9.41459L12.1052 13.9966L7.99989 11.8333Z"
                                                        stroke-width="1.25" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            @endif
                                        </a>
                                        <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.delete', $entry->slug)) }}"
                                            onclick="return confirm('Are you sure?')"
                                            class="btn relative z-10 p-0 border w-[36px] shrink-0 h-[36px] hover:bg-red-600 hover:text-white"
                                            title="{{ __('Delete') }}">
                                            <svg width="10" height="10" viewBox="0 0 10 10"
                                                fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z" />
                                            </svg>
                                        </a>
                                        <div class="grow-0 shrink-0 relative">
                                            <button
                                                class="inline-flex items-center justify-center p-0 border-none bg-[transparent] w-9 h-9 rounded-full text-inherit transition-all relative z-10 hover:bg-white hover:text-black group-[.theme-dark]/body:hover:bg-zinc-800 group-[.theme-dark]/body:hover:text-white hover:border hover:border-solid border-[--tblr-border-color] hover:shadow-sm"
                                                data-bs-toggle="dropdown">
                                                <svg width="5" height="16" viewBox="0 0 5 16"
                                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                    class="opacity-50">
                                                    <path
                                                        d="M2.50065 15.4066C1.97357 15.4066 1.52235 15.219 1.14701 14.8436C0.771658 14.4683 0.583984 14.0171 0.583984 13.49C0.583984 12.9629 0.771658 12.5117 1.14701 12.1363C1.52235 11.761 1.97357 11.5733 2.50065 11.5733C3.02773 11.5733 3.47895 11.761 3.8543 12.1363C4.22964 12.5117 4.41732 12.9629 4.41732 13.49C4.41732 14.0171 4.22964 14.4683 3.8543 14.8436C3.47895 15.219 3.02773 15.4066 2.50065 15.4066ZM2.50065 9.65664C1.97357 9.65664 1.52235 9.46896 1.14701 9.09362C0.771658 8.71827 0.583984 8.26705 0.583984 7.73997C0.583984 7.21289 0.771658 6.76167 1.14701 6.38632C1.52235 6.01098 1.97357 5.8233 2.50065 5.8233C3.02773 5.8233 3.47895 6.01098 3.8543 6.38632C4.22964 6.76167 4.41732 7.21289 4.41732 7.73997C4.41732 8.26705 4.22964 8.71827 3.8543 9.09362C3.47895 9.46896 3.02773 9.65664 2.50065 9.65664ZM2.50065 3.90664C1.97357 3.90664 1.52235 3.71896 1.14701 3.34362C0.771658 2.96827 0.583984 2.51705 0.583984 1.98997C0.583984 1.46289 0.771658 1.01167 1.14701 0.636324C1.52235 0.260977 1.97357 0.0733032 2.50065 0.0733032C3.02773 0.0733032 3.47895 0.260977 3.8543 0.636324C4.22964 1.01167 4.41732 1.46289 4.41732 1.98997C4.41732 2.51705 4.22964 2.96827 3.8543 3.34362C3.47895 3.71896 3.02773 3.90664 2.50065 3.90664Z" />
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end z-20">
                                                {{-- <a data-bs-toggle="modal" data-file-slug="{{$entry->slug}}" data-bs-target="#renameFileModal" class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M8 20l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4h4z"></path> <path d="M13.5 6.5l4 4"></path> <path d="M16 18h4"></path> </svg>
                                                    {{__('Rename')}}
                                                </a> --}}
                                                <a data-bs-toggle="modal" data-file-slug="{{ $entry->slug }}"
                                                    data-bs-target="#moveFileModal"
                                                    class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19"
                                                        height="19" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="var(--tblr-red)" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M8 13v-8.5a1.5 1.5 0 0 1 3 0v7.5" />
                                                        <path d="M11 11.5v-2a1.5 1.5 0 0 1 3 0v2.5" />
                                                        <path d="M14 10.5a1.5 1.5 0 0 1 3 0v1.5" />
                                                        <path
                                                            d="M17 11.5a1.5 1.5 0 0 1 3 0v4.5a6 6 0 0 1 -6 6h-2h.208a6 6 0 0 1 -5.012 -2.7l-.196 -.3c-.312 -.479 -1.407 -2.388 -3.286 -5.728a1.5 1.5 0 0 1 .536 -2.022a1.867 1.867 0 0 1 2.28 .28l1.47 1.47" />
                                                        <path d="M2.541 5.594a13.487 13.487 0 0 1 2.46 -1.427" />
                                                        <path d="M14 3.458c1.32 .354 2.558 .902 3.685 1.612" />
                                                    </svg>
                                                    {{ __('Move') }}
                                                </a>
                                                {{-- <a href="#" class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" stroke-width="2" stroke="var(--tblr-red)" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path> <path d="M9 12l6 0"></path> </svg>
                                                    Remove
                                                </a> --}}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="w-full h-full absolute top-0 left-0 border-0 max-md:hidden">
                                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
                                        class="absolute top-0 left-0 w-full h-full z-[2]"
                                        title="{{ __('View and edit') }}"></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>
<div class="dgrid hidden">
    <div class="grid grid-cols-5 gap-10 max-lg:grid-cols-4 max-md:grid-cols-2 max-sm:grid-cols-1">
        @foreach ($items as $entry)
            @if ($entry->generator != null)
                <article
                    class="flex flex-col h-[185px] shadow-sm rounded-[10px] transition-all duration-300 group relative hover:shadow-xl">
                    <div
                        class="!pt-4 !px-4 grow shrink overflow-hidden relative after:absolute after:bottom-0 after:inset-x-0 after:h-12 after:bg-gradient-to-b after:from-transparent after:to-[--tblr-body-bg]">
                        <header class="mb-3">
                            <a onclick="return favoriteTemplate({{ $entry->generator->id }});"
                                id="favorite_area_{{ $entry->generator->id }}"
                                class="btn absolute top-2 end-3 z-10 w-[28px] shrink-0 h-[28px] p-0 opacity-100 transition-all hover:bg-[var(--tblr-primary)] hover:text-white">
                                @if (!isFavorited($entry->generator->id))
                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                        stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.99989 11.8333L3.88522 13.9966L4.67122 9.41459L1.33789 6.16993L5.93789 5.50326L7.99522 1.33459L10.0526 5.50326L14.6526 6.16993L11.3192 9.41459L12.1052 13.9966L7.99989 11.8333Z"
                                            stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @else
                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.99989 11.8333L3.88522 13.9966L4.67122 9.41459L1.33789 6.16993L5.93789 5.50326L7.99522 1.33459L10.0526 5.50326L14.6526 6.16993L11.3192 9.41459L12.1052 13.9966L7.99989 11.8333Z"
                                            stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @endif
                            </a>

                            <span data-file="{{ trim($entry->generator->title) }}"
                                class="sort-file inline-block !py-[0.15em] !px-1 rounded-md text-[11px] font-medium text-black"
                                style="background: {{ $entry->generator->color }}">
                                {{ $entry->generator->title }}
                            </span>
                        </header>
                        <div data-name="{{ trim($entry->generator->type) }}"
                            class="sort-name text-[13px] leading-[17px] text-heading w-4/5 shrink overflow-hidden">
                            @if ($entry->generator->type == 'text')
                                {{ \Illuminate\Support\Str::limit(strip_tags($entry->output), 100) }}
                            @elseif($entry->generator->type == 'audio')
                                {!! \Illuminate\Support\Str::limit($entry->output, 100) !!}
                            @elseif($entry->generator->type == 'code')
                                {{ \Illuminate\Support\Str::limit(strip_tags($entry->output), 100) }}
                            @else
                                <a href="{{ $entry->output }}" target="_blank">
                                    <img class="w-full h-[86px] rounded-lg shadow-md object-cover object-center"
                                        src="{{ $entry->output }}" alt="{{ $entry->generator->title }}">
                                </a>
                            @endif
                        </div>
                    </div>
                    <footer
                        class="flex items-center justify-between text-[13px] !ps-5 !pe-2 !py-2 border-t border-solid border-r-0 border-b-0 border-l-0 border-[--tblr-border-color] mt-auto">
                        <p class="m-0 text-heading sort-date" data-date="{{ trim(strtotime($entry->created_at)) }}">
                            {{ date('M j Y', strtotime($entry->created_at)) }}</p>
                        <div class="grow-0 shrink-0 relative sort-cost" data-cost="{{ trim($entry->credits) }}">
                            <button
                                class="inline-flex items-center justify-center p-0 border-none bg-[transparent] !w-7 !h-7 rounded-full text-inherit transition-all relative z-10 hover:bg-black hover:text-white group-[.theme-dark]/body:hover:bg-zinc-800 group-[.theme-dark]/body:hover:text-white hover:border hover:border-solid border-[--tblr-border-color] hover:shadow-sm"
                                data-bs-toggle="dropdown">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_352_1536" style="mask-type:alpha" maskUnits="userSpaceOnUse"
                                        x="0" y="0" width="15" height="15">
                                        <rect width="15" height="15" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_352_1536)">
                                        <path
                                            d="M7.5 12.5C7.15625 12.5 6.86198 12.3776 6.61719 12.1328C6.3724 11.888 6.25 11.5938 6.25 11.25C6.25 10.9062 6.3724 10.612 6.61719 10.3672C6.86198 10.1224 7.15625 10 7.5 10C7.84375 10 8.13802 10.1224 8.38281 10.3672C8.6276 10.612 8.75 10.9062 8.75 11.25C8.75 11.5938 8.6276 11.888 8.38281 12.1328C8.13802 12.3776 7.84375 12.5 7.5 12.5ZM7.5 8.75C7.15625 8.75 6.86198 8.6276 6.61719 8.38281C6.3724 8.13802 6.25 7.84375 6.25 7.5C6.25 7.15625 6.3724 6.86198 6.61719 6.61719C6.86198 6.3724 7.15625 6.25 7.5 6.25C7.84375 6.25 8.13802 6.3724 8.38281 6.61719C8.6276 6.86198 8.75 7.15625 8.75 7.5C8.75 7.84375 8.6276 8.13802 8.38281 8.38281C8.13802 8.6276 7.84375 8.75 7.5 8.75ZM7.5 5C7.15625 5 6.86198 4.8776 6.61719 4.63281C6.3724 4.38802 6.25 4.09375 6.25 3.75C6.25 3.40625 6.3724 3.11198 6.61719 2.86719C6.86198 2.6224 7.15625 2.5 7.5 2.5C7.84375 2.5 8.13802 2.6224 8.38281 2.86719C8.6276 3.11198 8.75 3.40625 8.75 3.75C8.75 4.09375 8.6276 4.38802 8.38281 4.63281C8.13802 4.8776 7.84375 5 7.5 5Z" />
                                    </g>
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end z-20">
                                {{-- <a href="#" class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M8 20l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4h4z"></path> <path d="M13.5 6.5l4 4"></path> <path d="M16 18h4"></path> </svg>
                                Rename
                            </a> --}}
                                <a data-bs-toggle="modal" data-file-slug="{{ $entry->slug }}"
                                    data-bs-target="#moveFileModal"
                                    class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="var(--tblr-red)" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M8 13v-8.5a1.5 1.5 0 0 1 3 0v7.5" />
                                        <path d="M11 11.5v-2a1.5 1.5 0 0 1 3 0v2.5" />
                                        <path d="M14 10.5a1.5 1.5 0 0 1 3 0v1.5" />
                                        <path
                                            d="M17 11.5a1.5 1.5 0 0 1 3 0v4.5a6 6 0 0 1 -6 6h-2h.208a6 6 0 0 1 -5.012 -2.7l-.196 -.3c-.312 -.479 -1.407 -2.388 -3.286 -5.728a1.5 1.5 0 0 1 .536 -2.022a1.867 1.867 0 0 1 2.28 .28l1.47 1.47" />
                                        <path d="M2.541 5.594a13.487 13.487 0 0 1 2.46 -1.427" />
                                        <path d="M14 3.458c1.32 .354 2.558 .902 3.685 1.612" />
                                    </svg>
                                    {{ __('Move') }}
                                </a>
                                {{-- <a href="#" class="flex items-center gap-2 p-2 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 group-[.theme-dark]/body:hover:bg-zinc-900">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" stroke-width="2" stroke="var(--tblr-red)" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path> <path d="M9 12l6 0"></path> </svg>
                                Remove
                            </a> --}}
                            </div>
                        </div>
                    </footer>
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
                        class="absolute top-0 left-0 w-full h-full z-[2]" title="{{ __('View and edit') }}"></a>
                </article>
            @endif
        @endforeach
    </div>
</div>
