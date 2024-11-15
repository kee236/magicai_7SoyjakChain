@extends('panel.layout.app')

@section('title', __($title))

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 items-center justify-content-between">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.voice.index')) }}"
                       class="page-pretitle flex items-center">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z" />
                        </svg>
                        {{ __('Back to voices') }}
                    </a>
                    <h2 class="page-title mb-2">
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-7 mx-auto">
                    <div class="bg-blue-100 text-blue-600 rounded-xl !p-3 mt-3 dark:bg-blue-600/20 dark:text-blue-200 mb-3">
                        <svg class="inline !me-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M12 9h.01"></path> <path d="M11 12h1v4h1"></path> </svg>
                        @lang('The cloned voice will appear in the list of voices available on AI Voiceover')
                    </div>
                    <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method($method)
                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">{{__('Name')}}</label>
                                            <input
                                                    type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="name"
                                                    name="name"
                                                    value="{{ old('name', $item?->name) }}"
                                                    required
                                            >
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">{{__('Status')}}</label>
                                            <select
                                                    class="form-select @error('status') is-invalid @enderror"
                                                    id="status"
                                                    name="status"
                                            >
                                                <option {{ $item?->status == '1' ? 'selected' : '' }} value="1">{{__('Active')}}</option>
                                                <option {{ $item?->status == '0' ? 'selected' : '' }} value="0">{{__('Passive')}}</option>
                                            </select>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @if(! $item?->id)
                                        <div class="mb-3 col-m-12">
                                            <label class="form-label">{{__('File')}}</label>
                                            <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" id="file">
                                            @error('file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                                <button  type="submit" class="btn btn-primary w-100">
                                    {{__('Add voice')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
