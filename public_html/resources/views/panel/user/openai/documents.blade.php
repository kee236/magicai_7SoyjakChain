@extends('panel.layout.app')
@section('title',  __('My Documents'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-end justify-between max-md:flex-col max-md:items-start max-md:gap-4">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}"
                        class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to dashboard') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ $currfolder?->name ?? __('My Documents') }}
                    </h2>
                    <div class="flex items-center flex-wrap !mt-5">
                        <div class="flex flex-wrap items-center gap-3">
                            {{ __('Sort by:') }}
                            <div class="grow-0 shrink-0 relative">
                                <button
                                    class="inline-flex items-center justify-center px-2 py-[0.15rem] rounded-md border-none bg-[transparent] text-inherit transition-[background] hover:bg-black hover:bg-opacity-5"
                                    data-bs-toggle="dropdown">
                                    <svg width="16" height="11" viewBox="0 0 16 11" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M6.33333 10.5V8.83333H9.66667V10.5H6.33333ZM3 6.33333V4.66667H13V6.33333H3ZM0.5 2.16667V0.5H15.5V2.16667H0.5Z" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu z-10">
                                    <button data-sort-type="file"
                                        class="flex items-center gap-2 w-full p-2 px-3 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900">
                                        {{ __('Type') }}
                                    </button>
                                    <button data-sort-type="name"
                                        class="flex items-center gap-2 w-full p-2 px-3 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900">
                                        {{ __('Name') }}
                                    </button>
                                    <button data-sort-type="date"
                                        class="flex items-center gap-2 w-full p-2 px-3 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900">
                                        {{ __('Date') }}
                                    </button>
                                    <button data-sort-type="cost"
                                        class="flex items-center gap-2 w-full p-2 px-3 border-none rounded-md bg-[transparent] text-[12px] !no-underline font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900">
                                        {{ __('Cost') }}
                                    </button>
                                </div>
                            </div>
                            <form id="filterForm" method="GET"
                                action="{{ route('dashboard.user.openai.documents.all') }}">

                                <ul
                                    class="flex flex-wrap items-center m-0 p-0 list-none text-[13px] text-[#2B2F37] gap-[20px] max-sm:gap-[10px]">
                                    <li>
                                        <button name="filter" value="all" data-filter-trigger="all"
                                            class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5 {{ $filter == 'all' ? 'active' : '' }}">
                                            {{ __('All') }}
                                        </button>
                                    </li>
                                    <li>
                                        <button name="filter" value="favorites" data-filter-trigger="favorite"
                                            class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5 {{ $filter == 'favorites' ? 'active' : '' }}">
                                            {{ __('Favorites') }}
                                        </button>
                                    </li>
                                    <li>
                                        <button name="filter" value="text" data-filter-trigger="text"
                                            class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5 {{ $filter == 'text' ? 'active' : '' }}">
                                            {{ __('Text') }}
                                        </button>
                                    </li>
                                    <li>
                                        <a href="{{ route('dashboard.user.openai.generator', 'ai_image_generator') }}"
                                            data-filter-trigger="image"
                                            class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5 {{ $filter == 'image' ? 'active' : '' }}">
                                            {{ __('Image') }}
                                        </a>
                                    </li>
                                    <li>
                                        <button name="filter" value="code" data-filter-trigger="code"
                                            class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5 {{ $filter == 'code' ? 'active' : '' }}">
                                            {{ __('Code') }}
                                        </button>
                                    </li>
                                    {{-- <li>
                                        <button name="filter" value="transcription" data-filter-trigger="transcription" class="filter-button inline-flex leading-none p-[0.3em_0.65em] rounded-full bg-[transparent] border-0 text-inherit hover:no-underline hover:bg-black/5 transition-colors [&.active]:bg-black/5 dark:text-[--tblr-muted] dark:[&.active]:bg-[--lqd-faded-out] dark:[&.active]:text-[--lqd-heading-color] dark:hover:bg-white/5 {{$filter=="transcription"?"active":""}}">
                                            {{__('Transcription')}}
                                        </button>
                                    </li> --}}
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        @if ($currfolder == null)
                            <a @if ($app_is_demo) onclick="return toastr.info('This feature is disabled in Demo version.')" @else data-bs-toggle="modal" data-bs-target="#creatFolderModal" @endif
                                class="btn btn-primary items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="!me-2" width="18" height="18"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                {{ __('New Folder') }}
                            </a>
                        @else
                            <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.all')) }}"
                                class="btn">
                                {{ __('My Documents') }}
                            </a>
                        @endif
                    </div>
                    <ul class="flex items-center list-none m-0 mt-3 p-0 gap-2 lg:justify-end">
                        <li>
                            <button
                                class="inline-flex w-8 h-8 items-center bg-transparent border-none justify-center rounded-md text-heading -mb-4 hover:!bg-black/5 dark:hover:!bg-white/5"
                                onclick="toggleView('dlist')" title="List view">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M9 6l11 0"></path>
                                    <path d="M9 12l11 0"></path>
                                    <path d="M9 18l11 0"></path>
                                    <path d="M5 6l0 .01"></path>
                                    <path d="M5 12l0 .01"></path>
                                    <path d="M5 18l0 .01"></path>
                                </svg>
                            </button>
                        </li>
                        <li>
                            <button
                                class="inline-flex w-8 h-8 items-center bg-transparent border-none justify-center rounded-md text-heading -mb-4 hover:!bg-black/5 dark:hover:!bg-white/5"
                                onclick="toggleView('dgrid')" title="Grid view">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z">
                                    </path>
                                    <path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z">
                                    </path>
                                    <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z">
                                    </path>
                                    <path
                                        d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z">
                                    </path>
                                </svg>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            @if ($filter != 'image')
                @if (view()->exists('panel.admin.custom.user.openai.filters.' . $filter))
                    @include('panel.admin.custom.user.openai.filters.' . $filter)
                @else
                    @include('panel.user.openai.filters.' . $filter)
                @endif
            @endif
        </div>
    </div>

    <!-- Rename Folder Modal -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameModalLabel">{{ __('Rename Folder') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="newFolderName" class="form-label">{{ __('New Folder Name:') }}</label>
                    <input type="text" class="form-control" id="newFolderName" name="newFolderName" required>
                    <input type="hidden" class="form-control" id="modalFolderId" name="modalFolderId" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary"
                        @if ($app_is_demo) onclick="return toastr.info('This feature is disabled in Demo version.')" @else onclick="renameFolder()" @endif>{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename File Modal -->
    <div class="modal fade" id="renameFileModal" tabindex="-1" aria-labelledby="renameFileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameFileModalLabel">{{ __('Rename File') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="newFileName" class="form-label">{{ __('New File Name:') }}</label>
                    <input type="text" class="form-control" id="newFileName" name="newFFileName" required>
                    <input type="hidden" class="form-control" id="modalFileSlug" name="modalFileSlug" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="renameFile()">{{ __('Save changes') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Deleting Folders Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">{{ __('Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Do you want to delete all files inside the folder?') }}</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="deleteAllFilesCheckbox">
                        <label class="form-check-label" for="deleteAllFilesCheckbox">
                            {{ __('Delete all files') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary"
                        @if ($app_is_demo) onclick="return toastr.info('This feature is disabled in Demo version.')" @else onclick="confirmDelete()" @endif>{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade" id="creatFolderModal" tabindex="-1" aria-labelledby="creatFolderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('dashboard.user.openai.documents.new-folder') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="creatFolderModalLabel">{{ __('New Folder') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="newFolderName" class="form-label">{{ __('New Folder Name:') }}</label>
                        <input type="text" class="form-control" name="newFolderName" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Move File Modal -->
    <div class="modal fade" id="moveFileModal" tabindex="-1" aria-labelledby="moveFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('dashboard.user.openai.documents.move-to-folder') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="moveFileModalLabel">{{ __('Move File') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="fileslug" id="fileslug" />
                        <label for="newFolderName" class="form-label">{{ __('Select Folder:') }}</label>
                        <select class="form-control" name="selectedFolderId" required>
                            @foreach (auth()->user()->folders ?? [] as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Move') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="/assets/js/panel/openai_list.js"></script>
    <script>
        function toggleView(view) {
            // Hide all elements with the class 'grid' and 'normal'
            document.querySelectorAll('.dgrid, .dlist').forEach(element => {
                element.style.display = 'none';
            });

            // Show the selected view
            document.querySelectorAll('.' + view).forEach(element => {
                element.style.display = 'block';
            });
        }
        toggleView('dlist');
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tableBody = document.querySelector('#table-default tbody');
            const sortButtons = document.querySelectorAll('[data-sort-type]');

            sortButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const sortType = button.getAttribute('data-sort-type');
                    sortBothViews(tableBody, sortType);
                });
            });

            function sortBothViews(tableBody, sortType) {
                sortTable(tableBody, sortType);

                const gridElements = document.querySelectorAll('.dgrid article');
                const gridContainer = document.querySelector('.dgrid .grid');

                sortGrid(gridElements, gridContainer, sortType);
            }

            function sortTable(tableBody, sortType) {
                const rows = Array.from(tableBody.querySelectorAll('tr'));

                rows.sort((a, b) => {
                    const aValue = extractSortValue(a, sortType);
                    const bValue = extractSortValue(b, sortType);

                    // Convert values to appropriate types for comparison
                    const convertedA = convertForComparison(aValue);
                    const convertedB = convertForComparison(bValue);

                    // Compare values
                    if (convertedA < convertedB) {
                        return -1;
                    } else if (convertedA > convertedB) {
                        return 1;
                    } else {
                        return 0;
                    }
                });

                // Remove existing rows
                rows.forEach(row => tableBody.removeChild(row));

                // Append sorted rows
                rows.forEach(row => tableBody.appendChild(row));
            }

            function sortGrid(gridElements, gridContainer, sortType) {
                const sortedGridElements = Array.from(gridElements).sort((a, b) => {
                    const aValue = extractSortValue(a, sortType);
                    const bValue = extractSortValue(b, sortType);

                    const convertedA = convertForComparison(aValue);
                    const convertedB = convertForComparison(bValue);

                    if (convertedA < convertedB) {
                        return -1;
                    } else if (convertedA > convertedB) {
                        return 1;
                    } else {
                        return 0;
                    }
                });

                // Remove existing grid elements
                gridElements.forEach(element => element.parentNode.removeChild(element));

                // Append sorted grid elements
                sortedGridElements.forEach(element => gridContainer.appendChild(element));
            }

            function extractSortValue(row, sortType) {
                switch (sortType) {
                    case 'file':
                        return row.querySelector('.sort-file').getAttribute('data-file');
                    case 'name':
                        return row.querySelector('.sort-name').getAttribute('data-name');
                    case 'date':
                        return row.querySelector('.sort-date').getAttribute('data-date');
                    case 'cost':
                        return row.querySelector('.sort-cost').getAttribute('data-cost');
                    default:
                        return '';
                }
            }

            function convertForComparison(value) {
                const numericValue = parseFloat(value);
                return isNaN(numericValue) ? value : numericValue;
            }

        });
    </script>
    <script>
        $('#renameFileModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var fileId = button.data('file-slug');
            $('#modalFileSlug').val(fileId);
        });

        function renameFile() {
            var fileSlug = $('#modalFileSlug').val();
            var newFileName = $('#newFileName').val();
            $.ajax({
                url: '/dashboard/user/openai/documents/update-file/' + fileSlug,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    newFileName: newFileName
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#file' + fileSlug).text(newFileName);
                    $('#renameFileModal').modal('hide');
                },
                error: function(error) {
                    toastr.error("{{ __('Error updating folder name:') }}", error);
                }
            });
        }
    </script>
    <script>
        $('#renameModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var folderId = button.data('folder-id');
            $('#modalFolderId').val(folderId);
        });

        function renameFolder() {
            var folderId = $('#modalFolderId').val();
            var newFolderName = $('#newFolderName').val();
            $.ajax({
                url: '/dashboard/user/openai/documents/update-folder/' + folderId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    newFolderName: newFolderName
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#folder' + folderId).text(newFolderName);
                    $('#renameModal').modal('hide');
                },
                error: function(error) {
                    toastr.error("{{ __('Error updating folder name:') }}", error);
                }
            });
        }
    </script>
    <script>
        function removeFolder(folderId) {
            $('#confirmationModal').modal('show');
            $('#confirmationModal .btn-primary').data('folder-id', folderId);
        }

        function confirmDelete() {
            var folderId = $('#confirmationModal .btn-primary').data('folder-id');
            var deleteAllFiles = $('#deleteAllFilesCheckbox').prop('checked');
            var all = 0;
            if (deleteAllFiles) {
                all = 1;
            }
            $.ajax({
                url: '/dashboard/user/openai/documents/delete-folder/' + folderId,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    folderId: folderId,
                    all: all
                }),
                success: function(data) {
                    toastr.success(data.message);
                    $('#confirmationModal').modal('hide');
                    location.reload();
                },
                error: function(error) {
                    toastr.error("{{ __('Error deleting folder:') }}", data.message);
                    $('#confirmationModal').modal('hide');
                }
            });
        }
    </script>
    <script>
        $('#moveFileModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var fileSlug = button.data('file-slug');
            $('#fileslug').val(fileSlug);
        });
    </script>
    <script>
        $('.filter-button').on('click', function() {
            $('#filterForm').submit();
        });
    </script>

@endsection
